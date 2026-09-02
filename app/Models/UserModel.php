<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nip', 'nama', 'email', 'password', 'divisi', 'role', 'status', 'reset_token',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'  => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'role'  => 'required|in_list[super_admin,admin,karyawan]',
    ];

    /**
     * Set password otomatis di-hash setiap kali insert/update melalui model ini.
     */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']); // jangan overwrite password lama dengan kosong
        }
        return $data;
    }

    public function findActiveByEmail(string $email)
    {
        return $this->where('email', $email)->where('status', 'aktif')->first();
    }
}
