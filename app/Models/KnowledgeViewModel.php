<?php
namespace App\Models;
use CodeIgniter\Model;
class KnowledgeViewModel extends Model
{
    protected $table         = 'knowledge_views';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['knowledge_id', 'user_id', 'viewed_at'];
    protected $useTimestamps = false;
    public function catat(int $knowledgeId, int $userId)
    {
        return $this->insert(['knowledge_id' => $knowledgeId, 'user_id' => $userId, 'viewed_at' => date('Y-m-d H:i:s')]);
    }
}
