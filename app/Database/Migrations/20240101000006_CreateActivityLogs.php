<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => 'login, upload_dokumen, lihat_dokumen, like_inovasi, dll'],
            'target_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'dokumen, inovasi, user'],
            'target_id'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('activity_logs');
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');
    }
}
