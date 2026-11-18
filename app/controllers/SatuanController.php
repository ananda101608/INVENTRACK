<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SatuanModel.php';

class SatuanController extends BaseController {
    private SatuanModel $satuanModel;

    public function __construct() {
        $this->satuanModel = new SatuanModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->render('satuan/index', [
            'daftar_satuan' => $this->satuanModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=satuan');
        }

        $nama      = trim($_POST['nama_satuan'] ?? '');
        $singkatan = trim($_POST['singkatan'] ?? '');

        if (empty($nama) || empty($singkatan)) {
            set_flash('warning', 'Nama satuan dan singkatan wajib diisi.');
            redirect('index.php?page=satuan');
        }

        $this->satuanModel->create([
            'nama_satuan' => $nama,
            'singkatan'   => $singkatan
        ]);

        set_flash('success', "Satuan '{$nama} ({$singkatan})' berhasil ditambahkan.");
        redirect('index.php?page=satuan');
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_POST['id_satuan'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=satuan');
        }

        $nama      = trim($_POST['nama_satuan'] ?? '');
        $singkatan = trim($_POST['singkatan'] ?? '');

        if (empty($nama) || empty($singkatan)) {
            set_flash('warning', 'Nama satuan dan singkatan wajib diisi.');
            redirect('index.php?page=satuan');
        }

        $this->satuanModel->update($id, [
            'nama_satuan' => $nama,
            'singkatan'   => $singkatan
        ]);

        set_flash('success', "Satuan '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=satuan');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->satuanModel->delete($id);
                set_flash('success', 'Satuan berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh produk.');
            }
        }
        redirect('index.php?page=satuan');
    }
}
