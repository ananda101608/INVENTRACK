    </main>

    <!-- Footer Sederhana -->
    <footer class="bg-white border-t border-slate-200 py-3 px-6 text-center text-xs text-slate-400">
        &copy; <?= date('Y') ?> <strong class="text-slate-600"><?= APP_NAME ?></strong>. <?= APP_TAGLINE ?> (SMK Project).
    </footer>

</div>
</div>

<!-- Main Script -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>

<!-- SweetAlert2 Flash Notification Handler -->
<?php 
$flash = get_flash(); 
if ($flash): 
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };
        const titles = {
            'success': 'Berhasil!',
            'error': 'Terjadi Kesalahan!',
            'warning': 'Peringatan!',
            'info': 'Informasi'
        };
        const tipe = '<?= $flash['tipe'] ?>';
        
        Swal.fire({
            icon: icons[tipe] || 'info',
            title: titles[tipe] || 'Notifikasi',
            text: '<?= addslashes($flash['pesan']) ?>',
            timer: 3500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
    });
</script>
<?php endif; ?>

</body>
</html>
