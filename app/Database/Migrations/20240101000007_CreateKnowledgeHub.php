<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

/**
 * Knowledge Management Hub - struktur MIRIP Innovation Hub (kelola konten pengetahuan
 * internal seperti best practice, tips teknis, pembelajaran/lesson learned) dengan CRUD
 * penuh oleh Admin/Super Admin, bisa dilihat/like/bookmark oleh karyawan.
 */
class CreateKnowledgeHub extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'penulis_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'divisi'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'topik'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'mis. Best Practice, Lesson Learned, Tips Teknis, SOP Ringkas'],
            'ringkasan'       => ['type' => 'TEXT', 'comment' => 'ringkasan singkat, tampil di listing'],
            'konten'          => ['type' => 'TEXT', 'comment' => 'isi lengkap pengetahuan'],
            'referensi'       => ['type' => 'TEXT', 'null' => true, 'comment' => 'sumber/referensi terkait'],
            'foto_sampul'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft', 'dipublikasikan', 'diarsipkan'], 'default' => 'dipublikasikan'],
            'jumlah_like'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_view'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('penulis_id');
        $this->forge->createTable('knowledge_hub');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'knowledge_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_file'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'tipe_file'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('knowledge_id');
        $this->forge->addForeignKey('knowledge_id', 'knowledge_hub', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('knowledge_lampiran');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'knowledge_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'   => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['knowledge_id', 'user_id']);
        $this->forge->createTable('knowledge_like');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'knowledge_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'   => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['knowledge_id', 'user_id']);
        $this->forge->createTable('knowledge_bookmark');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'knowledge_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'viewed_at'    => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('knowledge_views');
    }

    public function down()
    {
        $this->forge->dropTable('knowledge_views');
        $this->forge->dropTable('knowledge_bookmark');
        $this->forge->dropTable('knowledge_like');
        $this->forge->dropTable('knowledge_lampiran');
        $this->forge->dropTable('knowledge_hub');
    }
}
