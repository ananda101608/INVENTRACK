# INVENTRACK

Sistem manajemen inventaris berbasis PHP MVC tanpa framework. Mencakup master data,
transaksi stok, transfer antar gudang, stok opname, laporan, dan pengaturan akun.

- **Backend:** PHP 8+ (PDO MySQL), arsitektur MVC sederhana
- **Frontend:** Tailwind CSS v4 (di-compile lokal), vanilla JavaScript, ikon SVG inline
- **Database:** MySQL 8 / MariaDB

---

## 1. Kebutuhan

| Kebutuhan | Versi |
|---|---|
| PHP | >= 8.0 (disarankan 8.2+) dengan ekstensi `pdo_mysql`, `fileinfo`, `mbstring` |
| MySQL / MariaDB | MySQL 8 atau MariaDB 10.4+ |
| Node.js + npm | hanya untuk build ulang CSS (opsional saat runtime) |
| Web server | Apache / Nginx + PHP-FPM / `php -S` (DocumentRoot ke `public/`) |

Hasil build CSS (`public/assets/css/style.css`) sudah ikut di-commit, sehingga
aplikasi bisa langsung dijalankan tanpa Node.js.

---

## 2. Struktur Singkat

```
INVENTRACK/
├── app/
│   ├── controllers/      # Controller per modul
│   ├── models/           # Akses database (PDO)
│   └── helpers/          # format, session, csrf, auth
├── config/
│   ├── app.php           # APP_NAME, BASE_URL, ROOT_URL
│   ├── env.php           # loader .env
│   └── database.php      # koneksi PDO (membaca .env)
├── database/inventrack.sql
├── public/
│   ├── index.php         # front controller
│   ├── assets/           # css, js, images (hasil build ikut di-commit)
│   └── uploads/produk/   # foto produk
├── src/input.css         # sumber Tailwind
└── views/                # layout, partial, dan halaman
```

---

## 3. Instalasi

### 3.1 Salin konfigurasi environment

```bash
cp .env.example .env
```

Sesuaikan isi `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=inventrack
DB_USER=inventrack
DB_PASS=ubah_saya
DB_CHARSET=utf8mb4
```

> `.env` **tidak boleh di-commit**. File ini sudah masuk `.gitignore`.

### 3.2 Buat database dan user MySQL khusus

Jangan memakai akun `root` pribadi. Jalankan sebagai admin MySQL:

```sql
CREATE DATABASE IF NOT EXISTS inventrack
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER IF NOT EXISTS 'inventrack'@'localhost'
  IDENTIFIED BY 'PASSWORD_KUAT';

GRANT ALL PRIVILEGES ON inventrack.* TO 'inventrack'@'localhost';
FLUSH PRIVILEGES;
```

Gunakan password yang sama dengan `DB_PASS` di `.env`.

### 3.3 Import skema dan data

```bash
mysql -u inventrack -p inventrack < database/inventrack.sql
```

> File SQL **tidak** berisi `CREATE DATABASE`/`USE`, jadi database harus dibuat
> lebih dulu seperti langkah 3.2.

### 3.4 Permission folder upload (Linux)

Folder `public/uploads/produk/` harus bisa ditulis oleh user web server:

```bash
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 775 public/uploads
```

Jika tidak, upload foto produk akan gagal.

### 3.5 Build CSS (opsional)

Hanya diperlukan jika mengubah tampilan atau `src/input.css`:

```bash
npm install
npm run css:build     # sekali jalan
npm run css:watch     # saat pengembangan
```

### 3.6 Jalankan aplikasi

**DocumentRoot harus diarahkan ke folder `public/`**, bukan ke root project.
Dengan begitu `.env`, `database/`, `app/`, dan `config/` tidak dapat diunduh
lewat HTTP.

**Opsi A — server bawaan PHP (paling cepat):**

```bash
cd INVENTRACK
php -S localhost:8080 -t public
```

Buka `http://localhost:8080/`.

**Opsi B — Apache virtual host (Linux):**

```apache
<VirtualHost *:80>
    ServerName inventrack.local
    DocumentRoot /home/USER/INVENTRACK/public

    <Directory /home/USER/INVENTRACK/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Aktifkan dengan `sudo a2ensite`, tambahkan `inventrack.local` ke `/etc/hosts`,
lalu `sudo systemctl reload apache2`.

**Opsi C — XAMPP/Laragon (Windows):**

Buat virtual host yang menunjuk ke `C:\xampp\htdocs\INVENTRACK\public`
(atau gunakan Alias). Jangan mengakses `http://localhost/inventrack/` secara
langsung dari root project, karena `.env` dapat diunduh.

