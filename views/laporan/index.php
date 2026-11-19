<?php
// ====================================================================
// VIEW: LAPORAN
// ====================================================================
$tipe          = $tipe ?? 'produk';
$rows          = $hasil_laporan ?? [];
$gudangList    = $daftar_gudang ?? [];
$kategoriList  = $daftar_kategori ?? [];
$tglAwal       = $tgl_awal ?? date('Y-m-01');
$tglAkhir      = $tgl_akhir ?? date('Y-m-d');
$gudangId      = $gudang_id ?? null;
$kategoriId    = $kategori_id ?? null;

$tabs = [
    'produk'        => ['label' => 'Produk',        'icon' => 'box'],
    'stok_gudang'   => ['label' => 'Stok Gudang',   'icon' => 'warehouse'],
    'barang_masuk'  => ['label' => 'Barang Masuk',  'icon' => 'arrow-down'],
    'barang_keluar' => ['label' => 'Barang Keluar', 'icon' => 'arrow-up'],
];

$queryBase = ['page' => 'laporan', 'tipe' => $tipe, 'tgl_awal' => $tglAwal, 'tgl_akhir' => $tglAkhir];
if ($gudangId)   { $queryBase['gudang']   = $gudangId; }
if ($kategoriId) { $queryBase['kategori'] = $kategoriId; }

// Ringkasan
$totalBaris = count($rows);
$totalQty   = 0;
$totalNilai = 0.0;
$perluPerhatian = 0;

