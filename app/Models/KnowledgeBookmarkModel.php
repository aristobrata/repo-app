<?php
namespace App\Models;
use CodeIgniter\Model;
class KnowledgeBookmarkModel extends Model
{
    protected $table         = 'knowledge_bookmark';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['knowledge_id', 'user_id', 'created_at'];
    protected $useTimestamps = false;
    public function toggle(int $knowledgeId, int $userId): bool
    {
        $existing = $this->where('knowledge_id', $knowledgeId)->where('user_id', $userId)->first();
        if ($existing) { $this->delete($existing['id']); return false; }
        $this->insert(['knowledge_id' => $knowledgeId, 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')]);
        return true;
    }
    public function daftarBacaan(int $userId)
    {
        return $this->select('knowledge_hub.*, users.nama as nama_penulis')
            ->join('knowledge_hub', 'knowledge_hub.id = knowledge_bookmark.knowledge_id')
            ->join('users', 'users.id = knowledge_hub.penulis_id')
            ->where('knowledge_bookmark.user_id', $userId)
            ->orderBy('knowledge_bookmark.created_at', 'DESC')->findAll();
    }
}
