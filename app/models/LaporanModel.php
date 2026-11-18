<?php

class LaporanModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function laporanProduk(?int $kategoriId = null, ?string $status = null): array {
        $sql = "
            SELECT p.kode_produk, p.nama_produk, k.nama_kategori, s.singkatan AS nama_satuan,
                   p.harga_beli, p.harga_jual, p.stok_minimum, p.status,
                   COALESCE(SUM(st.jumlah_stok), 0) AS total_stok,
                   (COALESCE(SUM(st.jumlah_stok), 0) * p.harga_beli) AS total_aset_beli,
                   (COALESCE(SUM(st.jumlah_stok), 0) * p.harga_jual) AS total_potensi_jual
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN stok st ON p.id_produk = st.id_produk
            WHERE 1=1
        ";
        $params = [];

        if ($kategoriId) {
            $sql .= " AND p.id_kategori = :kat";
            $params[':kat'] = $kategoriId;
        }

        if ($status) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " GROUP BY p.id_produk ORDER BY p.nama_produk ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function laporanStokGudang(?int $gudangId = null): array {
        $sql = "
            SELECT g.nama_gudang, p.kode_produk, p.nama_produk, k.nama_kategori,
                   s.singkatan AS nama_satuan, p.stok_minimum, st.jumlah_stok,
                   CASE 
                       WHEN st.jumlah_stok = 0 THEN 'Habis'
                       WHEN st.jumlah_stok <= p.stok_minimum THEN 'Menipis'
                       ELSE 'Aman'
                   END AS status_stok
            FROM stok st
            JOIN gudang g ON st.id_gudang = g.id_gudang
            JOIN produk p ON st.id_produk = p.id_produk
            JOIN kategori k ON p.id_kategori = k.id_kategori
            JOIN satuan s ON p.id_satuan = s.id_satuan
            WHERE 1=1
        ";
        $params = [];

        if ($gudangId) {
            $sql .= " AND st.id_gudang = :gudang";
            $params[':gudang'] = $gudangId;
        }

        $sql .= " ORDER BY g.nama_gudang ASC, p.nama_produk ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function laporanBarangMasuk(?string $tglAwal = null, ?string $tglAkhir = null, ?int $gudangId = null): array {
        $sql = "
            SELECT bm.tanggal_masuk, p.nama_pemasok, g.nama_gudang, u.nama AS petugas,
                   pr.kode_produk, pr.nama_produk, sat.singkatan AS nama_satuan,
                   dbm.jumlah, dbm.harga_satuan, (dbm.jumlah * dbm.harga_satuan) AS subtotal
            FROM detail_barang_masuk dbm
            JOIN barang_masuk bm ON dbm.id_barang_masuk = bm.id_barang_masuk
            JOIN pemasok p ON bm.id_pemasok = p.id_pemasok
            JOIN gudang g ON bm.id_gudang = g.id_gudang
            JOIN pengguna u ON bm.id_pengguna = u.id_pengguna
            JOIN produk pr ON dbm.id_produk = pr.id_produk
            JOIN satuan sat ON pr.id_satuan = sat.id_satuan
            WHERE 1=1
        ";
        $params = [];

        if ($tglAwal && $tglAkhir) {
            $sql .= " AND DATE(bm.tanggal_masuk) BETWEEN :awal AND :akhir";
            $params[':awal'] = $tglAwal;
            $params[':akhir'] = $tglAkhir;
        }

        if ($gudangId) {
            $sql .= " AND bm.id_gudang = :gudang";
            $params[':gudang'] = $gudangId;
        }

        $sql .= " ORDER BY bm.tanggal_masuk DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function laporanBarangKeluar(?string $tglAwal = null, ?string $tglAkhir = null, ?int $gudangId = null): array {
        $sql = "
            SELECT bk.tanggal_keluar, g.nama_gudang, bk.nama_penerima, bk.tujuan, u.nama AS petugas,
                   pr.kode_produk, pr.nama_produk, sat.singkatan AS nama_satuan,
                   dbk.jumlah
            FROM detail_barang_keluar dbk
            JOIN barang_keluar bk ON dbk.id_barang_keluar = bk.id_barang_keluar
            JOIN gudang g ON bk.id_gudang = g.id_gudang
            JOIN pengguna u ON bk.id_pengguna = u.id_pengguna
            JOIN produk pr ON dbk.id_produk = pr.id_produk
            JOIN satuan sat ON pr.id_satuan = sat.id_satuan
            WHERE 1=1
        ";
        $params = [];

        if ($tglAwal && $tglAkhir) {
            $sql .= " AND DATE(bk.tanggal_keluar) BETWEEN :awal AND :akhir";
            $params[':awal'] = $tglAwal;
            $params[':akhir'] = $tglAkhir;
        }

        if ($gudangId) {
            $sql .= " AND bk.id_gudang = :gudang";
            $params[':gudang'] = $gudangId;
        }

        $sql .= " ORDER BY bk.tanggal_keluar DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
