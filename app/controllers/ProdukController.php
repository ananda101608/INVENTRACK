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
        }

        $data = [
            'id_kategori'  => (int) $_POST['id_kategori'],
            'id_satuan'    => (int) $_POST['id_satuan'],
            'kode_produk'  => $kode,
            'nama_produk'  => $nama,
            'deskripsi'    => $_POST['deskripsi'] ?? null,
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
            'deskripsi'    => $_POST['deskripsi'] ?? null,
            'harga_beli'   => (float) ($_POST['harga_beli'] ?? 0),
            'harga_jual'   => (float) ($_POST['harga_jual'] ?? 0),
            'stok_minimum' => (int) ($_POST['stok_minimum'] ?? 5),
            'status'       => $_POST['status'] ?? 'aktif'
        ];

        if (!empty($_FILES['foto_produk']['name'])) {
            $data['foto_produk'] = $this->handleUploadFoto($_FILES['foto_produk']);
        }

        $this->produkModel->update($id, $data);
        set_flash('success', "Data produk '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=produk');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
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
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed, true)) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetDir = __DIR__ . '/../../public/uploads/produk/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetDir . $newName)) {
            return $newName;
        }
        return null;
    }
}
