<?php
// ====================================================================
// VIEW: LOGIN (standalone, tanpa layout)
// ====================================================================
require_once __DIR__ . '/../partials/icon.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? ('Login — ' . APP_NAME), ENT_QUOTES) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/style.css?v=' . APP_VERSION) ?>">
</head>
<body class="min-h-screen bg-slate-100">
<div class="grid min-h-screen lg:grid-cols-2">

    <!-- Panel brand -->
    <section class="relative hidden overflow-hidden bg-navy-900 lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>

        <div class="relative flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-xl shadow-brand-900/50">
                <?= ui_icon('box', 'w-6 h-6') ?>
            </span>
            <div>
                <p class="text-lg font-bold tracking-wide text-white"><?= htmlspecialchars(APP_NAME) ?></p>
                <p class="text-xs text-slate-400">Inventory Management System</p>
            </div>
        </div>

        <div class="relative max-w-md">
            <h2 class="text-3xl font-bold leading-tight text-white">
                Track Every Item.<br>
                <span class="text-brand-300">Control Every Stock.</span>
            </h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-400">
                Pantau stok, catat barang masuk dan keluar, kelola transfer antar gudang,
                serta hasilkan laporan inventaris dalam satu tempat.
            </p>

            <ul class="mt-8 space-y-3 text-sm text-slate-300">
                <?php foreach ([
                    ['scan', 'Stok real-time per gudang'],
                    ['transfer', 'Transfer & stok opname terintegrasi'],
                    ['chart', 'Laporan dan analisis siap cetak'],
                ] as [$icon, $text]): ?>
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-brand-300">
                            <?= ui_icon($icon, 'w-4 h-4') ?>
                        </span>
                        <?= htmlspecialchars($text) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <p class="relative text-xs text-slate-500">&copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?> · v<?= htmlspecialchars(APP_VERSION) ?></p>
    </section>

    <!-- Panel form -->
    <section class="flex items-center justify-center px-5 py-10 sm:px-8">
        <div class="w-full max-w-md">

            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <?= ui_icon('box', 'w-5 h-5') ?>
                </span>
                <div>
                    <p class="text-base font-bold text-slate-900"><?= htmlspecialchars(APP_NAME) ?></p>
                    <p class="text-[11px] text-slate-500"><?= htmlspecialchars(APP_TAGLINE) ?></p>
                </div>
            </div>

            <div class="card p-6 sm:p-8">
                <h1 class="text-2xl font-bold text-slate-900">Masuk ke akun</h1>
                <p class="mt-1 text-sm text-slate-500">Gunakan email dan kata sandi yang terdaftar.</p>

                <?php if ($flash): ?>
                    <?php
                    $tone = match ($flash['tipe']) {
                        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                        'error'   => 'border-rose-200 bg-rose-50 text-rose-800',
                        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
                        default   => 'border-sky-200 bg-sky-50 text-sky-800',
                    };
                    ?>
                    <div class="mt-5 flex items-start gap-2 rounded-xl border px-4 py-3 text-sm <?= $tone ?>">
                        <?= ui_icon('info', 'mt-0.5 h-4 w-4 shrink-0') ?>
                        <span><?= htmlspecialchars($flash['pesan']) ?></span>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="post" class="mt-6 space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label for="email" class="label">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <?= ui_icon('mail', 'w-4 h-4') ?>
                            </span>
                            <input type="email" id="email" name="email" required autocomplete="username"
                                   placeholder="nama@perusahaan.com"
                                   class="input pl-10" value="<?= htmlspecialchars(old('email')) ?>">
                        </div>
                    </div>

                    <div>
                        <label for="kata_sandi" class="label">Kata sandi</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <?= ui_icon('key', 'w-4 h-4') ?>
                            </span>
                            <input type="password" id="kata_sandi" name="kata_sandi" required autocomplete="current-password"
                                   placeholder="••••••••" class="input pl-10 pr-10">
                            <button type="button" data-toggle-password
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                                    aria-label="Tampilkan kata sandi">
                                <?= ui_icon('eye', 'w-4 h-4') ?>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3">
                        Masuk
                    </button>
                </form>
            </div>

            <div class="mt-6 rounded-xl border border-dashed border-slate-300 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Akun demo</p>
                <ul class="mt-2 space-y-1 text-xs text-slate-500">
                    <li><span class="font-medium text-slate-700">admin</span> · admin@inventrack.com</li>
                    <li><span class="font-medium text-slate-700">petugas</span> · petugas@inventrack.com</li>
                    <li><span class="font-medium text-slate-700">manager</span> · manager@inventrack.com</li>
                </ul>
            </div>
        </div>
    </section>
</div>

<script>
    document.querySelector('[data-toggle-password]')?.addEventListener('click', function () {
        const input = document.getElementById('kata_sandi');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.classList.toggle('text-brand-600');
    });
</script>
</body>
</html>
