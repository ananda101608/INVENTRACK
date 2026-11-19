<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GudangModel.php';

class GudangController extends BaseController {
    private GudangModel $gudangModel;

    public function __construct() {
        $this->gudangModel = new GudangModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->render('gudang/index', [
            'daftar_gudang' => $this->gudangModel->getAll()
        ]);
    }

    public function detail(): void {
        $this->checkAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $gudang = $this->gudangModel->getById($id);

        if (!$gudang) {
            set_flash('error', 'Gudang tidak ditemukan.');
            redirect('index.php?page=gudang');
        }

        $stokList = $this->gudangModel->getStokByGudang($id);
        $this->render('gudang/detail', [
            'gudang'     => $gudang,
            'daftar_stok' => $stokList
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=gudang');
        }

        $nama = trim($_POST['nama_gudang'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama gudang wajib diisi.');
            redirect('index.php?page=gudang');
        }

        $this->gudangModel->create([
            'nama_gudang'      => $nama,
            'alamat'           => $_POST['alamat'] ?? '',
            'penanggung_jawab' => $_POST['penanggung_jawab'] ?? ''
        ]);

        set_flash('success', "Gudang '{$nama}' berhasil ditambahkan.");
        redirect('index.php?page=gudang');
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $id = (int) ($_POST['id_gudang'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=gudang');
        }

        $nama = trim($_POST['nama_gudang'] ?? '');
        if (empty($nama)) {
            set_flash('warning', 'Nama gudang wajib diisi.');
            redirect('index.php?page=gudang');
        }

        $this->gudangModel->update($id, [
            'nama_gudang'      => $nama,
            'alamat'           => $_POST['alamat'] ?? '',
            'penanggung_jawab' => $_POST['penanggung_jawab'] ?? ''
        ]);

        set_flash('success', "Gudang '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=gudang');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan hapus tidak valid.');
            redirect('index.php?page=gudang');
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->gudangModel->delete($id);
                set_flash('success', 'Gudang berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Gudang tidak dapat dihapus karena masih menyimpan stok atau terkait transaksi.');
            }
        }
        redirect('index.php?page=gudang');
    }
}
