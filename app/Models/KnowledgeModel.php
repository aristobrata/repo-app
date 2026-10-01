<?php
namespace App\Models;
use CodeIgniter\Model;
class KnowledgeModel extends Model
{
    protected $table            = 'knowledge_hub';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 'penulis_id', 'divisi', 'topik', 'ringkasan', 'konten',
        'referensi', 'foto_sampul', 'status', 'jumlah_like', 'jumlah_view',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules = [
        'judul'     => 'required|min_length[5]',
        'ringkasan' => 'required',
        'konten'    => 'required',
    ];

    public function direktori(array $filters = [])
    {
        $builder = $this->select('knowledge_hub.*, users.nama as nama_penulis')
            ->join('users', 'users.id = knowledge_hub.penulis_id');
        if (!empty($filters['divisi'])) { $builder->where('knowledge_hub.divisi', $filters['divisi']); }
        if (!empty($filters['topik']))  { $builder->where('knowledge_hub.topik', $filters['topik']); }
        if (!empty($filters['status'])) { $builder->where('knowledge_hub.status', $filters['status']); }
        else { $builder->where('knowledge_hub.status', 'dipublikasikan'); } // default: karyawan hanya lihat yang published
        return $builder->orderBy('knowledge_hub.created_at', 'DESC');
    }

    public function incrementView(int $id) { $this->set('jumlah_view', 'jumlah_view + 1', false)->where('id', $id)->update(); }
    public function topDilihat(int $limit = 10) { return $this->orderBy('jumlah_view', 'DESC')->findAll($limit); }
}
