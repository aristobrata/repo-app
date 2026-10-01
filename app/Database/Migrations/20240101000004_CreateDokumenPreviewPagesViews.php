<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDokumenPreviewPagesViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'dokumen_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'halaman_ke'  => ['type' => 'INT', 'constraint' => 4],
            'file_gambar' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('dokumen_id');
        $this->forge->addForeignKey('dokumen_id', 'dokumen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumen_preview_pages');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'dokumen_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'viewed_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('dokumen_id');
        $this->forge->addForeignKey('dokumen_id', 'dokumen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_views');
    }
    public function down()
    {
        $this->forge->dropTable('document_views');
        $this->forge->dropTable('dokumen_preview_pages');
    }
}
