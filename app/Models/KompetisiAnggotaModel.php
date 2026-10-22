<?php
namespace App\Models;
use CodeIgniter\Model;

class KompetisiAnggotaModel extends Model
{
    protected $table         = 'kompetisi_inovasi_anggota';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kompetisi_id', 'nik', 'nama_karyawan'];
    protected $useTimestamps = false;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        new KompetisiInovasiModel(); // memastikan kedua tabel sudah ada
    }

    public function getFor(int $kompetisiId): array
    {
        return $this->where('kompetisi_id', $kompetisiId)->orderBy('id', 'ASC')->findAll();
    }
}
