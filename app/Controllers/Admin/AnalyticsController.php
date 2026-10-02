<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\DokumenModel;
use App\Models\DocumentViewModel;
use App\Models\ActivityLogModel;

/**
 * Analytics khusus modul Repository Dokumen (dokumen paling dilihat, kategori, tren baca).
 * Analytics untuk Inovasi & Knowledge Management sudah tercakup penuh di Dashboard utama
 * (lihat DashboardController) sesuai spesifikasi desain dashboard, sehingga tidak diduplikasi di sini.
 */
class AnalyticsController extends BaseController
{
    public function index()
    {
        $dokumenModel = new DokumenModel();
        $viewModel    = new DocumentViewModel();
        $logModel     = new ActivityLogModel();

        $data = [
            'dokumen_populer'      => $dokumenModel->topDilihat(10),
            'dokumen_per_kategori' => $dokumenModel->jumlahPerKategori(),
            'tren_harian'          => $viewModel->trenHarian(30),
            'log_aktivitas'        => $logModel->terbaru(50),
            'total_dokumen'        => $dokumenModel->countAll(),
        ];
        return view('admin/analytics/index', $data);
    }

    public function chartData()
    {
        $dokumenModel = new DokumenModel();
        $viewModel    = new DocumentViewModel();
        return $this->response->setJSON([
            'per_kategori' => $dokumenModel->jumlahPerKategori(),
            'tren_harian'  => $viewModel->trenHarian(30),
            'top_dokumen'  => $dokumenModel->topDilihat(10),
        ]);
    }
}
