<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KategoriModel.php';

class KategoriController extends BaseController {
    private KategoriModel $kategoriModel;

    public function __construct() {
        $this->kategoriModel = new KategoriModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->render('kategori/index', [
            'daftar_kategori' => $this->kategoriModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=kategori');
        }

        $nama = trim($_POST['nama_kategori'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama kategori wajib diisi.');
            redirect('index.php?page=kategori');
        }

        $this->kategoriModel->create([
            'nama_kategori' => $nama,
            'deskripsi'     => $_POST['deskripsi'] ?? null
        ]);

        set_flash('success', "Kategori '{$nama}' berhasil ditambahkan.");
        redirect('index.php?page=kategori');
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_POST['id_kategori'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=kategori');
        }

        $nama = trim($_POST['nama_kategori'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama kategori wajib diisi.');
            redirect('index.php?page=kategori');
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $nama,
            'deskripsi'     => $_POST['deskripsi'] ?? null
        ]);

        set_flash('success', "Kategori '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=kategori');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->kategoriModel->delete($id);
                set_flash('success', 'Kategori berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
            }
        }
        redirect('index.php?page=kategori');
    }
}
