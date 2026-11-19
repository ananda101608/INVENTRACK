<?php
// ====================================================================
// VIEW: PEMASOK
// ====================================================================
$role      = user_role();
$canManage = in_array($role, ['admin', 'petugas'], true);
$canDelete = $role === 'admin';
$rows      = $daftar_pemasok ?? [];
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Pemasok</h2>
            <p class="mt-1 text-sm text-slate-500">Mitra penyedia barang untuk transaksi masuk.</p>
        </div>
        <?php if ($canManage): ?>
            <button type="button" class="btn btn-primary"
                    data-modal-open="modal-pemasok"
                    data-modal-action="index.php?page=pemasok&action=simpan"
                    data-reset="1"
                    data-modal-title="Tambah Pemasok">
                <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Pemasok
            </button>
        <?php endif; ?>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari pemasok..." data-table-search="tbl-pemasok">
            </div>
            <span class="text-xs text-slate-400"><span data-table-count><?= count($rows) ?></span> pemasok</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-pemasok">
                <thead>
                <tr>
                    <th>Pemasok</th>
                    <th>Kontak</th>
                    <th>Alamat</th>
                    <th class="text-center">Transaksi</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                                    <?= ui_icon('truck', 'w-4 h-4') ?>
                                </span>
                                <span class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_pemasok']) ?></span>
                            </div>
                        </td>
                        <td>
                            <p class="flex items-center gap-1.5 text-sm text-slate-600">
                                <?= ui_icon('phone', 'w-3.5 h-3.5 text-slate-400') ?>
                                <?= htmlspecialchars($row['nomor_telepon'] ?: '—') ?>
                            </p>
                            <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-400">
                                <?= ui_icon('mail', 'w-3.5 h-3.5') ?>
                                <?= htmlspecialchars($row['email'] ?: '—') ?>
                            </p>
                        </td>
                        <td class="max-w-xs text-slate-500"><?= htmlspecialchars($row['alamat'] ?: '—') ?></td>
                        <td class="text-center"><span class="badge badge-neutral"><?= (int) $row['total_transaksi'] ?>x</span></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($canManage): ?>
                                    <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                            data-modal-open="modal-pemasok"
                                            data-modal-action="index.php?page=pemasok&action=perbarui"
                                            data-modal-title="Edit Pemasok"
                                            data-fill="<?= htmlspecialchars(json_encode([
                                                'id_pemasok'     => $row['id_pemasok'],
                                                'nama_pemasok'   => $row['nama_pemasok'],
                                                'nomor_telepon'  => $row['nomor_telepon'],
                                                'email'          => $row['email'],
                                                'alamat'         => $row['alamat'],
                                            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                        <?= ui_icon('edit', 'w-3.5 h-3.5') ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="index.php?page=pemasok&action=hapus&id=<?= (int) $row['id_pemasok'] ?>"
                                       class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                       data-confirm="Hapus pemasok '<?= htmlspecialchars($row['nama_pemasok'], ENT_QUOTES) ?>'?">
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
                                <span class="text-slate-300"><?= ui_icon('truck', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada pemasok</p>
                                <p class="text-xs text-slate-400">Tambahkan data pemasok untuk mencatat barang masuk.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada pemasok yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
<dialog id="modal-pemasok" class="modal">
    <form action="index.php?page=pemasok&action=simpan" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_pemasok" value="">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-base font-semibold text-slate-800" data-modal-title>Tambah Pemasok</h3>
            <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><?= ui_icon('close', 'w-4 h-4') ?></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="label" for="pem-nama">Nama Pemasok <span class="text-rose-500">*</span></label>
                <input class="input" id="pem-nama" name="nama_pemasok" required maxlength="100" placeholder="PT Sumber Makmur">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="pem-telp">Nomor Telepon</label>
                    <input class="input" id="pem-telp" name="nomor_telepon" maxlength="20" placeholder="0812xxxxxxx">
                </div>
                <div>
                    <label class="label" for="pem-email">Email</label>
                    <input type="email" class="input" id="pem-email" name="email" maxlength="100" placeholder="sales@pemasok.com">
                </div>
            </div>
            <div>
                <label class="label" for="pem-alamat">Alamat</label>
                <textarea class="input min-h-[90px]" id="pem-alamat" name="alamat" placeholder="Alamat lengkap pemasok"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" class="btn btn-ghost" data-modal-close>Batal</button>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan</button>
        </div>
    </form>
</dialog>
<?php endif; ?>
