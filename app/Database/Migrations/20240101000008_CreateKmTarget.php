<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

/**
 * Target tahunan poin KM -- dipakai untuk menghitung "% Pencapaian vs Target" di
 * dashboard (KPI-02). Tidak ada di file sumber Excel (hanya data aktual), jadi
 * dibuat tabel pengaturan terpisah yang bisa diisi admin per tahun.
 */
class CreateKmTarget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun'               => ['type' => 'VARCHAR', 'constraint' => 10],
            'target_poin_tahunan' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tahun');
        $this->forge->createTable('km_target');
    }
    public function down() { $this->forge->dropTable('km_target'); }
}
