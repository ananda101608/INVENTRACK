<?php
// ====================================================================
// VIEW: PRODUK — TAMBAH
// ====================================================================
?>
<div class="mx-auto max-w-4xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="index.php?page=produk" class="btn btn-ghost btn-sm" title="Kembali"><?= ui_icon('arrow-left', 'w-4 h-4') ?></a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Tambah Produk</h2>
            <p class="mt-0.5 text-sm text-slate-500">Lengkapi informasi produk baru.</p>
        </div>
    </div>

    <form action="index.php?page=produk&action=simpan" method="post" enctype="multipart/form-data" class="card">
        <?= csrf_field() ?>

        <div class="grid gap-6 p-5 lg:grid-cols-[1fr,260px] lg:p-6">

            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="kode">Kode Produk <span class="text-rose-500">*</span></label>
                        <input class="input" id="kode" name="kode_produk" required maxlength="50" placeholder="PRD-011">
                        <p class="hint">Harus unik, contoh: PRD-011.</p>
                    </div>
                    <div>
                        <label class="label" for="nama">Nama Produk <span class="text-rose-500">*</span></label>
                        <input class="input" id="nama" name="nama_produk" required maxlength="150" placeholder="Nama produk">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="kategori">Kategori <span class="text-rose-500">*</span></label>
                        <select class="input" id="kategori" name="id_kategori" required>
                            <option value="">Pilih kategori</option>
                            <?php foreach ($kategori_list ?? [] as $kat): ?>
                                <option value="<?= (int) $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="satuan">Satuan <span class="text-rose-500">*</span></label>
                        <select class="input" id="satuan" name="id_satuan" required>
                            <option value="">Pilih satuan</option>
                            <?php foreach ($satuan_list ?? [] as $sat): ?>
                                <option value="<?= (int) $sat['id_satuan'] ?>">
                                    <?= htmlspecialchars($sat['nama_satuan'] . ' (' . $sat['singkatan'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label" for="harga_beli">Harga Beli</label>
                        <input type="number" min="0" step="1" class="input" id="harga_beli" name="harga_beli" value="0" placeholder="0">
                    </div>
                    <div>
                        <label class="label" for="harga_jual">Harga Jual</label>
                        <input type="number" min="0" step="1" class="input" id="harga_jual" name="harga_jual" value="0" placeholder="0">
                    </div>
                    <div>
                        <label class="label" for="stok_min">Stok Minimum</label>
                        <input type="number" min="0" step="1" class="input" id="stok_min" name="stok_minimum" value="5">
                    </div>
                </div>

                <div>
                    <label class="label" for="deskripsi">Deskripsi</label>
                    <textarea class="input min-h-[110px]" id="deskripsi" name="deskripsi" placeholder="Keterangan produk (opsional)"></textarea>
                </div>

                <div>
                    <label class="label" for="status">Status</label>
                    <select class="input" id="status" name="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Upload foto -->
            <div class="space-y-3">
                <p class="label">Foto Produk</p>
                <div class="overflow-hidden rounded-xl border-2 border-dashed border-slate-200 bg-slate-50">
                    <img id="foto-preview" src="<?= url('assets/images/default.svg') ?>" alt="Pratinjau"
                         class="mx-auto h-44 w-full object-contain p-4">
                </div>
                <input type="file" id="foto" name="foto_produk" accept="image/jpeg,image/png,image/webp"
                       class="block w-full cursor-pointer rounded-lg border border-slate-200 bg-white text-xs text-slate-500 file:mr-3 file:cursor-pointer file:border-0 file:bg-brand-600 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700">
                <p class="hint">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <a href="index.php?page=produk" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary" data-submit-guard><?= ui_icon('save', 'w-4 h-4') ?> Simpan Produk</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('foto')?.addEventListener('change', function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        const preview = document.getElementById('foto-preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('object-contain', 'p-4');
        preview.classList.add('object-cover');
    });
</script>
