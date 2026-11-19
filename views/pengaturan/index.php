<?php
// ====================================================================
// VIEW: PENGATURAN
// ====================================================================
$u = $user ?? current_user() ?? [];
$peran = (string) ($u['peran'] ?? user_role() ?? '');
$roleBadge = match ($peran) {
    'admin'   => 'badge-danger',
    'manager' => 'badge-info',
    default   => 'badge-neutral',
};
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Pengaturan Akun</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola profil dan keamanan akun Anda.</p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-[300px,1fr]">

        <!-- Kartu profil -->
        <div class="card h-fit p-6 text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-brand-600 text-2xl font-bold text-white shadow-lg shadow-brand-600/25">
                <?= htmlspecialchars(ui_initial($u['nama'] ?? 'U')) ?>
            </span>
            <h3 class="mt-4 truncate text-lg font-bold text-slate-900"><?= htmlspecialchars($u['nama'] ?? 'Pengguna') ?></h3>
            <p class="truncate text-sm text-slate-500"><?= htmlspecialchars($u['email'] ?? '') ?></p>
            <div class="mt-3 flex flex-wrap justify-center gap-2">
                <span class="badge <?= $roleBadge ?> capitalize"><?= htmlspecialchars($peran) ?></span>
                <span class="badge <?= ($u['status'] ?? 'aktif') === 'aktif' ? 'badge-success' : 'badge-neutral' ?>">
                    <?= ($u['status'] ?? 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                </span>
            </div>

            <dl class="mt-6 space-y-3 border-t border-slate-100 pt-5 text-left text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Bergabung</dt>
                    <dd class="font-medium text-slate-700">
                        <?= !empty($u['dibuat_pada']) ? tanggal_indonesia($u['dibuat_pada']) : '—' ?>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Peran</dt>
                    <dd class="font-medium capitalize text-slate-700"><?= htmlspecialchars($peran ?: '—') ?></dd>
                </div>
            </dl>
        </div>

        <div class="space-y-5">

            <!-- Form profil -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Profil</h3>
                        <p class="mt-0.5 text-xs text-slate-500">Nama dan email yang digunakan untuk masuk.</p>
                    </div>
                    <?= ui_icon('user', 'w-5 h-5 text-slate-400') ?>
                </div>
                <form action="index.php?page=pengaturan&action=perbarui_profil" method="post">
                    <?= csrf_field() ?>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <label class="label" for="nama">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input class="input" id="nama" name="nama" required maxlength="100"
                                   value="<?= htmlspecialchars($u['nama'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="label" for="email">Email <span class="text-rose-500">*</span></label>
                            <input type="email" class="input" id="email" name="email" required maxlength="100"
                                   value="<?= htmlspecialchars($u['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="flex justify-end border-t border-slate-100 px-5 py-4">
                        <button type="submit" class="btn btn-primary" data-submit-guard>
                            <?= ui_icon('save', 'w-4 h-4') ?> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form password -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Keamanan</h3>
                        <p class="mt-0.5 text-xs text-slate-500">Gunakan kata sandi yang kuat dan tidak dipakai di tempat lain.</p>
                    </div>
                    <?= ui_icon('key', 'w-5 h-5 text-slate-400') ?>
                </div>
                <form action="index.php?page=pengaturan&action=ganti_password" method="post">
                    <?= csrf_field() ?>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label" for="password_lama">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                            <input type="password" class="input" id="password_lama" name="password_lama"
                                   required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="label" for="password_baru">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                            <input type="password" class="input" id="password_baru" name="password_baru"
                                   required minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter">
                        </div>
                        <div>
                            <label class="label" for="konfirmasi_password">Konfirmasi Kata Sandi <span class="text-rose-500">*</span></label>
                            <input type="password" class="input" id="konfirmasi_password" name="konfirmasi_password"
                                   required minlength="6" autocomplete="new-password" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>
                    <div class="flex justify-end border-t border-slate-100 px-5 py-4">
                        <button type="submit" class="btn btn-primary" data-submit-guard>
                            <?= ui_icon('key', 'w-4 h-4') ?> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
