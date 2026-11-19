<?php
// ====================================================================
// VIEW: PRODUK — EDIT
// ====================================================================
$p = $produk ?? [];
$existingFoto = (string) ($p['foto_produk'] ?? '');
$preview = ($existingFoto !== '' && is_file(__DIR__ . '/../../public/uploads/produk/' . $existingFoto))
    ? ROOT_URL . '/uploads/produk/' . rawurlencode($existingFoto)
    : url('assets/images/default.svg');
?>
<div class="mx-auto max-w-4xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="index.php?page=produk" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Edit Produk</h2>
            <p class="mt-0.5 text-sm text-slate-500"><?= htmlspecialchars($p['kode_produk'] ?? '') ?></p>
        </div>
    </div>

    <form action="index.php?page=produk&action=perbarui" method="post" enctype="multipart/form-data" class="card">
        <?= csrf_field() ?>
        <input type="hidden" name="id_produk" value="<?= (int) ($p['id_produk'] ?? 0) ?>">

        <div class="grid gap-6 p-5 lg:grid-cols-[1fr,260px] lg:p-6">

            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="kode">Kode Produk <span class="text-rose-500">*</span></label>
                        <input class="input" id="kode" name="kode_produk" required maxlength="50"
                               value="<?= htmlspecialchars($p['kode_produk'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="label" for="nama">Nama Produk <span class="text-rose-500">*</span></label>
                        <input class="input" id="nama" name="nama_produk" required maxlength="150"
                               value="<?= htmlspecialchars($p['nama_produk'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="kategori">Kategori <span class="text-rose-500">*</span></label>
                        <select class="input" id="kategori" name="id_kategori" required>
                            <option value="">Pilih kategori</option>
                            <?php foreach ($kategori_list ?? [] as $kat): ?>
                                <option value="<?= (int) $kat['id_kategori'] ?>"
                                    <?= (int) ($p['id_kategori'] ?? 0) === (int) $kat['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="satuan">Satuan <span class="text-rose-500">*</span></label>
                        <select class="input" id="satuan" name="id_satuan" required>
                            <option value="">Pilih satuan</option>
                            <?php foreach ($satuan_list ?? [] as $sat): ?>
                                <option value="<?= (int) $sat['id_satuan'] ?>"
                                    <?= (int) ($p['id_satuan'] ?? 0) === (int) $sat['id_satuan'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sat['nama_satuan'] . ' (' . $sat['singkatan'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label" for="harga_beli">Harga Beli</label>
                        <input type="number" min="0" step="1" class="input" id="harga_beli" name="harga_beli"
                               value="<?= htmlspecialchars((string) ($p['harga_beli'] ?? 0)) ?>">
                    </div>
                    <div>
                        <label class="label" for="harga_jual">Harga Jual</label>
                        <input type="number" min="0" step="1" class="input" id="harga_jual" name="harga_jual"
                               value="<?= htmlspecialchars((string) ($p['harga_jual'] ?? 0)) ?>">
                    </div>
                    <div>
                        <label class="label" for="stok_min">Stok Minimum</label>
                        <input type="number" min="0" step="1" class="input" id="stok_min" name="stok_minimum"
                               value="<?= (int) ($p['stok_minimum'] ?? 0) ?>">
                    </div>
                </div>

                <div>
                    <label class="label" for="deskripsi">Deskripsi</label>
                    <textarea class="input min-h-[110px]" id="deskripsi" name="deskripsi"><?= htmlspecialchars($p['deskripsi'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="label" for="status">Status</label>
                    <select class="input" id="status" name="status">
                        <option value="aktif" <?= ($p['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($p['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                <p class="label">Foto Produk</p>
                <div class="overflow-hidden rounded-xl border-2 border-dashed border-slate-200 bg-slate-50">
                    <img id="foto-preview" src="<?= htmlspecialchars($preview) ?>" alt="Pratinjau"
                         class="mx-auto h-44 w-full object-cover">
                </div>
                <input type="file" id="foto" name="foto_produk" accept="image/jpeg,image/png,image/webp"
                       class="block w-full cursor-pointer rounded-lg border border-slate-200 bg-white text-xs text-slate-500 file:mr-3 file:cursor-pointer file:border-0 file:bg-brand-600 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700">
                <p class="hint">Biarkan kosong jika tidak ingin mengganti foto.</p>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <a href="index.php?page=produk" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('foto')?.addEventListener('change', function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        document.getElementById('foto-preview').src = URL.createObjectURL(file);
    });
</script>
