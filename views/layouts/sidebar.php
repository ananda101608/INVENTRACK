<?php
// ====================================================================
// LAYOUT: SIDEBAR
// ====================================================================
require_once __DIR__ . '/../partials/icon.php';

$activePage = $_GET['page'] ?? 'dashboard';
$role       = user_role() ?? '';
$sidebarUser = current_user();

$menuGroups = [
    'Utama' => [
        ['page' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'roles' => null],
    ],
    'Master Data' => [
        ['page' => 'produk',   'label' => 'Produk',   'icon' => 'box',       'roles' => null],
        ['page' => 'kategori', 'label' => 'Kategori', 'icon' => 'tag',       'roles' => null],
        ['page' => 'satuan',   'label' => 'Satuan',   'icon' => 'ruler',     'roles' => null],
        ['page' => 'gudang',   'label' => 'Gudang',   'icon' => 'warehouse', 'roles' => null],
        ['page' => 'pemasok',  'label' => 'Pemasok',  'icon' => 'truck',     'roles' => null],
    ],
    'Transaksi' => [
        ['page' => 'barang_masuk',  'label' => 'Barang Masuk',  'icon' => 'arrow-down', 'roles' => null],
        ['page' => 'barang_keluar', 'label' => 'Barang Keluar', 'icon' => 'arrow-up',   'roles' => null],
        ['page' => 'transfer',      'label' => 'Transfer',      'icon' => 'transfer',   'roles' => null],
        ['page' => 'opname',        'label' => 'Stok Opname',   'icon' => 'clipboard',  'roles' => null],
    ],
    'Analitik & Sistem' => [
        ['page' => 'laporan',    'label' => 'Laporan',    'icon' => 'chart',    'roles' => ['admin', 'manager']],
        ['page' => 'pengguna',   'label' => 'Pengguna',   'icon' => 'users',    'roles' => ['admin']],
        ['page' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'settings', 'roles' => null],
    ],
];
?>
<!-- Overlay untuk mobile -->
<div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden print:hidden"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-navy-900 transition-transform duration-300 lg:translate-x-0 print:hidden">

    <!-- Brand -->
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/5 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-lg shadow-brand-900/40">
            <?= ui_icon('box', 'w-5 h-5') ?>
        </span>
        <div class="leading-tight">
            <p class="text-sm font-bold tracking-wide text-white"><?= htmlspecialchars(APP_NAME) ?></p>
            <p class="text-[11px] text-slate-400">Inventory System</p>
        </div>
        <button type="button" id="sidebar-close"
                class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white lg:hidden"
                aria-label="Tutup menu">
            <?= ui_icon('close', 'w-4 h-4') ?>
        </button>
    </div>

    <!-- Navigasi -->
    <nav class="flex-1 overflow-y-auto px-3 pb-4">
        <?php foreach ($menuGroups as $group => $items): ?>
            <?php
            $visible = array_filter($items, static function (array $item) use ($role): bool {
                return $item['roles'] === null || in_array($role, $item['roles'], true);
            });
            if (empty($visible)) {
                continue;
            }
            ?>
            <p class="nav-section"><?= htmlspecialchars($group) ?></p>
            <ul class="space-y-1">
                <?php foreach ($visible as $item): ?>
                    <?php $isActive = $activePage === $item['page']; ?>
                    <li>
                        <a href="index.php?page=<?= urlencode($item['page']) ?>"
                           class="nav-link <?= $isActive ? 'nav-link-active' : '' ?>">
                            <?= ui_icon($item['icon'], 'w-5 h-5 shrink-0') ?>
                            <span><?= htmlspecialchars($item['label']) ?></span>
                            <?php if ($isActive): ?>
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/80"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </nav>

    <!-- Profil singkat -->
    <div class="shrink-0 border-t border-white/5 p-3">
        <a href="index.php?page=pengaturan"
           class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/5">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500/20 text-sm font-bold text-brand-200">
                <?= htmlspecialchars(ui_initial($sidebarUser['nama'] ?? 'User')) ?>
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white"><?= htmlspecialchars($sidebarUser['nama'] ?? 'Pengguna') ?></p>
                <p class="truncate text-[11px] capitalize text-slate-400"><?= htmlspecialchars($role) ?></p>
            </div>
            <?= ui_icon('chevron-down', 'w-4 h-4 rotate-[-90deg] text-slate-500') ?>
        </a>
    </div>
</aside>
