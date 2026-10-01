<?php
namespace App\Models;
use CodeIgniter\Model;
class InovasiModel extends Model
{
    protected $table            = 'inovasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 'karyawan_id', 'divisi', 'deskripsi_masalah', 'solusi_inovatif',
        'dampak_manfaat', 'foto_ilustrasi', 'status', 'jumlah_like', 'jumlah_view',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules = [
        'judul'             => 'required|min_length[5]',
        'deskripsi_masalah' => 'required',
        'solusi_inovatif'   => 'required',
        'dampak_manfaat'    => 'required',
    ];

    public function direktori(array $filters = [])
    {
        $builder = $this->select('inovasi.*, users.nama as nama_karyawan')
            ->join('users', 'users.id = inovasi.karyawan_id');
        if (!empty($filters['divisi'])) { $builder->where('inovasi.divisi', $filters['divisi']); }
        if (!empty($filters['tahun']))  { $builder->where('YEAR(inovasi.created_at)', $filters['tahun']); }
        if (!empty($filters['status'])) { $builder->where('inovasi.status', $filters['status']); }
        return $builder->orderBy('inovasi.created_at', 'DESC');
    }

    public function incrementView(int $id) { $this->set('jumlah_view', 'jumlah_view + 1', false)->where('id', $id)->update(); }
    public function topDilihat(int $limit = 10) { return $this->orderBy('jumlah_view', 'DESC')->findAll($limit); }
}
