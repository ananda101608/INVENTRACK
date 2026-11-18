<?php

class KategoriModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT k.*, COUNT(p.id_produk) AS total_produk
            FROM kategori k
            LEFT JOIN produk p ON k.id_kategori = p.id_kategori
            GROUP BY k.id_kategori
            ORDER BY k.nama_kategori ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (:nama_kategori, :deskripsi)");
        $stmt->execute([
            ':nama_kategori' => $data['nama_kategori'],
            ':deskripsi'     => $data['deskripsi'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE kategori SET nama_kategori = :nama_kategori, deskripsi = :deskripsi WHERE id_kategori = :id");
        return $stmt->execute([
            ':nama_kategori' => $data['nama_kategori'],
            ':deskripsi'     => $data['deskripsi'] ?? null,
            ':id'            => $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        return $stmt->execute([$id]);
    }
}
