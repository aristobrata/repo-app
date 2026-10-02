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
}
