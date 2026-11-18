<?php

class PenggunaModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT id_pengguna, nama, email, peran, status, dibuat_pada FROM pengguna ORDER BY id_pengguna DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id_pengguna, nama, email, peran, status, dibuat_pada FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pengguna WHERE email = ?");
        $stmt->execute([$email]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO pengguna (nama, email, kata_sandi, peran, status, dibuat_pada)
            VALUES (:nama, :email, :kata_sandi, :peran, :status, NOW())
        ");
        $stmt->execute([
            ':nama'       => $data['nama'],
            ':email'      => $data['email'],
            ':kata_sandi' => $data['kata_sandi'],
            ':peran'      => $data['peran'] ?? 'petugas',
            ':status'     => $data['status'] ?? 'aktif'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE pengguna SET nama = :nama, email = :email, peran = :peran, status = :status WHERE id_pengguna = :id";
        $params = [
            ':nama'   => $data['nama'],
            ':email'  => $data['email'],
            ':peran'  => $data['peran'],
            ':status' => $data['status'],
            ':id'     => $id
        ];
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePassword(int $id, string $hashPassword): bool {
        $stmt = $this->db->prepare("UPDATE pengguna SET kata_sandi = ? WHERE id_pengguna = ?");
        return $stmt->execute([$hashPassword, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        return $stmt->execute([$id]);
    }

    public function toggleStatus(int $id): bool {
        $user = $this->getById($id);
        if (!$user) return false;
        $newStatus = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $stmt = $this->db->prepare("UPDATE pengguna SET status = ? WHERE id_pengguna = ?");
        return $stmt->execute([$newStatus, $id]);
    }
}
