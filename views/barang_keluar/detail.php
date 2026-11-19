<?php
// ====================================================================
// VIEW: BARANG KELUAR — DETAIL
// ====================================================================
$t = $transaksi ?? [];
$items = $t['items'] ?? [];
$totalQty = array_sum(array_map(static fn ($i) => (int) $i['jumlah'], $items));
?>
<div class="mx-auto max-w-4xl space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="index.php?page=barang_keluar" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Detail Barang Keluar</h2>
                <p class="mt-0.5 text-sm text-slate-500">Transaksi #<?= (int) ($t['id_barang_keluar'] ?? 0) ?></p>
            </div>
        </div>
        <span class="badge badge-warning"><?= ui_icon('arrow-up', 'w-3.5 h-3.5') ?> Stok dikurangi</span>
    </div>

    <div class="card p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $meta = [
                ['Gudang Asal', $t['nama_gudang'] ?? '—', 'warehouse'],
                ['Penerima', $t['nama_penerima'] ?? '—', 'user'],
                ['Tanggal Keluar', tanggal_waktu_indonesia($t['tanggal_keluar'] ?? null), 'calendar'],
                ['Tujuan', $t['tujuan'] ?? '—', 'pin'],
                ['Petugas', $t['nama_petugas'] ?? '—', 'users'],
                ['Jumlah Item', count($items) . ' baris', 'box'],
            ];
            ?>
            <?php foreach ($meta as [$label, $value, $icon]): ?>
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <?= ui_icon($icon, 'w-4 h-4') ?>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400"><?= $label ?></p>
                        <p class="truncate text-sm font-medium text-slate-800"><?= htmlspecialchars((string) $value) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($t['catatan'])): ?>
            <div class="mt-5 rounded-xl bg-slate-50 px-4 py-3">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Catatan</p>
                <p class="mt-1 text-sm text-slate-600"><?= nl2br(htmlspecialchars($t['catatan'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Item Barang</h3></div>
        <div class="table-wrap">
            <table class="table-modern">
                <thead>
                <tr>
                    <th class="w-10 text-center">#</th>
                    <th>Produk</th>
                    <th class="text-right">Jumlah</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td class="text-center text-slate-400"><?= $i + 1 ?></td>
                        <td>
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($item['nama_produk']) ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($item['kode_produk']) ?></p>
                        </td>
                        <td class="text-right font-semibold"><?= (int) $item['jumlah'] ?> <?= htmlspecialchars($item['nama_satuan']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr class="bg-slate-50">
                    <td colspan="2" class="px-4 py-3 text-sm font-semibold text-slate-600">Total Kuantitas</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-800"><?= $totalQty ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="index.php?page=barang_keluar" class="btn btn-ghost">Kembali ke Daftar</a>
    </div>
</div>
