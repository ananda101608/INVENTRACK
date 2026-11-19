<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PenggunaModel.php';

class AuthController extends BaseController {
    private PenggunaModel $penggunaModel;

    public function __construct() {
        $this->penggunaModel = new PenggunaModel();
    }

    public function index(): void {
        require_guest();
        $this->render('auth/login', [
            'page_title' => 'Login - ' . APP_NAME
        ]);
    }

    public function login(): void {
        require_guest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=login');
        }

        if (!verify_csrf()) {
            set_flash('error', 'Token keamanan CSRF tidak valid. Silakan coba lagi.');
            redirect('index.php?page=login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['kata_sandi'] ?? $_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            set_flash('warning', 'Email dan kata sandi wajib diisi.');
            redirect('index.php?page=login');
        }

        if (login_terkunci()) {
            set_flash('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam 5 menit.');
            redirect('index.php?page=login');
        }

        $user = $this->penggunaModel->getByEmail($email);
        if (!$user || !cek_password($password, $user['kata_sandi'])) {
            catat_login_gagal();
            set_flash('error', 'Email atau kata sandi salah.');
            redirect('index.php?page=login');
        }

        if ($user['status'] !== 'aktif') {
            set_flash('error', 'Akun dinonaktifkan. Hubungi admin.');
            redirect('index.php?page=login');
        }

        reset_login_gagal();
        session_regenerate_id(true);

        $_SESSION['pengguna'] = [
            'id_pengguna' => $user['id_pengguna'],
            'nama'        => $user['nama'],
            'email'       => $user['email'],
            'peran'       => $user['peran'],
            'status'      => $user['status']
        ];

        set_flash('success', "Selamat datang, {$user['nama']}!");
        redirect('index.php?page=dashboard');
    }

    public function logout(): void {
        unset($_SESSION['pengguna']);
        set_flash('info', 'Berhasil logout.');
        redirect('index.php?page=login');
    }
}
