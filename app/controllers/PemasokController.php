<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PemasokModel.php';

class PemasokController extends BaseController {
    private PemasokModel $pemasokModel;

    public function __construct() {
        $this->pemasokModel = new PemasokModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->render('pemasok/index', [
            'daftar_pemasok' => $this->pemasokModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pemasok');
        }

        $nama = trim($_POST['nama_pemasok'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama pemasok wajib diisi.');
            redirect('index.php?page=pemasok');
        }

        $this->pemasokModel->create([
            'nama_pemasok'  => $nama,
            'nomor_telepon' => $_POST['nomor_telepon'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'alamat'        => $_POST['alamat'] ?? ''
        ]);

        set_flash('success', "Pemasok '{$nama}' berhasil ditambahkan.");
        redirect('index.php?page=pemasok');
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_POST['id_pemasok'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pemasok');
        }

        $nama = trim($_POST['nama_pemasok'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama pemasok wajib diisi.');
            redirect('index.php?page=pemasok');
        }

        $this->pemasokModel->update($id, [
            'nama_pemasok'  => $nama,
            'nomor_telepon' => $_POST['nomor_telepon'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'alamat'        => $_POST['alamat'] ?? ''
        ]);

        set_flash('success', "Pemasok '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=pemasok');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->pemasokModel->delete($id);
                set_flash('success', 'Pemasok berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Pemasok tidak dapat dihapus karena tercatat dalam transaksi barang masuk.');
            }
        }
        redirect('index.php?page=pemasok');
    }
}
