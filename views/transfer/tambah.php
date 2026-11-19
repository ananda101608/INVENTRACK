<?php
// ====================================================================
// VIEW: TRANSFER GUDANG — TAMBAH
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
                            data-stock="<?= (int) $pr['total_stok'] ?>"
                            data-unit="<?= htmlspecialchars($pr['nama_satuan'] ?? '', ENT_QUOTES) ?>">
                        <?= htmlspecialchars($pr['kode_produk'] . ' — ' . $pr['nama_produk']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="hint">Total stok semua gudang: <span data-stock-label>—</span></p>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="jumlah[]" min="1" step="1" class="input w-28 text-right" data-qty required>
        </td>
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
        <a href="index.php?page=transfer" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Buat Transfer Gudang</h2>
            <p class="mt-0.5 text-sm text-slate-500">Stok asal berkurang dan stok tujuan bertambah secara otomatis.</p>
        </div>
    </div>

    <form action="index.php?page=transfer&action=simpan" method="post" class="space-y-5" data-transfer-form>
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Rute &amp; Jadwal</h3></div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="label" for="asal">Gudang Asal <span class="text-rose-500">*</span></label>
                    <select class="input" id="asal" name="id_gudang_asal" required data-transfer-origin>
                        <option value="">Pilih gudang asal</option>
                        <?php foreach ($daftar_gudang ?? [] as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>"><?= htmlspecialchars($gd['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label" for="tujuan">Gudang Tujuan <span class="text-rose-500">*</span></label>
                    <select class="input" id="tujuan" name="id_gudang_tujuan" required>
                        <option value="">Pilih gudang tujuan</option>
                        <?php foreach ($daftar_gudang ?? [] as $gd): ?>
                            <option value="<?= (int) $gd['id_gudang'] ?>"><?= htmlspecialchars($gd['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 hidden text-xs font-medium text-rose-600" data-transfer-warning>
                        Gudang asal dan tujuan tidak boleh sama.
                    </p>
                </div>
                <div>
                    <label class="label" for="tanggal">Tanggal Transfer <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" class="input" id="tanggal" name="tanggal_transfer"
                           value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="label" for="catatan">Catatan</label>
                    <textarea class="input min-h-[70px]" id="catatan" name="catatan" placeholder="Alasan atau keterangan transfer (opsional)"></textarea>
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
                <table class="table-modern min-w-[560px]">
                    <thead>
                    <tr>
                        <th class="w-10 text-center">#</th>
                        <th>Produk</th>
                        <th class="w-32 text-right">Jumlah</th>
                        <th class="w-12"></th>
                    </tr>
                    </thead>
                    <tbody data-repeater-rows>
                    <?= $rowTemplate() ?>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                <p class="text-sm text-slate-500">Total kuantitas dipindahkan</p>
                <p class="text-lg font-bold text-slate-900" data-grand-qty>0</p>
            </div>

            <template data-repeater-template>
                <?= $rowTemplate() ?>
            </template>
        </div>

        <div class="flex flex-wrap justify-end gap-2">
            <a href="index.php?page=transfer" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Proses Transfer</button>
        </div>
    </form>
</div>

<script nonce="<?= csp_nonce() ?>">
    (function () {
        const form = document.querySelector('[data-transfer-form]');
        if (!form) return;

        const asal    = form.querySelector('[name="id_gudang_asal"]');
        const tujuan  = form.querySelector('[name="id_gudang_tujuan"]');
        const warning = form.querySelector('[data-transfer-warning]');

        const check = () => {
            const same = asal.value !== '' && asal.value === tujuan.value;
            warning?.classList.toggle('hidden', !same);
            if (tujuan) tujuan.setCustomValidity(same ? 'Gudang asal dan tujuan tidak boleh sama.' : '');
        };

        asal?.addEventListener('change', check);
        tujuan?.addEventListener('change', check);
        form.addEventListener('submit', check);
    })();
</script>
