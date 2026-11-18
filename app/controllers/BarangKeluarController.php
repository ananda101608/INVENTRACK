<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/BarangKeluarModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/ProdukModel.php';
require_once __DIR__ . '/../models/StokModel.php';

class BarangKeluarController extends BaseController {
    private BarangKeluarModel $keluarModel;
    private GudangModel $gudangModel;
    private ProdukModel $produkModel;
    private StokModel $stokModel;

    public function __construct() {
        $this->keluarModel = new BarangKeluarModel();
        $this->gudangModel  = new GudangModel();
        $this->produkModel  = new ProdukModel();
        $this->stokModel    = new StokModel();
    }

    public function index(): void {
        $this->checkAuth();
        $tglAwal  = $_GET['tgl_awal'] ?? null;
        $tglAkhir = $_GET['tgl_akhir'] ?? null;

        $this->render('barang_keluar/index', [
            'daftar_keluar' => $this->keluarModel->getAll($tglAwal, $tglAkhir),
            'tgl_awal'      => $tglAwal,
            'tgl_akhir'     => $tglAkhir
        ]);
    }

    public function tambah(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $this->render('barang_keluar/tambah', [
            'daftar_gudang' => $this->gudangModel->getAll(),
            'daftar_produk' => $this->produkModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=barang_keluar&action=tambah');
        }

        $idGudang  = (int) ($_POST['id_gudang'] ?? 0);
        $penerima  = trim($_POST['nama_penerima'] ?? '');
        $tujuan    = trim($_POST['tujuan'] ?? '');
        $tanggal   = $_POST['tanggal_keluar'] ?? date('Y-m-d H:i:s');
        $catatan   = $_POST['catatan'] ?? '';

        if (!$idGudang || empty($penerima) || empty($tujuan)) {
            set_flash('warning', 'Gudang, nama penerima, dan tujuan pengeluaran wajib diisi.');
            redirect('index.php?page=barang_keluar&action=tambah');
        }

        // Susun item produk yang dikeluarkan
        $items = [];
        $produkIds = $_POST['id_produk'] ?? [];
        $jumlahs   = $_POST['jumlah'] ?? [];

        if (!is_array($produkIds)) {
            $produkIds = [$produkIds];
            $jumlahs   = [$jumlahs];
        }

        foreach ($produkIds as $idx => $idPrd) {
            $idPrd = (int) $idPrd;
            $qty   = (int) ($jumlahs[$idx] ?? 0);

            if ($idPrd > 0 && $qty > 0) {
                $items[] = [
                    'id_produk' => $idPrd,
                    'jumlah'    => $qty
                ];
            }
        }

        if (empty($items)) {
            set_flash('warning', 'Silakan pilih minimal satu produk dan jumlah yang valid.');
            redirect('index.php?page=barang_keluar&action=tambah');
        }

        $header = [
            'id_pengguna'    => (int) $_SESSION['pengguna']['id_pengguna'],
            'id_gudang'      => $idGudang,
            'tanggal_keluar' => $tanggal,
            'nama_penerima'  => $penerima,
            'tujuan'         => $tujuan,
            'catatan'        => $catatan
        ];

        try {
            $idKeluar = $this->keluarModel->createTransaksi($header, $items);
            set_flash('success', "Transaksi barang keluar #{$idKeluar} berhasil diproses. Stok otomatis berkurang.");
            redirect('index.php?page=barang_keluar');
        } catch (Throwable $e) {
            set_flash('error', "Gagal memproses barang keluar: " . $e->getMessage());
            redirect('index.php?page=barang_keluar&action=tambah');
        }
    }

    public function detail(): void {
        $this->checkAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->keluarModel->getById($id);

        if (!$data) {
            set_flash('error', 'Data barang keluar tidak ditemukan.');
            redirect('index.php?page=barang_keluar');
        }

        $this->render('barang_keluar/detail', [
            'transaksi' => $data
        ]);
    }
}
