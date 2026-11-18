<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/TransferModel.php';
require_once __DIR__ . '/../models/GudangModel.php';
require_once __DIR__ . '/../models/ProdukModel.php';

class TransferController extends BaseController {
    private TransferModel $transferModel;
    private GudangModel $gudangModel;
    private ProdukModel $produkModel;

    public function __construct() {
        $this->transferModel = new TransferModel();
        $this->gudangModel   = new GudangModel();
        $this->produkModel   = new ProdukModel();
    }

    public function index(): void {
        $this->checkAuth();
        $status = $_GET['status'] ?? null;

        $this->render('transfer/index', [
            'daftar_transfer' => $this->transferModel->getAll($status),
            'status'          => $status
        ]);
    }

    public function tambah(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        $this->render('transfer/tambah', [
            'daftar_gudang' => $this->gudangModel->getAll(),
            'daftar_produk' => $this->produkModel->getAll()
        ]);
    }

    public function simpan(): void {
        $this->checkAuth();
        $this->checkRole(['admin', 'petugas']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('index.php?page=transfer&action=tambah');
        }

        $idAsal   = (int) ($_POST['id_gudang_asal'] ?? 0);
        $idTujuan = (int) ($_POST['id_gudang_tujuan'] ?? 0);
        $tanggal  = $_POST['tanggal_transfer'] ?? date('Y-m-d H:i:s');
        $catatan  = $_POST['catatan'] ?? '';

        if (!$idAsal || !$idTujuan) {
            set_flash('warning', 'Gudang asal dan tujuan wajib dipilih.');
            redirect('index.php?page=transfer&action=tambah');
        }

        if ($idAsal === $idTujuan) {
            set_flash('error', 'Gudang asal dan gudang tujuan tidak boleh sama!');
            redirect('index.php?page=transfer&action=tambah');
        }

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
            set_flash('warning', 'Pilih minimal satu produk dan jumlah transfer yang valid.');
            redirect('index.php?page=transfer&action=tambah');
        }

        $header = [
            'id_gudang_asal'   => $idAsal,
            'id_gudang_tujuan' => $idTujuan,
            'id_pengguna'      => (int) $_SESSION['pengguna']['id_pengguna'],
            'tanggal_transfer' => $tanggal,
            'status'           => 'selesai',
            'catatan'          => $catatan
        ];

        try {
            $idTransfer = $this->transferModel->createTransaksi($header, $items);
            set_flash('success', "Transfer antar gudang #{$idTransfer} sukses. Saldo stok kedua gudang telah diperbarui.");
            redirect('index.php?page=transfer');
        } catch (Throwable $e) {
            set_flash('error', "Gagal memproses transfer gudang: " . $e->getMessage());
            redirect('index.php?page=transfer&action=tambah');
        }
    }

    public function detail(): void {
        $this->checkAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->transferModel->getById($id);

        if (!$data) {
            set_flash('error', 'Data transfer gudang tidak ditemukan.');
            redirect('index.php?page=transfer');
        }

        $this->render('transfer/detail', [
            'transaksi' => $data
        ]);
    }
}
