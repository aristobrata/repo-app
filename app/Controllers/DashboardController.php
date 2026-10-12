<?php
namespace App\Controllers;
use App\Models\DokumenModel;
use App\Models\InovasiModel;
use App\Models\KmAktivitasModel;
use App\Models\KmRekapKaryawanModel;
use App\Models\KmKaryawanModel;
use App\Models\KmTargetModel;
use App\Models\ActivityLogModel;

/**
 * Dashboard Knowledge Management & Inovasi -- struktur & metrik mengikuti
 * "Rekomendasi UI Dashboard Knowledge Management & Inovasi.docx":
 * Row 1: KPI cards (Total Poin KM, % Target, Partisipasi, Total Inovasi)
 * Row 2: Tren Poin Bulanan (line) + Poin per Pilar KM (bar)
 * Row 3: Inovasi per Dept (bar) + Keterlibatan per Band (donut) + Leaderboard (tab)
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $dokumenModel = new DokumenModel();
        $inovasiModel = new InovasiModel();
        $aktivitasModel = new KmAktivitasModel();
        $rekapModel   = new KmRekapKaryawanModel();
        $karyawanModel= new KmKaryawanModel();
        $targetModel  = new KmTargetModel();
        $logModel     = new ActivityLogModel();

        // Filter global: periode (tahun)
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        // ---- Row 1: KPI ----
        $totalPoinKm = $aktivitasModel->totalPoin($tahun);
        $target      = $targetModel->getForTahun($tahun);
        $targetTahunan = (int) ($target['target_poin_tahunan'] ?? 0);
        $pctAchievement = $targetTahunan > 0 ? round(($totalPoinKm / $targetTahunan) * 100, 1) : null;

        $jumlahPesertaAktif = $aktivitasModel->jumlahPesertaAktif($tahun);
        $totalKaryawan      = $karyawanModel->countAll() ?: $rekapModel->totalKaryawan();
        $pctPartisipasi     = $totalKaryawan > 0 ? round(($jumlahPesertaAktif / $totalKaryawan) * 100, 1) : null;

        $totalInovasiTahun = $inovasiModel->where('tahun', $tahun)->countAllResults();

        // ---- Row 2 ----
        $trenPoinBulanan = $aktivitasModel->trenPoinBulanan($tahun);
        $poinPerPilar    = $aktivitasModel->poinPerPilar($tahun);

        // ---- Row 3 ----
        $inovasiPerDept   = $inovasiModel->jumlahPerDept($tahun);
        $keterlibatanBand = $rekapModel->keterlibatanPerBand();
        $topKaryawan      = $rekapModel->topKaryawan(10);
        $topDepartemen    = $rekapModel->topDepartemen(5);

        $data = [
            'tahun' => $tahun,
            'total_dokumen'  => $dokumenModel->countAll(),
            'total_poin_km'  => $totalPoinKm,
            'target_tahunan' => $targetTahunan,
            'pct_achievement'=> $pctAchievement,
            'pct_partisipasi'=> $pctPartisipasi,
            'jumlah_peserta_aktif' => $jumlahPesertaAktif,
            'total_karyawan' => $totalKaryawan,
            'total_inovasi_tahun' => $totalInovasiTahun,
            'tren_poin_bulanan' => $trenPoinBulanan,
            'poin_per_pilar'    => $poinPerPilar,
            'inovasi_per_dept'  => $inovasiPerDept,
            'keterlibatan_band' => $keterlibatanBand,
            'top_karyawan'      => $topKaryawan,
            'top_departemen'    => $topDepartemen,
            'aktivitas_terbaru' => $logModel->terbaru(10),
        ];

        return view('dashboard', $data);
    }

    /**
     * Dashboard Summary Inovasi -- halaman ringkasan terpisah (Tailwind + Chart.js)
     * di dalam modul Dashboard yang sama, diakses lewat tombol "Summary Inovasi"
     * pada dashboard utama.
     *
     * CATATAN (penting, hasil cek langsung ke data Anda): rancangan awal halaman
     * ini memakai istilah "FI" dan "RI" sebagai dua kategori utama, tapi kategori
     * yang BENAR-BENAR ada di tabel inovasi Anda adalah SS, PKM, TPP (beberapa
     * varian penulisan), MANAJEMEN, 5P, AM, GKM, dan FI (sangat sedikit) -- tidak
     * ada satu pun baris berkategori "RI". Supaya angkanya tidak selalu 0, bagian
     * ini diganti jadi breakdown per kategori ASLI yang ada di database, bukan
     * dipaksa FI/RI. Field tahun di tabel inovasi juga bervariasi (2011-2024),
     * sedangkan field tahun di km_aktivitas di data Anda semuanya "2026" (bukan
     * tanggal aktivitas sesungguhnya) -- karena itu filter tahun di halaman ini
     * HANYA memengaruhi angka Inovasi; angka Karyawan Terlibat & Participation per
     * Band ditampilkan akumulasi seluruh waktu supaya tidak terlihat 0 akibat
     * filter tahun yang tidak relevan untuk data KM.
     */
    public function summaryInovasi()
    {
        $karyawanModel  = new KmKaryawanModel();
        $rekapModel     = new KmRekapKaryawanModel();
        $aktivitasModel = new KmAktivitasModel();
        $inovasiModel   = new InovasiModel();

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $periode = $bulanIndo[(int) date('n')] . ' ' . date('Y');

        // Filter tahun: default "Semua Tahun" (bukan tahun berjalan), karena data
        // Inovasi Anda terbaru hanya sampai 2024 -- kalau defaultnya tahun
        // berjalan, kartu-kartu akan selalu tampil 0 untuk organisasi seperti ini.
        $tahunGet = $this->request->getGet('tahun');
        $tahun    = ($tahunGet !== null && $tahunGet !== '') ? $tahunGet : null;

        $daftarTahun = $inovasiModel->daftarTahun();

        // ---- Inovasi: breakdown per kategori ASLI (SS, PKM, TPP, MANAJEMEN, 5P, AM, GKM, dst) ----
        $totalInovasi        = $inovasiModel->totalTahun($tahun);
        $kategoriBreakdown    = $inovasiModel->jumlahPerKategori($tahun); // [{kategori, total}, ...] urut terbanyak
        $topKategori          = $kategoriBreakdown[0] ?? null;
        $jumlahKategoriAktif  = count($kategoriBreakdown);
        $cakupanDept          = $inovasiModel->cakupanDept($tahun);

        // ---- Karyawan terlibat KM (akumulasi seluruh waktu, lihat catatan di atas) ----
        $totalKaryawan    = $karyawanModel->totalKaryawan();
        $karyawanTerlibat = $aktivitasModel->jumlahPesertaAktif(null);
        $pctTerlibat      = $totalKaryawan > 0 ? round(($karyawanTerlibat / $totalKaryawan) * 100, 1) : 0.0;

        // ---- Participation rate per Band: total per band (master) vs terlibat per band (rekap) ----
        // Dibungkus try/catch: kolom 'band' di km_karyawan baru ada setelah migration
        // 20240101000010_AddBandToKmKaryawan dijalankan. Kalau migration itu belum
        // dijalankan di server, jangan sampai seluruh halaman error -- anggap saja
        // belum ada data Band (view sudah punya pesan untuk kondisi ini).
        $totalPerBand = [];
        try {
            foreach ($karyawanModel->totalPerBand() as $row) {
                $totalPerBand[$row['band']] = (int) $row['total'];
            }
        } catch (\Throwable $e) {
            $totalPerBand = [];
        }
        $terlibatPerBand = [];
        try {
            foreach ($rekapModel->keterlibatanPerBand() as $row) {
                $terlibatPerBand[$row['band']] = (int) $row['total'];
            }
        } catch (\Throwable $e) {
            $terlibatPerBand = [];
        }
        $bandData = [];
        foreach ($totalPerBand as $band => $total) {
            $terlibat = $terlibatPerBand[$band] ?? 0;
            $bandData[] = [
                'band'     => $band,
                'terlibat' => $terlibat,
                'total'    => $total,
                'pct'      => $total > 0 ? round(($terlibat / $total) * 100, 1) : 0.0,
            ];
        }

        return view('dashboard/summary_inovasi', [
            'title'                 => 'Summary Inovasi',
            'periode'               => $periode,
            'tahun'                 => $tahun,
            'daftar_tahun'          => $daftarTahun,
            'total_inovasi'         => $totalInovasi,
            'kategori_breakdown'    => $kategoriBreakdown,
            'top_kategori'          => $topKategori,
            'jumlah_kategori_aktif' => $jumlahKategoriAktif,
            'cakupan_dept'          => $cakupanDept,
            'total_karyawan'        => $totalKaryawan,
            'karyawan_terlibat'     => $karyawanTerlibat,
            'pct_terlibat'          => $pctTerlibat,
            'band_data'             => $bandData,
        ]);
    }
}