Alternatif subfolder (misalnya `http://localhost/inventrack/`): symlink/alias
**hanya** folder `public`, bukan seluruh project.

```bash
sudo ln -s /home/USER/INVENTRACK/public /var/www/html/inventrack
sudo chmod o+x /home/USER
```

> `.htaccess` di root project menyediakan lapisan tambahan untuk menolak `.env`
> dan folder internal, tetapi hanya aktif jika `AllowOverride` diizinkan.
> Mengarahkan DocumentRoot ke `public/` tetap merupakan cara utama yang aman.

---

## 4. Akun Demo

Data seed menyediakan tiga akun demo:

| Email | Password | Peran |
|---|---|---|
| `admin@inventrack.com` | `admin123` | admin |
| `petugas@inventrack.com` | `petugas123` | petugas |
| `manager@inventrack.com` | `manager123` | manager |

> **Peringatan:** akun di atas hanya untuk demo/pengembangan. Ganti semua password
> sebelum aplikasi dipakai di lingkungan nyata.

Jika perlu reset manual:

```bash
php -r 'echo password_hash("PasswordBaru123", PASSWORD_BCRYPT), PHP_EOL;'
```

```sql
UPDATE pengguna
SET kata_sandi = '<hash-dari-perintah-di-atas>'
WHERE email = 'admin@inventrack.com';
```

---

## 5. Catatan Portabilitas (Linux ↔ Windows)

- Semua path PHP memakai `__DIR__`, tidak ada path absolut.
- Nama file konsisten; aman untuk filesystem case-sensitive Linux.
- `.gitattributes` menormalkan akhir baris ke LF.
- Tidak memakai Composer atau ekstensi di luar PDO MySQL dan `fileinfo`.
- Hasil build Tailwind bersifat statis; runtime tidak membutuhkan Node.js.
- Zona waktu: disarankan `Asia/Jakarta` untuk PHP dan MySQL.
- `upload_max_filesize`/`post_max_size`: naikkan ke 8–10 MB (Linux default 2 MB).
- Karena folder `uploads/produk/` berada di dalam `public/`, foto produk tetap
  dapat diakses meskipun DocumentRoot diarahkan langsung ke `public/`
  (tidak perlu alias Apache tambahan).

---

## 6. Alur Pengembangan Frontend

| Perintah | Fungsi |
|---|---|
| `npm run css:build` | build CSS minified ke `public/assets/css/style.css` |
| `npm run css:watch` | build otomatis saat file berubah |
| `find app views public -name '*.php' -exec php -l {} \;` | cek sintaks PHP |

Komponen Tailwind kustom (`.btn`, `.card`, `.input`, `.badge`, `.table-modern`,
`.nav-link`, dan lainnya) didefinisikan di `src/input.css`.

Interaksi JavaScript global ada di `public/assets/js/main.js`:
sidebar, dropdown, toast, modal `<dialog>`, konfirmasi hapus, pencarian tabel,
repeater item transaksi, dan perhitungan stok opname.

---

## 7. Catatan Keamanan

- **DocumentRoot = `public/`.** Jangan menaruh root project langsung di `htdocs`
  atau document root, karena `.env` berisi kredensial database.
- **`.env` tidak di-commit.** Gunakan `.env.example` sebagai template dan isi
  password sendiri.
- **Akun demo wajib diganti** sebelum dipakai di lingkungan nyata.
- **Upload foto** divalidasi dari isi file (`finfo` + `getimagesize`), ekstensi
  ditentukan server (JPG/PNG/WEBP, maks. 2 MB), dan folder upload dilindungi
  `.htaccess` agar tidak mengeksekusi skrip.
- **CSRF** diterapkan pada semua form POST, termasuk hapus dan ubah status.
  Aksi destruktif tidak lagi memakai GET.
- **Sesi:** cookie `HttpOnly` + `SameSite=Lax` (+ `Secure` saat HTTPS),
  `session_regenerate_id()` setelah login, dan batas 5 percobaan login gagal
  per 5 menit.
- **Kata sandi** minimal 8 karakter; hash memakai BCRYPT.
- **Security header** (CSP dengan nonce, `X-Frame-Options`,
  `X-Content-Type-Options`, `Referrer-Policy`) dikirim untuk halaman HTML.
- **Error database** dicatat ke log server; pengguna hanya melihat pesan umum.
- Untuk produksi: aktifkan HTTPS, batasi percobaan login per-IP di web server,
  gunakan user MySQL dengan hak minimum, dan lakukan backup rutin.

> Batas percobaan login berbasis sesi dapat diakali dengan menghapus cookie.
> Pembatasan per-IP di level web server tetap disarankan untuk produksi.
