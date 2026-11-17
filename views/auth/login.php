<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/helpers/session_helper.php';
require_once __DIR__ . '/../../app/helpers/format_helper.php';
require_once __DIR__ . '/../../app/helpers/csrf_helper.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563EB',
                        darkslate: '#0F172A',
                        bglight: '#F8FAFC'
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        
        <!-- Header / Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-blue-600 text-white items-center justify-center font-black text-2xl shadow-lg shadow-blue-500/25 mb-4">
                IN
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight"><?= APP_NAME ?></h1>
            <p class="text-sm text-slate-500 mt-1"><?= APP_TAGLINE ?></p>
        </div>

        <!-- Card Login -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">Selamat Datang Kembali</h2>
                <p class="text-xs text-slate-500 mt-0.5">Silakan masuk menggunakan akun yang sudah didaftarkan.</p>
            </div>

            <!-- Pesan Kesalahan / Notifikasi Flash -->
            <?php $flash = get_flash(); if ($flash): ?>
                <div class="mb-5 p-3.5 rounded-xl text-xs font-medium flex items-center gap-2.5 <?= $flash['tipe'] === 'error' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?= bersihkan($flash['pesan']) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?page=login_proses" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Pengguna</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" required 
                               placeholder="nama@inventrack.com"
                               class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               placeholder="••••••••"
                               class="w-full px-4 py-2.5 pr-11 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" data-target="password">
                            <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">
                    Akun didaftarkan oleh Administrator sistem.
                </p>
            </div>
        </div>

        <!-- Footer Copyright -->
        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. SMK Learning Project.
        </p>

    </div>

    <!-- Main JavaScript -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
