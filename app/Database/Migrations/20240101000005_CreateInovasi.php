<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

/**
 * Struktur SESUAI file sumber DATABASE_INOVASI_BERSIH.xlsx.
 * Data asli berbentuk 1 baris per anggota tim (7.891 baris = 1.903 inovasi unik),
 * sehingga dipecah menjadi 2 tabel: `inovasi` (header/ringkasan 1x per inovasi)
 * dan `inovasi_anggota_tim` (banyak baris per inovasi).
 *
 * `file_dokumen` SENGAJA dibuat NULLABLE (tidak wajib) karena data lama memakai
 * `hyperlink_dokumen` (link/path referensi, bukan file upload) dan import massal
 * dari Excel tidak menyertakan file fisik.
 */
class CreateInovasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kategori_inovasi'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'FI, TPP, PKM, GKM, SS, 5P, dll'],
            'tanggal_registrasi'          => ['type' => 'DATE', 'null' => true],
            'nama_tim'                    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'judul_inovasi'               => ['type' => 'VARCHAR', 'constraint' => 500],
            'area_improvement'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'unit_dept_area_implementasi' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'unit_biro_area_implementasi' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'biaya_project'               => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'saving'                      => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'opp_lost'                    => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'revenue'                     => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'total_benefit'               => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'status_saat_ini'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'mis. Jalan, Stop, dll'],
            'keterangan'                  => ['type' => 'TEXT', 'null' => true],
            'hyperlink_dokumen'           => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'comment' => 'referensi path/link dokumen lama (data historis)'],
            'file_dokumen'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'TIDAK WAJIB - upload file baru (opsional), di writable/uploads/originals'],
            'cover_thumbnail'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tahun'                       => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'jumlah_like'                 => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_view'                 => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'                  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tahun');
        $this->forge->addKey('kategori_inovasi');
        $this->forge->createTable('inovasi');

        // Anggota tim - banyak baris per inovasi (Ketua, Sekretaris, Anggota, dst)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'inovasi_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_personil'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'nik'            => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'struktur_tim'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'Ketua, Sekretaris, Anggota'],
            'org_unit'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('inovasi_id');
        $this->forge->addForeignKey('inovasi_id', 'inovasi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inovasi_anggota_tim');

        // Lampiran tambahan opsional (selain file_dokumen utama)
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

        // Interaksi ringan (apresiasi & log dilihat) - tetap relevan untuk engagement karyawan
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
            'viewed_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('innovation_views');
    }

    public function down()
    {
        $this->forge->dropTable('innovation_views');
        $this->forge->dropTable('inovasi_like');
        $this->forge->dropTable('inovasi_lampiran');
        $this->forge->dropTable('inovasi_anggota_tim');
        $this->forge->dropTable('inovasi');
    }
}
