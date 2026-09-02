<?php

namespace App\Models;

use CodeIgniter\Model;

class InovasiLikeModel extends Model
{
    protected $table         = 'inovasi_like';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['inovasi_id', 'user_id', 'created_at'];
    protected $useTimestamps = false;

    public function sudahLike(int $inovasiId, int $userId): bool
    {
        return (bool) $this->where('inovasi_id', $inovasiId)->where('user_id', $userId)->first();
    }

    /**
     * Toggle like: kembalikan true jika sekarang berstatus "liked", false jika "unliked".
     */
    public function toggle(int $inovasiId, int $userId): bool
    {
        $existing = $this->where('inovasi_id', $inovasiId)->where('user_id', $userId)->first();
        if ($existing) {
            $this->delete($existing['id']);
            return false;
        }
        $this->insert(['inovasi_id' => $inovasiId, 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')]);
        return true;
    }
}
