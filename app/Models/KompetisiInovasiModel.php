<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * Modul "Kompetisi Inovasi": inovasi yang diikutsertakan dalam ajang kompetisi
 * dan memperoleh penghargaan (sumber awal: kompetisi_inovasi_database_ready.xlsx).
 * Satu baris = satu entri kompetisi (tim + judul + event + penghargaan);
 * personil/pesertanya ada di tabel kompetisi_inovasi_anggota.
 *
 * Kedua tabel dibuat otomatis jika belum ada, jadi modul langsung jalan tanpa
 * perlu menjalankan migration/SQL manual.
 */
class KompetisiInovasiModel extends Model
{
    protected $table            = 'kompetisi_inovasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_tim', 'judul_inovasi', 'jenis_penghargaan', 'event_kompetisi', 'penyelenggara',
        'tanggal_penghargaan', 'tahun', 'keterangan', 'link_dokumen', 'file_dokumen', 'cover_thumbnail', 'jumlah_view',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->pastikanTabel();
    }

    private function pastikanTabel(): void
    {
        if ($this->db->tableExists($this->table)) { return; }
        $this->db->query("CREATE TABLE IF NOT EXISTS `kompetisi_inovasi` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nama_tim` VARCHAR(255) NULL,
            `judul_inovasi` VARCHAR(500) NOT NULL,
            `jenis_penghargaan` VARCHAR(50) NULL,
            `event_kompetisi` VARCHAR(150) NULL,
            `penyelenggara` VARCHAR(150) NULL,
            `tanggal_penghargaan` DATE NULL,
            `tahun` VARCHAR(10) NULL,
            `keterangan` TEXT NULL,
            `link_dokumen` VARCHAR(500) NULL,
            `file_dokumen` VARCHAR(255) NULL,
            `cover_thumbnail` VARCHAR(255) NULL,
            `jumlah_view` INT NOT NULL DEFAULT 0,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            KEY `idx_jenis` (`jenis_penghargaan`), KEY `idx_event` (`event_kompetisi`), KEY `idx_tahun` (`tahun`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->query("CREATE TABLE IF NOT EXISTS `kompetisi_inovasi_anggota` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `kompetisi_id` INT UNSIGNED NOT NULL,
            `nik` VARCHAR(30) NULL,
            `nama_karyawan` VARCHAR(150) NOT NULL,
            KEY `idx_kompetisi` (`kompetisi_id`), KEY `idx_nik` (`nik`),
            CONSTRAINT `fk_kompetisi_anggota` FOREIGN KEY (`kompetisi_id`) REFERENCES `kompetisi_inovasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /** Pencarian + filter: keyword (judul/tim/event), jenis penghargaan, event, tahun. */
    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword'])) {
            $builder = $builder->groupStart()
                ->like('judul_inovasi', $filters['keyword'])
                ->orLike('nama_tim', $filters['keyword'])
                ->orLike('event_kompetisi', $filters['keyword'])
                ->groupEnd();
        }
        if (!empty($filters['jenis']))  { $builder = $builder->where('jenis_penghargaan', $filters['jenis']); }
        if (!empty($filters['event']))  { $builder = $builder->where('event_kompetisi', $filters['event']); }
        if (!empty($filters['tahun']))  { $builder = $builder->where('tahun', $filters['tahun']); }
        return $builder->orderBy('tanggal_penghargaan', 'DESC')->orderBy('id', 'DESC');
    }

    /** Nilai unik sebuah kolom, untuk dropdown filter / saran isian. */
    public function nilaiUnik(string $kolom): array
    {
        if (!in_array($kolom, ['jenis_penghargaan', 'event_kompetisi', 'penyelenggara', 'tahun'], true)) { return []; }
        $rows = $this->select($kolom)->distinct()->where("$kolom IS NOT NULL")->where("$kolom !=", '')->orderBy($kolom, 'ASC')->findAll();
        return array_column($rows, $kolom);
    }

    public function incrementView(int $id): void
    {
        $this->set('jumlah_view', 'jumlah_view + 1', false)->where('id', $id)->update();
    }

    /**
     * Import baris Excel (sheet Data_Import, tanpa header). 1 baris = 1 personil;
     * baris dengan tim+judul+event+penghargaan+tanggal sama digabung jadi 1 entri.
     * Entri yang sudah ada dilewati. Mengembalikan [entri_baru, dilewati, personil].
     */
    public function importBaris(array $rows): array
    {
        $grup = [];
        foreach ($rows as $r) {
            $judul = trim((string) ($r[5] ?? ''));
            if ($judul === '') { continue; }
            $tanggal = $this->parseTanggal($r[8] ?? null);
            $kunci = implode('|', [trim((string) ($r[3] ?? '')), $judul, trim((string) ($r[6] ?? '')), strtoupper(trim((string) ($r[4] ?? ''))), (string) $tanggal]);
            if (!isset($grup[$kunci])) {
                $grup[$kunci] = ['header' => $r, 'tanggal' => $tanggal, 'link' => null, 'anggota' => []];
            }
            if (empty($grup[$kunci]['link']) && !empty($r[9])) { $grup[$kunci]['link'] = trim((string) $r[9]); }
            $nik  = trim((string) ($r[1] ?? ''));
            $nama = trim((string) ($r[2] ?? ''));
            if ($nama !== '') { $grup[$kunci]['anggota'][$nik . '|' . $nama] = ['nik' => $nik ?: null, 'nama' => $nama]; }
        }

        $anggotaModel = new KompetisiAnggotaModel();
        $this->db->transStart();
        $baru = 0; $dilewati = 0; $personil = 0;
        foreach ($grup as $g) {
            $h     = $g['header'];
            $jenis = strtoupper(trim((string) ($h[4] ?? ''))) ?: null;
            $q = $this->builder()->where('judul_inovasi', trim((string) $h[5]))
                ->where('nama_tim', $h[3] ?: null)->where('event_kompetisi', $h[6] ?: null)->where('jenis_penghargaan', $jenis);
            $g['tanggal'] ? $q->where('tanggal_penghargaan', $g['tanggal']) : $q->where('tanggal_penghargaan IS NULL');
            if ($q->countAllResults() > 0) { $dilewati++; continue; }

            $id = $this->insert([
                'nama_tim' => $h[3] ?: null, 'judul_inovasi' => trim((string) $h[5]), 'jenis_penghargaan' => $jenis,
                'event_kompetisi' => $h[6] ?: null, 'penyelenggara' => $h[7] ?: null,
                'tanggal_penghargaan' => $g['tanggal'], 'tahun' => $g['tanggal'] ? date('Y', strtotime($g['tanggal'])) : null,
                'link_dokumen' => $g['link'],
            ]);
            $baru++;
            foreach ($g['anggota'] as $a) {
                $anggotaModel->insert(['kompetisi_id' => $id, 'nik' => $a['nik'], 'nama_karyawan' => $a['nama']]);
                $personil++;
            }
        }
        $this->db->transComplete();
        return [$baru, $dilewati, $personil];
    }

    private function parseTanggal($v): ?string
    {
        if (empty($v)) { return null; }
        if ($v instanceof \DateTimeInterface) { return $v->format('Y-m-d'); }
        if (is_numeric($v)) { return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v)->format('Y-m-d'); }
        $ts = strtotime((string) $v);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}
