<?php
// ====================================================================
// VIEW: PENGGUNA (admin)
// ====================================================================
$rows     = $daftar_pengguna ?? [];
$me       = current_user();
$roleTone = static function (string $peran): string {
    return match ($peran) {
        'admin'   => 'badge-danger',
        'manager' => 'badge-info',
        default   => 'badge-neutral',
    };
};
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Pengguna</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola akun, peran, dan status akses.</p>
        </div>
        <button type="button" class="btn btn-primary"
                data-modal-open="modal-pengguna"
                data-modal-action="index.php?page=pengguna&action=simpan"
                data-reset="1"
                data-modal-title="Tambah Pengguna">
            <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Pengguna
        </button>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <?php
        $counts = ['admin' => 0, 'petugas' => 0, 'manager' => 0];
        foreach ($rows as $row) {
            $counts[$row['peran']] = ($counts[$row['peran']] ?? 0) + 1;
        }
        ?>
        <?php foreach ([['Admin', 'admin', 'shield'], ['Petugas', 'petugas', 'user'], ['Manager', 'manager', 'chart']] as [$label, $key, $icon]): ?>
            <div class="stat-card">
                <span class="stat-icon bg-slate-100 text-slate-600"><?= ui_icon($icon === 'shield' ? 'key' : $icon, 'w-6 h-6') ?></span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500"><?= $label ?></p>
                    <p class="text-2xl font-bold text-slate-900"><?= (int) ($counts[$key] ?? 0) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card" data-table-wrap>
        <div class="card-header">
            <div class="relative w-full max-w-xs">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                <input type="text" class="input pl-9" placeholder="Cari nama atau email..." data-table-search="tbl-pengguna">
            </div>
            <span class="text-xs text-slate-400"><span data-table-count><?= count($rows) ?></span> pengguna</span>
        </div>

        <div class="table-wrap">
            <table class="table-modern" id="tbl-pengguna">
                <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php $isMe = (int) $row['id_pengguna'] === (int) ($me['id_pengguna'] ?? 0); ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-700">
                                    <?= htmlspecialchars(ui_initial($row['nama'])) ?>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-800">
                                        <?= htmlspecialchars($row['nama']) ?>
                                        <?php if ($isMe): ?><span class="ml-1 text-[10px] font-semibold uppercase text-brand-600">(Anda)</span><?php endif; ?>
                                    </p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($row['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge <?= $roleTone($row['peran']) ?> capitalize"><?= htmlspecialchars($row['peran']) ?></span></td>
                        <td>
                            <span class="badge <?= $row['status'] === 'aktif' ? 'badge-success' : 'badge-neutral' ?>">
                                <?= $row['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="whitespace-nowrap text-slate-500"><?= tanggal_indonesia($row['dibuat_pada']) ?></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                        data-modal-open="modal-pengguna"
                                        data-modal-action="index.php?page=pengguna&action=perbarui"
                                        data-modal-title="Edit Pengguna"
                                        data-fill="<?= htmlspecialchars(json_encode([
                                            'id_pengguna' => $row['id_pengguna'],
                                            'nama'        => $row['nama'],
                                            'email'       => $row['email'],
                                            'peran'       => $row['peran'],
                                            'status'      => $row['status'],
                                            'kata_sandi'  => '',
                                        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                    <?= ui_icon('edit', 'w-3.5 h-3.5') ?>
                                </button>
                                <?php if (!$isMe): ?>
                                    <a href="index.php?page=pengguna&action=toggle_status&id=<?= (int) $row['id_pengguna'] ?>"
                                       class="btn btn-ghost btn-sm <?= $row['status'] === 'aktif' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' ?>"
                                       title="<?= $row['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                       data-confirm="<?= $row['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> akun '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>'?">
                                        <?= ui_icon($row['status'] === 'aktif' ? 'close' : 'check', 'w-3.5 h-3.5') ?>
                                    </a>
                                    <a href="index.php?page=pengguna&action=hapus&id=<?= (int) $row['id_pengguna'] ?>"
                                       class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                       data-confirm="Hapus akun '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>' secara permanen?">
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
                                <span class="text-slate-300"><?= ui_icon('users', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Belum ada pengguna</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr data-search-empty class="hidden">
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada pengguna yang cocok.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<dialog id="modal-pengguna" class="modal">
    <form action="index.php?page=pengguna&action=simpan" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_pengguna" value="">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-base font-semibold text-slate-800" data-modal-title>Tambah Pengguna</h3>
            <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><?= ui_icon('close', 'w-4 h-4') ?></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="label" for="usr-nama">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input class="input" id="usr-nama" name="nama" required maxlength="100" placeholder="Nama pengguna">
            </div>
            <div>
                <label class="label" for="usr-email">Email <span class="text-rose-500">*</span></label>
                <input type="email" class="input" id="usr-email" name="email" required maxlength="100" placeholder="nama@perusahaan.com">
            </div>
            <div>
                <label class="label" for="usr-pass">Kata Sandi</label>
                <input type="password" class="input" id="usr-pass" name="kata_sandi" autocomplete="new-password" placeholder="••••••••">
                <p class="hint">Wajib untuk pengguna baru. Kosongkan saat edit jika tidak ingin mengubah.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="usr-peran">Peran</label>
                    <select class="input" id="usr-peran" name="peran">
                        <option value="petugas">Petugas</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="usr-status">Status</label>
                    <select class="input" id="usr-status" name="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" class="btn btn-ghost" data-modal-close>Batal</button>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan</button>
        </div>
    </form>
</dialog>
