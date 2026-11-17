<?php
// Mock data untuk tampilan jika data dari database belum tersedia
$stat = $stat ?? [
    'total_produk'   => 124,
    'total_kategori' => 8,
    'total_gudang'   => 3,
    'total_stok'     => 1420,
    'stok_menipis'   => 5,
    'stok_habis'     => 2
];

$produk_menipis = $produk_menipis ?? [
    ['kode' => 'PRD-001', 'nama' => 'Beras Pandan Wangi 5kg', 'kategori' => 'Sembako', 'stok' => 4, 'min' => 10, 'satuan' => 'Pcs'],
    ['kode' => 'PRD-004', 'nama' => 'Minyak Goreng 2L', 'kategori' => 'Sembako', 'stok' => 2, 'min' => 15, 'satuan' => 'Pcs'],
    ['kode' => 'PRD-012', 'nama' => 'Kopi Robusta 250gr', 'kategori' => 'Minuman', 'stok' => 5, 'min' => 10, 'satuan' => 'Pcs'],
    ['kode' => 'PRD-025', 'nama' => 'Tepung Terigu 1kg', 'kategori' => 'Bahan Kue', 'stok' => 0, 'min' => 20, 'satuan' => 'Pcs'],
    ['kode' => 'PRD-031', 'nama' => 'Gula Pasir Kristal 1kg', 'kategori' => 'Sembako', 'stok' => 0, 'min' => 25, 'satuan' => 'Pcs'],
];

$transaksi_terbaru = $transaksi_terbaru ?? [
    ['tipe' => 'masuk', 'tanggal' => date('Y-m-d'), 'keterangan' => 'Penerimaan dari PT Maju Jaya (Gudang Utama)', 'jumlah' => '+150 Pcs'],
    ['tipe' => 'keluar', 'tanggal' => date('Y-m-d'), 'keterangan' => 'Pengeluaran ke Toko Berkah', 'jumlah' => '-45 Pcs'],
    ['tipe' => 'transfer', 'tanggal' => date('Y-m-d', strtotime('-1 days')), 'keterangan' => 'Transfer dari Gudang Utama ke Gudang Cabang', 'jumlah' => '20 Pcs'],
    ['tipe' => 'masuk', 'tanggal' => date('Y-m-d', strtotime('-2 days')), 'keterangan' => 'Penerimaan dari CV Sumber Pangan', 'jumlah' => '+80 Pcs'],
];
?>

