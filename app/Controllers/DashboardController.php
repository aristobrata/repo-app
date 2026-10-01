<?php
namespace App\Controllers;
use App\Models\DokumenModel;
use App\Models\InovasiModel;
use App\Models\KnowledgeModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

/**
 * Dashboard ringkasan: statistik total per modul, konten terbaru gabungan
 * (Inovasi + Knowledge), dan aktivitas terakhir.
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $dokumenModel   = new DokumenModel();
        $inovasiModel   = new InovasiModel();
        $knowledgeModel = new KnowledgeModel();
        $userModel      = new UserModel();
        $logModel       = new ActivityLogModel();

        $inovasiTerbaru = $inovasiModel->select("inovasi.id, inovasi.judul, inovasi.created_at, inovasi.jumlah_view, 'inovasi' as tipe")
            ->orderBy('created_at', 'DESC')->findAll(5);
        $knowledgeTerbaru = $knowledgeModel->select("knowledge_hub.id, knowledge_hub.judul, knowledge_hub.created_at, knowledge_hub.jumlah_view, 'knowledge' as tipe")
            ->where('status', 'dipublikasikan')->orderBy('created_at', 'DESC')->findAll(5);

        // Gabungkan & urutkan ulang berdasarkan created_at, ambil 6 teratas
        $gabungan = array_merge($inovasiTerbaru, $knowledgeTerbaru);
        usort($gabungan, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
        $gabungan = array_slice($gabungan, 0, 6);

        $data = [
            'total_dokumen'    => $dokumenModel->countAll(),
            'total_inovasi'    => $inovasiModel->countAll(),
            'total_knowledge'  => $knowledgeModel->countAll(),
            'total_user'       => $userModel->where('status', 'aktif')->countAllResults(),
            'dokumen_terbaru'  => $dokumenModel->orderBy('created_at', 'DESC')->findAll(5),
            'konten_terbaru'   => $gabungan, // gabungan inovasi + knowledge
            'dokumen_populer'  => $dokumenModel->topDilihat(5),
            'aktivitas_terbaru'=> $logModel->terbaru(10),
        ];

        return view('dashboard', $data);
    }
}
