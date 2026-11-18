<?php

require_once __DIR__ . '/StokModel.php';

class TransferModel {
    private PDO $db;
    private StokModel $stokModel;

    public function __construct() {
        $this->db = db();
        $this->stokModel = new StokModel();
    }

    public function getAll(?string $status = null): array {
        $sql = "
            SELECT t.*, ga.nama_gudang AS gudang_asal, gt.nama_gudang AS gudang_tujuan,
                   u.nama AS nama_petugas,
                   COUNT(dt.id_detail_transfer) AS total_item,
                   COALESCE(SUM(dt.jumlah), 0) AS total_kuantitas
            FROM transfer_gudang t
            JOIN gudang ga ON t.id_gudang_asal = ga.id_gudang
            JOIN gudang gt ON t.id_gudang_tujuan = gt.id_gudang
            JOIN pengguna u ON t.id_pengguna = u.id_pengguna
            LEFT JOIN detail_transfer dt ON t.id_transfer = dt.id_transfer
            WHERE 1=1
        ";
        $params = [];

        if ($status) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " GROUP BY t.id_transfer ORDER BY t.tanggal_transfer DESC, t.id_transfer DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT t.*, ga.nama_gudang AS gudang_asal, gt.nama_gudang AS gudang_tujuan,
                   u.nama AS nama_petugas
            FROM transfer_gudang t
            JOIN gudang ga ON t.id_gudang_asal = ga.id_gudang
            JOIN gudang gt ON t.id_gudang_tujuan = gt.id_gudang
            JOIN pengguna u ON t.id_pengguna = u.id_pengguna
            WHERE t.id_transfer = ?
        ");
        $stmt->execute([$id]);
        $header = $stmt->fetch();
        if (!$header) return null;

        $stmtItem = $this->db->prepare("
            SELECT dt.*, pr.kode_produk, pr.nama_produk, s.singkatan AS nama_satuan
            FROM detail_transfer dt
            JOIN produk pr ON dt.id_produk = pr.id_produk
            JOIN satuan s ON pr.id_satuan = s.id_satuan
            WHERE dt.id_transfer = ?
        ");
        $stmtItem->execute([$id]);
        $header['items'] = $stmtItem->fetchAll();

        return $header;
    }

    /**
     * Buat transaksi transfer gudang dengan validasi stok asal & pemindahan saldo
     */
    public function createTransaksi(array $header, array $items): int {
        $idAsal   = (int) $header['id_gudang_asal'];
        $idTujuan = (int) $header['id_gudang_tujuan'];

        if ($idAsal === $idTujuan) {
            throw new InvalidArgumentException("Gudang asal dan gudang tujuan tidak boleh sama.");
        }

        if (empty($items)) {
            throw new InvalidArgumentException("Transfer harus memiliki minimal 1 produk.");
        }

        $this->db->beginTransaction();

        try {
            $status = $header['status'] ?? 'selesai';

            $stmtHeader = $this->db->prepare("
                INSERT INTO transfer_gudang (id_gudang_asal, id_gudang_tujuan, id_pengguna, tanggal_transfer, status, catatan)
                VALUES (:asal, :tujuan, :pengguna, :tanggal, :status, :catatan)
            ");
            $stmtHeader->execute([
                ':asal'     => $idAsal,
                ':tujuan'   => $idTujuan,
                ':pengguna' => $header['id_pengguna'],
                ':tanggal'  => $header['tanggal_transfer'] ?? date('Y-m-d H:i:s'),
                ':status'   => $status,
                ':catatan'  => $header['catatan'] ?? ''
            ]);

            $idTransfer = (int) $this->db->lastInsertId();

            $stmtDetail = $this->db->prepare("
                INSERT INTO detail_transfer (id_transfer, id_produk, jumlah)
                VALUES (:id_transfer, :produk, :jumlah)
            ");

            foreach ($items as $item) {
                $idProduk = (int) $item['id_produk'];
                $jumlah   = (int) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new InvalidArgumentException("Jumlah transfer harus lebih dari 0.");
                }

                // Jika status transfer 'selesai', langsung potong stok asal dan tambahkan ke gudang tujuan
                if ($status === 'selesai') {
                    $this->stokModel->kurangStok($idProduk, $idAsal, $jumlah);
                    $this->stokModel->tambahStok($idProduk, $idTujuan, $jumlah);
                }

                $stmtDetail->execute([
                    ':id_transfer' => $idTransfer,
                    ':produk'      => $idProduk,
                    ':jumlah'      => $jumlah
                ]);
            }

            $this->db->commit();
            return $idTransfer;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
