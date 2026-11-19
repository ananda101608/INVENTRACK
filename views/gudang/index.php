<?php
// ====================================================================
// VIEW: GUDANG
// ====================================================================
$role      = user_role();
$canManage = in_array($role, ['admin', 'petugas'], true);
$canDelete = $role === 'admin';
$rows      = $daftar_gudang ?? [];
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Gudang</h2>
            <p class="mt-1 text-sm text-slate-500">Lokasi penyimpanan beserta ringkasan stoknya.</p>
        </div>
        <?php if ($canManage): ?>
            <button type="button" class="btn btn-primary"
                    data-modal-open="modal-gudang"
                    data-modal-action="index.php?page=gudang&action=simpan"
                    data-reset="1"
                    data-modal-title="Tambah Gudang">
                <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Gudang
            </button>
        <?php endif; ?>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari gudang..." data-table-search="tbl-gudang">
            </div>
            <span class="text-xs text-slate-400"><span data-table-count><?= count($rows) ?></span> gudang</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-gudang">
                <thead>
                <tr>
                    <th>Gudang</th>
                    <th>Penanggung Jawab</th>
                    <th class="text-center">Jenis Produk</th>
                    <th class="text-center">Total Stok</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                    <?= ui_icon('warehouse', 'w-4 h-4') ?>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_gudang']) ?></p>
                                    <p class="max-w-xs truncate text-xs text-slate-400"><?= htmlspecialchars($row['alamat'] ?: '—') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-600"><?= htmlspecialchars($row['penanggung_jawab'] ?: '—') ?></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_jenis_produk'] ?></span></td>
                        <td class="text-center"><span class="badge badge-brand"><?= (int) $row['total_stok'] ?></span></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="index.php?page=gudang&action=detail&id=<?= (int) $row['id_gudang'] ?>"
                                   class="btn btn-ghost btn-sm" title="Detail">
                                    <?= ui_icon('eye', 'w-3.5 h-3.5') ?>
                                </a>
                                <?php if ($canManage): ?>
                                    <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                            data-modal-open="modal-gudang"
                                            data-modal-action="index.php?page=gudang&action=perbarui"
                                            data-modal-title="Edit Gudang"
                                            data-fill="<?= htmlspecialchars(json_encode([
                                                'id_gudang'        => $row['id_gudang'],
                                                'nama_gudang'      => $row['nama_gudang'],
                                                'alamat'           => $row['alamat'],
                                                'penanggung_jawab' => $row['penanggung_jawab'],
                                            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                        <?= ui_icon('edit', 'w-3.5 h-3.5') ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="index.php?page=gudang&action=hapus&id=<?= (int) $row['id_gudang'] ?>"
                                       class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                       data-confirm="Hapus gudang '<?= htmlspecialchars($row['nama_gudang'], ENT_QUOTES) ?>'?">
                                        <?= ui_icon('trash', 'w-3.5 h-3.5') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr data-empty-row>
                        <td colspan="5">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('warehouse', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada gudang</p>
                                <p class="text-xs text-slate-400">Tambahkan lokasi penyimpanan pertama.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada gudang yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
<dialog id="modal-gudang" class="modal">
    <form action="index.php?page=gudang&action=simpan" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_gudang" value="">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-base font-semibold text-slate-800" data-modal-title>Tambah Gudang</h3>
            <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><?= ui_icon('close', 'w-4 h-4') ?></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="label" for="gd-nama">Nama Gudang <span class="text-rose-500">*</span></label>
                <input class="input" id="gd-nama" name="nama_gudang" required maxlength="100" placeholder="Gudang Utama">
            </div>
            <div>
                <label class="label" for="gd-pj">Penanggung Jawab</label>
                <input class="input" id="gd-pj" name="penanggung_jawab" maxlength="100" placeholder="Nama penanggung jawab">
            </div>
            <div>
                <label class="label" for="gd-alamat">Alamat</label>
                <textarea class="input min-h-[90px]" id="gd-alamat" name="alamat" placeholder="Alamat lokasi gudang"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" class="btn btn-ghost" data-modal-close>Batal</button>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan</button>
        </div>
    </form>
</dialog>
<?php endif; ?>
