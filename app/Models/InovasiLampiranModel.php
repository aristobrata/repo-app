<?php

namespace App\Models;

use CodeIgniter\Model;

class InovasiLampiranModel extends Model
{
    protected $table         = 'inovasi_lampiran';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['inovasi_id', 'nama_file', 'tipe_file'];
    protected $useTimestamps = false;

    public function getFor(int $inovasiId)
    {
        return $this->where('inovasi_id', $inovasiId)->findAll();
    }
}
