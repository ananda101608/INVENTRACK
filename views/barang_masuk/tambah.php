<?php
// ====================================================================
// VIEW: BARANG MASUK — TAMBAH
// ====================================================================
$produkList = $daftar_produk ?? [];
$rowTemplate = static function () use ($produkList): string {
    ob_start(); ?>
    <tr data-repeater-row class="align-top">
        <td class="px-3 py-2 text-center text-xs font-semibold text-slate-400" data-row-number>1</td>
        <td class="px-3 py-2">
            <select name="id_produk[]" class="input min-w-[220px]" data-product-select required>
                <option value="">Pilih produk</option>
                <?php foreach ($produkList as $pr): ?>
                    <option value="<?= (int) $pr['id_produk'] ?>"
                            data-price="<?= (float) $pr['harga_beli'] ?>"
                            data-stock="<?= (int) $pr['total_stok'] ?>"
                            data-unit="<?= htmlspecialchars($pr['nama_satuan'] ?? '', ENT_QUOTES) ?>">
                        <?= htmlspecialchars($pr['kode_produk'] . ' — ' . $pr['nama_produk']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="hint">Stok tersedia: <span data-stock-label>—</span></p>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="jumlah[]" min="1" step="1" class="input w-28 text-right" data-qty required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="harga_satuan[]" min="0" step="1" class="input w-36 text-right" data-price required>
        </td>
        <td class="px-3 py-2 text-right text-sm font-semibold text-slate-700" data-line-total>0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" data-repeater-remove title="Hapus baris">
                <?= ui_icon('trash', 'w-3.5 h-3.5') ?>
            </button>
        </td>
    </tr>
    <?php
    return (string) ob_get_clean();
};
?>
<div class="mx-auto max-w-5xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="index.php?page=barang_masuk" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Catat Barang Masuk</h2>
            <p class="mt-0.5 text-sm text-slate-500">Stok gudang akan otomatis bertambah setelah disimpan.</p>
        </div>
    </div>

    <form action="index.php?page=barang_masuk&action=simpan" method="post" class="space-y-5">
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Informasi Transaksi</h3></div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="label" for="pemasok">Pemasok <span class="text-rose-500">*</span></label>
                    <select class="input" id="pemasok" name="id_pemasok" required>
                        <option value="">Pilih pemasok</option>
                        <?php foreach ($daftar_pemasok ?? [] as $pem): ?>
                            <option value="<?= (int) $pem['id_pemasok'] ?>"><?= htmlspecialchars($pem['nama_pemasok']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label" for="gudang">Gudang Tujuan <span class="text-rose-500">*</span></label>
                    <select class="input" id="gudang" name="id_gudang" required>
                        <option value="">Pilih gudang</option>
                        <?php foreach ($daftar_gudang ?? [] as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>"><?= htmlspecialchars($gd['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label" for="tanggal">Tanggal Masuk <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" class="input" id="tanggal" name="tanggal_masuk"
                           value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="label" for="catatan">Catatan</label>
                    <textarea class="input min-h-[70px]" id="catatan" name="catatan" placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>
        </div>

        <div class="card" data-repeater>
            <div class="card-header">
                <h3 class="card-title">Daftar Item</h3>
                <button type="button" class="btn btn-soft btn-sm" data-repeater-add>
                    <?= ui_icon('plus', 'w-3.5 h-3.5') ?> Tambah Item
                </button>
            </div>

            <div class="table-wrap">
                <table class="table-modern min-w-[760px]">
                    <thead>
                    <tr>
                        <th class="w-10 text-center">#</th>
                        <th>Produk</th>
                        <th class="w-28 text-right">Jumlah</th>
                        <th class="w-36 text-right">Harga Satuan</th>
                        <th class="w-32 text-right">Subtotal</th>
                        <th class="w-12"></th>
                    </tr>
                    </thead>
                    <tbody data-repeater-rows>
                    <?= $rowTemplate() ?>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                <p class="text-sm text-slate-500">Total keseluruhan</p>
                <p class="text-lg font-bold text-slate-900" data-grand-total>Rp 0</p>
            </div>

            <template data-repeater-template>
                <?= $rowTemplate() ?>
            </template>
        </div>

        <div class="flex flex-wrap justify-end gap-2">
            <a href="index.php?page=barang_masuk" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan Transaksi</button>
        </div>
    </form>
</div>
