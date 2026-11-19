<?php

abstract class BaseController {

    protected function render(string $viewPath, array $data = []): void {
        kirim_header_keamanan();

        extract($data);
        $fullPath = __DIR__ . "/../../views/{$viewPath}.php";

        if ($viewPath === 'auth/login') {
            if (file_exists($fullPath) && filesize($fullPath) > 0) {
                require $fullPath;
            } else {
                echo "Halaman Login (views/auth/login.php)";
            }
            return;
        }

        if (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_GET['format']) && $_GET['format'] === 'json')
        ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'view' => $viewPath, 'data' => $data]);
            exit;
        }

        $headerFile  = __DIR__ . '/../../views/layouts/header.php';
        $sidebarFile = __DIR__ . '/../../views/layouts/sidebar.php';
        $navbarFile  = __DIR__ . '/../../views/layouts/navbar.php';
        $footerFile  = __DIR__ . '/../../views/layouts/footer.php';

        if (file_exists($headerFile) && filesize($headerFile) > 0) {
            require $headerFile;
            require $sidebarFile;
            require $navbarFile;
        }

        if (file_exists($fullPath) && filesize($fullPath) > 0) {
            require $fullPath;
        } else {
            echo "<div>Data siap ditampilkan pada view: " . htmlspecialchars($viewPath) . "</div>";
        }

        if (file_exists($footerFile) && filesize($footerFile) > 0) {
            require $footerFile;
        }
    }

    protected function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function checkAuth(): void {
        require_login();
    }

    protected function checkRole(string|array $roles): void {
        require_role($roles);
    }
}
