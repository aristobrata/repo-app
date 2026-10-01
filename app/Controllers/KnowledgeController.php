<?php
namespace App\Controllers;
use App\Models\KnowledgeModel;
use App\Models\KnowledgeLampiranModel;
use App\Models\KnowledgeLikeModel;
use App\Models\KnowledgeBookmarkModel;
use App\Models\KnowledgeViewModel;
use App\Models\ActivityLogModel;

/**
 * Controller sisi karyawan untuk Knowledge Management Hub.
 * Listing gabungan ada di HubController. Di sini hanya detail + interaksi.
 */
class KnowledgeController extends BaseController
{
    protected KnowledgeModel $knowledgeModel;
    protected KnowledgeLampiranModel $lampiranModel;
    protected KnowledgeLikeModel $likeModel;
    protected KnowledgeBookmarkModel $bookmarkModel;
    protected KnowledgeViewModel $viewModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->knowledgeModel = new KnowledgeModel();
        $this->lampiranModel  = new KnowledgeLampiranModel();
        $this->likeModel      = new KnowledgeLikeModel();
        $this->bookmarkModel  = new KnowledgeBookmarkModel();
        $this->viewModel      = new KnowledgeViewModel();
        $this->logModel       = new ActivityLogModel();
    }

    public function detail(int $id)
    {
        $item = $this->knowledgeModel->select('knowledge_hub.*, users.nama as nama_penulis')
            ->join('users', 'users.id = knowledge_hub.penulis_id')->find($id);
        if (!$item) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $userId = session()->get('user_id');
        $data = [
            'item'           => $item,
            'lampiran'       => $this->lampiranModel->getFor($id),
            'sudah_like'     => $this->likeModel->sudahLike($id, $userId),
            'sudah_bookmark' => (bool) $this->bookmarkModel->where('knowledge_id', $id)->where('user_id', $userId)->first(),
        ];
        $this->knowledgeModel->incrementView($id);
        $this->viewModel->catat($id, $userId);
        $this->logModel->catat($userId, 'lihat_knowledge', 'knowledge', $id, $item['judul']);
        return view('knowledge/detail', $data);
    }

    public function like(int $id)
    {
        $userId  = session()->get('user_id');
        $isLiked = $this->likeModel->toggle($id, $userId);
        $this->knowledgeModel->set('jumlah_like', $isLiked ? 'jumlah_like + 1' : 'jumlah_like - 1', false)->where('id', $id)->update();
        $this->logModel->catat($userId, $isLiked ? 'like_knowledge' : 'unlike_knowledge', 'knowledge', $id);
        $total = $this->knowledgeModel->find($id)['jumlah_like'];
        return $this->response->setJSON(['liked' => $isLiked, 'total_like' => $total]);
    }

    public function bookmark(int $id)
    {
        $userId       = session()->get('user_id');
        $isBookmarked = $this->bookmarkModel->toggle($id, $userId);
        $this->logModel->catat($userId, $isBookmarked ? 'bookmark_knowledge' : 'unbookmark_knowledge', 'knowledge', $id);
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
