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
        'kategori_inovasi', 'tanggal_registrasi', 'nama_tim', 'judul_inovasi', 'area_improvement',
        'unit_dept_area_implementasi', 'unit_biro_area_implementasi', 'biaya_project', 'saving',
        'opp_lost', 'revenue', 'total_benefit', 'status_saat_ini', 'keterangan',
        'hyperlink_dokumen', 'file_dokumen', 'cover_thumbnail', 'tahun', 'jumlah_like', 'jumlah_view',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules = ['judul_inovasi' => 'required|min_length[3]'];

    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword']))  { $builder = $builder->like('judul_inovasi', $filters['keyword']); }
        if (!empty($filters['kategori'])) { $builder = $builder->where('kategori_inovasi', $filters['kategori']); }
        if (!empty($filters['tahun']))    { $builder = $builder->where('tahun', $filters['tahun']); }
        if (!empty($filters['dept']))     { $builder = $builder->where('unit_dept_area_implementasi', $filters['dept']); }
        return $builder->orderBy('created_at', 'DESC');
    }

    public function incrementView(int $id) { $this->set('jumlah_view', 'jumlah_view + 1', false)->where('id', $id)->update(); }

    /** Untuk dashboard: jumlah inovasi per departemen (CHT-03) */
    public function jumlahPerDept(?string $tahun = null)
    {
        $b = $this->select('unit_dept_area_implementasi as dept, COUNT(id) as total')
            ->where('unit_dept_area_implementasi IS NOT NULL');
        if ($tahun) { $b->where('tahun', $tahun); }
        return $b->groupBy('unit_dept_area_implementasi')->orderBy('total', 'DESC')->findAll(10);
    }

    /** Untuk dashboard: jumlah inovasi per kategori (FI, KOMET, TPP, dst) */
    public function jumlahPerKategori(?string $tahun = null)
    {
        $b = $this->select('kategori_inovasi as kategori, COUNT(id) as total')->where('kategori_inovasi IS NOT NULL');
        if ($tahun) { $b->where('tahun', $tahun); }
        return $b->groupBy('kategori_inovasi')->orderBy('total', 'DESC')->findAll();
    }

    public function totalBenefitTahun(?string $tahun = null)
    {
        $b = $this->selectSum('total_benefit');
        if ($tahun) { $b->where('tahun', $tahun); }
        $r = $b->first();
        return (float) ($r['total_benefit'] ?? 0);
    }
}
