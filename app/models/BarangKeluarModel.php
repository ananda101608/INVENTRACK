<?php

require_once __DIR__ . '/StokModel.php';

class BarangKeluarModel {
    private PDO $db;
    private StokModel $stokModel;

    public function __construct() {
        $this->db = db();
        $this->stokModel = new StokModel();
    }

    public function getAll(?string $tglAwal = null, ?string $tglAkhir = null): array {
        $sql = "
            SELECT bk.*, g.nama_gudang, u.nama AS nama_petugas,
                   COUNT(dbk.id_detail_keluar) AS total_item,
                   COALESCE(SUM(dbk.jumlah), 0) AS total_kuantitas
            FROM barang_keluar bk
            JOIN gudang g ON bk.id_gudang = g.id_gudang
            JOIN pengguna u ON bk.id_pengguna = u.id_pengguna
            LEFT JOIN detail_barang_keluar dbk ON bk.id_barang_keluar = dbk.id_barang_keluar
            WHERE 1=1
        ";
        $params = [];

        if ($tglAwal && $tglAkhir) {
            $sql .= " AND DATE(bk.tanggal_keluar) BETWEEN :awal AND :akhir";
            $params[':awal'] = $tglAwal;
            $params[':akhir'] = $tglAkhir;
        }

        $sql .= " GROUP BY bk.id_barang_keluar ORDER BY bk.tanggal_keluar DESC, bk.id_barang_keluar DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT bk.*, g.nama_gudang, u.nama AS nama_petugas
            FROM barang_keluar bk
            JOIN gudang g ON bk.id_gudang = g.id_gudang
            JOIN pengguna u ON bk.id_pengguna = u.id_pengguna
            WHERE bk.id_barang_keluar = ?
        ");
        $stmt->execute([$id]);
        $header = $stmt->fetch();
        if (!$header) return null;

        $stmtItem = $this->db->prepare("
            SELECT dbk.*, pr.kode_produk, pr.nama_produk, s.singkatan AS nama_satuan
            FROM detail_barang_keluar dbk
            JOIN produk pr ON dbk.id_produk = pr.id_produk
            JOIN satuan s ON pr.id_satuan = s.id_satuan
            WHERE dbk.id_barang_keluar = ?
        ");
        $stmtItem->execute([$id]);
        $header['items'] = $stmtItem->fetchAll();

        return $header;
    }

    /**
     * Simpan transaksi Barang Keluar dengan Validasi Stok & ACID Transaction
     */
    public function createTransaksi(array $header, array $items): int {
        if (empty($items)) {
            throw new InvalidArgumentException("Transaksi barang keluar harus memiliki minimal 1 produk.");
        }

        $this->db->beginTransaction();

        try {
            $idGudang = (int) $header['id_gudang'];

            // 1. Simpan Header Barang Keluar
            $stmtHeader = $this->db->prepare("
                INSERT INTO barang_keluar (id_pengguna, id_gudang, tanggal_keluar, nama_penerima, tujuan, catatan)
                VALUES (:pengguna, :gudang, :tanggal, :penerima, :tujuan, :catatan)
            ");
            $stmtHeader->execute([
                ':pengguna' => $header['id_pengguna'],
                ':gudang'   => $idGudang,
                ':tanggal'  => $header['tanggal_keluar'] ?? date('Y-m-d H:i:s'),
                ':penerima' => $header['nama_penerima'],
                ':tujuan'   => $header['tujuan'],
                ':catatan'  => $header['catatan'] ?? ''
            ]);

            $idBarangKeluar = (int) $this->db->lastInsertId();

            // 2. Simpan Detail & Kurangi Stok (otomatis dicek kecukupan stoknya)
            $stmtDetail = $this->db->prepare("
                INSERT INTO detail_barang_keluar (id_barang_keluar, id_produk, jumlah)
                VALUES (:id_keluar, :produk, :jumlah)
            ");

            foreach ($items as $item) {
                $idProduk = (int) $item['id_produk'];
                $jumlah   = (int) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new InvalidArgumentException("Kuantitas barang keluar harus lebih dari 0.");
                }

                // Kurangi stok (akan melempar RuntimeException jika stok < jumlah)
                $this->stokModel->kurangStok($idProduk, $idGudang, $jumlah);

                $stmtDetail->execute([
                    ':id_keluar' => $idBarangKeluar,
                    ':produk'    => $idProduk,
                    ':jumlah'    => $jumlah
                ]);
            }

            $this->db->commit();
            return $idBarangKeluar;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
