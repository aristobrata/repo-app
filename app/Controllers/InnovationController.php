<?php
namespace App\Controllers;
use App\Models\InovasiModel;
use App\Models\InovasiAnggotaTimModel;
use App\Models\InovasiLampiranModel;
use App\Models\InovasiLikeModel;
use App\Models\ActivityLogModel;

class InnovationController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected InovasiAnggotaTimModel $timModel;
    protected InovasiLampiranModel $lampiranModel;
    protected InovasiLikeModel $likeModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel  = new InovasiModel();
        $this->timModel      = new InovasiAnggotaTimModel();
        $this->lampiranModel = new InovasiLampiranModel();
        $this->likeModel     = new InovasiLikeModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'kategori', 'tahun', 'dept']);
        $inovasi = $this->inovasiModel->daftar($filters)->paginate(15, 'inovasi');
        return view('innovation/index', [
            'inovasi' => $inovasi, 'pager' => $this->inovasiModel->pager, 'filters' => $filters,
        ]);
    }

    public function detail(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $userId = session()->get('user_id');
        $data = [
            'inovasi'    => $inovasi,
            'tim'        => $this->timModel->getFor($id),
            'lampiran'   => $this->lampiranModel->getFor($id),
            'sudah_like' => $this->likeModel->sudahLike($id, $userId),
        ];
        $this->inovasiModel->incrementView($id);
        $this->logModel->catat($userId, 'lihat_inovasi', 'inovasi', $id, $inovasi['judul_inovasi']);
        return view('innovation/detail', $data);
    }

    public function like(int $id)
    {
        $userId  = session()->get('user_id');
        $isLiked = $this->likeModel->toggle($id, $userId);
        $this->inovasiModel->set('jumlah_like', $isLiked ? 'jumlah_like + 1' : 'jumlah_like - 1', false)->where('id', $id)->update();
        $total = $this->inovasiModel->find($id)['jumlah_like'];
        return $this->response->setJSON(['liked' => $isLiked, 'total_like' => $total]);
    }

    public function streamCoverImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/covers/' . basename($filename);
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $mime = mime_content_type($path) ?: 'image/jpeg';
        return $this->response->setHeader('Content-Type', $mime)->setHeader('Cache-Control', 'public, max-age=86400')->setBody(file_get_contents($path));
    }

    /** Download file_dokumen (opsional) jika admin pernah upload */
    public function downloadDokumen(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi || empty($inovasi['file_dokumen'])) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $path = WRITEPATH . 'uploads/originals/' . $inovasi['file_dokumen'];
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return $this->response->download($path, null);
    }
}
