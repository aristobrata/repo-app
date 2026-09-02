# Internal Digital Repository & Knowledge Center (CodeIgniter 4)

Starter kit ini berisi **kode aplikasi (app-specific)** untuk sistem Internal Digital
Repository & Knowledge Center: Modul Repository Digital, Innovation Hub, dan Admin &
Control Panel — sesuai rancangan fitur yang telah dibahas.

> ⚠️ **Ini BUKAN instalasi CI4 penuh.** Folder ini hanya berisi file-file yang perlu
> Anda salin/gabungkan ke dalam project CodeIgniter 4 yang sudah ada (atau baru dibuat
> via Composer). Framework inti CI4 (vendor/, system/, dsb) tidak disertakan karena
> harus di-generate lewat Composer di lingkungan Anda sendiri.

## 1. Cara Instalasi

### a. Buat project CI4 baru (jika belum ada)
```bash
composer create-project codeigniter4/appstarter repository-knowledge-center
cd repository-knowledge-center
```

### b. Salin file dari starter kit ini
Salin & timpa (merge) folder berikut ke root project CI4 Anda:
```
app/Config/Routes.php          -> app/Config/Routes.php   (timpa)
app/Controllers/*              -> app/Controllers/         (gabung)
app/Models/*                   -> app/Models/               (gabung)
app/Filters/*                  -> app/Filters/               (gabung)
app/Views/*                    -> app/Views/                 (gabung)
app/Database/Migrations/*      -> app/Database/Migrations/   (gabung)
app/Database/Seeds/*           -> app/Database/Seeds/        (gabung)
app/Helpers/*                  -> app/Helpers/                (gabung)
writable/uploads/*             -> writable/uploads/           (gabung, termasuk .htaccess)
```

**KHUSUS `app/Config/Filters.php`**: JANGAN ditimpa langsung. File di starter kit ini
hanya berisi CUPLIKAN. Buka `app/Config/Filters.php` bawaan CI4 Anda, lalu tambahkan
2 baris berikut ke dalam array `$aliases`:
```php
'auth'  => \App\Filters\AuthFilter::class,
'admin' => \App\Filters\AdminFilter::class,
```

### c. Konfigurasi environment
```bash
cp env.example .env
```
Sesuaikan `database.default.*` dengan kredensial MySQL/MariaDB Anda (XAMPP default:
`root` tanpa password, database dibuat manual dulu lewat phpMyAdmin: buat schema
`repository_knowledge_center`).

### d. Jalankan migration & seeder
```bash
php spark migrate
php spark db:seed InitialAdminSeeder
```
Login pertama: `admin@perusahaan.local` / `admin12345` — **segera ganti password**
lewat fitur reset di Admin Panel setelah login.

### e. Requirement server tambahan (untuk fitur Document Preview)
Fitur auto-split PDF → gambar preview + watermark membutuhkan:
- Ekstensi PHP **Imagick** aktif (`php_imagick.dll` di XAMPP Windows, atau `php-imagick`
  di Linux)
- **Ghostscript** terinstall di server (Imagick memakai Ghostscript untuk rasterize PDF)

Jika kedua hal ini tidak tersedia, upload dokumen tetap berhasil tapi kolom
`status_preview` akan bernilai `failed` dan preview tidak akan tampil — pertimbangkan
memakai layanan konversi eksternal sebagai alternatif jika server tidak memungkinkan
instalasi Ghostscript.

### f. Jalankan server development
```bash
php spark serve
```
Akses di `http://localhost:8080`

## 2. Struktur Modul (Ringkasan)

| Modul | Controller | Fitur Utama |
|---|---|---|
| Repository Digital | `RepositoryController` | Search & filter cerdas, In-Browser Viewer, kategori |
| Innovation Hub | `InnovationController` | Direktori inovasi, like/bookmark, pengajuan inovasi |
| Admin Panel | `Admin\DocumentManageController`, `Admin\UserManageController`, `Admin\AnalyticsController` | Upload + auto-split preview, kelola user, dashboard analitik |

## 3. Alur Penyimpanan File (Keamanan)

```
project-root/
├── app/
├── public/              <- document root web server (SATU-SATUNYA folder publicly accessible)
│   └── index.php
├── writable/
│   └── uploads/
│       ├── originals/   <- file PDF asli, TIDAK bisa diakses langsung via URL
│       └── previews/    <- gambar hasil convert halaman preview (sudah watermark)
```

- `writable/uploads/` berada **di luar** `public/`, sehingga web server (Apache/Nginx)
  tidak bisa melayani request langsung ke path tersebut — satu-satunya cara mengakses
  isinya adalah lewat controller (`RepositoryController::streamPreviewImage()`), yang
  memastikan sesi/otorisasi user diperiksa dulu sebelum file dikirim.
- Nama file di-hash/randomize saat upload (`$file->getRandomName()`) agar tidak mudah
  ditebak.
