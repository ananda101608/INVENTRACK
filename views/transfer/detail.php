<?php
// ====================================================================
// VIEW: TRANSFER GUDANG — DETAIL
// ====================================================================
$t = $transaksi ?? [];
$items = $t['items'] ?? [];
$totalQty = array_sum(array_map(static fn ($i) => (int) $i['jumlah'], $items));
[$statusBadge, $statusLabel] = match ($t['status'] ?? 'diproses') {
    'selesai' => ['badge-success', 'Selesai'],
    'batal'   => ['badge-danger', 'Batal'],
    default   => ['badge-warning', 'Diproses'],
};
?>
<div class="mx-auto max-w-4xl space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="index.php?page=transfer" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Detail Transfer Gudang</h2>
                <p class="mt-0.5 text-sm text-slate-500">Transaksi #<?= (int) ($t['id_transfer'] ?? 0) ?></p>
            </div>
        </div>
        <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>
    </div>

    <div class="card p-5">
        <div class="flex flex-wrap items-center justify-center gap-3 rounded-xl bg-slate-50 p-5">
            <div class="text-center">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Dari</p>
                <p class="mt-1 text-sm font-bold text-slate-800"><?= htmlspecialchars($t['gudang_asal'] ?? '—') ?></p>
            </div>
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-white">
                <?= ui_icon('transfer', 'w-5 h-5') ?>
            </span>
            <div class="text-center">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Ke</p>
                <p class="mt-1 text-sm font-bold text-brand-700"><?= htmlspecialchars($t['gudang_tujuan'] ?? '—') ?></p>
            </div>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $meta = [
                ['Tanggal Transfer', tanggal_waktu_indonesia($t['tanggal_transfer'] ?? null), 'calendar'],
                ['Petugas', $t['nama_petugas'] ?? '—', 'user'],
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
        <div class="card-header"><h3 class="card-title">Item yang Ditransfer</h3></div>
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
        <a href="index.php?page=transfer" class="btn btn-ghost">Kembali ke Daftar</a>
    </div>
</div>
