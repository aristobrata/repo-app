<?php
namespace App\Models;
use CodeIgniter\Model;
class KmAktivitasModel extends Model
{
    protected $table            = 'km_aktivitas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'bulan', 'pillar_km', 'aktivitas', 'activity_type', 'subactivity', 'judul_event',
        'tanggal_score', 'tanggal_create', 'tempat', 'nik', 'nip', 'nama_peserta',
        'direktorat', 'departemen', 'biro', 'org_unit', 'bidang', 'peran', 'poin',
        'jumlah_karyawan_bulanan', 'poin_corporate', 'file_dokumen', 'tahun',
    ];
    protected $useTimestamps = false; // created_at diisi manual saat import

    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword']))  { $builder = $builder->like('judul_event', $filters['keyword']); }
        if (!empty($filters['pillar']))   { $builder = $builder->where('pillar_km', $filters['pillar']); }
        if (!empty($filters['bulan']))    { $builder = $builder->where('bulan', $filters['bulan']); }
        if (!empty($filters['tahun']))    { $builder = $builder->where('tahun', $filters['tahun']); }
        if (!empty($filters['dept']))     { $builder = $builder->where('departemen', $filters['dept']); }
        return $builder->orderBy('tanggal_score', 'DESC');
    }

    /** KPI-01: total poin KM akumulasi */
    public function totalPoin(?string $tahun = null)
    {
        $b = $this->selectSum('poin');
        if ($tahun) { $b->where('tahun', $tahun); }
        $r = $b->first();
        return (int) ($r['poin'] ?? 0);
    }

    /** CHT-01: tren poin bulanan (line chart aktual) */
    public function trenPoinBulanan(?string $tahun = null)
    {
        $urutanBulan = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];
        $b = $this->select('bulan, SUM(poin) as total_poin')->groupBy('bulan');
        if ($tahun) { $b->where('tahun', $tahun); }
        $rows = $b->findAll();
        $map = [];
        foreach ($rows as $r) { $map[strtoupper($r['bulan'])] = (int) $r['total_poin']; }
        $hasil = [];
        foreach ($urutanBulan as $bln) { $hasil[] = ['bulan' => $bln, 'total_poin' => $map[$bln] ?? 0]; }
        return $hasil;
    }

    /** CHT-02: poin per pilar KM (bar chart) */
    public function poinPerPilar(?string $tahun = null)
    {
        $b = $this->select('pillar_km, SUM(poin) as total_poin')->where('pillar_km IS NOT NULL')->groupBy('pillar_km');
        if ($tahun) { $b->where('tahun', $tahun); }
        return $b->orderBy('total_poin', 'DESC')->findAll();
    }

    /** KPI-03: jumlah karyawan unik yang pernah berpartisipasi */
    public function jumlahPesertaAktif(?string $tahun = null)
    {
        $b = $this->select('nik')->where('nik IS NOT NULL')->distinct();
        if ($tahun) { $b->where('tahun', $tahun); }
        return $b->countAllResults();
    }
}
