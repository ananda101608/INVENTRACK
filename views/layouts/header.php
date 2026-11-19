<?php
// ====================================================================
// LAYOUT: HEADER
// ====================================================================
$currentPage = $_GET['page'] ?? 'dashboard';

$titleMap = [
    'dashboard'     => ['Dashboard', 'Ringkasan kondisi inventaris'],
    'produk'        => ['Produk', 'Kelola data barang dan harga'],
    'kategori'      => ['Kategori', 'Pengelompokan jenis produk'],
    'satuan'        => ['Satuan', 'Satuan ukur produk'],
    'pemasok'       => ['Pemasok', 'Daftar mitra penyedia barang'],
    'gudang'        => ['Gudang', 'Lokasi penyimpanan stok'],
    'barang_masuk'  => ['Barang Masuk', 'Pencatatan penerimaan barang'],
    'barang_keluar' => ['Barang Keluar', 'Pencatatan pengeluaran barang'],
    'transfer'      => ['Transfer Gudang', 'Perpindahan stok antar gudang'],
    'opname'        => ['Stok Opname', 'Pemeriksaan stok fisik'],
    'laporan'       => ['Laporan', 'Analisis dan rekap inventaris'],
    'pengguna'      => ['Pengguna', 'Kelola akun dan hak akses'],
    'pengaturan'    => ['Pengaturan', 'Profil dan keamanan akun'],
];

[$docTitle, $docSubtitle] = $titleMap[$currentPage] ?? ['INVENTRACK', APP_TAGLINE];
$docTitle = $docTitle . ' — ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(APP_TAGLINE, ENT_QUOTES) ?>">
    <meta name="theme-color" content="#0f172a">
    <title><?= htmlspecialchars($docTitle, ENT_QUOTES) ?></title>

    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/style.css?v=' . APP_VERSION) ?>">
</head>
<body class="min-h-screen bg-slate-100">
<div id="app" class="min-h-screen">
