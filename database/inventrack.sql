-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2026 at 10:28 AM
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
-- Table structure for table `Tabel barang_keluar`
--

CREATE TABLE `Tabel barang_keluar` (
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
-- Table structure for table `Tabel barang_masuk`
--

CREATE TABLE `Tabel barang_masuk` (
  `id_barang_masuk` int(11) NOT NULL,
  `id_pemasok` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel detail_barang_keluar`
--

CREATE TABLE `Tabel detail_barang_keluar` (
  `id_detail_keluar` int(11) NOT NULL,
  `id_barang_keluar` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel detail_barang_masuk`
--

CREATE TABLE `Tabel detail_barang_masuk` (
  `id_detail_produk` int(11) NOT NULL,
  `id_barang_masuk` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel detail_stok_opname`
--

CREATE TABLE `Tabel detail_stok_opname` (
  `id_detail_opname` int(11) NOT NULL,
  `id_opname` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `stok_sistem` int(11) NOT NULL,
  `stok_fisik` int(11) NOT NULL,
  `selisih` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel detail_transfer`
--

CREATE TABLE `Tabel detail_transfer` (
  `id_detail_transfer` int(11) NOT NULL,
  `id_transfer` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel gudang`
--

CREATE TABLE `Tabel gudang` (
  `id_gudang` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `penanggung_jawab` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel kategori`
--

CREATE TABLE `Tabel kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel pemasok`
--

CREATE TABLE `Tabel pemasok` (
  `id_pemasok` int(11) NOT NULL,
  `nama_pemasok` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel pengguna`
--

CREATE TABLE `Tabel pengguna` (
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
-- Table structure for table `Tabel produk`
--

CREATE TABLE `Tabel produk` (
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
-- Table structure for table `Tabel satuan`
--

CREATE TABLE `Tabel satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel stok`
--

CREATE TABLE `Tabel stok` (
  `id_stok` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jumlah_stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel stok_opname`
--

CREATE TABLE `Tabel stok_opname` (
  `id_opname` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_opname` datetime NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tabel transfer_gudang`
--

CREATE TABLE `Tabel transfer_gudang` (
  `id_transfer` int(11) NOT NULL,
  `id_gudang_asal` int(11) NOT NULL,
  `id_gudang_tujuan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_transfer` datetime NOT NULL,
  `status` enum('diproses','selesai','batal') NOT NULL DEFAULT 'diproses',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Tabel barang_keluar`
--
ALTER TABLE `Tabel barang_keluar`
  ADD PRIMARY KEY (`id_barang_keluar`),
  ADD KEY `idx_pengguna` (`id_pengguna`),
  ADD KEY `idx_gudang` (`id_gudang`);

--
-- Indexes for table `Tabel barang_masuk`
--
ALTER TABLE `Tabel barang_masuk`
  ADD PRIMARY KEY (`id_barang_masuk`),
  ADD KEY `idx_pemasok` (`id_pemasok`),
  ADD KEY `idx_pengguna` (`id_pengguna`),
  ADD KEY `idx_gudang` (`id_gudang`);

--
-- Indexes for table `Tabel detail_barang_keluar`
--
ALTER TABLE `Tabel detail_barang_keluar`
  ADD PRIMARY KEY (`id_detail_keluar`),
  ADD KEY `idx_barang_keluar` (`id_barang_keluar`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `Tabel detail_barang_masuk`
--
ALTER TABLE `Tabel detail_barang_masuk`
  ADD PRIMARY KEY (`id_detail_produk`),
  ADD KEY `idx_barang_masuk` (`id_barang_masuk`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `Tabel detail_stok_opname`
--
ALTER TABLE `Tabel detail_stok_opname`
  ADD PRIMARY KEY (`id_detail_opname`),
  ADD KEY `idx_opname` (`id_opname`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `Tabel detail_transfer`
--
ALTER TABLE `Tabel detail_transfer`
  ADD PRIMARY KEY (`id_detail_transfer`),
  ADD KEY `idx_transfer` (`id_transfer`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `Tabel gudang`
--
ALTER TABLE `Tabel gudang`
  ADD PRIMARY KEY (`id_gudang`);

--
-- Indexes for table `Tabel kategori`
--
ALTER TABLE `Tabel kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `Tabel pemasok`
--
ALTER TABLE `Tabel pemasok`
  ADD PRIMARY KEY (`id_pemasok`);

--
-- Indexes for table `Tabel pengguna`
--
ALTER TABLE `Tabel pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `Tabel produk`
--
ALTER TABLE `Tabel produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `fk_produk_kategori` (`id_kategori`),
  ADD KEY `fk_produk_satuan` (`id_satuan`);

--
-- Indexes for table `Tabel satuan`
--
ALTER TABLE `Tabel satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `Tabel stok`
--
ALTER TABLE `Tabel stok`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `fk_stok_produk` (`id_produk`),
  ADD KEY `fk_stok_gudang` (`id_gudang`);

--
-- Indexes for table `Tabel stok_opname`
--
ALTER TABLE `Tabel stok_opname`
  ADD PRIMARY KEY (`id_opname`),
  ADD KEY `idx_gudang` (`id_gudang`),
  ADD KEY `idx_pengguna` (`id_pengguna`);

--
-- Indexes for table `Tabel transfer_gudang`
--
ALTER TABLE `Tabel transfer_gudang`
  ADD PRIMARY KEY (`id_transfer`),
  ADD KEY `idx_gudang_asal` (`id_gudang_asal`),
  ADD KEY `idx_gudang_tujuan` (`id_gudang_tujuan`),
  ADD KEY `idx_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Tabel barang_keluar`
--
ALTER TABLE `Tabel barang_keluar`
  MODIFY `id_barang_keluar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel barang_masuk`
--
ALTER TABLE `Tabel barang_masuk`
  MODIFY `id_barang_masuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel detail_barang_keluar`
--
ALTER TABLE `Tabel detail_barang_keluar`
  MODIFY `id_detail_keluar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel detail_barang_masuk`
--
ALTER TABLE `Tabel detail_barang_masuk`
  MODIFY `id_detail_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel detail_stok_opname`
--
ALTER TABLE `Tabel detail_stok_opname`
  MODIFY `id_detail_opname` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel detail_transfer`
--
ALTER TABLE `Tabel detail_transfer`
  MODIFY `id_detail_transfer` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel gudang`
--
ALTER TABLE `Tabel gudang`
  MODIFY `id_gudang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel kategori`
--
ALTER TABLE `Tabel kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel pemasok`
--
ALTER TABLE `Tabel pemasok`
  MODIFY `id_pemasok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel pengguna`
--
ALTER TABLE `Tabel pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel produk`
--
ALTER TABLE `Tabel produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel satuan`
--
ALTER TABLE `Tabel satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel stok`
--
ALTER TABLE `Tabel stok`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel stok_opname`
--
ALTER TABLE `Tabel stok_opname`
  MODIFY `id_opname` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tabel transfer_gudang`
--
ALTER TABLE `Tabel transfer_gudang`
  MODIFY `id_transfer` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Tabel barang_keluar`
--
ALTER TABLE `Tabel barang_keluar`
  ADD CONSTRAINT `fk_barang_keluar_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `Tabel gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_keluar_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `Tabel pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel barang_masuk`
--
ALTER TABLE `Tabel barang_masuk`
  ADD CONSTRAINT `fk_barang_masuk_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `Tabel gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_masuk_pemasok` FOREIGN KEY (`id_pemasok`) REFERENCES `Tabel pemasok` (`id_pemasok`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_masuk_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `Tabel pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel detail_barang_keluar`
--
ALTER TABLE `Tabel detail_barang_keluar`
  ADD CONSTRAINT `fk_detail_keluar_barang_keluar` FOREIGN KEY (`id_barang_keluar`) REFERENCES `Tabel barang_keluar` (`id_barang_keluar`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_keluar_produk` FOREIGN KEY (`id_produk`) REFERENCES `Tabel produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel detail_barang_masuk`
--
ALTER TABLE `Tabel detail_barang_masuk`
  ADD CONSTRAINT `fk_detail_barang_masuk` FOREIGN KEY (`id_barang_masuk`) REFERENCES `Tabel barang_masuk` (`id_barang_masuk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `Tabel produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel detail_stok_opname`
--
ALTER TABLE `Tabel detail_stok_opname`
  ADD CONSTRAINT `fk_detail_opname_opname` FOREIGN KEY (`id_opname`) REFERENCES `Tabel stok_opname` (`id_opname`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_opname_produk` FOREIGN KEY (`id_produk`) REFERENCES `Tabel produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel detail_transfer`
--
ALTER TABLE `Tabel detail_transfer`
  ADD CONSTRAINT `fk_detail_transfer_produk` FOREIGN KEY (`id_produk`) REFERENCES `Tabel produk` (`id_produk`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transfer_transfer` FOREIGN KEY (`id_transfer`) REFERENCES `Tabel transfer_gudang` (`id_transfer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tabel produk`
--
ALTER TABLE `Tabel produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `Tabel kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_satuan` FOREIGN KEY (`id_satuan`) REFERENCES `Tabel satuan` (`id_satuan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tabel stok`
--
ALTER TABLE `Tabel stok`
  ADD CONSTRAINT `fk_stok_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `Tabel gudang` (`id_gudang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stok_produk` FOREIGN KEY (`id_produk`) REFERENCES `Tabel produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tabel stok_opname`
--
ALTER TABLE `Tabel stok_opname`
  ADD CONSTRAINT `fk_stok_opname_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `Tabel gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stok_opname_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `Tabel pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `Tabel transfer_gudang`
--
ALTER TABLE `Tabel transfer_gudang`
  ADD CONSTRAINT `fk_transfer_gudang_asal` FOREIGN KEY (`id_gudang_asal`) REFERENCES `Tabel gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transfer_gudang_tujuan` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `Tabel gudang` (`id_gudang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transfer_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `Tabel pengguna` (`id_pengguna`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
