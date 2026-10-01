<?php
namespace App\Models;
use CodeIgniter\Model;
class KnowledgeLikeModel extends Model
{
    protected $table         = 'knowledge_like';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['knowledge_id', 'user_id', 'created_at'];
    protected $useTimestamps = false;
    public function sudahLike(int $knowledgeId, int $userId): bool { return (bool) $this->where('knowledge_id', $knowledgeId)->where('user_id', $userId)->first(); }
    public function toggle(int $knowledgeId, int $userId): bool
    {
        $existing = $this->where('knowledge_id', $knowledgeId)->where('user_id', $userId)->first();
        if ($existing) { $this->delete($existing['id']); return false; }
        $this->insert(['knowledge_id' => $knowledgeId, 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')]);
        return true;
    }
}
