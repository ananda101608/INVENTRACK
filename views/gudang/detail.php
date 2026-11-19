<?php
// ====================================================================
// VIEW: GUDANG — DETAIL
// ====================================================================
$rows      = $daftar_stok ?? [];
$totalQty  = array_sum(array_map(static fn ($r) => (int) $r['jumlah_stok'], $rows));
$totalNilai = array_sum(array_map(static fn ($r) => (int) $r['jumlah_stok'] * (float) $r['harga_beli'], $rows));
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="index.php?page=gudang" class="btn btn-ghost btn-sm" title="Kembali">
                <?= ui_icon('arrow-left', 'w-4 h-4') ?>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($gudang['nama_gudang']) ?></h2>
                <p class="mt-0.5 flex items-center gap-1.5 text-sm text-slate-500">
                    <?= ui_icon('pin', 'w-3.5 h-3.5 text-slate-400') ?>
                    <?= htmlspecialchars($gudang['alamat'] ?: 'Alamat belum diisi') ?>
                </p>
            </div>
        </div>
        <span class="badge badge-brand">PJ: <?= htmlspecialchars($gudang['penanggung_jawab'] ?: '—') ?></span>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <span class="stat-icon bg-brand-50 text-brand-600"><?= ui_icon('box', 'w-6 h-6') ?></span>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jenis Produk</p>
                <p class="text-2xl font-bold text-slate-900"><?= count($rows) ?></p>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon bg-emerald-50 text-emerald-600"><?= ui_icon('layers', 'w-6 h-6') ?></span>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Stok</p>
                <p class="text-2xl font-bold text-slate-900"><?= number_format($totalQty, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon bg-sky-50 text-sky-600"><?= ui_icon('money', 'w-6 h-6') ?></span>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Nilai Stok (Beli)</p>
                <p class="text-lg font-bold text-slate-900"><?= rupiah($totalNilai) ?></p>
            </div>
        </div>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <h3 class="card-title">Stok per Produk</h3>
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari produk..." data-table-search="tbl-stok-gudang">
            </div>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-stok-gudang">
                <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $qty  = (int) $row['jumlah_stok'];
                    $min  = (int) $row['stok_minimum'];
                    [$badge, $label] = $qty === 0
                        ? ['badge-danger', 'Habis']
                        : ($qty <= $min ? ['badge-warning', 'Menipis'] : ['badge-success', 'Aman']);
                    ?>
                    <tr>
                        <td>
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                        </td>
                        <td class="text-slate-500"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td class="text-right text-slate-600"><?= rupiah($row['harga_beli']) ?></td>
                        <td class="text-right text-slate-600"><?= rupiah($row['harga_jual']) ?></td>
                        <td class="text-center font-semibold text-slate-800"><?= $qty ?> <?= htmlspecialchars($row['nama_satuan']) ?></td>
                        <td class="text-center"><span class="badge <?= $badge ?>"><?= $label ?></span></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr data-empty-row>
                        <td colspan="6">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('box', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada stok di gudang ini</p>
                                <p class="text-xs text-slate-400">Stok bertambah melalui transaksi barang masuk atau transfer.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada produk yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
