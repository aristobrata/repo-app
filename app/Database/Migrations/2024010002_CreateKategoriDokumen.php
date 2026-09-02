<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriDokumen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kategori_dokumen');

        // seed default categories
        $this->db->table('kategori_dokumen')->insertBatch([
            ['nama' => 'Laporan Internal', 'slug' => 'laporan-internal', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'E-Book',           'slug' => 'e-book',           'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'SOP',              'slug' => 'sop',              'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Jurnal Inovasi',   'slug' => 'jurnal-inovasi',   'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Dokumen Teknis',   'slug' => 'dokumen-teknis',   'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('kategori_dokumen');
    }
}
