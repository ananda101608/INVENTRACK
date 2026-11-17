/**
 * INVENTRACK - Main Vanilla JavaScript
 * Mengatur interaksi UI, sidebar, modal, dan SweetAlert2
 */

document.addEventListener('DOMContentLoaded', function () {
    // ---------------------------------------------------------------
    // 1. PENGATURAN SIDEBAR RESPONSIVE
    // ---------------------------------------------------------------
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        if (!sidebar) return;
        const isClosed = sidebar.classList.contains('-translate-x-full');
        if (isClosed) {
            sidebar.classList.remove('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        }
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    // ---------------------------------------------------------------
    // 2. TOGGLE TAMPIL / SEMBUNYIKAN PASSWORD
    // ---------------------------------------------------------------
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (!targetInput) return;

            const isPassword = targetInput.type === 'password';
            targetInput.type = isPassword ? 'text' : 'password';

            // Ubah ikon mata jika ada
            const icon = this.querySelector('svg');
            if (icon) {
                if (isPassword) {
                    icon.classList.add('text-blue-600');
                } else {
                    icon.classList.remove('text-blue-600');
                }
            }
        });
    });

    // ---------------------------------------------------------------
    // 3. DIALOG KONFIRMASI HAPUS DENGAN SWEETALERT2
    // ---------------------------------------------------------------
    const deleteButtons = document.querySelectorAll('.btn-konfirmasi-hapus');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const item = this.getAttribute('data-item') || 'data ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Menghapus ${item} tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus ${item}?`)) {
                    window.location.href = href;
                }
            }
        });
    });
});