<div class="space-y-6">

    <!-- Header Selamat Datang -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 card-shadow">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Selamat Datang di INVENTRACK 👋</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau pergerakan stok barang dan aktivitas gudang secara langsung.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= BASE_URL ?>/index.php?page=barang_masuk&action=tambah" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-blue-500/20 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Barang Masuk</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=barang_keluar&action=tambah" 
               class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                <span>Barang Keluar</span>
            </a>
        </div>
    </div>

    <!-- 6 Statistik Card (StatCard Component) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <!-- Total Produk -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 card-shadow card-hover">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Produk</p>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mt-2"><?= number_format($stat['total_produk']) ?></h3>
            <span class="text-[11px] text-slate-400">Total data produk</span>
        </div>

        <!-- Total Kategori -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 card-shadow card-hover">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Kategori</p>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mt-2"><?= number_format($stat['total_kategori']) ?></h3>
            <span class="text-[11px] text-slate-400">Kelompok barang</span>
        </div>

        <!-- Total Gudang -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 card-shadow card-hover">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Gudang</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mt-2"><?= number_format($stat['total_gudang']) ?></h3>
            <span class="text-[11px] text-slate-400">Lokasi aktif</span>
        </div>

        <!-- Total Stok -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 card-shadow card-hover">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Stok</p>
                <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mt-2"><?= number_format($stat['total_stok']) ?></h3>
            <span class="text-[11px] text-slate-400">Unit seluruh gudang</span>
        </div>

        <!-- Stok Menipis (Warning) -->
        <div class="bg-white p-4 rounded-2xl border border-amber-200 card-shadow card-hover bg-amber-50/20">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Stok Menipis</p>
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-amber-600 mt-2"><?= number_format($stat['stok_menipis']) ?></h3>
            <span class="text-[11px] text-amber-500 font-medium">Di bawah stok min</span>
        </div>

        <!-- Stok Habis (Danger) -->
        <div class="bg-white p-4 rounded-2xl border border-rose-200 card-shadow card-hover bg-rose-50/20">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Stok Habis</p>
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-rose-600 mt-2"><?= number_format($stat['stok_habis']) ?></h3>
            <span class="text-[11px] text-rose-500 font-medium">Stok mencapai 0</span>
        </div>

    </div>

    <!-- Baris Grafik & Transaksi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Grafik Transaksi (2 Span) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Tren Barang Masuk & Keluar</h2>
                    <p class="text-xs text-slate-400">Ringkasan transaksi stok 7 hari terakhir</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">7 Hari Terakhir</span>
            </div>
            <div class="h-64">
                <canvas id="chartTransaksi"></canvas>
            </div>
        </div>

        <!-- Aktivitas Transaksi Terbaru -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 card-shadow flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-800">Aktivitas Terbaru</h2>
                    <a href="<?= BASE_URL ?>/index.php?page=laporan" class="text-xs font-semibold text-blue-600 hover:underline">Semua</a>
                </div>
                
                <div class="space-y-3">
                    <?php foreach ($transaksi_terbaru as $trx): ?>
                        <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 <?= $trx['tipe'] === 'masuk' ? 'bg-emerald-100 text-emerald-600' : ($trx['tipe'] === 'keluar' ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600') ?>">
                                <?php if ($trx['tipe'] === 'masuk'): ?>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                                <?php elseif ($trx['tipe'] === 'keluar'): ?>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                <?php else: ?>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-800 truncate"><?= bersihkan($trx['keterangan']) ?></p>
                                <p class="text-[10px] text-slate-400 mt-0.5"><?= tanggal_indonesia($trx['tanggal']) ?></p>
                            </div>
                            <span class="text-xs font-bold <?= $trx['tipe'] === 'masuk' ? 'text-emerald-600' : ($trx['tipe'] === 'keluar' ? 'text-rose-600' : 'text-blue-600') ?>">
                                <?= $trx['jumlah'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <span class="text-[11px] text-slate-400">Sinkron otomatis dengan transaksi gudang</span>
            </div>
        </div>

    </div>

    <!-- Tabel Peringatan Stok Menipis & Habis -->
    <div class="bg-white rounded-2xl border border-slate-200 card-shadow overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Peringatan Stok Menipis & Habis</h2>
                <p class="text-xs text-slate-400">Segera lakukan pengadaan barang (Barang Masuk) untuk produk di bawah batas minimum.</p>
            </div>
            <a href="<?= BASE_URL ?>/index.php?page=produk" class="text-xs font-semibold text-blue-600 hover:underline">Kelola Produk &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Produk</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3 text-center">Stok Minimum</th>
                        <th class="px-6 py-3 text-center">Sisa Stok</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($produk_menipis as $item): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-semibold text-slate-700"><?= $item['kode'] ?></td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900"><?= $item['nama'] ?></td>
                            <td class="px-6 py-3.5 text-slate-500"><?= $item['kategori'] ?></td>
                            <td class="px-6 py-3.5 text-center font-medium"><?= $item['min'] ?> <?= $item['satuan'] ?></td>
                            <td class="px-6 py-3.5 text-center font-bold <?= $item['stok'] == 0 ? 'text-rose-600' : 'text-amber-600' ?>">
                                <?= $item['stok'] ?> <?= $item['satuan'] ?>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <?php if ($item['stok'] == 0): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                        Habis
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                        Menipis
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Inisialisasi Chart.js untuk Grafik Dashboard -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartTransaksi');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: [65, 59, 80, 81, 56, 95, 40],
                        backgroundColor: 'rgba(37, 99, 235, 0.85)',
                        borderRadius: 6
                    },
                    {
                        label: 'Barang Keluar',
                        data: [28, 48, 40, 50, 46, 60, 30],
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                    }
                }
            }
        });
    }
});
</script>
