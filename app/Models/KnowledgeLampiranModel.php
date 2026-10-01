<?php
namespace App\Models;
use CodeIgniter\Model;
class KnowledgeLampiranModel extends Model
{
    protected $table         = 'knowledge_lampiran';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['knowledge_id', 'nama_file', 'tipe_file'];
    protected $useTimestamps = false;
    public function getFor(int $knowledgeId) { return $this->where('knowledge_id', $knowledgeId)->findAll(); }
}
