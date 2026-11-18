<?php

class ProdukModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(?string $keyword = null, ?int $kategoriId = null): array {
        $sql = "
            SELECT p.*, k.nama_kategori, s.singkatan AS nama_satuan,
                   COALESCE(SUM(st.jumlah_stok), 0) AS total_stok
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN stok st ON p.id_produk = st.id_produk
            WHERE 1=1
        ";
        $params = [];

        if ($keyword) {
            $sql .= " AND (p.nama_produk LIKE :kw OR p.kode_produk LIKE :kw)";
            $params[':kw'] = "%{$keyword}%";
        }

        if ($kategoriId) {
            $sql .= " AND p.id_kategori = :kat";
            $params[':kat'] = $kategoriId;
        }

        $sql .= " GROUP BY p.id_produk ORDER BY p.id_produk DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, k.nama_kategori, s.nama_satuan, s.singkatan AS satuan_singkatan,
                   COALESCE(SUM(st.jumlah_stok), 0) AS total_stok
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN stok st ON p.id_produk = st.id_produk
            WHERE p.id_produk = ?
            GROUP BY p.id_produk
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByKode(string $kode): ?array {
        $stmt = $this->db->prepare("SELECT * FROM produk WHERE kode_produk = ?");
        $stmt->execute([$kode]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getStokPerGudang(int $idProduk): array {
        $stmt = $this->db->prepare("
            SELECT g.id_gudang, g.nama_gudang, COALESCE(st.jumlah_stok, 0) AS jumlah_stok
            FROM gudang g
            LEFT JOIN stok st ON g.id_gudang = st.id_gudang AND st.id_produk = ?
            ORDER BY g.nama_gudang ASC
        ");
        $stmt->execute([$idProduk]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO produk (
                id_kategori, id_satuan, kode_produk, nama_produk, deskripsi,
                harga_beli, harga_jual, stok_minimum, foto_produk, status, dibuat_pada
            ) VALUES (
                :kategori, :satuan, :kode, :nama, :deskripsi,
                :beli, :jual, :min, :foto, :status, NOW()
            )
        ");
        $stmt->execute([
            ':kategori'  => $data['id_kategori'],
            ':satuan'    => $data['id_satuan'],
            ':kode'      => $data['kode_produk'],
            ':nama'      => $data['nama_produk'],
            ':deskripsi' => $data['deskripsi'] ?? null,
            ':beli'      => $data['harga_beli'] ?? 0,
            ':jual'      => $data['harga_jual'] ?? 0,
            ':min'       => $data['stok_minimum'] ?? 5,
            ':foto'      => $data['foto_produk'] ?? 'default.png',
            ':status'    => $data['status'] ?? 'aktif'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "
            UPDATE produk SET
                id_kategori = :kategori,
                id_satuan = :satuan,
                kode_produk = :kode,
                nama_produk = :nama,
                deskripsi = :deskripsi,
                harga_beli = :beli,
                harga_jual = :jual,
                stok_minimum = :min,
                status = :status
        ";
        $params = [
            ':kategori'  => $data['id_kategori'],
            ':satuan'    => $data['id_satuan'],
            ':kode'      => $data['kode_produk'],
            ':nama'      => $data['nama_produk'],
            ':deskripsi' => $data['deskripsi'] ?? null,
            ':beli'      => $data['harga_beli'] ?? 0,
            ':jual'      => $data['harga_jual'] ?? 0,
            ':min'       => $data['stok_minimum'] ?? 5,
            ':status'    => $data['status'] ?? 'aktif',
            ':id'        => $id
        ];

        if (!empty($data['foto_produk'])) {
            $sql .= ", foto_produk = :foto";
            $params[':foto'] = $data['foto_produk'];
        }

        $sql .= " WHERE id_produk = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM produk WHERE id_produk = ?");
        return $stmt->execute([$id]);
    }

    public function getStokMenipis(): array {
        $stmt = $this->db->query("
            SELECT p.id_produk, p.kode_produk, p.nama_produk, p.stok_minimum, k.nama_kategori, s.singkatan AS nama_satuan,
                   COALESCE(SUM(st.jumlah_stok), 0) AS total_stok
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN stok st ON p.id_produk = st.id_produk
            GROUP BY p.id_produk
            HAVING total_stok <= p.stok_minimum AND total_stok > 0
            ORDER BY total_stok ASC
        ");
        return $stmt->fetchAll();
    }

    public function getStokHabis(): array {
        $stmt = $this->db->query("
            SELECT p.id_produk, p.kode_produk, p.nama_produk, p.stok_minimum, k.nama_kategori, s.singkatan AS nama_satuan,
                   COALESCE(SUM(st.jumlah_stok), 0) AS total_stok
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN stok st ON p.id_produk = st.id_produk
            GROUP BY p.id_produk
            HAVING total_stok = 0
            ORDER BY p.nama_produk ASC
        ");
        return $stmt->fetchAll();
    }
}
