<?php

class SatuanModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT s.*, COUNT(p.id_produk) AS total_produk
            FROM satuan s
            LEFT JOIN produk p ON s.id_satuan = p.id_satuan
            GROUP BY s.id_satuan
            ORDER BY s.nama_satuan ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM satuan WHERE id_satuan = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO satuan (nama_satuan, singkatan) VALUES (:nama_satuan, :singkatan)");
        $stmt->execute([
            ':nama_satuan' => $data['nama_satuan'],
            ':singkatan'   => $data['singkatan']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE satuan SET nama_satuan = :nama_satuan, singkatan = :singkatan WHERE id_satuan = :id");
        return $stmt->execute([
            ':nama_satuan' => $data['nama_satuan'],
            ':singkatan'   => $data['singkatan'],
            ':id'          => $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM satuan WHERE id_satuan = ?");
        return $stmt->execute([$id]);
    }
}
