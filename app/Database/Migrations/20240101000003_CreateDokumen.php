<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDokumen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kategori_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'judul'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'penulis'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'tahun'             => ['type' => 'YEAR'],
            'kata_kunci'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'abstrak'           => ['type' => 'TEXT', 'null' => true],
            'nomor_dokumen'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_berlaku'   => ['type' => 'DATE', 'null' => true],
            'file_asli'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'jumlah_halaman'    => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'halaman_preview'   => ['type' => 'INT', 'constraint' => 3, 'default' => 5],
            'cover_thumbnail'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ukuran_file'       => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status_preview'    => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'ready', 'failed'], 'default' => 'pending'],
            'jumlah_view'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'uploaded_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kategori_id');
        $this->forge->addForeignKey('kategori_id', 'kategori_dokumen', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('dokumen');
        $this->db->query('ALTER TABLE dokumen ADD FULLTEXT KEY ft_search (judul, penulis, kata_kunci, abstrak)');
    }
    public function down() { $this->forge->dropTable('dokumen'); }
}
