<?php
// ====================================================================
// VIEW: DASHBOARD
// ====================================================================
$user = current_user();
$canReport = in_array(user_role(), ['admin', 'manager'], true);
$canTransaction = in_array(user_role(), ['admin', 'petugas'], true);

$stats = [
    ['label' => 'Total Produk', 'value' => (int) ($total_produk ?? 0), 'icon' => 'box',   'tone' => 'bg-brand-50 text-brand-600',    'link' => 'index.php?page=produk'],
    ['label' => 'Total Stok',   'value' => (int) ($total_stok ?? 0),   'icon' => 'layers', 'tone' => 'bg-emerald-50 text-emerald-600', 'link' => $canReport ? 'index.php?page=laporan&tipe=stok_gudang' : 'index.php?page=gudang'],
    ['label' => 'Gudang',       'value' => (int) ($total_gudang ?? 0), 'icon' => 'warehouse', 'tone' => 'bg-sky-50 text-sky-600',      'link' => 'index.php?page=gudang'],
    ['label' => 'Kategori',     'value' => (int) ($total_kategori ?? 0), 'icon' => 'tag',   'tone' => 'bg-violet-50 text-violet-600',  'link' => 'index.php?page=kategori'],
];

$masuk  = $grafik_masuk ?? [];
$keluar = $grafik_keluar ?? [];
$maxMasuk  = max(1, ...array_map(static fn ($r) => (int) $r['total'], $masuk ?: [['total' => 0]]));
$maxKeluar = max(1, ...array_map(static fn ($r) => (int) $r['total'], $keluar ?: [['total' => 0]]));
$sumMasuk  = array_sum(array_map(static fn ($r) => (int) $r['total'], $masuk));
$sumKeluar = array_sum(array_map(static fn ($r) => (int) $r['total'], $keluar));
?>
<div class="space-y-6">

    <!-- Sapaan -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
                Selamat datang, <?= htmlspecialchars($user['nama'] ?? 'Pengguna') ?> 👋
            </h2>
            <p class="mt-1 text-sm text-slate-500">Berikut ringkasan inventaris hari ini.</p>
        </div>
        <?php if ($canTransaction): ?>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?page=barang_masuk&action=tambah" class="btn btn-primary btn-sm">
                    <?= ui_icon('arrow-down', 'w-4 h-4') ?> Barang Masuk
                </a>
                <a href="index.php?page=barang_keluar&action=tambah" class="btn btn-ghost btn-sm">
                    <?= ui_icon('arrow-up', 'w-4 h-4') ?> Barang Keluar
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistik utama -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($stats as $stat): ?>
            <a href="<?= htmlspecialchars($stat['link']) ?>" class="stat-card transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="stat-icon <?= $stat['tone'] ?>"><?= ui_icon($stat['icon'], 'w-6 h-6') ?></span>
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500"><?= htmlspecialchars($stat['label']) ?></p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($stat['value'], 0, ',', '.') ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Peringatan stok -->
    <div class="grid gap-4 sm:grid-cols-2">
        <a href="index.php?page=produk" class="flex items-center gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:shadow-md">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <?= ui_icon('alert', 'w-6 h-6') ?>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Stok Menipis</p>
                <p class="text-2xl font-bold text-amber-900"><?= (int) ($total_menipis ?? 0) ?> <span class="text-sm font-medium">produk</span></p>
            </div>
        </a>
        <a href="index.php?page=produk" class="flex items-center gap-4 rounded-2xl border border-rose-200 bg-rose-50 p-5 transition hover:shadow-md">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                <?= ui_icon('alert', 'w-6 h-6') ?>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Stok Habis</p>
                <p class="text-2xl font-bold text-rose-900"><?= (int) ($total_habis ?? 0) ?> <span class="text-sm font-medium">produk</span></p>
            </div>
        </a>
    </div>

    <!-- Grafik -->
    <div class="grid gap-4 xl:grid-cols-2">
        <?php
        $charts = [
            ['title' => 'Barang Masuk', 'subtitle' => '7 hari terakhir', 'data' => $masuk, 'max' => $maxMasuk, 'sum' => $sumMasuk, 'color' => 'bg-brand-500', 'chip' => 'badge-brand', 'icon' => 'arrow-down'],
            ['title' => 'Barang Keluar', 'subtitle' => '7 hari terakhir', 'data' => $keluar, 'max' => $maxKeluar, 'sum' => $sumKeluar, 'color' => 'bg-emerald-500', 'chip' => 'badge-success', 'icon' => 'arrow-up'],
        ];
        ?>
        <?php foreach ($charts as $chart): ?>
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                            <?= ui_icon($chart['icon'], 'w-5 h-5') ?>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($chart['title']) ?></p>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($chart['subtitle']) ?></p>
                        </div>
                    </div>
                    <span class="badge <?= $chart['chip'] ?>"><?= (int) $chart['sum'] ?> transaksi</span>
                </div>

                <?php if (empty($chart['data'])): ?>
                    <div class="empty-state">
                        <span class="text-slate-300"><?= ui_icon('chart', 'w-10 h-10') ?></span>
                        <p class="text-sm text-slate-500">Belum ada transaksi 7 hari terakhir.</p>
                    </div>
                <?php else: ?>
                    <div data-bar-chart class="mt-6 flex h-44 items-end gap-2 sm:gap-3">
                        <?php foreach ($chart['data'] as $bar): ?>
                            <?php $pct = max(4, (int) round(((int) $bar['total'] / $chart['max']) * 100)); ?>
                            <div class="flex flex-1 flex-col items-center gap-2">
                                <span class="text-[11px] font-semibold text-slate-600"><?= (int) $bar['total'] ?></span>
                                <div class="flex h-28 w-full items-end">
                                    <div data-bar
                                         data-label="<?= htmlspecialchars($bar['label']) ?>: <?= (int) $bar['total'] ?> transaksi"
                                         class="w-full rounded-t-lg <?= $chart['color'] ?> opacity-90 transition hover:opacity-100"
                                         style="height: <?= $pct ?>%"></div>
                                </div>
                                <span class="text-[10px] font-medium text-slate-400"><?= htmlspecialchars($bar['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Daftar stok kritis -->
    <div class="grid gap-4 xl:grid-cols-2">
        <?php
        $lists = [
            ['title' => 'Stok Menipis', 'data' => $daftar_menipis ?? [], 'tone' => 'text-amber-600', 'badge' => 'badge-warning', 'empty' => 'Tidak ada produk dengan stok menipis.'],
            ['title' => 'Stok Habis',   'data' => $daftar_habis ?? [],   'tone' => 'text-rose-600',  'badge' => 'badge-danger',  'empty' => 'Tidak ada produk yang habis.'],
        ];
        ?>
        <?php foreach ($lists as $list): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= htmlspecialchars($list['title']) ?></h3>
                    <span class="badge <?= $list['badge'] ?>"><?= count($list['data']) ?> produk</span>
                </div>
                <?php if (empty($list['data'])): ?>
                    <div class="empty-state">
                        <span class="text-emerald-400"><?= ui_icon('check', 'w-9 h-9') ?></span>
                        <p class="text-sm text-slate-500"><?= htmlspecialchars($list['empty']) ?></p>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table-modern min-w-[520px]">
                            <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-right">Stok</th>
                                <th class="text-right">Min.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach (array_slice($list['data'], 0, 6) as $row): ?>
                                <tr>
                                    <td>
                                        <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                                        <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                                    </td>
                                    <td class="text-slate-500"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                                    <td class="text-right font-semibold <?= $list['tone'] ?>">
                                        <?= (int) $row['total_stok'] ?> <?= htmlspecialchars($row['nama_satuan'] ?? '') ?>
                                    </td>
                                    <td class="text-right text-slate-400"><?= (int) $row['stok_minimum'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($list['data']) > 6): ?>
                        <div class="border-t border-slate-100 px-5 py-3 text-right">
                            <a href="index.php?page=produk" class="link text-xs">Lihat semua &rarr;</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Transaksi terbaru -->
    <div class="grid gap-4 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Barang Masuk Terbaru</h3>
                <a href="index.php?page=barang_masuk" class="link text-xs">Lihat semua</a>
            </div>
            <?php if (empty($masuk_terbaru)): ?>
                <div class="empty-state"><p class="text-sm text-slate-500">Belum ada transaksi.</p></div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table-modern min-w-[520px]">
                        <thead><tr><th>Tanggal</th><th>Pemasok</th><th class="text-right">Qty</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($masuk_terbaru as $row): ?>
                            <tr>
                                <td class="whitespace-nowrap text-slate-500"><?= tanggal_indonesia($row['tanggal_masuk']) ?></td>
                                <td>
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_pemasok']) ?></p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($row['nama_gudang']) ?></p>
                                </td>
                                <td class="text-right font-semibold"><?= (int) $row['total_kuantitas'] ?></td>
                                <td class="text-right">
                                    <a href="index.php?page=barang_masuk&action=detail&id=<?= (int) $row['id_barang_masuk'] ?>" class="btn btn-ghost btn-sm">
                                        <?= ui_icon('eye', 'w-3.5 h-3.5') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Barang Keluar Terbaru</h3>
                <a href="index.php?page=barang_keluar" class="link text-xs">Lihat semua</a>
            </div>
            <?php if (empty($keluar_terbaru)): ?>
                <div class="empty-state"><p class="text-sm text-slate-500">Belum ada transaksi.</p></div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table-modern min-w-[520px]">
                        <thead><tr><th>Tanggal</th><th>Penerima</th><th class="text-right">Qty</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($keluar_terbaru as $row): ?>
                            <tr>
                                <td class="whitespace-nowrap text-slate-500"><?= tanggal_indonesia($row['tanggal_keluar']) ?></td>
                                <td>
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_penerima']) ?></p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($row['nama_gudang']) ?></p>
                                </td>
                                <td class="text-right font-semibold"><?= (int) $row['total_kuantitas'] ?></td>
                                <td class="text-right">
                                    <a href="index.php?page=barang_keluar&action=detail&id=<?= (int) $row['id_barang_keluar'] ?>" class="btn btn-ghost btn-sm">
                                        <?= ui_icon('eye', 'w-3.5 h-3.5') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
