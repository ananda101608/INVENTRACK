<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/LaporanModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';

class LaporanController extends BaseController {
    private LaporanModel $laporanModel;
    private GudangModel $gudangModel;
    private KategoriModel $kategoriModel;

    public function __construct() {
        $this->laporanModel  = new LaporanModel();
        $this->gudangModel   = new GudangModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'manager']);

        $tipe     = $_GET['tipe'] ?? 'produk';
        $tglAwal  = $_GET['tgl_awal'] ?? date('Y-m-01');
        $tglAkhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
        $gudangId = !empty($_GET['gudang']) ? (int) $_GET['gudang'] : null;
        $katId    = !empty($_GET['kategori']) ? (int) $_GET['kategori'] : null;

        $hasilLaporan = [];

        switch ($tipe) {
            case 'stok_gudang':
                $hasilLaporan = $this->laporanModel->laporanStokGudang($gudangId);
                break;
            case 'barang_masuk':
                $hasilLaporan = $this->laporanModel->laporanBarangMasuk($tglAwal, $tglAkhir, $gudangId);
                break;
            case 'barang_keluar':
                $hasilLaporan = $this->laporanModel->laporanBarangKeluar($tglAwal, $tglAkhir, $gudangId);
                break;
            case 'produk':
            default:
                $tipe = 'produk';
                $hasilLaporan = $this->laporanModel->laporanProduk($katId);
                break;
        }

        $this->render('laporan/index', [
            'tipe'          => $tipe,
            'hasil_laporan' => $hasilLaporan,
            'daftar_gudang' => $this->gudangModel->getAll(),
            'daftar_kategori' => $this->kategoriModel->getAll(),
            'tgl_awal'      => $tglAwal,
            'tgl_akhir'     => $tglAkhir,
            'gudang_id'     => $gudangId,
            'kategori_id'   => $katId
        ]);
    }
}