foreach ($rows as $r) {
    if ($tipe === 'produk') {
        $totalQty   += (int) ($r['total_stok'] ?? 0);
        $totalNilai += (float) ($r['total_aset_beli'] ?? 0);
    } elseif ($tipe === 'stok_gudang') {
        $totalQty += (int) ($r['jumlah_stok'] ?? 0);
        if (($r['status_stok'] ?? 'Aman') !== 'Aman') { $perluPerhatian++; }
    } elseif ($tipe === 'barang_masuk') {
        $totalQty   += (int) ($r['jumlah'] ?? 0);
        $totalNilai += (float) ($r['subtotal'] ?? 0);
    } else {
        $totalQty += (int) ($r['jumlah'] ?? 0);
    }
}
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3 print:hidden">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Laporan Inventaris</h2>
            <p class="mt-1 text-sm text-slate-500">Rekap data produk, stok, dan transaksi sesuai filter.</p>
        </div>
        <button type="button" class="btn btn-ghost btn-sm" data-print>
            <?= ui_icon('print', 'w-4 h-4') ?> Cetak
        </button>
    </div>

    <!-- Tab -->
    <div class="flex flex-wrap gap-1.5 rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm print:hidden">
        <?php foreach ($tabs as $key => $tab): ?>
            <?php
            $tabQuery = http_build_query(array_merge($queryBase, ['tipe' => $key]));
            $isActive = $tipe === $key;
            ?>
            <a href="index.php?<?= htmlspecialchars($tabQuery, ENT_QUOTES) ?>"
               class="flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium transition <?= $isActive
                    ? 'bg-brand-600 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-100' ?>">
                <?= ui_icon($tab['icon'], 'w-4 h-4') ?>
                <span><?= htmlspecialchars($tab['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filter -->
    <div class="card print:hidden">
        <form method="get" class="flex flex-col gap-3 p-5 lg:flex-row lg:items-end">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="tipe" value="<?= htmlspecialchars($tipe) ?>">

            <?php if ($tipe === 'produk'): ?>
                <div class="w-full lg:max-w-xs">
                    <label class="label" for="kategori">Kategori</label>
                    <select class="input" id="kategori" name="kategori">
                        <option value="">Semua kategori</option>
                        <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= (int) $kat['id_kategori'] ?>" <?= (int) $kategoriId === (int) $kat['id_kategori'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <?php elseif ($tipe === 'stok_gudang'): ?>
                <div class="w-full lg:max-w-xs">
                    <label class="label" for="gudang">Gudang</label>
                    <select class="input" id="gudang" name="gudang">
                        <option value="">Semua gudang</option>
                        <?php foreach ($gudangList as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>" <?= (int) $gudangId === (int) $gd['id_gudang'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gd['nama_gudang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <?php else: ?>
                <div class="w-full lg:max-w-[12rem]">
                    <label class="label" for="tgl_awal">Dari Tanggal</label>
                    <input type="date" class="input" id="tgl_awal" name="tgl_awal" value="<?= htmlspecialchars($tglAwal) ?>">
                </div>
                <div class="w-full lg:max-w-[12rem]">
                    <label class="label" for="tgl_akhir">Sampai Tanggal</label>
                    <input type="date" class="input" id="tgl_akhir" name="tgl_akhir" value="<?= htmlspecialchars($tglAkhir) ?>">
                </div>
                <div class="w-full lg:max-w-xs">
                    <label class="label" for="gudang">Gudang</label>
                    <select class="input" id="gudang" name="gudang">
                        <option value="">Semua gudang</option>
                        <?php foreach ($gudangList as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>" <?= (int) $gudangId === (int) $gd['id_gudang'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gd['nama_gudang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary"><?= ui_icon('filter', 'w-4 h-4') ?> Terapkan</button>
            <a href="index.php?page=laporan&tipe=<?= urlencode($tipe) ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <!-- Ringkasan -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <span class="stat-icon bg-brand-50 text-brand-600"><?= ui_icon('layers', 'w-6 h-6') ?></span>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Baris Data</p>
                <p class="text-2xl font-bold text-slate-900"><?= number_format($totalBaris, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon bg-emerald-50 text-emerald-600"><?= ui_icon('box', 'w-6 h-6') ?></span>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Kuantitas</p>
                <p class="text-2xl font-bold text-slate-900"><?= number_format($totalQty, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="stat-card">
            <?php if ($tipe === 'stok_gudang'): ?>
                <span class="stat-icon bg-amber-50 text-amber-600"><?= ui_icon('alert', 'w-6 h-6') ?></span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Perlu Perhatian</p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($perluPerhatian, 0, ',', '.') ?></p>
                </div>
            <?php elseif ($tipe === 'barang_keluar'): ?>
                <span class="stat-icon bg-rose-50 text-rose-600"><?= ui_icon('arrow-up', 'w-6 h-6') ?></span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jenis Laporan</p>
                    <p class="text-lg font-bold text-slate-900">Pengeluaran</p>
                </div>
            <?php else: ?>
                <span class="stat-icon bg-sky-50 text-sky-600"><?= ui_icon('money', 'w-6 h-6') ?></span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                        <?= $tipe === 'barang_masuk' ? 'Nilai Pembelian' : 'Nilai Aset (Beli)' ?>
                    </p>
                    <p class="text-lg font-bold text-slate-900"><?= rupiah($totalNilai) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= htmlspecialchars($tabs[$tipe]['label']) ?></h3>
            <span class="text-xs text-slate-400">
                <?php if (in_array($tipe, ['barang_masuk', 'barang_keluar'], true)): ?>
                    <?= tanggal_indonesia($tglAwal) ?> – <?= tanggal_indonesia($tglAkhir) ?>
                <?php else: ?>
                    Data terkini
                <?php endif; ?>
            </span>
        </div>

        <div class="table-wrap">
            <?php if ($tipe === 'produk'): ?>
                <table class="table-modern min-w-[900px]">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Harga Jual</th>
                        <th class="text-right">Stok</th>
                        <th class="text-right">Min.</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $stok = (int) $row['total_stok'];
                        $min  = (int) $row['stok_minimum'];
                        [$badge, $label] = $stok === 0
                            ? ['badge-danger', 'Habis']
                            : ($stok <= $min ? ['badge-warning', 'Menipis'] : ['badge-success', 'Aman']);
                        ?>
                        <tr>
                            <td class="font-medium text-slate-700"><?= htmlspecialchars($row['kode_produk']) ?></td>
                            <td class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_kategori'] ?? '—') ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_satuan'] ?? '—') ?></td>
                            <td class="text-right text-slate-600"><?= rupiah($row['harga_beli']) ?></td>
                            <td class="text-right text-slate-600"><?= rupiah($row['harga_jual']) ?></td>
                            <td class="text-right font-semibold text-slate-800"><?= $stok ?></td>
                            <td class="text-right text-slate-400"><?= $min ?></td>
                            <td class="text-center"><span class="badge <?= $badge ?>"><?= $label ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($tipe === 'stok_gudang'): ?>
                <table class="table-modern min-w-[820px]">
                    <thead>
                    <tr>
                        <th>Gudang</th>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-right">Stok</th>
                        <th class="text-right">Min.</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        [$badge, $label] = match ($row['status_stok']) {
                            'Habis'   => ['badge-danger', 'Habis'],
                            'Menipis' => ['badge-warning', 'Menipis'],
                            default   => ['badge-success', 'Aman'],
                        };
                        ?>
                        <tr>
                            <td class="font-medium text-slate-700"><?= htmlspecialchars($row['nama_gudang']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['kode_produk']) ?></td>
                            <td class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_satuan']) ?></td>
                            <td class="text-right font-semibold text-slate-800"><?= (int) $row['jumlah_stok'] ?></td>
                            <td class="text-right text-slate-400"><?= (int) $row['stok_minimum'] ?></td>
                            <td class="text-center"><span class="badge <?= $badge ?>"><?= $label ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($tipe === 'barang_masuk'): ?>
                <table class="table-modern min-w-[980px]">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pemasok</th>
                        <th>Gudang</th>
                        <th>Petugas</th>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="whitespace-nowrap text-slate-500"><?= tanggal_indonesia($row['tanggal_masuk']) ?></td>
                            <td class="font-medium text-slate-700"><?= htmlspecialchars($row['nama_pemasok']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_gudang']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['petugas']) ?></td>
                            <td>
                                <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                            </td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_satuan']) ?></td>
                            <td class="text-right font-semibold text-slate-800"><?= (int) $row['jumlah'] ?></td>
                            <td class="whitespace-nowrap text-right text-slate-600"><?= rupiah($row['harga_satuan']) ?></td>
                            <td class="whitespace-nowrap text-right font-semibold text-slate-800"><?= rupiah($row['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <table class="table-modern min-w-[920px]">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Gudang</th>
                        <th>Penerima</th>
                        <th>Tujuan</th>
                        <th>Petugas</th>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="whitespace-nowrap text-slate-500"><?= tanggal_indonesia($row['tanggal_keluar']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_gudang']) ?></td>
                            <td class="font-medium text-slate-700"><?= htmlspecialchars($row['nama_penerima']) ?></td>
                            <td class="max-w-[14rem] truncate text-slate-500"><?= htmlspecialchars($row['tujuan']) ?></td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['petugas']) ?></td>
                            <td>
                                <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                            </td>
                            <td class="text-slate-500"><?= htmlspecialchars($row['nama_satuan']) ?></td>
                            <td class="text-right font-semibold text-slate-800"><?= (int) $row['jumlah'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (empty($rows)): ?>
                <div class="empty-state">
                    <span class="text-slate-300"><?= ui_icon('chart', 'w-10 h-10') ?></span>
                    <p class="text-sm font-medium text-slate-600">Tidak ada data untuk filter ini</p>
                    <p class="text-xs text-slate-400">Coba ubah rentang tanggal, gudang, atau kategori.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
