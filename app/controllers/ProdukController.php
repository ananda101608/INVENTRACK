<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProdukModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';
require_once __DIR__ . '/../models/SatuanModel.php';

class ProdukController extends BaseController {
    private ProdukModel $produkModel;
    private KategoriModel $kategoriModel;
    private SatuanModel $satuanModel;

    public function __construct() {
        $this->produkModel   = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->satuanModel   = new SatuanModel();
    }

    public function index(): void {
        $this->checkAuth();

        $keyword    = $_GET['search'] ?? null;
        $kategoriId = !empty($_GET['kategori']) ? (int) $_GET['kategori'] : null;

        $daftarProduk = $this->produkModel->getAll($keyword, $kategoriId);
        $kategoriList = $this->kategoriModel->getAll();

        $this->render('produk/index', [
            'daftar_produk'   => $daftarProduk,
            'daftar_kategori' => $kategoriList,
            'search'          => $keyword,
            'kategori_id'     => $kategoriId
        ]);
    }

    public function tambah(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $this->render('produk/tambah', [
            'kategori_list' => $this->kategoriModel->getAll(),
            'satuan_list'   => $this->satuanModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=produk');
        }

        if (!verify_csrf()) {
            set_flash('error', 'Token keamanan CSRF tidak valid.');
            redirect('index.php?page=produk&action=tambah');
        }

        $kode = trim($_POST['kode_produk'] ?? '');
        $nama = trim($_POST['nama_produk'] ?? '');

        if (empty($kode) || empty($nama)) {
            set_flash('warning', 'Kode dan nama produk wajib diisi.');
            redirect('index.php?page=produk&action=tambah');
        }

        if ($this->produkModel->getByKode($kode)) {
            set_flash('error', "Kode produk '{$kode}' sudah digunakan.");
            redirect('index.php?page=produk&action=tambah');
        }

        $fotoName = null;
        if (!empty($_FILES['foto_produk']['name'])) {
            $fotoName = $this->handleUploadFoto($_FILES['foto_produk']);
            if ($fotoName === null) {
                set_flash('warning', 'Foto produk tidak valid. Gunakan JPG/PNG/WEBP maksimal 2 MB.');
            }
        }

        $data = [
            'id_kategori'  => (int) $_POST['id_kategori'],
            'id_satuan'    => (int) $_POST['id_satuan'],
            'kode_produk'  => $kode,
            'nama_produk'  => $nama,
            'deskripsi'    => trim($_POST['deskripsi'] ?? ''),
            'harga_beli'   => (float) ($_POST['harga_beli'] ?? 0),
            'harga_jual'   => (float) ($_POST['harga_jual'] ?? 0),
            'stok_minimum' => (int) ($_POST['stok_minimum'] ?? 5),
            'foto_produk'  => $fotoName,
            'status'       => $_POST['status'] ?? 'aktif'
        ];

        $this->produkModel->create($data);
        set_flash('success', "Produk '{$nama}' berhasil ditambahkan.");
        redirect('index.php?page=produk');
    }

    public function edit(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_GET['id'] ?? 0);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            set_flash('error', 'Produk tidak ditemukan.');
            redirect('index.php?page=produk');
        }

        $this->render('produk/edit', [
            'produk'        => $produk,
            'kategori_list' => $this->kategoriModel->getAll(),
            'satuan_list'   => $this->satuanModel->getAll()
        ]);
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_POST['id_produk'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('index.php?page=produk');
        }

        if (!verify_csrf()) {
            set_flash('error', 'Token keamanan CSRF tidak valid.');
            redirect("index.php?page=produk&action=edit&id={$id}");
        }

        $kode = trim($_POST['kode_produk'] ?? '');
        $nama = trim($_POST['nama_produk'] ?? '');

        $existing = $this->produkModel->getByKode($kode);
        if ($existing && (int) $existing['id_produk'] !== $id) {
            set_flash('error', "Kode produk '{$kode}' sudah digunakan produk lain.");
            redirect("index.php?page=produk&action=edit&id={$id}");
        }

        $data = [
            'id_kategori'  => (int) $_POST['id_kategori'],
            'id_satuan'    => (int) $_POST['id_satuan'],
            'kode_produk'  => $kode,
            'nama_produk'  => $nama,
            'deskripsi'    => trim($_POST['deskripsi'] ?? ''),
            'harga_beli'   => (float) ($_POST['harga_beli'] ?? 0),
            'harga_jual'   => (float) ($_POST['harga_jual'] ?? 0),
            'stok_minimum' => (int) ($_POST['stok_minimum'] ?? 5),
            'status'       => $_POST['status'] ?? 'aktif'
        ];

        if (!empty($_FILES['foto_produk']['name'])) {
            $fotoBaru = $this->handleUploadFoto($_FILES['foto_produk']);
            if ($fotoBaru === null) {
                set_flash('warning', 'Foto produk tidak valid. Gunakan JPG/PNG/WEBP maksimal 2 MB.');
            } else {
                $data['foto_produk'] = $fotoBaru;
            }
        }

        $this->produkModel->update($id, $data);
        set_flash('success', "Data produk '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=produk');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan hapus tidak valid.');
            redirect('index.php?page=produk');
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->produkModel->delete($id);
                set_flash('success', 'Produk berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Produk tidak dapat dihapus karena tercatat dalam transaksi/stok.');
            }
        }
        redirect('index.php?page=produk');
    }

    private function handleUploadFoto(array $file): ?string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $maxSize = 2 * 1024 * 1024;
        if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxSize) {
            return null;
        }

        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!isset($mimeMap[$mime])) {
            return null;
        }

        if (@getimagesize($file['tmp_name']) === false) {
            return null;
        }

        $ext       = $mimeMap[$mime];
        $newName   = 'prod_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetDir = __DIR__ . '/../../public/uploads/produk/';

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return null;
        }

        return move_uploaded_file($file['tmp_name'], $targetDir . $newName) ? $newName : null;
    }
}
