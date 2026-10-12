<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambahkan kolom 'cover' (sampul) pada km_aktivitas, opsional/nullable,
 * agar entri Knowledge Management juga bisa tampil dengan sampul di modul
 * Library (seperti Inovasi), tanpa mewajibkan upload sampul.
 */
class AddCoverToKmAktivitas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('km_aktivitas', [
            'cover' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'file_dokumen',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('km_aktivitas', 'cover');
    }
}
