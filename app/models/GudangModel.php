<?php

class GudangModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT g.*, 
                   COALESCE(SUM(s.jumlah_stok), 0) AS total_stok,
                   COUNT(DISTINCT s.id_produk) AS total_jenis_produk
            FROM gudang g
            LEFT JOIN stok s ON g.id_gudang = s.id_gudang
            GROUP BY g.id_gudang
            ORDER BY g.nama_gudang ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM gudang WHERE id_gudang = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getStokByGudang(int $idGudang): array {
        $stmt = $this->db->prepare("
            SELECT s.*, p.kode_produk, p.nama_produk, p.stok_minimum, p.harga_beli, p.harga_jual,
                   k.nama_kategori, sat.singkatan AS nama_satuan
            FROM stok s
            JOIN produk p ON s.id_produk = p.id_produk
            JOIN kategori k ON p.id_kategori = k.id_kategori
            JOIN satuan sat ON p.id_satuan = sat.id_satuan
            WHERE s.id_gudang = ?
            ORDER BY p.nama_produk ASC
        ");
        $stmt->execute([$idGudang]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO gudang (nama_gudang, alamat, penanggung_jawab)
            VALUES (:nama, :alamat, :pj)
        ");
        $stmt->execute([
            ':nama'   => $data['nama_gudang'],
            ':alamat' => $data['alamat'] ?? '',
            ':pj'     => $data['penanggung_jawab'] ?? ''
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE gudang
            SET nama_gudang = :nama, alamat = :alamat, penanggung_jawab = :pj
            WHERE id_gudang = :id
        ");
        return $stmt->execute([
            ':nama'   => $data['nama_gudang'],
            ':alamat' => $data['alamat'] ?? '',
            ':pj'     => $data['penanggung_jawab'] ?? '',
            ':id'     => $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM gudang WHERE id_gudang = ?");
        return $stmt->execute([$id]);
    }
}
