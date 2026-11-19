<?php
// ====================================================================
// VIEW: TRANSFER GUDANG — DAFTAR
// ====================================================================
$rows        = $daftar_transfer ?? [];
$activeTab   = $status ?? '';
$canManage   = in_array(user_role(), ['admin', 'petugas'], true);
$tabs        = [
    ''         => ['label' => 'Semua',    'badge' => 'badge-neutral'],
    'diproses' => ['label' => 'Diproses', 'badge' => 'badge-warning'],
    'selesai'  => ['label' => 'Selesai',  'badge' => 'badge-success'],
    'batal'    => ['label' => 'Batal',    'badge' => 'badge-danger'],
];
$statusBadge = static function (string $status): array {
    return match ($status) {
        'selesai'  => ['badge-success', 'Selesai'],
        'batal'    => ['badge-danger', 'Batal'],
        default    => ['badge-warning', 'Diproses'],
    };
};
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Transfer Gudang</h2>
            <p class="mt-1 text-sm text-slate-500">Perpindahan stok dari satu gudang ke gudang lain.</p>
        </div>
        <?php if ($canManage): ?>
            <a href="index.php?page=transfer&action=tambah" class="btn btn-primary">
                <?= ui_icon('plus', 'w-4 h-4') ?> Buat Transfer
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="flex flex-wrap gap-1.5">
                <?php foreach ($tabs as $key => $tab): ?>
                    <a href="index.php?page=transfer<?= $key !== '' ? '&status=' . urlencode($key) : '' ?>"
                       class="rounded-lg px-3 py-2 text-sm font-medium transition <?= $activeTab === $key
                            ? 'bg-brand-600 text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= htmlspecialchars($tab['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <span class="text-xs text-slate-400"><?= count($rows) ?> transaksi</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Rute Transfer</th>
                    <th>Petugas</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php [$badgeClass, $badgeLabel] = $statusBadge((string) $row['status']); ?>
                    <tr>
                        <td class="whitespace-nowrap">
                            <p class="font-medium text-slate-800"><?= tanggal_indonesia($row['tanggal_transfer']) ?></p>
                            <p class="text-xs text-slate-400">#<?= (int) $row['id_transfer'] ?></p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="badge badge-neutral"><?= htmlspecialchars($row['gudang_asal']) ?></span>
                                <?= ui_icon('transfer', 'w-4 h-4 shrink-0 text-slate-400') ?>
                                <span class="badge badge-brand"><?= htmlspecialchars($row['gudang_tujuan']) ?></span>
                            </div>
                        </td>
                        <td class="text-slate-500"><?= htmlspecialchars($row['nama_petugas']) ?></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_item'] ?></span></td>
                        <td class="text-center font-semibold text-slate-800"><?= (int) $row['total_kuantitas'] ?></td>
                        <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                        <td class="text-right">
                            <a href="index.php?page=transfer&action=detail&id=<?= (int) $row['id_transfer'] ?>"
                               class="btn btn-ghost btn-sm"><?= ui_icon('eye', 'w-3.5 h-3.5') ?> Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('transfer', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada transfer gudang</p>
                                <p class="text-xs text-slate-400">
                                    <?= $activeTab !== '' ? 'Tidak ada transaksi dengan status ini.' : 'Buat transfer untuk memindahkan stok antar lokasi.' ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
