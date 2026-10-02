<?php
namespace App\Models;
use CodeIgniter\Model;
class KmKaryawanModel extends Model
{
    protected $table         = 'km_karyawan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['perner', 'id_number', 'personnel_number', 'direktorat', 'departemen', 'nama_unit_kerja', 'biro', 'org_unit', 'bidang'];
    protected $useTimestamps = false;
}
