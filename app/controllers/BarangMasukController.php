<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/BarangMasukModel.php';
require_once __DIR__ . '/../models/PemasokModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/ProdukModel.php';

class BarangMasukController extends BaseController {
    private BarangMasukModel $masukModel;
    private PemasokModel $pemasokModel;
    private GudangModel $gudangModel;
    private ProdukModel $produkModel;

    public function __construct() {
        $this->masukModel   = new BarangMasukModel();
        $this->pemasokModel = new PemasokModel();
        $this->gudangModel  = new GudangModel();
        $this->produkModel  = new ProdukModel();
    }

    public function index(): void {
        $this->checkAuth();
        $tglAwal  = $_GET['tgl_awal'] ?? null;
        $tglAkhir = $_GET['tgl_akhir'] ?? null;

        $this->render('barang_masuk/index', [
            'daftar_masuk' => $this->masukModel->getAll($tglAwal, $tglAkhir),
            'tgl_awal'     => $tglAwal,
            'tgl_akhir'    => $tglAkhir
        ]);
    }

    public function tambah(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $this->render('barang_masuk/tambah', [
            'daftar_pemasok' => $this->pemasokModel->getAll(),
            'daftar_gudang'  => $this->gudangModel->getAll(),
            'daftar_produk'  => $this->produkModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=barang_masuk&action=tambah');
        }

        $idPemasok = (int) ($_POST['id_pemasok'] ?? 0);
        $idGudang  = (int) ($_POST['id_gudang'] ?? 0);
        $tanggal   = $_POST['tanggal_masuk'] ?? date('Y-m-d H:i:s');
        $catatan   = $_POST['catatan'] ?? '';

        if (!$idPemasok || !$idGudang) {
            set_flash('warning', 'Pemasok dan gudang wajib dipilih.');
            redirect('index.php?page=barang_masuk&action=tambah');
        }

        // Susun item produk yang diinput
        $items = [];
        $produkIds = $_POST['id_produk'] ?? [];
        $jumlahs   = $_POST['jumlah'] ?? [];
        $hargas    = $_POST['harga_satuan'] ?? [];

        // Support single item form (misal form sederhana) atau multiple item
        if (!is_array($produkIds)) {
            $produkIds = [$produkIds];
            $jumlahs   = [$jumlahs];
            $hargas    = [$hargas];
        }

        foreach ($produkIds as $idx => $idPrd) {
            $idPrd = (int) $idPrd;
            $qty   = (int) ($jumlahs[$idx] ?? 0);
            $hrg   = (float) ($hargas[$idx] ?? 0);

            if ($idPrd > 0 && $qty > 0) {
                $items[] = [
                    'id_produk'    => $idPrd,
                    'jumlah'       => $qty,
                    'harga_satuan' => $hrg
                ];
            }
        }

        if (empty($items)) {
            set_flash('warning', 'Silakan pilih minimal satu produk dan jumlah yang valid.');
            redirect('index.php?page=barang_masuk&action=tambah');
        }

        $header = [
            'id_pemasok'    => $idPemasok,
            'id_pengguna'   => (int) $_SESSION['pengguna']['id_pengguna'],
            'id_gudang'     => $idGudang,
            'tanggal_masuk' => $tanggal,
            'catatan'       => $catatan
        ];

        try {
            $idMasuk = $this->masukModel->createTransaksi($header, $items);
            set_flash('success', "Transaksi barang masuk #{$idMasuk} berhasil disimpan. Stok otomatis bertambah.");
            redirect('index.php?page=barang_masuk');
        } catch (Throwable $e) {
            set_flash('error', "Gagal menyimpan barang masuk: " . $e->getMessage());
            redirect('index.php?page=barang_masuk&action=tambah');
        }
    }

    public function detail(): void {
        $this->checkAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->masukModel->getById($id);

        if (!$data) {
            set_flash('error', 'Data barang masuk tidak ditemukan.');
            redirect('index.php?page=barang_masuk');
        }

        $this->render('barang_masuk/detail', [
            'transaksi' => $data
        ]);
    }
}
