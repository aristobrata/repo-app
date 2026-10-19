<?php
namespace App\Models;
use CodeIgniter\Model;
class KmKaryawanModel extends Model
{
    protected $table         = 'km_karyawan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['perner', 'id_number', 'personnel_number', 'direktorat', 'departemen', 'nama_unit_kerja', 'biro', 'org_unit', 'bidang', 'band'];
    protected $useTimestamps = false;

    /** Pencarian + filter Master Karyawan: keyword (nama/NIK/NIP), departemen, band. */
    public function daftar(array $filters = [])
    {
        $builder = $this;
        if (!empty($filters['keyword'])) {
            $builder = $builder->groupStart()
                ->like('personnel_number', $filters['keyword'])
                ->orLike('perner', $filters['keyword'])
                ->orLike('id_number', $filters['keyword'])
                ->groupEnd();
        }
        if (!empty($filters['departemen'])) { $builder = $builder->where('departemen', $filters['departemen']); }
        if (!empty($filters['band'])) {
            if ($filters['band'] === '-') { $builder = $builder->groupStart()->where('band IS NULL')->orWhere('band', '')->groupEnd(); }
            else { $builder = $builder->where('band', $filters['band']); }
        }
        return $builder->orderBy('personnel_number', 'ASC');
    }

    public function daftarDepartemen(): array
    {
        $rows = $this->select('departemen')->distinct()
            ->where('departemen IS NOT NULL')->where('departemen !=', '')
            ->orderBy('departemen', 'ASC')->findAll();
        return array_column($rows, 'departemen');
    }

    public function totalKaryawan(): int
    {
        return $this->countAll();
    }

    /** Total seluruh karyawan per Band (master), dipakai sebagai penyebut Participation Rate per Band. */
    public function totalPerBand()
    {
        return $this->select('band, COUNT(id) as total')->where('band IS NOT NULL')->groupBy('band')->orderBy('band', 'ASC')->findAll();
    }

    /**
     * Isi kolom band di km_karyawan dari km_rekap_karyawan (relasi: NIK = perner,
     * cadangan: NIP = id_number untuk yang NIK-nya tidak cocok). Hanya mengisi
     * baris yang band-nya masih kosong, kecuali $timpa = true.
     * Mengembalikan jumlah baris yang terisi.
     */
    public function sinkronBandDariRekap(bool $timpa = false): int
    {
        $db = $this->db;
        if (!$db->fieldExists('band', 'km_karyawan')) {
            $db->query('ALTER TABLE km_karyawan ADD COLUMN band VARCHAR(20) NULL AFTER bidang');
        }
        $kondisi = $timpa ? '' : " AND (k.band IS NULL OR k.band = '')";
        $total = 0;

        // 1) cocokkan lewat NIK
        $db->query("UPDATE km_karyawan k JOIN km_rekap_karyawan r ON r.nik = k.perner
                    SET k.band = r.band
                    WHERE r.band IS NOT NULL AND r.band <> '' AND k.perner IS NOT NULL AND k.perner <> ''" . $kondisi);
        $total += $db->affectedRows();

        // 2) sisanya lewat NIP
        $db->query("UPDATE km_karyawan k JOIN km_rekap_karyawan r ON r.nip = k.id_number
                    SET k.band = r.band
                    WHERE r.band IS NOT NULL AND r.band <> '' AND k.id_number IS NOT NULL AND k.id_number <> ''
                    AND (k.band IS NULL OR k.band = '')");
        $total += $db->affectedRows();

        return $total;
    }
}
