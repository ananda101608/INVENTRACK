<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PenggunaModel.php';

class PenggunaController extends BaseController {
    private PenggunaModel $penggunaModel;

    public function __construct() {
        $this->penggunaModel = new PenggunaModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $this->render('pengguna/index', [
            'daftar_pengguna' => $this->penggunaModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pengguna');
        }

        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['kata_sandi'] ?? '');
        $peran    = $_POST['peran'] ?? 'petugas';

        if (empty($nama) || empty($email) || empty($password)) {
            set_flash('warning', 'Nama, email, dan kata sandi wajib diisi.');
            redirect('index.php?page=pengguna');
        }

        // Cek email ganda
        if ($this->penggunaModel->getByEmail($email)) {
            set_flash('error', "Email '{$email}' sudah terdaftar dalam sistem.");
            redirect('index.php?page=pengguna');
        }

        $this->penggunaModel->create([
            'nama'       => $nama,
            'email'      => $email,
            'kata_sandi' => enkripsi_password($password),
            'peran'      => $peran,
            'status'     => $_POST['status'] ?? 'aktif'
        ]);

        set_flash('success', "Akun pengguna '{$nama}' berhasil dibuat.");
        redirect('index.php?page=pengguna');
    }

    public function perbarui(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_POST['id_pengguna'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pengguna');
        }

        $nama  = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $peran = $_POST['peran'] ?? 'petugas';

        $existing = $this->penggunaModel->getByEmail($email);
        if ($existing && (int) $existing['id_pengguna'] !== $id) {
            set_flash('error', "Email '{$email}' sudah digunakan oleh pengguna lain.");
            redirect('index.php?page=pengguna');
        }

        $this->penggunaModel->update($id, [
            'nama'   => $nama,
            'email'  => $email,
            'peran'  => $peran,
            'status' => $_POST['status'] ?? 'aktif'
        ]);

        // Ganti password jika diisi
        if (!empty($_POST['kata_sandi'])) {
            $this->penggunaModel->updatePassword($id, enkripsi_password($_POST['kata_sandi']));
        }

        set_flash('success', "Data pengguna '{$nama}' berhasil diperbarui.");
        redirect('index.php?page=pengguna');
    }

    public function toggle_status(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id === (int) $_SESSION['pengguna']['id_pengguna']) {
            set_flash('warning', 'Anda tidak dapat menonaktifkan akun sendiri.');
            redirect('index.php?page=pengguna');
        }

        if ($id) {
            $this->penggunaModel->toggleStatus($id);
            set_flash('success', 'Status pengguna berhasil diperbarui.');
        }
        redirect('index.php?page=pengguna');
    }

    public function hapus(): void {
        $this->checkAuth();
        $this->checkRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id === (int) $_SESSION['pengguna']['id_pengguna']) {
            set_flash('warning', 'Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('index.php?page=pengguna');
        }

        if ($id) {
            try {
                $this->penggunaModel->delete($id);
                set_flash('success', 'Akun pengguna berhasil dihapus.');
            } catch (PDOException $e) {
                set_flash('error', 'Pengguna tidak dapat dihapus karena telah memiliki catatan riwayat transaksi.');
            }
        }
        redirect('index.php?page=pengguna');
    }
}
