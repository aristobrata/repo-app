<?php
namespace App\Models;
use CodeIgniter\Model;
class KmTargetModel extends Model
{
    protected $table         = 'km_target';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['tahun', 'target_poin_tahunan'];
    protected $useTimestamps = false;
    public function getForTahun(string $tahun) { return $this->where('tahun', $tahun)->first(); }
}
