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

    /**
     * Dashboard utama (satu halaman, lengkap): Inovasi + Kompetisi + Knowledge Management.
     * Filter tahun hanya memengaruhi bagian Inovasi & Kompetisi; data KM ditampilkan akumulasi
     * (kolom tahun di km_aktivitas semuanya sama sehingga tidak bermakna sebagai filter).
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $inovasiModel = new InovasiModel();
        new \App\Models\KompetisiInovasiModel(); // memastikan tabel kompetisi ada (auto-create)

        $tahunGet = $this->request->getGet('tahun');
        $tahun    = ($tahunGet !== null && $tahunGet !== '') ? (string) $tahunGet : null;

        $q = function (string $sql, array $bind = []) use ($db) {
            try { return $db->query($sql, $bind)->getResultArray(); } catch (\Throwable $e) { return []; }
        };
        $wI = $tahun ? ' WHERE tahun = ? ' : '';
        $bI = $tahun ? [$tahun] : [];

        // ===== INOVASI =====
        $tot = $q("SELECT COUNT(*) n, COALESCE(SUM(biaya_project),0) biaya, COALESCE(SUM(saving),0) saving,
                   COALESCE(SUM(revenue),0) revenue, COALESCE(SUM(total_benefit),0) benefit,
                   COALESCE(SUM(jumlah_view),0) views, COALESCE(SUM(total_benefit > 0),0) n_benefit FROM inovasi $wI", $bI)[0] ?? [];
        $perTahun = $q("SELECT tahun, COUNT(*) total, COALESCE(SUM(total_benefit),0) benefit FROM inovasi
                        WHERE tahun REGEXP '^[0-9]{4}$' GROUP BY tahun ORDER BY tahun ASC");
        $perKategori = $q("SELECT CASE WHEN kategori_inovasi LIKE 'TPP%' THEN 'TPP' WHEN kategori_inovasi IS NULL OR kategori_inovasi = '' THEN 'Belum dikategorikan' ELSE kategori_inovasi END kategori,
                           COUNT(*) total FROM inovasi $wI GROUP BY kategori ORDER BY total DESC", $bI);
        $perDept = $q("SELECT unit_dept_area_implementasi dept, COUNT(*) total FROM inovasi
                       " . ($tahun ? 'WHERE tahun = ? AND' : 'WHERE') . " unit_dept_area_implementasi IS NOT NULL AND unit_dept_area_implementasi <> ''
                       GROUP BY dept ORDER BY total DESC LIMIT 10", $bI);
        $perArea = $q("SELECT area_improvement area, COUNT(*) total FROM inovasi
                       " . ($tahun ? 'WHERE tahun = ? AND' : 'WHERE') . " area_improvement IS NOT NULL AND area_improvement <> ''
                       GROUP BY area ORDER BY total DESC LIMIT 8", $bI);
        $topBenefit = $q("SELECT id, judul_inovasi, nama_tim, kategori_inovasi, tahun, total_benefit FROM inovasi
                          " . ($tahun ? 'WHERE tahun = ? AND' : 'WHERE') . " total_benefit > 0 ORDER BY total_benefit DESC LIMIT 10", $bI);
        $inovasiTerbaru = $q("SELECT id, judul_inovasi, nama_tim, kategori_inovasi, tahun FROM inovasi $wI ORDER BY COALESCE(tanggal_registrasi, created_at) DESC, id DESC LIMIT 8", $bI);
        $strukturTim = $q("SELECT COALESCE(NULLIF(a.struktur_tim,''),'Lainnya') peran, COUNT(*) total FROM inovasi_anggota_tim a
                           JOIN inovasi i ON i.id = a.inovasi_id " . ($tahun ? 'WHERE i.tahun = ?' : '') . " GROUP BY peran ORDER BY total DESC", $bI);
        $personilInovasi = (int) ($q("SELECT COUNT(DISTINCT a.nik) n FROM inovasi_anggota_tim a JOIN inovasi i ON i.id = a.inovasi_id
                                      WHERE a.nik IS NOT NULL AND a.nik <> '' " . ($tahun ? 'AND i.tahun = ?' : ''), $bI)[0]['n'] ?? 0);
        $daftarTahun = $inovasiModel->daftarTahun();

        // ===== KOMPETISI =====
        $wK = $tahun ? ' WHERE tahun = ? ' : '';
        $kTot = $q("SELECT COUNT(*) n, COUNT(DISTINCT event_kompetisi) events FROM kompetisi_inovasi $wK", $bI)[0] ?? ['n' => 0, 'events' => 0];
        $kPersonil = (int) ($q("SELECT COUNT(DISTINCT a.nik) n FROM kompetisi_inovasi_anggota a JOIN kompetisi_inovasi k ON k.id = a.kompetisi_id
                                WHERE a.nik IS NOT NULL AND a.nik <> '' " . ($tahun ? 'AND k.tahun = ?' : ''), $bI)[0]['n'] ?? 0);
        $kJenis = $q("SELECT COALESCE(jenis_penghargaan,'-') jenis, COUNT(*) total FROM kompetisi_inovasi $wK GROUP BY jenis ORDER BY total DESC", $bI);
        $kEvent = $q("SELECT COALESCE(event_kompetisi,'-') event, COUNT(*) total FROM kompetisi_inovasi $wK GROUP BY event ORDER BY total DESC LIMIT 8", $bI);
        $kPenyelenggara = $q("SELECT COALESCE(penyelenggara,'-') nama, COUNT(*) total FROM kompetisi_inovasi $wK GROUP BY nama ORDER BY total DESC LIMIT 6", $bI);
        $kTahunJenis = $q("SELECT tahun, COALESCE(jenis_penghargaan,'-') jenis, COUNT(*) total FROM kompetisi_inovasi WHERE tahun IS NOT NULL GROUP BY tahun, jenis ORDER BY tahun ASC");
        $kTerbaru = $q("SELECT id, judul_inovasi, nama_tim, jenis_penghargaan, event_kompetisi, tanggal_penghargaan FROM kompetisi_inovasi $wK
                        ORDER BY tanggal_penghargaan DESC, id DESC LIMIT 8", $bI);

        // ===== KNOWLEDGE MANAGEMENT (akumulasi) =====
        $aktivitasModel = new KmAktivitasModel();
        $rekapModel     = new KmRekapKaryawanModel();
        $karyawanModel  = new KmKaryawanModel();
        $targetRow      = (new KmTargetModel())->orderBy('tahun', 'DESC')->first();
        $targetTahunan  = (int) ($targetRow['target_poin_tahunan'] ?? 0);
        $tahunTarget    = $targetRow['tahun'] ?? date('Y');
        $totalPoinKm    = $aktivitasModel->totalPoin(null);
        $poinTahunTarget = $aktivitasModel->totalPoin((string) $tahunTarget);
        $pctTarget      = $targetTahunan > 0 ? round($poinTahunTarget / $targetTahunan * 100, 1) : null;
        $totalKaryawan  = $karyawanModel->countAll() ?: $rekapModel->totalKaryawan();
        $pesertaAktif   = $aktivitasModel->jumlahPesertaAktif(null);
        $pctPartisipasi = $totalKaryawan > 0 ? round($pesertaAktif / $totalKaryawan * 100, 1) : 0;
        $jumlahAktivitas = $aktivitasModel->countAll();
        $trenBulanan    = $aktivitasModel->trenPoinBulanan(null);
        $perPilar       = $q("SELECT pillar_km pilar, COUNT(*) kegiatan, COALESCE(SUM(poin),0) poin FROM km_aktivitas WHERE pillar_km IS NOT NULL GROUP BY pilar ORDER BY poin DESC");
        $kegiatanBulanan = $q("SELECT bulan, COUNT(*) kegiatan, COUNT(DISTINCT nik) peserta FROM km_aktivitas GROUP BY bulan");

        $totalPerBand = []; $terlibatPerBand = [];
        try { foreach ($karyawanModel->totalPerBand() as $r) { $totalPerBand[$r['band']] = (int) $r['total']; } } catch (\Throwable $e) {}
        try { foreach ($rekapModel->keterlibatanPerBand() as $r) { $terlibatPerBand[$r['band']] = (int) $r['total']; } } catch (\Throwable $e) {}
        $bandData = [];
        foreach ($totalPerBand as $band => $total) {
            $t = $terlibatPerBand[$band] ?? 0;
            $bandData[] = ['band' => $band, 'terlibat' => $t, 'total' => $total, 'pct' => $total > 0 ? round($t / $total * 100, 1) : 0];
        }
        $topKaryawan   = $rekapModel->topKaryawan(10);
        $topDepartemen = $rekapModel->topDepartemen(8);

        $logModel = new ActivityLogModel();

        return view('dashboard', [
            'tahun' => $tahun, 'daftar_tahun' => $daftarTahun,
            'tot' => $tot, 'per_tahun' => $perTahun, 'per_kategori' => $perKategori, 'per_dept' => $perDept, 'per_area' => $perArea,
            'top_benefit' => $topBenefit, 'inovasi_terbaru' => $inovasiTerbaru, 'struktur_tim' => $strukturTim, 'personil_inovasi' => $personilInovasi,
            'k_tot' => $kTot, 'k_personil' => $kPersonil, 'k_jenis' => $kJenis, 'k_event' => $kEvent, 'k_penyelenggara' => $kPenyelenggara,
            'k_tahun_jenis' => $kTahunJenis, 'k_terbaru' => $kTerbaru,
            'total_poin_km' => $totalPoinKm, 'target_tahunan' => $targetTahunan, 'tahun_target' => $tahunTarget, 'poin_tahun_target' => $poinTahunTarget, 'pct_target' => $pctTarget,
            'total_karyawan' => $totalKaryawan, 'peserta_aktif' => $pesertaAktif, 'pct_partisipasi' => $pctPartisipasi, 'jumlah_aktivitas' => $jumlahAktivitas,
            'tren_bulanan' => $trenBulanan, 'per_pilar' => $perPilar, 'kegiatan_bulanan' => $kegiatanBulanan, 'band_data' => $bandData,
            'top_karyawan' => $topKaryawan, 'top_departemen' => $topDepartemen,
            'log_terbaru' => $logModel->terbaru(8),
            'periode' => date('d M Y'),
        ]);
    }
}
