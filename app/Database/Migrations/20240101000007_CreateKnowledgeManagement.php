<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

/**
 * Struktur SESUAI file sumber Database_KM_2026_Clean.xlsx (3 sheet):
 * - fact_aktivitas_km : log tiap aktivitas KM per karyawan (Learn & Share, COP, dll) + poin
 * - dim_karyawan      : master data karyawan (dept/biro/org unit/bidang)
 * - rekap_karyawan    : rekap total poin & band per karyawan (dipakai leaderboard dashboard)
 *
 * `file_dokumen` pada km_aktivitas SENGAJA NULLABLE (tidak wajib) -- dipakai hanya jika
 * admin ingin lampirkan materi/dokumentasi, import massal dari Excel tidak perlu ini.
 */
class CreateKnowledgeManagement extends Migration
{
    public function up()
    {
        // ---- fact_aktivitas_km ----
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bulan'                 => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'pillar_km'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'LS, COP, PR, COI, PA'],
            'aktivitas'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'activity_type'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'subactivity'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'judul_event'           => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'tanggal_score'         => ['type' => 'DATE', 'null' => true],
            'tanggal_create'        => ['type' => 'DATE', 'null' => true],
            'tempat'                => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nik'                   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'nip'                   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'nama_peserta'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'direktorat'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'departemen'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'biro'                  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'org_unit'              => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'bidang'                => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'peran'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'Pembicara, Peserta'],
            'poin'                  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_karyawan_bulanan' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'poin_corporate'        => ['type' => 'DECIMAL', 'constraint' => '10,6', 'null' => true],
            'file_dokumen'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'TIDAK WAJIB - lampiran materi/dokumentasi opsional'],
            'tahun'                 => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nik');
        $this->forge->addKey('pillar_km');
        $this->forge->addKey('bulan');
        $this->forge->addKey('tahun');
        $this->forge->createTable('km_aktivitas');

        // ---- dim_karyawan ----
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'perner'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'comment' => 'NIK'],
            'id_number'        => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'comment' => 'NIP'],
            'personnel_number' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'Nama karyawan'],
            'direktorat'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'departemen'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_unit_kerja'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'biro'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'org_unit'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'bidang'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('perner');
        $this->forge->createTable('km_karyawan');

        // ---- rekap_karyawan (leaderboard) ----
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nik'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'nip'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'departemen'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'unit'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'seksi'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'total_poin'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'band'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'comment' => 'I, II, III, IV, V'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nik');
        $this->forge->addKey('band');
        $this->forge->createTable('km_rekap_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('km_rekap_karyawan');
        $this->forge->dropTable('km_karyawan');
        $this->forge->dropTable('km_aktivitas');
    }
}
