<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nip'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'password'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'divisi'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'role'        => ['type' => 'ENUM', 'constraint' => ['super_admin', 'admin', 'karyawan'], 'default' => 'karyawan'],
            'status'      => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif'],
            'reset_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }
    public function down() { $this->forge->dropTable('users'); }
}
