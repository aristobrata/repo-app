# Internal Digital Repository & Knowledge Center (CodeIgniter 4) - v2.0

Starter kit kode aplikasi (bukan instalasi CI4 penuh) untuk sistem Internal Digital
Repository & Knowledge Center. Salin/gabungkan file-file di sini ke project CI4 yang
sudah ada (dibuat via `composer create-project codeigniter4/appstarter`).

## Modul Utama

1. **Repository Digital** (`/dokumen`) - arsip dokumen PDF dengan preview watermark terbatas
2. **Inovasi & Knowledge Hub** (`/inovasi`) - LISTING GABUNGAN dalam satu tabel:
   - **Inovasi**: pencapaian/karya inovatif karyawan (deskripsi masalah, solusi, dampak)
   - **Knowledge Management**: best practice, lesson learned, tips teknis (ringkasan, konten, referensi)
   - Keduanya tampil dalam SATU tabel dengan kolom "Tipe" pembeda, tapi detail & CRUD tetap terpisah
     karena struktur datanya berbeda
3. **Admin & Control Panel** (`/admin/*`) - CRUD lengkap untuk semua modul + Analytics Dashboard

## Instalasi

```bash
composer create-project codeigniter4/appstarter nama-project
cd nama-project
# salin folder app/, writable/uploads/ dari starter kit ini ke sini (gabung, jangan timpa app/Config/Filters.php penuh)
cp env.example .env
# edit .env: database.default.database, username, password
php spark migrate
php spark db:seed InitialAdminSeeder
php spark serve
```

Login awal: `admin@perusahaan.local` / `admin12345` -- segera ganti setelah login pertama.

**Requirement tambahan**: ekstensi PHP Imagick + Ghostscript terinstall di server untuk
fitur auto-split preview PDF (lihat app/Helpers/preview_helper.php).

## app/Config/Filters.php

File di starter kit ini sudah LENGKAP (bukan cuplikan) -- berisi konfigurasi default CI4
4.7.x ditambah 2 alias (`auth`, `admin`). Aman ditimpa langsung menggantikan file bawaan.

## Struktur Database (11 + 5 tabel)

- `users`, `kategori_dokumen`, `dokumen`, `dokumen_preview_pages`, `document_views`
- `inovasi`, `inovasi_lampiran`, `inovasi_like`, `inovasi_bookmark`, `innovation_views`
- `knowledge_hub`, `knowledge_lampiran`, `knowledge_like`, `knowledge_bookmark`, `knowledge_views` (BARU)
- `activity_logs`

## Routing Penting

| URL | Keterangan |
|---|---|
| `/inovasi` | Listing GABUNGAN Inovasi + Knowledge (tabel, bukan card) |
| `/inovasi/{id}` | Detail Inovasi |
| `/pengetahuan/{id}` | Detail Knowledge item |
| `/inovasi/bacaan-saya` | Daftar bacaan gabungan (bookmark dari kedua tipe) |
| `/admin/inovasi` | CRUD Inovasi (Admin) |
| `/admin/knowledge` | CRUD Knowledge Hub (Admin) -- struktur sama persis dengan Inovasi |
| `/admin/dokumen/{id}/lihat-lengkap` | Admin lihat PDF lengkap (semua halaman, tanpa watermark) |
| `/admin/dokumen/{id}/download` | Admin download file asli |

## Keamanan File & Watermark

- File asli selalu di `writable/uploads/` (di luar `public/`, tidak bisa diakses via URL langsung)
- Preview dokumen: watermark tipis (grid merata, abu-abu 12% opacity) + watermark dinamis
  forensik (nama+email+waktu viewer, overlay CSS, beda tiap kali dibuka) -- lihat
  `app/Views/repository/preview.php`
- **Catatan jujur**: screenshot TIDAK bisa dicegah 100% lewat web (keterbatasan browser/OS).
  Pendekatan aplikasi ini: deterrent (disable klik kanan/print/devtools) + watermark forensik
  untuk pelacakan jika terjadi kebocoran, bukan pencegahan mutlak.
- Admin/Super Admin punya akses tanpa batas (lihat dokumen lengkap + download), karyawan biasa
  tetap dibatasi mode preview.

## Dashboard

`DashboardController` menampilkan: total dokumen/inovasi/knowledge/user aktif, tabel dokumen
terbaru, tabel konten terbaru GABUNGAN (inovasi+knowledge), dokumen populer, dan log aktivitas
terbaru (khusus admin).
