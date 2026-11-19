<?php
// ====================================================================
// VIEW: BARANG MASUK — DAFTAR
// ====================================================================
$rows = $daftar_masuk ?? [];
$canManage = in_array(user_role(), ['admin', 'petugas'], true);
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Barang Masuk</h2>
            <p class="mt-1 text-sm text-slate-500">Riwayat penerimaan barang dari pemasok.</p>
        </div>
        <?php if ($canManage): ?>
            <a href="index.php?page=barang_masuk&action=tambah" class="btn btn-primary">
                <?= ui_icon('plus', 'w-4 h-4') ?> Catat Barang Masuk
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="get" class="flex w-full flex-col gap-2 sm:flex-row sm:items-end">
                <input type="hidden" name="page" value="barang_masuk">
                <div class="flex-1">
                    <label class="label" for="tgl_awal">Dari Tanggal</label>
                    <input type="date" class="input" id="tgl_awal" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal ?? '') ?>">
                </div>
                <div class="flex-1">
                    <label class="label" for="tgl_akhir">Sampai Tanggal</label>
                    <input type="date" class="input" id="tgl_akhir" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= ui_icon('filter', 'w-4 h-4') ?> Filter</button>
                <?php if (!empty($tgl_awal) || !empty($tgl_akhir)): ?>
                    <a href="index.php?page=barang_masuk" class="btn btn-ghost">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table-modern">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pemasok</th>
                    <th>Gudang</th>
                    <th>Petugas</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="whitespace-nowrap">
                            <p class="font-medium text-slate-800"><?= tanggal_indonesia($row['tanggal_masuk']) ?></p>
                            <p class="text-xs text-slate-400">#<?= (int) $row['id_barang_masuk'] ?></p>
                        </td>
                        <td class="font-medium text-slate-700"><?= htmlspecialchars($row['nama_pemasok']) ?></td>
                        <td class="text-slate-500"><?= htmlspecialchars($row['nama_gudang']) ?></td>
                        <td class="text-slate-500"><?= htmlspecialchars($row['nama_petugas']) ?></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_item'] ?></span></td>
                        <td class="text-center font-semibold text-slate-800"><?= (int) $row['total_kuantitas'] ?></td>
                        <td class="whitespace-nowrap text-right font-semibold text-slate-800"><?= rupiah($row['total_nominal']) ?></td>
                        <td class="text-right">
                            <a href="index.php?page=barang_masuk&action=detail&id=<?= (int) $row['id_barang_masuk'] ?>"
                               class="btn btn-ghost btn-sm"><?= ui_icon('eye', 'w-3.5 h-3.5') ?> Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('arrow-down', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada transaksi barang masuk</p>
                                <p class="text-xs text-slate-400">Catat penerimaan barang untuk menambah stok gudang.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