- File `.htaccess` di `writable/uploads/` ditambahkan sebagai lapisan proteksi kedua,
  untuk berjaga-jaga jika konfigurasi virtual host keliru.
- File PDF **full/asli** tidak pernah dikirim ke browser lewat rute preview — yang
  dikirim hanya gambar per-halaman hasil auto-split, sehingga proteksi klik-kanan &
  disable print di sisi client menjadi lebih berarti (karena bukan file PDF utuh yang
  bisa di-download langsung dari DevTools).

## 4. Perubahan Terbaru (v1.1)

- **Pengajuan inovasi mandiri oleh karyawan DIHAPUS.** Aplikasi ini adalah repository/knowledge
  center yang kontennya dikelola Admin/Super Admin — karyawan (akun dibuatkan admin) hanya bisa
  melihat, memberi apresiasi (like), dan bookmark. Semua entri inovasi (atas nama karyawan
  manapun) di-input lewat `/admin/inovasi`.
- **Fitur upload sampul/cover** ditambahkan untuk Dokumen (`/admin/dokumen`) dan Inovasi
  (`/admin/inovasi`). File disimpan di `writable/uploads/covers/` dan di-stream lewat controller
  (`RepositoryController::streamCoverImage()` / `InnovationController::streamCoverImage()`),
  konsisten dengan pola keamanan file lain di aplikasi ini.
- **CRUD lengkap** ditambahkan untuk:
  - Dokumen: `Admin\DocumentManageController::editForm()` / `update()`
  - Kategori Dokumen (baru): `Admin\KategoriManageController` — index, create, edit, delete
    (delete diblokir jika kategori masih dipakai dokumen)
  - Inovasi (baru, menggantikan form pengajuan karyawan): `Admin\InnovationManageController` —
    index, create, edit, delete, plus kelola lampiran
  - User: `Admin\UserManageController::editForm()` / `update()` (edit profil terpisah dari reset
    password)
- Tidak ada migration baru yang perlu dijalankan — kolom `cover_thumbnail` (tabel `dokumen`) dan
  `foto_ilustrasi` (tabel `inovasi`) sudah ada sejak migration awal, hanya belum dipakai.
- Pastikan folder `writable/uploads/covers/` ada di server Anda (buat manual jika hasil merge
  tidak menyertakannya, karena folder kosong kadang tidak ter-track oleh beberapa tool zip/git).

## 5. Akses Penuh untuk Admin/Super Admin (v1.2)

Sebelumnya, pembatasan preview (N halaman + watermark + disable klik-kanan/print) berlaku untuk
SEMUA role, termasuk admin. Sekarang dibedakan:

- **Karyawan (role `karyawan`)**: tetap hanya bisa akses `/dokumen/{id}/preview` — dibatasi
  jumlah halaman, ada watermark "INTERNAL PREVIEW ONLY", klik-kanan & print dinonaktifkan.
- **Admin/Super Admin**: mendapat 2 tombol tambahan di halaman detail dokumen (dan ikon
  langsung di `/admin/dokumen`):
  - **👁 Lihat Dokumen Lengkap** (`/admin/dokumen/{id}/lihat-lengkap`) — membuka file PDF ASLI
    (bukan gambar preview) secara utuh di native PDF viewer browser, tanpa batas halaman dan
    tanpa watermark.
  - **⬇️ Download File Asli** (`/admin/dokumen/{id}/download`) — mengunduh file PDF asli
    langsung ke perangkat admin.

Kedua endpoint ini otomatis terlindungi oleh filter `['auth', 'admin']` di `Routes.php` (grup
`admin`), jadi karyawan biasa akan ditolak oleh `AdminFilter` sebelum sempat mengakses file
sama sekali — bukan sekadar disembunyikan tombolnya di UI. Setiap akses juga dicatat ke
`activity_logs` (`lihat_dokumen_lengkap` dan `unduh_dokumen`) untuk keperluan audit.

## 6. Catatan Pengembangan Lanjutan

- Proteksi klik-kanan/print di viewer adalah **deterrent**, bukan proteksi mutlak
  (screenshot tetap memungkinkan) — ini sudah dijelaskan juga sebagai komentar di
  `app/Views/repository/preview.php`.
- Integrasi email (reset password, notifikasi) belum diimplementasi — tempat yang perlu
  diisi ditandai `// TODO` di `AuthController` dan `Admin\UserManageController`.
- Tambahkan `CSRF filter` global dan `rate limiting` untuk endpoint login jika akan
  dipakai di lingkungan produksi.
- Untuk skala dokumen yang sangat besar, pertimbangkan migrasi dari MySQL FULLTEXT ke
  mesin pencari khusus (Meilisearch/Elasticsearch) — namun untuk kebutuhan internal
  perusahaan skala menengah, FULLTEXT index MySQL/MariaDB sudah cukup.
