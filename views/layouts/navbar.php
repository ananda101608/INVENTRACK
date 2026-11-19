<?php
// ====================================================================
// LAYOUT: NAVBAR
// ====================================================================
require_once __DIR__ . '/../partials/icon.php';

$navUser = current_user();
$navRole = user_role() ?? '';
$today   = tanggal_indonesia(date('Y-m-d'), true);
?>
<div class="lg:pl-64 print:pl-0">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur print:hidden">
        <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
            <button type="button" id="sidebar-open"
                    class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                    aria-label="Buka menu">
                <?= ui_icon('menu', 'w-5 h-5') ?>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-base font-bold text-slate-900 lg:text-lg"><?= htmlspecialchars($docTitle ?? APP_NAME) ?></h1>
                <p class="hidden truncate text-xs text-slate-500 sm:block"><?= htmlspecialchars($docSubtitle ?? APP_TAGLINE) ?></p>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <span class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-500 xl:flex">
                    <?= ui_icon('calendar', 'w-4 h-4 text-slate-400') ?>
                    <?= htmlspecialchars($today) ?>
                </span>

                <div class="relative">
                    <button type="button" id="user-menu-button"
                            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-2.5 transition hover:bg-slate-50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">
                            <?= htmlspecialchars(ui_initial($navUser['nama'] ?? 'U')) ?>
                        </span>
                        <span class="hidden text-left leading-tight sm:block">
                            <span class="block max-w-[10rem] truncate text-xs font-semibold text-slate-800"><?= htmlspecialchars($navUser['nama'] ?? 'Pengguna') ?></span>
                            <span class="block text-[11px] capitalize text-slate-500"><?= htmlspecialchars($navRole) ?></span>
                        </span>
                        <?= ui_icon('chevron-down', 'w-4 h-4 text-slate-400') ?>
                    </button>

                    <div id="user-menu"
                         class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($navUser['nama'] ?? 'Pengguna') ?></p>
                            <p class="truncate text-xs text-slate-500"><?= htmlspecialchars($navUser['email'] ?? '') ?></p>
                        </div>
                        <a href="index.php?page=pengaturan" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <?= ui_icon('settings', 'w-4 h-4 text-slate-400') ?> Pengaturan
                        </a>
                        <a href="index.php?page=logout" class="flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                            <?= ui_icon('logout', 'w-4 h-4') ?> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php require __DIR__ . '/../partials/flash.php'; ?>

    <main class="mx-auto w-full max-w-[1440px] px-4 py-6 lg:px-8 lg:py-8 print:max-w-none print:p-0">
