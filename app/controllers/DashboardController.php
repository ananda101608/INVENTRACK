<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProdukModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/StokModel.php';
require_once __DIR__ . '/../models/BarangMasukModel.php';
require_once __DIR__ . '/../models/BarangKeluarModel.php';

class DashboardController extends BaseController {
    private ProdukModel $produkModel;
    private KategoriModel $kategoriModel;
    private GudangModel $gudangModel;
    private StokModel $stokModel;
    private BarangMasukModel $masukModel;
    private BarangKeluarModel $keluarModel;

    public function __construct() {
        $this->produkModel   = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->gudangModel   = new GudangModel();
        $this->stokModel     = new StokModel();
        $this->masukModel    = new BarangMasukModel();
        $this->keluarModel   = new BarangKeluarModel();
    }

    public function index(): void {
        $this->checkAuth();

        $totalProduk   = count($this->produkModel->getAll());
        $totalKategori = count($this->kategoriModel->getAll());
        $totalGudang   = count($this->gudangModel->getAll());
        $totalStok     = $this->stokModel->getTotalStok();

        $produkMenipis = $this->produkModel->getStokMenipis();
        $produkHabis   = $this->produkModel->getStokHabis();

        $masukTerbaru  = array_slice($this->masukModel->getAll(), 0, 5);
        $keluarTerbaru = array_slice($this->keluarModel->getAll(), 0, 5);

        $db = db();
        $grafikMasuk = $db->query("
            SELECT DATE_FORMAT(tanggal_masuk, '%d %b') AS label, COUNT(*) AS total
            FROM barang_masuk
            WHERE tanggal_masuk >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(tanggal_masuk)
            ORDER BY tanggal_masuk ASC
        ")->fetchAll();

        $grafikKeluar = $db->query("
            SELECT DATE_FORMAT(tanggal_keluar, '%d %b') AS label, COUNT(*) AS total
            FROM barang_keluar
            WHERE tanggal_keluar >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(tanggal_keluar)
            ORDER BY tanggal_keluar ASC
        ")->fetchAll();

        $this->render('dashboard/index', [
            'total_produk'   => $totalProduk,
            'total_kategori' => $totalKategori,
            'total_gudang'   => $totalGudang,
            'total_stok'     => $totalStok,
            'total_menipis'  => count($produkMenipis),
            'total_habis'    => count($produkHabis),
            'daftar_menipis' => $produkMenipis,
            'daftar_habis'   => $produkHabis,
            'masuk_terbaru'  => $masukTerbaru,
            'keluar_terbaru' => $keluarTerbaru,
            'grafik_masuk'   => $grafikMasuk,
            'grafik_keluar'  => $grafikKeluar
        ]);
    }
}
