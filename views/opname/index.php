<?php
// ====================================================================
// VIEW: STOK OPNAME — DAFTAR
// ====================================================================
$rows = $daftar_opname ?? [];
$canManage = in_array(user_role(), ['admin', 'petugas'], true);
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Stok Opname</h2>
            <p class="mt-1 text-sm text-slate-500">Pemeriksaan stok fisik dan sinkronisasi dengan saldo sistem.</p>
        </div>
        <?php if ($canManage): ?>
            <a href="index.php?page=opname&action=tambah" class="btn btn-primary">
                <?= ui_icon('clipboard', 'w-4 h-4') ?> Mulai Opname
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Opname</h3>
            <span class="text-xs text-slate-400"><?= count($rows) ?> pemeriksaan</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Gudang</th>
                    <th>Petugas</th>
                    <th class="text-center">Item Diperiksa</th>
                    <th class="text-center">Total Selisih</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $selisih = (int) $row['total_selisih'];
                    [$diffBadge, $diffText] = $selisih === 0
                        ? ['badge-success', 'Sesuai']
                        : ($selisih > 0 ? ['badge-info', '+' . $selisih . ' lebih'] : ['badge-danger', $selisih . ' kurang']);
                    ?>
                    <tr>
                        <td class="whitespace-nowrap">
                            <p class="font-medium text-slate-800"><?= tanggal_indonesia($row['tanggal_opname']) ?></p>
                            <p class="text-xs text-slate-400">#<?= (int) $row['id_opname'] ?></p>
                        </td>
                        <td class="font-medium text-slate-700"><?= htmlspecialchars($row['nama_gudang']) ?></td>
                        <td class="text-slate-500"><?= htmlspecialchars($row['nama_petugas']) ?></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_item'] ?> produk</span></td>
                        <td class="text-center"><span class="badge <?= $diffBadge ?>"><?= htmlspecialchars($diffText) ?></span></td>
                        <td class="text-right">
                            <a href="index.php?page=opname&action=detail&id=<?= (int) $row['id_opname'] ?>"
                               class="btn btn-ghost btn-sm"><?= ui_icon('eye', 'w-3.5 h-3.5') ?> Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('clipboard', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada riwayat opname</p>
                                <p class="text-xs text-slate-400">Mulai pemeriksaan stok fisik untuk menjaga akurasi data.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
