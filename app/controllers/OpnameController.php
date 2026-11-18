<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/OpnameModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/ProdukModel.php';

class OpnameController extends BaseController {
    private OpnameModel $opnameModel;
    private GudangModel $gudangModel;
    private ProdukModel $produkModel;

    public function __construct() {
        $this->opnameModel = new OpnameModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->render('opname/index', [
            'daftar_opname' => $this->opnameModel->getAll()
        ]);
    }

    public function tambah(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $idGudang = (int) ($_GET['id_gudang'] ?? 0);
        $produkDiGudang = [];

        if ($idGudang > 0) {
            $produkDiGudang = $this->gudangModel->getStokByGudang($idGudang);
        }

        $this->render('opname/tambah', [
            'daftar_gudang'  => $this->gudangModel->getAll(),
            'id_gudang'      => $idGudang,
            'daftar_produk'  => $produkDiGudang
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=opname&action=tambah');
        }

        $idGudang = (int) ($_POST['id_gudang'] ?? 0);
        $tanggal  = $_POST['tanggal_opname'] ?? date('Y-m-d H:i:s');
        $catatan  = $_POST['catatan'] ?? '';

        if (!$idGudang) {
            set_flash('warning', 'Pilih gudang yang akan diperiksa stok fisiknya.');
            redirect('index.php?page=opname&action=tambah');
        }

        $items = [];
        $produkIds = $_POST['id_produk'] ?? [];
        $stokFisik = $_POST['stok_fisik'] ?? [];

        if (!is_array($produkIds)) {
            $produkIds = [$produkIds];
            $stokFisik = [$stokFisik];
        }

        foreach ($produkIds as $idx => $idPrd) {
            $idPrd = (int) $idPrd;
            $fisik = isset($stokFisik[$idx]) ? (int) $stokFisik[$idx] : 0;

            if ($idPrd > 0 && $fisik >= 0) {
                $items[] = [
                    'id_produk'  => $idPrd,
                    'stok_fisik' => $fisik
                ];
            }
        }

        if (empty($items)) {
            set_flash('warning', 'Tidak ada data produk yang diperiksa.');
            redirect("index.php?page=opname&action=tambah&id_gudang={$idGudang}");
        }

        $header = [
            'id_gudang'      => $idGudang,
            'id_pengguna'    => (int) $_SESSION['pengguna']['id_pengguna'],
            'tanggal_opname' => $tanggal,
            'catatan'        => $catatan
        ];

        try {
            $idOpname = $this->opnameModel->createTransaksi($header, $items);
            set_flash('success', "Stok opname #{$idOpname} berhasil disimpan. Saldo sistem telah disinkronkan dengan fisik.");
            redirect('index.php?page=opname');
        } catch (Throwable $e) {
            set_flash('error', "Gagal memproses stok opname: " . $e->getMessage());
            redirect("index.php?page=opname&action=tambah&id_gudang={$idGudang}");
        }
    }

    public function detail(): void {
        $this->checkAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->opnameModel->getById($id);

        if (!$data) {
            set_flash('error', 'Data stok opname tidak ditemukan.');
            redirect('index.php?page=opname');
        }

        $this->render('opname/detail', [
            'transaksi' => $data
        ]);
    }
}
