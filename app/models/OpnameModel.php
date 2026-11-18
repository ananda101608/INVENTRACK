<?php

require_once __DIR__ . '/StokModel.php';

class OpnameModel {
    private PDO $db;
    private StokModel $stokModel;

    public function __construct() {
        $this->db = db();
        $this->stokModel = new StokModel();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT o.*, g.nama_gudang, u.nama AS nama_petugas,
                   COUNT(dso.id_detail_opname) AS total_item,
                   COALESCE(SUM(dso.selisih), 0) AS total_selisih
            FROM stok_opname o
            JOIN gudang g ON o.id_gudang = g.id_gudang
            JOIN pengguna u ON o.id_pengguna = u.id_pengguna
            LEFT JOIN detail_stok_opname dso ON o.id_opname = dso.id_opname
            GROUP BY o.id_opname
            ORDER BY o.tanggal_opname DESC, o.id_opname DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*, g.nama_gudang, u.nama AS nama_petugas
            FROM stok_opname o
            JOIN gudang g ON o.id_gudang = g.id_gudang
            JOIN pengguna u ON o.id_pengguna = u.id_pengguna
            WHERE o.id_opname = ?
        ");
        $stmt->execute([$id]);
        $header = $stmt->fetch();
        if (!$header) return null;

        $stmtItem = $this->db->prepare("
            SELECT dso.*, p.kode_produk, p.nama_produk, s.singkatan AS nama_satuan
            FROM detail_stok_opname dso
            JOIN produk p ON dso.id_produk = p.id_produk
            JOIN satuan s ON p.id_satuan = s.id_satuan
            WHERE dso.id_opname = ?
        ");
        $stmtItem->execute([$id]);
        $header['items'] = $stmtItem->fetchAll();

        return $header;
    }

    /**
     * Simpan proses opname & sesuaikan saldo stok sistem ke saldo fisik
     */
    public function createTransaksi(array $header, array $items): int {
        if (empty($items)) {
            throw new InvalidArgumentException("Stok opname harus memeriksa minimal 1 produk.");
        }

        $this->db->beginTransaction();

        try {
            $idGudang = (int) $header['id_gudang'];

            $stmtHeader = $this->db->prepare("
                INSERT INTO stok_opname (id_gudang, id_pengguna, tanggal_opname, catatan)
                VALUES (:gudang, :pengguna, :tanggal, :catatan)
            ");
            $stmtHeader->execute([
                ':gudang'   => $idGudang,
                ':pengguna' => $header['id_pengguna'],
                ':tanggal'  => $header['tanggal_opname'] ?? date('Y-m-d H:i:s'),
                ':catatan'  => $header['catatan'] ?? ''
            ]);

            $idOpname = (int) $this->db->lastInsertId();

            $stmtDetail = $this->db->prepare("
                INSERT INTO detail_stok_opname (id_opname, id_produk, stok_sistem, stok_fisik, selisih)
                VALUES (:opname, :produk, :sistem, :fisik, :selisih)
            ");

            foreach ($items as $item) {
                $idProduk   = (int) $item['id_produk'];
                $stokFisik  = (int) $item['stok_fisik'];
                $stokSistem = $this->stokModel->getStok($idProduk, $idGudang);
                $selisih    = $stokFisik - $stokSistem;

                $stmtDetail->execute([
                    ':opname'  => $idOpname,
                    ':produk'  => $idProduk,
                    ':sistem'  => $stokSistem,
                    ':fisik'   => $stokFisik,
                    ':selisih' => $selisih
                ]);

                // Sesuaikan saldo stok sistem ke fisik
                $this->stokModel->setStok($idProduk, $idGudang, $stokFisik);
            }

            $this->db->commit();
            return $idOpname;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
