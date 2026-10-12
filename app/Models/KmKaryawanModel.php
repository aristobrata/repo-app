<?php
namespace App\Models;
use CodeIgniter\Model;
class KmKaryawanModel extends Model
{
    protected $table         = 'km_karyawan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['perner', 'id_number', 'personnel_number', 'direktorat', 'departemen', 'nama_unit_kerja', 'biro', 'org_unit', 'bidang', 'band'];
    protected $useTimestamps = false;

    public function totalKaryawan(): int
    {
        return $this->countAll();
    }

    /** Total seluruh karyawan per Band (master), dipakai sebagai penyebut Participation Rate per Band. */
    public function totalPerBand()
    {
        return $this->select('band, COUNT(id) as total')->where('band IS NOT NULL')->groupBy('band')->orderBy('band', 'ASC')->findAll();
    }
}
