<?php

namespace App\Controllers;

use App\Models\DokumenModel;
use App\Models\InovasiModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $dokumenModel = new DokumenModel();
        $inovasiModel = new InovasiModel();

        $data = [
            'dokumen_terbaru' => $dokumenModel->orderBy('created_at', 'DESC')->findAll(6),
            'inovasi_terbaru' => $inovasiModel->direktori()->findAll(6),
            'dokumen_populer' => $dokumenModel->topDilihat(5),
        ];

        return view('dashboard', $data);
    }
}
