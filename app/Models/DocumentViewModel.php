<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentViewModel extends Model
{
    protected $table         = 'document_views';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['dokumen_id', 'user_id', 'viewed_at'];
    protected $useTimestamps = false;

    public function catat(int $dokumenId, int $userId)
    {
        return $this->insert([
            'dokumen_id' => $dokumenId,
            'user_id'    => $userId,
            'viewed_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /** Tren aktivitas per hari, 30 hari terakhir */
    public function trenHarian(int $hari = 30)
    {
        return $this->select("DATE(viewed_at) as tanggal, COUNT(*) as total")
            ->where('viewed_at >=', date('Y-m-d', strtotime("-{$hari} days")))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }
}
