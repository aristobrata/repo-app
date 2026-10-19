<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel master karyawan khusus modul Inovasi, terpisah dari master karyawan
 * Knowledge Management (km_karyawan). Struktur kolom mengikuti file Excel
 * "KOMPOSISI_SEPTEMBER_Diklat_SHE.xlsx" (sheet "SEPTEMBER") yang dipakai
 * sebagai master data karyawan untuk modul Inovasi.
 */
class CreateInovasiKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'perner'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'comment' => 'Pers No / NIK'],
            'id_number'        => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'comment' => 'ID Number / NIP'],
            'nama'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'Nama karyawan'],
            'band'             => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'jabatan'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'Position'],
            'gender'           => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'tempat_lahir'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_lahir'    => ['type' => 'DATE', 'null' => true],
            'direktorat'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'departemen_en'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'TXT_DEPT (Inggris)'],
            'departemen'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'DEPARTEMEN (Indonesia)'],
            'biro'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'TXT_BIRO'],
            'unit_kerja_en'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'UNIT (Inggris)'],
            'nama_unit_kerja'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'comment' => 'Nama Unit Kerja (Indonesia)'],
            'seksi'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('perner');
        $this->forge->addKey('nama');
        $this->forge->createTable('inovasi_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('inovasi_karyawan');
    }
}
