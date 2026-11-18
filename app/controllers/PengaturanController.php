<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PenggunaModel.php';

class PengaturanController extends BaseController {
    private PenggunaModel $penggunaModel;

    public function __construct() {
        $this->penggunaModel = new PenggunaModel();
    }

    public function index(): void {
        $this->checkAuth();
        $user = $this->penggunaModel->getById((int) $_SESSION['pengguna']['id_pengguna']);

        $this->render('pengaturan/index', [
            'user' => $user
        ]);
    }

    public function perbarui_profil(): void {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pengaturan');
        }

        $id   = (int) $_SESSION['pengguna']['id_pengguna'];
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($nama) || empty($email)) {
            set_flash('warning', 'Nama dan email wajib diisi.');
            redirect('index.php?page=pengaturan');
        }

        $existing = $this->penggunaModel->getByEmail($email);
        if ($existing && (int) $existing['id_pengguna'] !== $id) {
            set_flash('error', "Email '{$email}' sudah digunakan pengguna lain.");
            redirect('index.php?page=pengaturan');
        }

        $user = $this->penggunaModel->getById($id);
        $this->penggunaModel->update($id, [
            'nama'   => $nama,
            'email'  => $email,
            'peran'  => $user['peran'],
            'status' => $user['status']
        ]);

        $_SESSION['pengguna']['nama']  = $nama;
        $_SESSION['pengguna']['email'] = $email;

        set_flash('success', 'Profil Anda berhasil diperbarui.');
        redirect('index.php?page=pengaturan');
    }

    public function ganti_password(): void {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=pengaturan');
        }

        $id        = (int) $_SESSION['pengguna']['id_pengguna'];
        $passLama  = (string) ($_POST['password_lama'] ?? '');
        $passBaru  = (string) ($_POST['password_baru'] ?? '');
        $passKonf  = (string) ($_POST['konfirmasi_password'] ?? '');

        if (empty($passLama) || empty($passBaru)) {
            set_flash('warning', 'Kata sandi lama dan baru wajib diisi.');
            redirect('index.php?page=pengaturan');
        }

        if ($passBaru !== $passKonf) {
            set_flash('error', 'Konfirmasi kata sandi baru tidak cocok.');
            redirect('index.php?page=pengaturan');
        }

        $user = $this->penggunaModel->getByEmail($_SESSION['pengguna']['email']);
        if (!cek_password($passLama, $user['kata_sandi'])) {
            set_flash('error', 'Kata sandi lama Anda salah.');
            redirect('index.php?page=pengaturan');
        }

        $this->penggunaModel->updatePassword($id, enkripsi_password($passBaru));
        set_flash('success', 'Kata sandi Anda berhasil diperbarui.');
        redirect('index.php?page=pengaturan');
    }
}
