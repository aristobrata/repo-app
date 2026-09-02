<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DokumenModel;
use App\Models\InovasiModel;
use App\Models\DocumentViewModel;
use App\Models\ActivityLogModel;

class AnalyticsController extends BaseController
{
    public function index()
    {
        $dokumenModel = new DokumenModel();
        $inovasiModel = new InovasiModel();
        $viewModel    = new DocumentViewModel();
        $logModel     = new ActivityLogModel();

        $data = [
            'dokumen_populer'    => $dokumenModel->topDilihat(10),
            'inovasi_populer'    => $inovasiModel->topDilihat(10),
            'dokumen_per_kategori' => $dokumenModel->jumlahPerKategori(),
            'tren_harian'        => $viewModel->trenHarian(30),
            'log_aktivitas'      => $logModel->terbaru(50),
            'total_dokumen'      => $dokumenModel->countAll(),
            'total_inovasi'      => $inovasiModel->countAll(),
        ];

        return view('admin/analytics/index', $data);
    }

    /** Endpoint JSON untuk di-consume Chart.js di sisi frontend */
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
