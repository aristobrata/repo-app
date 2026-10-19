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

    /** Pencarian + filter Rekap: keyword (nama/NIK/NIP), departemen, band. Urut poin tertinggi (id sebagai pemecah seri supaya paginasi stabil). */
    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword'])) {
            $builder = $builder->groupStart()
                ->like('nama', $filters['keyword'])
                ->orLike('nik', $filters['keyword'])
                ->orLike('nip', $filters['keyword'])
                ->groupEnd();
        }
        if (!empty($filters['departemen'])) { $builder = $builder->where('departemen', $filters['departemen']); }
        if (!empty($filters['band']))       { $builder = $builder->where('band', $filters['band']); }
        return $builder->orderBy('total_poin', 'DESC')->orderBy('id', 'ASC');
    }

    public function daftarDepartemen(): array
    {
        $rows = $this->select('departemen')->distinct()
            ->where('departemen IS NOT NULL')->where('departemen !=', '')
            ->orderBy('departemen', 'ASC')->findAll();
        return array_column($rows, 'departemen');
    }

    /** Peringkat sebenarnya (global) sebuah baris: poin lebih tinggi + seri dengan id lebih kecil + 1. */
    public function peringkat(array $row): int
    {
        $lebihTinggi = $this->builder()->where('total_poin >', $row['total_poin'])->countAllResults();
        $seriDulu    = $this->builder()->where('total_poin', $row['total_poin'])->where('id <', $row['id'])->countAllResults();
        return $lebihTinggi + $seriDulu + 1;
    }

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
