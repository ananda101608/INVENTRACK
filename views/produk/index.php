<?php
// ====================================================================
// VIEW: PRODUK — DAFTAR
// ====================================================================
$rows      = $daftar_produk ?? [];
$role      = user_role();
$canManage = in_array($role, ['admin', 'petugas'], true);
$canDelete = $role === 'admin';

$fotoUrl = static function (string $file): string {
    if ($file !== '' && is_file(__DIR__ . '/../../public/uploads/produk/' . $file)) {
        return ROOT_URL . '/uploads/produk/' . rawurlencode($file);
    }
    return url('assets/images/default.svg');
};
?>
<div class="space-y-5">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Produk</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola data barang, harga, dan batas stok minimum.</p>
        </div>
        <?php if ($canManage): ?>
            <a href="index.php?page=produk&action=tambah" class="btn btn-primary">
                <?= ui_icon('plus', 'w-4 h-4') ?> Tambah Produk
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="get" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                <input type="hidden" name="page" value="produk">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= ui_icon('search', 'w-4 h-4') ?></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                           class="input pl-9" placeholder="Cari kode atau nama produk...">
                </div>
                <select name="kategori" class="input sm:w-56">
                    <option value="">Semua kategori</option>
                    <?php foreach ($daftar_kategori ?? [] as $kat): ?>
                        <option value="<?= (int) $kat['id_kategori'] ?>" <?= (int) ($kategori_id ?? 0) === (int) $kat['id_kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><?= ui_icon('filter', 'w-4 h-4') ?> Filter</button>
                <?php if (!empty($search) || !empty($kategori_id)): ?>
                    <a href="index.php?page=produk" class="btn btn-ghost">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table-modern min-w-[900px]">
                <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $stok = (int) $row['total_stok'];
                    $min  = (int) $row['stok_minimum'];
                    [$stokBadge, $stokLabel] = $stok === 0
                        ? ['badge-danger', 'Habis']
                        : ($stok <= $min ? ['badge-warning', 'Menipis'] : ['badge-success', 'Aman']);
                    ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="<?= htmlspecialchars($fotoUrl((string) $row['foto_produk'])) ?>"
                                     alt="" class="h-10 w-10 rounded-lg border border-slate-200 bg-slate-50 object-cover">
                                <div class="min-w-0">
                                    <p class="max-w-[18rem] truncate font-medium text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($row['kode_produk']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-neutral"><?= htmlspecialchars($row['nama_kategori'] ?? '—') ?></span></td>
                        <td class="whitespace-nowrap text-right text-slate-600"><?= rupiah($row['harga_beli']) ?></td>
                        <td class="whitespace-nowrap text-right text-slate-600"><?= rupiah($row['harga_jual']) ?></td>
                        <td class="text-center">
                            <p class="font-semibold text-slate-800"><?= $stok ?> <?= htmlspecialchars($row['nama_satuan'] ?? '') ?></p>
                            <span class="badge <?= $stokBadge ?> mt-1"><?= $stokLabel ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $row['status'] === 'aktif' ? 'badge-success' : 'badge-neutral' ?>">
                                <?= $row['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($canManage): ?>
                                    <a href="index.php?page=produk&action=edit&id=<?= (int) $row['id_produk'] ?>"
                                       class="btn btn-ghost btn-sm" title="Edit"><?= ui_icon('edit', 'w-3.5 h-3.5') ?></a>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="index.php?page=produk&action=hapus&id=<?= (int) $row['id_produk'] ?>"
                                       class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50" title="Hapus"
                                       data-confirm="Hapus produk '<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES) ?>'?">
                                        <?= ui_icon('trash', 'w-3.5 h-3.5') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <span class="text-slate-300"><?= ui_icon('box', 'w-10 h-10') ?></span>
                                <p class="text-sm font-medium text-slate-600">Tidak ada produk ditemukan</p>
                                <p class="text-xs text-slate-400">
                                    <?= (!empty($search) || !empty($kategori_id)) ? 'Coba ubah kata kunci atau filter kategori.' : 'Tambahkan produk pertama untuk memulai.' ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
