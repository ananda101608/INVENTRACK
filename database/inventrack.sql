-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2026 at 12:11 PM
-- Server version: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_barang_keluar` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `tanggal_keluar` datetime NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `tujuan` varchar(150) NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_barang_masuk` int(11) NOT NULL,
  `id_pemasok` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_barang_keluar`
--

CREATE TABLE `detail_barang_keluar` (
  `id_detail_keluar` int(11) NOT NULL,
  `id_barang_keluar` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_barang_masuk`
--

CREATE TABLE `detail_barang_masuk` (
  `id_detail_masuk` int(11) NOT NULL,
  `id_barang_masuk` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_stok_opname`
--

CREATE TABLE `detail_stok_opname` (
  `id_detail_opname` int(11) NOT NULL,
  `id_opname` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `stok_sistem` int(11) NOT NULL,
  `stok_fisik` int(11) NOT NULL,
  `selisih` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_transfer`
--

CREATE TABLE `detail_transfer` (
  `id_detail_transfer` int(11) NOT NULL,
  `id_transfer` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `id_gudang` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `penanggung_jawab` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemasok`
--

CREATE TABLE `pemasok` (
  `id_pemasok` int(11) NOT NULL,
  `nama_pemasok` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `peran` enum('admin','petugas','manager') NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL,
  `dibuat_pada` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_satuan` int(11) NOT NULL,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `stok_minimum` int(11) NOT NULL,
  `foto_produk` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL,
  `dibuat_pada` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok`
--

CREATE TABLE `stok` (
  `id_stok` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jumlah_stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname`
--

CREATE TABLE `stok_opname` (
  `id_opname` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_opname` datetime NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_gudang`
--

CREATE TABLE `transfer_gudang` (
  `id_transfer` int(11) NOT NULL,
  `id_gudang_asal` int(11) NOT NULL,
  `id_gudang_tujuan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_transfer` datetime NOT NULL,
  `status` enum('diproses','selesai','batal') NOT NULL DEFAULT 'diproses',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `transfer_gudang`
--
DELIMITER $$
CREATE TRIGGER `cek_transfer_gudang_insert` BEFORE INSERT ON `transfer_gudang` FOR EACH ROW BEGIN
    IF NEW.id_gudang_asal = NEW.id_gudang_tujuan THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gudang asal dan tujuan tidak boleh sama!';
    END IF;
END$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `cek_transfer_gudang_update` BEFORE UPDATE ON `transfer_gudang` FOR EACH ROW BEGIN
    IF NEW.id_gudang_asal = NEW.id_gudang_tujuan THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gudang asal dan tujuan tidak boleh sama!';
    END IF;
END$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_barang_keluar`),
  ADD KEY `idx_pengguna` (`id_pengguna`),
  ADD KEY `idx_gudang` (`id_gudang`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_barang_masuk`),
  ADD KEY `idx_pemasok` (`id_pemasok`),
  ADD KEY `idx_pengguna` (`id_pengguna`),
  ADD KEY `idx_gudang` (`id_gudang`);

--
-- Indexes for table `detail_barang_keluar`
--
ALTER TABLE `detail_barang_keluar`
  ADD PRIMARY KEY (`id_detail_keluar`),
  ADD KEY `idx_barang_keluar` (`id_barang_keluar`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  ADD PRIMARY KEY (`id_detail_masuk`),
  ADD KEY `idx_barang_masuk` (`id_barang_masuk`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `detail_stok_opname`
--
ALTER TABLE `detail_stok_opname`
  ADD PRIMARY KEY (`id_detail_opname`),
  ADD KEY `idx_opname` (`id_opname`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `detail_transfer`
--
ALTER TABLE `detail_transfer`
  ADD PRIMARY KEY (`id_detail_transfer`),
  ADD KEY `idx_transfer` (`id_transfer`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id_gudang`),
  ADD UNIQUE KEY `unik_nama_gudang` (`nama_gudang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `unik_nama_kategori` (`nama_kategori`);

--
-- Indexes for table `pemasok`
--
ALTER TABLE `pemasok`
  ADD PRIMARY KEY (`id_pemasok`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `unik_email` (`email`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `unik_kode_produk` (`kode_produk`),
  ADD KEY `fk_produk_kategori` (`id_kategori`),
  ADD KEY `fk_produk_satuan` (`id_satuan`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`),
  ADD UNIQUE KEY `unik_nama_satuan` (`nama_satuan`),
  ADD UNIQUE KEY `unik_singkatan` (`singkatan`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`id_stok`),
  ADD UNIQUE KEY `unik_produk_gudang` (`id_produk`,`id_gudang`),
  ADD KEY `fk_stok_gudang` (`id_gudang`);

--
-- Indexes for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD PRIMARY KEY (`id_opname`),
  ADD KEY `idx_gudang` (`id_gudang`),
  ADD KEY `idx_pengguna` (`id_pengguna`);

--
-- Indexes for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  ADD PRIMARY KEY (`id_transfer`),
  ADD KEY `idx_gudang_asal` (`id_gudang_asal`),
  ADD KEY `idx_gudang_tujuan` (`id_gudang_tujuan`),
  ADD KEY `idx_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id_barang_keluar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_barang_masuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_barang_keluar`
--
ALTER TABLE `detail_barang_keluar`
  MODIFY `id_detail_keluar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  MODIFY `id_detail_masuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_stok_opname`
--
ALTER TABLE `detail_stok_opname`
  MODIFY `id_detail_opname` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_transfer`
--
ALTER TABLE `detail_transfer`
  MODIFY `id_detail_transfer` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id_gudang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pemasok`
--
ALTER TABLE `pemasok`
  MODIFY `id_pemasok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok`
--
ALTER TABLE `stok`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_opname`
--
ALTER TABLE `stok_opname`
  MODIFY `id_opname` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  MODIFY `id_transfer` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `fk_barang_keluar_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_keluar_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `fk_barang_masuk_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_masuk_pemasok` FOREIGN KEY (`id_pemasok`) REFERENCES `pemasok` (`id_pemasok`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_masuk_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `detail_barang_keluar`
--
ALTER TABLE `detail_barang_keluar`
  ADD CONSTRAINT `fk_detail_keluar_barang_keluar` FOREIGN KEY (`id_barang_keluar`) REFERENCES `barang_keluar` (`id_barang_keluar`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_keluar_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  ADD CONSTRAINT `fk_detail_barang_masuk` FOREIGN KEY (`id_barang_masuk`) REFERENCES `barang_masuk` (`id_barang_masuk`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `detail_stok_opname`
--
ALTER TABLE `detail_stok_opname`
  ADD CONSTRAINT `fk_detail_opname_opname` FOREIGN KEY (`id_opname`) REFERENCES `stok_opname` (`id_opname`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_opname_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `detail_transfer`
--
ALTER TABLE `detail_transfer`
  ADD CONSTRAINT `fk_detail_transfer_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transfer_transfer` FOREIGN KEY (`id_transfer`) REFERENCES `transfer_gudang` (`id_transfer`) ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_satuan` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`) ON UPDATE CASCADE;

--
-- Constraints for table `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `fk_stok_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stok_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD CONSTRAINT `fk_stok_opname_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stok_opname_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  ADD CONSTRAINT `fk_transfer_gudang_asal` FOREIGN KEY (`id_gudang_asal`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transfer_gudang_tujuan` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transfer_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================
-- DATA DEMO (dihasilkan dari database yang sudah terbukti jalan)
-- ============================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `barang_keluar` WRITE;
/*!40000 ALTER TABLE `barang_keluar` DISABLE KEYS */;
INSERT INTO `barang_keluar` VALUES (1,2,1,'2026-11-15 04:31:49','Divisi IT','Kantor Pusat Lt. 3','Distribusi Laptop & Mouse untuk karyawan baru'),(2,2,1,'2026-11-17 04:31:49','Koperasi Karyawan','Gedung Koperasi','Penyaluran sembako minyak dan beras');
/*!40000 ALTER TABLE `barang_keluar` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `barang_masuk` WRITE;
/*!40000 ALTER TABLE `barang_masuk` DISABLE KEYS */;
INSERT INTO `barang_masuk` VALUES (1,1,1,1,'2026-11-14 04:31:49','Pengadaan Laptop dan Aksesoris'),(2,2,2,1,'2026-11-16 04:31:49','Pasokan Sembako Mingguan'),(3,3,1,1,'2026-11-18 04:31:49','Restock Kertas dan ATK Kantor');
/*!40000 ALTER TABLE `barang_masuk` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `detail_barang_keluar` WRITE;
/*!40000 ALTER TABLE `detail_barang_keluar` DISABLE KEYS */;
INSERT INTO `detail_barang_keluar` VALUES (1,1,1,3),(2,1,2,5),(3,2,3,20),(4,2,4,25);
/*!40000 ALTER TABLE `detail_barang_keluar` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `detail_barang_masuk` WRITE;
/*!40000 ALTER TABLE `detail_barang_masuk` DISABLE KEYS */;
INSERT INTO `detail_barang_masuk` VALUES (1,1,1,5,6500000.00),(2,1,2,50,150000.00),(3,2,3,100,68000.00),(4,2,4,40,32000.00),(5,3,5,70,46000.00),(6,3,6,35,120000.00);
/*!40000 ALTER TABLE `detail_barang_masuk` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `detail_stok_opname` WRITE;
/*!40000 ALTER TABLE `detail_stok_opname` DISABLE KEYS */;
INSERT INTO `detail_stok_opname` VALUES (1,1,1,2,2,0),(2,1,5,62,60,-2);
/*!40000 ALTER TABLE `detail_stok_opname` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `detail_transfer` WRITE;
/*!40000 ALTER TABLE `detail_transfer` DISABLE KEYS */;
INSERT INTO `detail_transfer` VALUES (1,1,2,20),(2,1,3,50);
/*!40000 ALTER TABLE `detail_transfer` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `gudang` WRITE;
/*!40000 ALTER TABLE `gudang` DISABLE KEYS */;
INSERT INTO `gudang` VALUES (1,'Gudang Utama Jakarta','Jl. Daan Mogot No. 64, Jakarta Barat','Budi Santoso'),(2,'Gudang Cabang Surabaya','Jl. Rungkut Industri No. 12, Surabaya','Ahmad Fauzi'),(3,'Gudang Transit Bandung','Jl. Soekarno Hatta No. 108, Bandung','Dewi Lestari');
/*!40000 ALTER TABLE `gudang` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Elektronik & Gadget','Perangkat komputer, laptop, dan aksesoris'),(2,'Sembako & Pangan','Bahan makanan pokok dan kebutuhan sembako'),(3,'Alat Tulis Kantor','Perlengkapan kantor, kertas, dan alat tulis'),(4,'Perkakas & Jaringan','Peralatan teknisi, kabel, dan perkakas instalasi'),(5,'Medis & Sanitasi','Perlengkapan sanitasi dan kesehatan');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `pemasok` WRITE;
/*!40000 ALTER TABLE `pemasok` DISABLE KEYS */;
INSERT INTO `pemasok` VALUES (1,'PT Sumber Makmur Elektronik','081234567890','sales@sumbermakmur.com','Jl. Hayam Wuruk No. 10, Jakarta Barat'),(2,'CV Pangan Nusantara Sejahtera','081398765432','order@pangannusantara.co.id','Jl. Kramat Jati No. 45, Jakarta Timur'),(3,'PT Stationery Global Mandiri','081122334455','kontak@stationeryglobal.com','Jl. Mangga Dua Raya No. 88, Jakarta Pusat'),(4,'CV Perkakas Jaya Teknik','081567890123','perkakasjaya@yahoo.com','Jl. Glodok Jaya No. 15, Jakarta Barat'),(5,'PT Medika Prima Farma','081890123456','info@medikaprima.com','Jl. Salemba Raya No. 22, Jakarta Pusat');
/*!40000 ALTER TABLE `pemasok` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` VALUES (1,'admin','admin@inventrack.com','$2y$12$LaUbzSDJ4bRjC.rGgMf4iubrpJyO1ET49N1Tp4iz/u7OwLHeWfirm','admin','aktif','2026-11-19 04:31:49'),(2,'Petugas Gudang','petugas@inventrack.com','$2y$10$1CNn3FhOJNNjkJdKx2xonO6hT3X2w6blRqJy4f5S3QVl2r0AfPfXK','petugas','aktif','2026-11-19 04:31:49'),(3,'Manager Operasional','manager@inventrack.com','$2y$10$PJfmLBeP9ak16iXaxkftB.T1t3UBRUuyQ5kBkNK7qCYjtb/G4SRb.','manager','aktif','2026-11-19 04:31:49');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (1,1,1,'PRD-001','Laptop Asus Vivobook 14','Laptop Core i5 RAM 8GB SSD 512GB',6500000.00,7800000.00,3,'default.png','aktif','2026-11-19 04:31:49'),(2,1,1,'PRD-002','Mouse Wireless Logitech M331','Mouse silent click optical sensor',150000.00,220000.00,10,'default.png','aktif','2026-11-19 04:31:49'),(3,2,1,'PRD-003','Beras Premium Ramos 5kg','Beras putih pulen kualitas super 5kg',68000.00,76000.00,20,'default.png','aktif','2026-11-19 04:31:49'),(4,2,1,'PRD-004','Minyak Goreng Tropical 2 Liter','Minyak goreng kelapa sawit botol 2L',32000.00,38000.00,25,'default.png','aktif','2026-11-19 04:31:49'),(5,3,5,'PRD-005','Kertas HVS A4 PaperOne 80gr','Kertas HVS premium ukuran A4 80 gram',46000.00,55000.00,15,'default.png','aktif','2026-11-19 04:31:49'),(6,3,2,'PRD-006','Pulpen Gel Pilot G2 0.5 (Isi 12)','Pulpen gel hitam 0.5mm 1 pack box',120000.00,150000.00,5,'default.png','aktif','2026-11-19 04:31:49'),(7,4,2,'PRD-007','Kabel LAN UTP Cat6 305m','Roll kabel jaringan Cat6 1 roll 305 meter',850000.00,1100000.00,4,'default.png','aktif','2026-11-19 04:31:49'),(8,4,1,'PRD-008','Tang Crimping RJ45 Proskit','Tang press crimping modular plug RJ45/RJ11',95000.00,140000.00,5,'default.png','aktif','2026-11-19 04:31:49'),(9,5,1,'PRD-009','Hand Sanitizer Antis 500ml','Pembersih tangan antiseptik botol pump',28000.00,35000.00,10,'default.png','aktif','2026-11-19 04:31:49'),(10,5,2,'PRD-010','Masker Medis 3-Ply Earloop','Masker bedah 3 lapis standar Kemenkes 1 box isi 50',20000.00,30000.00,10,'default.png','aktif','2026-11-19 04:31:49');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `satuan` WRITE;
/*!40000 ALTER TABLE `satuan` DISABLE KEYS */;
INSERT INTO `satuan` VALUES (1,'Pieces','pcs'),(2,'Box / Dus','box'),(3,'Kilogram','kg'),(4,'Liter','L'),(5,'Rim','rim');
/*!40000 ALTER TABLE `satuan` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `stok` WRITE;
/*!40000 ALTER TABLE `stok` DISABLE KEYS */;
INSERT INTO `stok` VALUES (1,1,1,2),(2,2,1,45),(3,2,2,20),(4,3,1,80),(5,3,2,50),(6,4,1,15),(7,5,1,60),(8,5,3,25),(9,6,1,30),(10,7,1,8),(11,7,2,5),(12,8,1,0),(13,9,1,40),(14,9,3,15),(15,10,1,50);
/*!40000 ALTER TABLE `stok` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `stok_opname` WRITE;
/*!40000 ALTER TABLE `stok_opname` DISABLE KEYS */;
INSERT INTO `stok_opname` VALUES (1,1,1,'2026-11-18 04:31:49','Audit Fisik Berkala Gudang Utama');
/*!40000 ALTER TABLE `stok_opname` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `transfer_gudang` WRITE;
/*!40000 ALTER TABLE `transfer_gudang` DISABLE KEYS */;
INSERT INTO `transfer_gudang` VALUES (1,1,2,1,'2026-11-17 04:31:49','selesai','Relokasi stok mouse dan beras ke cabang Surabaya');
/*!40000 ALTER TABLE `transfer_gudang` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

