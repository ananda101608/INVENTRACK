<?php

class PemasokModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT p.*, COUNT(bm.id_barang_masuk) AS total_transaksi
            FROM pemasok p
            LEFT JOIN barang_masuk bm ON p.id_pemasok = bm.id_pemasok
            GROUP BY p.id_pemasok
            ORDER BY p.nama_pemasok ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pemasok WHERE id_pemasok = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO pemasok (nama_pemasok, nomor_telepon, email, alamat)
            VALUES (:nama, :telepon, :email, :alamat)
        ");
        $stmt->execute([
            ':nama'    => $data['nama_pemasok'],
            ':telepon' => $data['nomor_telepon'] ?? '',
            ':email'   => $data['email'] ?? '',
            ':alamat'  => $data['alamat'] ?? ''
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE pemasok
            SET nama_pemasok = :nama, nomor_telepon = :telepon, email = :email, alamat = :alamat
            WHERE id_pemasok = :id
        ");
        return $stmt->execute([
            ':nama'    => $data['nama_pemasok'],
            ':telepon' => $data['nomor_telepon'] ?? '',
            ':email'   => $data['email'] ?? '',
            ':alamat'  => $data['alamat'] ?? '',
            ':id'      => $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM pemasok WHERE id_pemasok = ?");
        return $stmt->execute([$id]);
    }
}
