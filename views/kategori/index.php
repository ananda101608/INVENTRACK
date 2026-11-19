<?php
// ====================================================================
// VIEW: KATEGORI
// ====================================================================
$role      = user_role();
$canManage = in_array($role, ['admin', 'petugas'], true);
$canDelete = $role === 'admin';
$rows      = $daftar_kategori ?? [];
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kategori Produk</h2>
            <p class="mt-1 text-sm text-slate-500">Kelompokkan produk agar pencarian dan laporan lebih rapi.</p>
        </div>
        <?php if ($canManage): ?>
            <button type="button" class="btn btn-primary"
                    data-modal-open="modal-kategori"
                    data-modal-action="index.php?page=kategori&action=simpan"
                    data-reset="1"
                    data-modal-title="Tambah Kategori">
                <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Kategori
            </button>
        <?php endif; ?>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari kategori..."
                       data-table-search="tbl-kategori">
            </div>
            <span class="text-xs text-slate-400"><span data-table-count><?= count($rows) ?></span> kategori</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-kategori">
                <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Jumlah Produk</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                    <?= ui_icon('tag', 'w-4 h-4') ?>
                                </span>
                                <span class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                            </div>
                        </td>
                        <td class="max-w-md text-slate-500"><?= htmlspecialchars($row['deskripsi'] ?: '—') ?></td>
                        <td class="text-center">
                            <span class="badge badge-neutral"><?= (int) $row['total_produk'] ?> produk</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($canManage): ?>
                                    <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                            data-modal-open="modal-kategori"
                                            data-modal-action="index.php?page=kategori&action=perbarui"
                                            data-modal-title="Edit Kategori"
                                            data-fill="<?= htmlspecialchars(json_encode([
                                                'id_kategori'   => $row['id_kategori'],
                                                'nama_kategori' => $row['nama_kategori'],
                                                'deskripsi'     => $row['deskripsi'],
                                            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                        <?= ui_icon('edit', 'w-3.5 h-3.5') ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <form method="post" action="index.php?page=kategori&action=hapus&id=<?= (int) $row['id_kategori'] ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id_kategori'] ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                                data-confirm="Hapus kategori '<?= htmlspecialchars($row['nama_kategori'], ENT_QUOTES) ?>'?">
                                            <?= ui_icon('trash', 'w-3.5 h-3.5') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr data-empty-row>
                        <td colspan="4">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('tag', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada kategori</p>
                                <p class="text-xs text-slate-400">Tambahkan kategori pertama untuk mulai mengelompokkan produk.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada kategori yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
<dialog id="modal-kategori" class="modal">
    <form action="index.php?page=kategori&action=simpan" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_kategori" value="">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-base font-semibold text-slate-800" data-modal-title>Tambah Kategori</h3>
            <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><?= ui_icon('close', 'w-4 h-4') ?></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="label" for="kat-nama">Nama Kategori <span class="text-rose-500">*</span></label>
                <input class="input" id="kat-nama" name="nama_kategori" required maxlength="100" placeholder="Contoh: Elektronik">
            </div>
            <div>
                <label class="label" for="kat-deskripsi">Deskripsi</label>
                <textarea class="input min-h-[96px]" id="kat-deskripsi" name="deskripsi" placeholder="Keterangan singkat kategori"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" class="btn btn-ghost" data-modal-close>Batal</button>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan</button>
        </div>
    </form>
</dialog>
<?php endif; ?>
