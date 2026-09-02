<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'action', 'target_type', 'target_id', 'keterangan', 'ip_address', 'created_at'];
    protected $useTimestamps = false;

    public function catat(?int $userId, string $action, ?string $targetType = null, ?int $targetId = null, ?string $keterangan = null)
    {
        return $this->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'keterangan'  => $keterangan,
            'ip_address'  => service('request')->getIPAddress(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function terbaru(int $limit = 50)
    {
        return $this->select('activity_logs.*, users.nama as nama_user')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->findAll($limit);
    }
}
