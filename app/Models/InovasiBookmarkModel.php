<?php
namespace App\Models;
use CodeIgniter\Model;
class InovasiBookmarkModel extends Model
{
    protected $table         = 'inovasi_bookmark';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['inovasi_id', 'user_id', 'created_at'];
    protected $useTimestamps = false;
    public function toggle(int $inovasiId, int $userId): bool
    {
        $existing = $this->where('inovasi_id', $inovasiId)->where('user_id', $userId)->first();
        if ($existing) { $this->delete($existing['id']); return false; }
        $this->insert(['inovasi_id' => $inovasiId, 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')]);
        return true;
    }
    public function daftarBacaan(int $userId)
    {
        return $this->select('inovasi.*, users.nama as nama_karyawan')
            ->join('inovasi', 'inovasi.id = inovasi_bookmark.inovasi_id')
            ->join('users', 'users.id = inovasi.karyawan_id')
            ->where('inovasi_bookmark.user_id', $userId)
            ->orderBy('inovasi_bookmark.created_at', 'DESC')->findAll();
    }
}
