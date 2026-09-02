<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInovasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'karyawan_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'divisi'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'deskripsi_masalah'  => ['type' => 'TEXT'],
            'solusi_inovatif'    => ['type' => 'TEXT'],
            'dampak_manfaat'     => ['type' => 'TEXT'],
            'foto_ilustrasi'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'             => ['type' => 'ENUM', 'constraint' => ['diajukan', 'diverifikasi', 'diterapkan'], 'default' => 'diajukan'],
            'jumlah_like'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_view'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('karyawan_id');
        $this->forge->createTable('inovasi');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inovasi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_file'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'tipe_file'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('inovasi_id');
        $this->forge->addForeignKey('inovasi_id', 'inovasi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inovasi_lampiran');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inovasi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['inovasi_id', 'user_id']);
        $this->forge->createTable('inovasi_like');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inovasi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['inovasi_id', 'user_id']);
        $this->forge->createTable('inovasi_bookmark');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inovasi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'viewed_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('innovation_views');
    }

    public function down()
    {
        $this->forge->dropTable('innovation_views');
        $this->forge->dropTable('inovasi_bookmark');
        $this->forge->dropTable('inovasi_like');
        $this->forge->dropTable('inovasi_lampiran');
        $this->forge->dropTable('inovasi');
    }
}
