<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * Master karyawan untuk modul Inovasi (terpisah dari km_karyawan milik modul
 * Knowledge Management). Sumber data awal: file Excel
 * "KOMPOSISI_SEPTEMBER_Diklat_SHE.xlsx" sheet "SEPTEMBER".
 */
class InovasiKaryawanModel extends Model
{
    protected $table            = 'inovasi_karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'perner', 'id_number', 'nama', 'band', 'jabatan', 'gender', 'tempat_lahir', 'tanggal_lahir',
        'direktorat', 'departemen_en', 'departemen', 'biro', 'unit_kerja_en', 'nama_unit_kerja', 'seksi',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules = ['nama' => 'required|min_length[3]'];

    /**
     * Buat tabel otomatis bila belum ada (supaya modul langsung jalan tanpa
     * harus menjalankan migration / SQL manual terlebih dahulu).
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->pastikanTabel();
    }

    private function pastikanTabel(): void
    {
        if ($this->db->tableExists($this->table)) { return; }
        $this->db->query("CREATE TABLE IF NOT EXISTS `inovasi_karyawan` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `perner` VARCHAR(30) NULL, `id_number` VARCHAR(30) NULL, `nama` VARCHAR(150) NULL,
            `band` VARCHAR(10) NULL, `jabatan` VARCHAR(150) NULL, `gender` VARCHAR(10) NULL,
            `tempat_lahir` VARCHAR(100) NULL, `tanggal_lahir` DATE NULL,
            `direktorat` VARCHAR(150) NULL, `departemen_en` VARCHAR(150) NULL, `departemen` VARCHAR(150) NULL,
            `biro` VARCHAR(150) NULL, `unit_kerja_en` VARCHAR(150) NULL, `nama_unit_kerja` VARCHAR(150) NULL,
            `seksi` VARCHAR(150) NULL, `created_at` DATETIME NULL, `updated_at` DATETIME NULL,
            KEY `perner` (`perner`), KEY `nama` (`nama`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword'])) {
            $builder = $builder->groupStart()
                ->like('nama', $filters['keyword'])
                ->orLike('perner', $filters['keyword'])
                ->orLike('id_number', $filters['keyword'])
                ->groupEnd();
        }
        if (!empty($filters['departemen'])) { $builder = $builder->where('departemen', $filters['departemen']); }
        if (!empty($filters['band']))       { $builder = $builder->where('band', $filters['band']); }
        return $builder->orderBy('nama', 'ASC');
    }

    public function totalKaryawan(): int
    {
        return $this->countAll();
    }

    public function totalPerBand()
    {
        return $this->select('band, COUNT(id) as total')->where('band IS NOT NULL')->groupBy('band')->orderBy('band', 'ASC')->findAll();
    }

    /** Daftar departemen unik, untuk dropdown filter. */
    public function daftarDepartemen(): array
    {
        $rows = $this->select('departemen')->distinct()
            ->where('departemen IS NOT NULL')->where('departemen !=', '')
            ->orderBy('departemen', 'ASC')->findAll();
        return array_column($rows, 'departemen');
    }
}
