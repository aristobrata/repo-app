<?php
namespace App\Models;
use CodeIgniter\Model;
class KmRekapKaryawanModel extends Model
{
    protected $table         = 'km_rekap_karyawan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nik', 'nip', 'nama', 'departemen', 'unit', 'seksi', 'total_poin', 'band'];
    protected $useTimestamps = false;

    /** LDB-01 Tab A: Top karyawan kontributor poin terbanyak */
    public function topKaryawan(int $limit = 10) { return $this->orderBy('total_poin', 'DESC')->findAll($limit); }

    /** LDB-01 Tab B: Top departemen (agregat dari rekap individu) */
    public function topDepartemen(int $limit = 5)
    {
        return $this->select('departemen, SUM(total_poin) as total_poin, COUNT(id) as jumlah_karyawan')
            ->where('departemen IS NOT NULL')->groupBy('departemen')
            ->orderBy('total_poin', 'DESC')->findAll($limit);
    }

    /** CHT-04: keterlibatan per band jabatan (donut chart) */
    public function keterlibatanPerBand()
    {
        return $this->select('band, COUNT(id) as total')->where('band IS NOT NULL')->groupBy('band')->orderBy('band', 'ASC')->findAll();
    }

    public function totalKaryawan() { return $this->countAll(); }
}
