<?php
namespace App\Controllers;
use App\Models\InovasiModel;
use App\Models\InovasiLampiranModel;
use App\Models\InovasiLikeModel;
use App\Models\InovasiBookmarkModel;
use App\Models\ActivityLogModel;

/**
 * Controller sisi karyawan untuk Inovasi (BUKAN untuk pengajuan mandiri).
 * Listing gabungan ada di HubController. Di sini hanya detail + interaksi.
 */
class InnovationController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected InovasiLampiranModel $lampiranModel;
    protected InovasiLikeModel $likeModel;
    protected InovasiBookmarkModel $bookmarkModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel  = new InovasiModel();
        $this->lampiranModel = new InovasiLampiranModel();
        $this->likeModel     = new InovasiLikeModel();
        $this->bookmarkModel = new InovasiBookmarkModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function detail(int $id)
    {
        $inovasi = $this->inovasiModel->select('inovasi.*, users.nama as nama_karyawan')
            ->join('users', 'users.id = inovasi.karyawan_id')->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $userId = session()->get('user_id');
        $data = [
            'inovasi'        => $inovasi,
            'lampiran'       => $this->lampiranModel->getFor($id),
            'sudah_like'     => $this->likeModel->sudahLike($id, $userId),
            'sudah_bookmark' => (bool) $this->bookmarkModel->where('inovasi_id', $id)->where('user_id', $userId)->first(),
        ];
        $this->inovasiModel->incrementView($id);
        $this->logModel->catat($userId, 'lihat_inovasi', 'inovasi', $id, $inovasi['judul']);
        return view('innovation/detail', $data);
    }

    public function like(int $id)
    {
        $userId  = session()->get('user_id');
        $isLiked = $this->likeModel->toggle($id, $userId);
        $this->inovasiModel->set('jumlah_like', $isLiked ? 'jumlah_like + 1' : 'jumlah_like - 1', false)->where('id', $id)->update();
        $this->logModel->catat($userId, $isLiked ? 'like_inovasi' : 'unlike_inovasi', 'inovasi', $id);
        $total = $this->inovasiModel->find($id)['jumlah_like'];
        return $this->response->setJSON(['liked' => $isLiked, 'total_like' => $total]);
    }

    public function bookmark(int $id)
    {
        $userId       = session()->get('user_id');
        $isBookmarked = $this->bookmarkModel->toggle($id, $userId);
        $this->logModel->catat($userId, $isBookmarked ? 'bookmark_inovasi' : 'unbookmark_inovasi', 'inovasi', $id);
        return $this->response->setJSON(['bookmarked' => $isBookmarked]);
    }

    public function streamCoverImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/covers/' . basename($filename);
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $mime = mime_content_type($path) ?: 'image/jpeg';
        return $this->response->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=86400')->setBody(file_get_contents($path));
    }
}
