<?php
// ====================================================================
// VIEW: SATUAN
// ====================================================================
$role      = user_role();
$canManage = in_array($role, ['admin', 'petugas'], true);
$canDelete = $role === 'admin';
$rows      = $daftar_satuan ?? [];
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Satuan</h2>
            <p class="mt-1 text-sm text-slate-500">Satuan ukur yang dipakai pada data produk.</p>
        </div>
        <?php if ($canManage): ?>
            <button type="button" class="btn btn-primary"
                    data-modal-open="modal-satuan"
                    data-modal-action="index.php?page=satuan&action=simpan"
                    data-reset="1"
                    data-modal-title="Tambah Satuan">
                <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Satuan
            </button>
        <?php endif; ?>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari satuan..." data-table-search="tbl-satuan">
            </div>
            <span class="text-xs text-slate-400"><span data-table-count><?= count($rows) ?></span> satuan</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-satuan">
                <thead>
                <tr>
                    <th>Nama Satuan</th>
                    <th>Singkatan</th>
                    <th class="text-center">Dipakai Produk</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_satuan']) ?></td>
                        <td><span class="badge badge-brand"><?= htmlspecialchars($row['singkatan']) ?></span></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_produk'] ?> produk</span></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($canManage): ?>
                                    <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                            data-modal-open="modal-satuan"
                                            data-modal-action="index.php?page=satuan&action=perbarui"
                                            data-modal-title="Edit Satuan"
                                            data-fill="<?= htmlspecialchars(json_encode([
                                                'id_satuan'   => $row['id_satuan'],
                                                'nama_satuan' => $row['nama_satuan'],
                                                'singkatan'   => $row['singkatan'],
                                            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                        <?= ui_icon('edit', 'w-3.5 h-3.5') ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="index.php?page=satuan&action=hapus&id=<?= (int) $row['id_satuan'] ?>"
                                       class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                       data-confirm="Hapus satuan '<?= htmlspecialchars($row['nama_satuan'], ENT_QUOTES) ?>'?">
                                        <?= ui_icon('trash', 'w-3.5 h-3.5') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr data-empty-row>
                        <td colspan="4">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('ruler', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada satuan</p>
                                <p class="text-xs text-slate-400">Contoh: Pcs, Box, Kg, Liter.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada satuan yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
<dialog id="modal-satuan" class="modal">
    <form action="index.php?page=satuan&action=simpan" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_satuan" value="">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-base font-semibold text-slate-800" data-modal-title>Tambah Satuan</h3>
            <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><?= ui_icon('close', 'w-4 h-4') ?></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="label" for="sat-nama">Nama Satuan <span class="text-rose-500">*</span></label>
                <input class="input" id="sat-nama" name="nama_satuan" required maxlength="50" placeholder="Contoh: Pieces">
            </div>
            <div>
                <label class="label" for="sat-singkatan">Singkatan <span class="text-rose-500">*</span></label>
                <input class="input" id="sat-singkatan" name="singkatan" required maxlength="20" placeholder="Contoh: Pcs">
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" class="btn btn-ghost" data-modal-close>Batal</button>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan</button>
        </div>
    </form>
</dialog>
<?php endif; ?>
