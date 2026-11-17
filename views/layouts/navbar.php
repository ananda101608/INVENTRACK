<!-- Main Content Wrapper (Kanan Sidebar) -->
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    
    <!-- Top Navbar -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 z-30">
        
        <!-- Sisi Kiri: Tombol Hamburger & Judul Halaman -->
        <div class="flex items-center gap-4">
            <button id="sidebar-toggle" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 lg:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h2 class="text-lg font-bold text-slate-800 leading-tight">
                    <?= isset($judul_halaman) ? bersihkan($judul_halaman) : 'Dashboard' ?>
                </h2>
                <p class="text-xs text-slate-400 hidden sm:block"><?= APP_TAGLINE ?></p>
            </div>
        </div>

        <!-- Sisi Kanan: Profil & Tanggal -->
        <div class="flex items-center gap-4">
            <!-- Tanggal Hari Ini -->
            <div class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span><?= tanggal_indonesia(date('Y-m-d'), true) ?></span>
            </div>

            <!-- Info Akun Pengguna -->
            <div class="flex items-center gap-3 pl-2 border-l border-slate-100">
                <?php 
                    $user_info = current_user() ?? [
                        'nama'  => 'Admin Demo',
                        'email' => 'admin@inventrack.local',
                        'peran' => 'admin'
                    ];
                    $role_colors = [
                        'admin'   => 'bg-blue-100 text-blue-700',
                        'petugas' => 'bg-emerald-100 text-emerald-700',
                        'manager' => 'bg-purple-100 text-purple-700'
                    ];
                    $badge_class = $role_colors[$user_info['peran']] ?? 'bg-slate-100 text-slate-700';
                ?>
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-semibold text-sm uppercase">
                    <?= substr($user_info['nama'], 0, 1) ?>
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-xs font-bold text-slate-800 leading-tight"><?= bersihkan($user_info['nama']) ?></p>
                    <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full mt-0.5 <?= $badge_class ?>">
                        <?= ucfirst(bersihkan($user_info['peran'])) ?>
                    </span>
                </div>
            </div>
        </div>

    </header>

    <!-- Main Page Content Body -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
