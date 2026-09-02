<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk membuat akun Super Admin pertama kali.
 * Jalankan: php spark db:seed InitialAdminSeeder
 */
class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nip'        => '0000000001',
            'nama'       => 'Super Admin',
            'email'      => 'admin@perusahaan.local',
            'password'   => password_hash('admin12345', PASSWORD_DEFAULT), // GANTI setelah login pertama!
            'divisi'     => 'IT / Knowledge Management',
            'role'       => 'super_admin',
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Cegah duplikat jika seeder dijalankan lebih dari sekali
        $existing = $this->db->table('users')->where('email', $data['email'])->get()->getRow();
        if (!$existing) {
            $this->db->table('users')->insert($data);
            echo "Super Admin dibuat: {$data['email']} / password: admin12345 (segera ganti!)\n";
        } else {
            echo "Super Admin sudah ada, seeder dilewati.\n";
        }
    }
}
