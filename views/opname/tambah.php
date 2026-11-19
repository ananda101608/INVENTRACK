<?php
// ====================================================================
// VIEW: STOK OPNAME — TAMBAH (2 langkah)
// ====================================================================
$idGudang   = (int) ($id_gudang ?? 0);
$produkList = $daftar_produk ?? [];
$gudangList = $daftar_gudang ?? [];

$gudangTerpilih = null;
foreach ($gudangList as $gd) {
    if ((int) $gd['id_gudang'] === $idGudang) {
        $gudangTerpilih = $gd;
        break;
    }
}
?>
<div class="mx-auto max-w-5xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="index.php?page=opname" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Stok Opname</h2>
            <p class="mt-0.5 text-sm text-slate-500">
                <?= $idGudang ? 'Masukkan jumlah stok fisik hasil hitung di gudang.' : 'Langkah 1 dari 2 — pilih gudang yang akan diperiksa.' ?>
            </p>
        </div>
    </div>

    <?php if (!$idGudang): ?>
        <!-- LANGKAH 1: pilih gudang -->
        <div class="card">
            <div class="card-header"><h3 class="card-title">Pilih Gudang</h3></div>
            <form method="get" action="index.php" class="space-y-5 p-5">
                <input type="hidden" name="page" value="opname">
                <input type="hidden" name="action" value="tambah">

                <div>
                    <label class="label" for="id_gudang">Gudang <span class="text-rose-500">*</span></label>
                    <select class="input" id="id_gudang" name="id_gudang" required>
                        <option value="">Pilih gudang</option>
                        <?php foreach ($gudangList as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>"><?= htmlspecialchars($gd['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="hint">Daftar produk yang ditampilkan adalah produk yang memiliki saldo stok di gudang ini.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="index.php?page=opname" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Lanjut <?= ui_icon('chevron-down', 'w-4 h-4 rotate-[-90deg]') ?></button>
                </div>
            </form>
        </div>

        <div class="card p-5">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <?= ui_icon('info', 'w-4 h-4') ?>
                </span>
                <p class="text-sm leading-relaxed text-slate-600">
                    Opname akan menyimpan saldo sistem saat ini sebagai pembanding, lalu menyesuaikan stok
                    dengan jumlah fisik yang Anda masukkan. Pastikan penghitungan dilakukan dengan teliti.
                </p>
            </div>
        </div>

    <?php else: ?>
        <!-- LANGKAH 2: isi stok fisik -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="badge badge-brand">Langkah 2 dari 2</span>
                <span class="font-semibold text-slate-800"><?= htmlspecialchars($gudangTerpilih['nama_gudang'] ?? 'Gudang') ?></span>
            </div>
            <a href="index.php?page=opname&action=tambah" class="btn btn-ghost btn-sm">
                <?= ui_icon('transfer', 'w-3.5 h-3.5') ?> Ganti Gudang
            </a>
        </div>

        <?php if (empty($produkList)): ?>
            <div class="card">
                <div class="empty-state">
                    <span class="text-slate-300"><?= ui_icon('box', 'w-10 h-10') ?></span>
                    <p class="text-sm font-medium text-slate-600">Tidak ada produk bersaldo di gudang ini</p>
                    <p class="text-xs text-slate-400">Tambahkan stok melalui barang masuk atau transfer terlebih dahulu.</p>
                    <a href="index.php?page=opname&action=tambah" class="btn btn-ghost btn-sm mt-2">Pilih gudang lain</a>
                </div>
            </div>
        <?php else: ?>
            <form action="index.php?page=opname&action=simpan" method="post" class="space-y-5">
                <?= csrf_field() ?>
                <input type="hidden" name="id_gudang" value="<?= $idGudang ?>">

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Informasi Opname</h3></div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <label class="label" for="tanggal">Tanggal Opname <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="input" id="tanggal" name="tanggal_opname"
                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div>
                            <label class="label" for="catatan">Catatan</label>
                            <input class="input" id="catatan" name="catatan" maxlength="255" placeholder="Catatan pemeriksaan (opsional)">
                        </div>
                    </div>
                </div>

                <div class="card" data-opname-table>
                    <div class="card-header">
                        <h3 class="card-title">Hitungan Fisik</h3>
                        <span class="text-xs text-slate-400"><?= count($produkList) ?> produk</span>
                    </div>

                    <div class="table-wrap">
                        <table class="table-modern min-w-[720px]">
                            <thead>
                            <tr>
                                <th class="w-10 text-center">#</th>
                                <th>Produk</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-right">Stok Sistem</th>
                                <th class="w-36 text-right">Stok Fisik</th>
                                <th class="w-28 text-right">Selisih</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($produkList as $i => $row): ?>
                                <?php $sistem = (int) $row['jumlah_stok']; ?>
                                <tr data-opname-row>
                                    <td class="text-center text-slate-400"><?= $i + 1 ?></td>
                                    <td>
                                        <p class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                                        <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                                    </td>
                                    <td class="text-center"><span class="badge badge-neutral"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                                    <td class="text-right font-semibold text-slate-700">
                                        <span data-system><?= $sistem ?></span>
                                        <span class="text-xs font-normal text-slate-400"><?= htmlspecialchars($row['nama_satuan']) ?></span>
                                    </td>
                                    <td class="text-right">
                                        <input type="hidden" name="id_produk[]" value="<?= (int) $row['id_produk'] ?>">
                                        <input type="number" name="stok_fisik[]" min="0" step="1"
                                               class="input w-28 text-right" data-physical
                                               value="<?= $sistem ?>" required>
                                    </td>
                                    <td class="px-3 py-2 text-right text-sm font-semibold text-slate-400" data-diff>0</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="grid gap-3 border-t border-slate-100 px-5 py-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-emerald-50 px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Surplus</p>
                            <p class="text-lg font-bold text-emerald-800" data-total-plus>0</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-rose-700">Selisih Kurang</p>
                            <p class="text-lg font-bold text-rose-800" data-total-minus>0</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Produk Diperiksa</p>
                            <p class="text-lg font-bold text-slate-800" data-total-checked>0</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-2">
                    <a href="index.php?page=opname" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan Opname</button>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
