<?php

class StokModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getStok(int $idProduk, int $idGudang): int {
        $stmt = $this->db->prepare("SELECT jumlah_stok FROM stok WHERE id_produk = ? AND id_gudang = ?");
        $stmt->execute([$idProduk, $idGudang]);
        $res = $stmt->fetch();
        return $res ? (int) $res['jumlah_stok'] : 0;
    }

    public function tambahStok(int $idProduk, int $idGudang, int $jumlah): bool {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah penambahan stok harus lebih dari 0.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO stok (id_produk, id_gudang, jumlah_stok)
            VALUES (:produk, :gudang, :jumlah)
            ON DUPLICATE KEY UPDATE jumlah_stok = jumlah_stok + :jumlah_dup
        ");
        return $stmt->execute([
            ':produk'     => $idProduk,
            ':gudang'     => $idGudang,
            ':jumlah'     => $jumlah,
            ':jumlah_dup' => $jumlah
        ]);
    }

    public function kurangStok(int $idProduk, int $idGudang, int $jumlah): bool {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah pengurangan stok harus lebih dari 0.");
        }

        $stokTersedia = $this->getStok($idProduk, $idGudang);
        if ($stokTersedia < $jumlah) {
            throw new RuntimeException("Stok tidak mencukupi! Stok tersedia: {$stokTersedia}, permintaan: {$jumlah}.");
        }

        $stmt = $this->db->prepare("
            UPDATE stok
            SET jumlah_stok = jumlah_stok - :jumlah
            WHERE id_produk = :produk AND id_gudang = :gudang
        ");
        return $stmt->execute([
            ':jumlah' => $jumlah,
            ':produk' => $idProduk,
            ':gudang' => $idGudang
        ]);
    }

    public function setStok(int $idProduk, int $idGudang, int $stokFisik): bool {
        if ($stokFisik < 0) {
            throw new InvalidArgumentException("Stok fisik tidak boleh negatif.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO stok (id_produk, id_gudang, jumlah_stok)
            VALUES (:produk, :gudang, :jumlah)
            ON DUPLICATE KEY UPDATE jumlah_stok = :jumlah_dup
        ");
        return $stmt->execute([
            ':produk'     => $idProduk,
            ':gudang'     => $idGudang,
            ':jumlah'     => $stokFisik,
            ':jumlah_dup' => $stokFisik
        ]);
    }

    public function getTotalStok(): int {
        $stmt = $this->db->query("SELECT COALESCE(SUM(jumlah_stok), 0) AS total FROM stok");
        return (int) $stmt->fetch()['total'];
    }
}
