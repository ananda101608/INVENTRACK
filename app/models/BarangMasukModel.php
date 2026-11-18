<?php

require_once __DIR__ . '/StokModel.php';

class BarangMasukModel {
    private PDO $db;
    private StokModel $stokModel;

    public function __construct() {
        $this->db = db();
        $this->stokModel = new StokModel();
    }

    public function getAll(?string $tglAwal = null, ?string $tglAkhir = null): array {
        $sql = "
            SELECT bm.*, p.nama_pemasok, g.nama_gudang, u.nama AS nama_petugas,
                   COUNT(dbm.id_detail_masuk) AS total_item,
                   COALESCE(SUM(dbm.jumlah), 0) AS total_kuantitas,
                   COALESCE(SUM(dbm.jumlah * dbm.harga_satuan), 0) AS total_nominal
            FROM barang_masuk bm
            JOIN pemasok p ON bm.id_pemasok = p.id_pemasok
            JOIN gudang g ON bm.id_gudang = g.id_gudang
            JOIN pengguna u ON bm.id_pengguna = u.id_pengguna
            LEFT JOIN detail_barang_masuk dbm ON bm.id_barang_masuk = dbm.id_barang_masuk
            WHERE 1=1
        ";
        $params = [];

        if ($tglAwal && $tglAkhir) {
            $sql .= " AND DATE(bm.tanggal_masuk) BETWEEN :awal AND :akhir";
            $params[':awal'] = $tglAwal;
            $params[':akhir'] = $tglAkhir;
        }

        $sql .= " GROUP BY bm.id_barang_masuk ORDER BY bm.tanggal_masuk DESC, bm.id_barang_masuk DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT bm.*, p.nama_pemasok, p.nomor_telepon AS telp_pemasok,
                   g.nama_gudang, u.nama AS nama_petugas
            FROM barang_masuk bm
            JOIN pemasok p ON bm.id_pemasok = p.id_pemasok
            JOIN gudang g ON bm.id_gudang = g.id_gudang
            JOIN pengguna u ON bm.id_pengguna = u.id_pengguna
            WHERE bm.id_barang_masuk = ?
        ");
        $stmt->execute([$id]);
        $header = $stmt->fetch();
        if (!$header) return null;

        $stmtItem = $this->db->prepare("
            SELECT dbm.*, pr.kode_produk, pr.nama_produk, s.singkatan AS nama_satuan,
                   (dbm.jumlah * dbm.harga_satuan) AS subtotal
            FROM detail_barang_masuk dbm
            JOIN produk pr ON dbm.id_produk = pr.id_produk
            JOIN satuan s ON pr.id_satuan = s.id_satuan
            WHERE dbm.id_barang_masuk = ?
        ");
        $stmtItem->execute([$id]);
        $header['items'] = $stmtItem->fetchAll();

        return $header;
    }

    /**
     * Simpan transaksi Barang Masuk dengan ACID Transaction & Update Stok
     */
    public function createTransaksi(array $header, array $items): int {
        if (empty($items)) {
            throw new InvalidArgumentException("Transaksi barang masuk harus memiliki minimal 1 produk.");
        }

        $this->db->beginTransaction();

        try {
            // 1. Simpan Header Barang Masuk
            $stmtHeader = $this->db->prepare("
                INSERT INTO barang_masuk (id_pemasok, id_pengguna, id_gudang, tanggal_masuk, catatan)
                VALUES (:pemasok, :pengguna, :gudang, :tanggal, :catatan)
            ");
            $stmtHeader->execute([
                ':pemasok'  => $header['id_pemasok'],
                ':pengguna' => $header['id_pengguna'],
                ':gudang'   => $header['id_gudang'],
                ':tanggal'  => $header['tanggal_masuk'] ?? date('Y-m-d H:i:s'),
                ':catatan'  => $header['catatan'] ?? ''
            ]);

            $idBarangMasuk = (int) $this->db->lastInsertId();

            // 2. Simpan Detail & Tambah Stok
            $stmtDetail = $this->db->prepare("
                INSERT INTO detail_barang_masuk (id_barang_masuk, id_produk, jumlah, harga_satuan)
                VALUES (:id_masuk, :produk, :jumlah, :harga)
            ");

            foreach ($items as $item) {
                $idProduk = (int) $item['id_produk'];
                $jumlah   = (int) $item['jumlah'];
                $harga    = (float) ($item['harga_satuan'] ?? 0);

                if ($jumlah <= 0) {
                    throw new InvalidArgumentException("Kuantitas barang harus lebih dari 0.");
                }

                $stmtDetail->execute([
                    ':id_masuk' => $idBarangMasuk,
                    ':produk'   => $idProduk,
                    ':jumlah'   => $jumlah,
                    ':harga'    => $harga
                ]);

                // Update saldo stok gudang terkait
                $this->stokModel->tambahStok($idProduk, (int) $header['id_gudang'], $jumlah);
            }

            $this->db->commit();
            return $idBarangMasuk;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
