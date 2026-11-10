<?php
namespace App\Libraries;

/**
 * Pencegah data ganda saat import Aktivitas KM.
 *
 * Kunci baris = nik, judul_event, tanggal_score, peran, tempat, pillar_km, aktivitas, subactivity, poin
 * (huruf besar/kecil dan spasi ganda diabaikan). Dicocokkan DENGAN JUMLAH KEMUNCULAN: bila di database sudah
 * ada 1 baris dengan kunci X dan file berisi 2 baris berkunci X, maka 1 dilewati dan 1 ditambahkan.
 * Dengan begitu import ulang file yang sama tidak menambah apa pun, tetapi baris kembar yang memang sah
 * di dalam satu file tetap masuk.
 */
class AktivitasDuplikat
{
    private array $sisa = [];

    public function __construct($db)
    {
        $q = $db->query('SELECT nik, judul_event, tanggal_score, peran, tempat, pillar_km, aktivitas, subactivity, poin FROM km_aktivitas');
        foreach ($q->getResultArray() as $r) {
            $k = self::kunci($r);
            $this->sisa[$k] = ($this->sisa[$k] ?? 0) + 1;
        }
    }

    public static function kunci(array $r): string
    {
        $n = fn($v) => mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) ($v ?? ''))));
        return implode('|', [
            $n($r['nik'] ?? null), $n($r['judul_event'] ?? null), $n($r['tanggal_score'] ?? null), $n($r['peran'] ?? null),
            $n($r['tempat'] ?? null), $n($r['pillar_km'] ?? null), $n($r['aktivitas'] ?? null), $n($r['subactivity'] ?? null),
            (string) (float) ($r['poin'] ?? 0),
        ]);
    }

    /** true bila baris ini sudah ada di database (dan menandainya terpakai); false bila baris baru. */
    public function sudahAda(array $data): bool
    {
        $k = self::kunci($data);
        if (($this->sisa[$k] ?? 0) > 0) { $this->sisa[$k]--; return true; }
        return false;
    }
}
