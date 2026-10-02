# Internal Digital Repository & Knowledge Center (CodeIgniter 4) - v3.0

> **v3.0 adalah PIVOT besar**: struktur tabel Inovasi & Knowledge Management diganti total
> agar sesuai PERSIS dengan data riil Anda (`DATABASE_INOVASI_BERSIH.xlsx` dan
> `Database_KM_2026_Clean.xlsx`), dan Dashboard dirancang ulang sesuai
> `Rekomendasi_UI_Dashboard_Knowledge_Management_&_Inovasi.docx`.

## WAJIB: Dependency Baru

Fitur import Excel butuh PhpSpreadsheet. Jalankan di project Anda:
```bash
composer require phpoffice/phpspreadsheet
```

## Struktur Database Baru

### Modul Inovasi (2 tabel header-detail)
Data asli 1 baris = 1 anggota tim (7.891 baris Excel = 1.903 inovasi unik), dipecah jadi:
- **`inovasi`** (1x per inovasi): kategori_inovasi, tanggal_registrasi, nama_tim, judul_inovasi,
  area_improvement, unit_dept/biro_area_implementasi, biaya_project, saving, opp_lost, revenue,
  total_benefit, status_saat_ini, keterangan, hyperlink_dokumen (referensi lama),
  **file_dokumen (NULLABLE/tidak wajib)**, cover_thumbnail, tahun
- **`inovasi_anggota_tim`** (banyak per inovasi): nama_personil, nik, struktur_tim (Ketua/Sekretaris/Anggota), org_unit
- `inovasi_lampiran`, `inovasi_like`, `innovation_views` (engagement ringan, tetap dipertahankan)

### Modul Knowledge Management (3 tabel sesuai 3 sheet Excel)
- **`km_aktivitas`** (fact): bulan, pillar_km, aktivitas, subactivity, judul_event, tanggal_score,
  nik/nip/nama_peserta, direktorat/departemen/biro/org_unit/bidang, peran, poin,
  **file_dokumen (NULLABLE/tidak wajib)**, tahun
- **`km_karyawan`** (dim, master data karyawan)
- **`km_rekap_karyawan`** (snapshot leaderboard: total_poin, band jabatan)
- **`km_target`** (BARU, tidak ada di Excel): target poin tahunan untuk hitung % pencapaian

**Semua kolom upload file (`file_dokumen`) di kedua modul SENGAJA NULLABLE** (validasi
`permit_empty`, bukan `required`) -- sesuai permintaan, karena data impor Excel tidak
menyertakan file fisik.

## Fitur Import Excel

| Menu Admin | Endpoint | Sheet yang dibaca | Perilaku |
|---|---|---|---|
| Kelola Inovasi → Import Excel | `/admin/inovasi/import` | Sheet1 | Tambah baru, skip duplikat (nama_tim+judul sama) |
| Kelola KM → Import Excel → Aktivitas | `/admin/knowledge/import` (form 1) | fact_aktivitas_km | Tambah baru (tidak menimpa) |
| Kelola KM → Import Excel → Karyawan | `/admin/knowledge/import` (form 2) | dim_karyawan | **Menimpa penuh** (snapshot master) |
| Kelola KM → Import Excel → Rekap | `/admin/knowledge/import` (form 3) | rekap_karyawan | **Menimpa penuh** (snapshot leaderboard) |

Import membaca kolom berdasarkan **urutan posisi** (A, B, C, ...) sesuai struktur file sumber
Anda -- jika urutan kolom di file Excel Anda berbeda, sesuaikan index array `$r[0], $r[1], ...`
di `Admin\InovasiManageController::import()` / `Admin\KmManageController::importAktivitas()` dst.

## Dashboard (sesuai spesifikasi Word)

`DashboardController` + `app/Views/dashboard.php` mengikuti layout 3-baris:
- **Row 1 (KPI Cards)**: Total Poin KM, % Target Tahunan, Partisipasi Karyawan, Total Inovasi
- **Row 2 (Tren & Distribusi)**: Line chart tren poin bulanan, bar chart poin per pilar KM
- **Row 3 (Detail & Leaderboard)**: Bar chart inovasi per dept, donut keterlibatan per band,
  tabbed leaderboard (Top Karyawan / Top Departemen)

**Palet warna korporat SIG** diterapkan lewat CSS variable di `app/Views/layouts/main.php`:
`--sig-red: #C8102E`, `--sig-dark-blue: #1E3A8A`, `--sig-gold: #D97706`, dst. Font: Inter
(Google Fonts). Card radius 8px, border #E2E8F0, sesuai design system di dokumen Word.

**Catatan**: Target poin tahunan tidak ada di file Excel sumber (hanya data aktual), jadi
diatur manual di menu Admin → Kelola KM → Target Tahunan. Tanpa target diisi, kartu "%
Target Tahunan" menampilkan "-".

## Instalasi

```bash
composer create-project codeigniter4/appstarter nama-project
cd nama-project
composer require phpoffice/phpspreadsheet
# salin folder app/, writable/uploads/ dari starter kit ini (gabung; app/Config/Filters.php
# sudah lengkap, aman ditimpa langsung)
cp env.example .env
# edit .env sesuai kredensial database Anda
php spark migrate
php spark db:seed InitialAdminSeeder
php spark serve
```

Login awal: `admin@perusahaan.local` / `admin12345`

Setelah login, masuk ke **Admin Panel → Import Excel Inovasi** dan **Import Excel KM**
untuk langsung mengunggah `DATABASE_INOVASI_BERSIH.xlsx` dan `Database_KM_2026_Clean.xlsx`
Anda.

## Modul yang TIDAK berubah dari versi sebelumnya
Repository Digital (dokumen/kategori/preview watermark), Management User & Akses.
