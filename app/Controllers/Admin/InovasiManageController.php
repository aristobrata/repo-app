<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\InovasiModel;
use App\Models\InovasiAnggotaTimModel;
use App\Models\InovasiLampiranModel;
use App\Models\ActivityLogModel;

/**
 * CRUD + Import Excel untuk modul Inovasi.
 * File upload (file_dokumen) SENGAJA TIDAK WAJIB (permit_empty) karena:
 * 1. Data historis memakai hyperlink_dokumen (referensi path lama, bukan file fisik)
 * 2. Import massal dari Excel tidak menyertakan file fisik sama sekali
 */
class InovasiManageController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected InovasiAnggotaTimModel $timModel;
    protected InovasiLampiranModel $lampiranModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel  = new InovasiModel();
        $this->timModel      = new InovasiAnggotaTimModel();
        $this->lampiranModel = new InovasiLampiranModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'kategori', 'tahun']);
        $inovasi = $this->inovasiModel->daftar($filters)->paginate(20);
        return view('admin/innovations/index', ['inovasi' => $inovasi, 'pager' => $this->inovasiModel->pager, 'filters' => $filters]);
    }

    public function createForm() { return view('admin/innovations/create'); }

    private function handleCoverUpload(): ?string
    {
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $nama = $cover->getRandomName();
            $cover->move(WRITEPATH . 'uploads/covers', $nama);
            return $nama;
        }
        return null;
    }

    private function handleDokumenUpload(): ?string
    {
        $file = $this->request->getFile('file_dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $nama = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/originals', $nama);
            return $nama;
        }
        return null;
    }

    public function store()
    {
        $rules = [
            'judul_inovasi' => 'required|min_length[3]',
            'file_dokumen'  => 'permit_empty|max_size[file_dokumen,51200]', // TIDAK WAJIB
            'cover'         => 'permit_empty|max_size[cover,3072]|ext_in[cover,jpg,jpeg,png]',
        ];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $id = $this->inovasiModel->insert([
            'kategori_inovasi' => $this->request->getPost('kategori_inovasi'),
            'tanggal_registrasi' => $this->request->getPost('tanggal_registrasi') ?: null,
            'nama_tim' => $this->request->getPost('nama_tim'),
            'judul_inovasi' => $this->request->getPost('judul_inovasi'),
            'area_improvement' => $this->request->getPost('area_improvement'),
            'unit_dept_area_implementasi' => $this->request->getPost('unit_dept_area_implementasi'),
            'unit_biro_area_implementasi' => $this->request->getPost('unit_biro_area_implementasi'),
            'biaya_project' => $this->request->getPost('biaya_project') ?: 0,
            'saving' => $this->request->getPost('saving') ?: 0,
            'opp_lost' => $this->request->getPost('opp_lost') ?: 0,
            'revenue' => $this->request->getPost('revenue') ?: 0,
            'total_benefit' => $this->request->getPost('total_benefit') ?: 0,
            'status_saat_ini' => $this->request->getPost('status_saat_ini'),
            'keterangan' => $this->request->getPost('keterangan'),
            'hyperlink_dokumen' => $this->request->getPost('hyperlink_dokumen'),
            'file_dokumen' => $this->handleDokumenUpload(),
            'cover_thumbnail' => $this->handleCoverUpload(),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        $this->simpanAnggotaTim($id);
        $this->logModel->catat(session()->get('user_id'), 'tambah_inovasi', 'inovasi', $id);
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi berhasil ditambahkan.');
    }

    private function simpanAnggotaTim(int $inovasiId, bool $hapusDulu = false): void
    {
        if ($hapusDulu) { $this->timModel->where('inovasi_id', $inovasiId)->delete(); }
        $nama   = $this->request->getPost('anggota_nama') ?? [];
        $nik    = $this->request->getPost('anggota_nik') ?? [];
        $peran  = $this->request->getPost('anggota_peran') ?? [];
        $unit   = $this->request->getPost('anggota_unit') ?? [];
        foreach ($nama as $i => $namaPersonil) {
            if (trim((string) $namaPersonil) === '') { continue; }
            $this->timModel->insert([
                'inovasi_id' => $inovasiId, 'nama_personil' => $namaPersonil,
                'nik' => $nik[$i] ?? null, 'struktur_tim' => $peran[$i] ?? null, 'org_unit' => $unit[$i] ?? null,
            ]);
        }
    }

    public function editForm(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/innovations/edit', ['inovasi' => $inovasi, 'tim' => $this->timModel->getFor($id)]);
    }

    public function update(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $rules = ['judul_inovasi' => 'required|min_length[3]', 'file_dokumen' => 'permit_empty|max_size[file_dokumen,51200]', 'cover' => 'permit_empty|max_size[cover,3072]|ext_in[cover,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $dataUpdate = [
            'kategori_inovasi' => $this->request->getPost('kategori_inovasi'),
            'tanggal_registrasi' => $this->request->getPost('tanggal_registrasi') ?: null,
            'nama_tim' => $this->request->getPost('nama_tim'),
            'judul_inovasi' => $this->request->getPost('judul_inovasi'),
            'area_improvement' => $this->request->getPost('area_improvement'),
            'unit_dept_area_implementasi' => $this->request->getPost('unit_dept_area_implementasi'),
            'unit_biro_area_implementasi' => $this->request->getPost('unit_biro_area_implementasi'),
            'biaya_project' => $this->request->getPost('biaya_project') ?: 0,
            'saving' => $this->request->getPost('saving') ?: 0,
            'opp_lost' => $this->request->getPost('opp_lost') ?: 0,
            'revenue' => $this->request->getPost('revenue') ?: 0,
            'total_benefit' => $this->request->getPost('total_benefit') ?: 0,
            'status_saat_ini' => $this->request->getPost('status_saat_ini'),
            'keterangan' => $this->request->getPost('keterangan'),
            'hyperlink_dokumen' => $this->request->getPost('hyperlink_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
        ];
        $namaDokumenBaru = $this->handleDokumenUpload();
        if ($namaDokumenBaru) { $dataUpdate['file_dokumen'] = $namaDokumenBaru; }
        $namaCoverBaru = $this->handleCoverUpload();
        if ($namaCoverBaru) { $dataUpdate['cover_thumbnail'] = $namaCoverBaru; }

        $this->inovasiModel->update($id, $dataUpdate);
        $this->simpanAnggotaTim($id, true);
        $this->logModel->catat(session()->get('user_id'), 'edit_inovasi', 'inovasi', $id, $dataUpdate['judul_inovasi']);
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if ($inovasi) {
            foreach (['file_dokumen' => 'originals', 'cover_thumbnail' => 'covers'] as $field => $dir) {
                if (!empty($inovasi[$field])) {
                    $path = WRITEPATH . "uploads/{$dir}/" . $inovasi[$field];
                    if (is_file($path)) { unlink($path); }
                }
            }
            $this->inovasiModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_inovasi', 'inovasi', $id, $inovasi['judul_inovasi']);
        }
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi dihapus.');
    }

    // ==================== IMPORT EXCEL ====================
    public function importForm() { return view('admin/innovations/import'); }

    /**
     * Import massal dari file Excel dengan struktur SAMA PERSIS seperti
     * DATABASE_INOVASI_BERSIH.xlsx (kolom A-U, 1 baris = 1 anggota tim).
     * Baris dikelompokkan berdasarkan (nama_tim + judul_inovasi) menjadi 1 inovasi
     * dengan banyak anggota tim. Membutuhkan: composer require phpoffice/phpspreadsheet
     */
    public function import()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel tidak valid.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        array_shift($rows); // buang header

        $db = \Config\Database::connect();
        $db->transStart();

        $grup = [];
        foreach ($rows as $r) {
            if (empty($r[4])) { continue; } // kolom E = judul_inovasi, wajib ada
            $key = trim((string) $r[3]) . '|' . trim((string) $r[4]); // nama_tim|judul_inovasi
            $grup[$key]['header'] = $r;
            $grup[$key]['anggota'][] = $r;
        }

        $jumlahInovasi = 0;
        $jumlahAnggota = 0;

        foreach ($grup as $g) {
            $h = $g['header'];
            // cek duplikat berdasarkan nama_tim + judul_inovasi
            $existing = $this->inovasiModel->where('nama_tim', $h[3])->where('judul_inovasi', $h[4])->first();
            if ($existing) { continue; } // skip yang sudah ada, hindari duplikasi saat re-import

            $inovasiId = $this->inovasiModel->insert([
                'kategori_inovasi' => $h[1] ?: null,
                'nama_tim' => $h[3] ?: null,
                'judul_inovasi' => $h[4],
                'area_improvement' => $h[5] ?: null,
                'unit_dept_area_implementasi' => $h[6] ?: null,
                'unit_biro_area_implementasi' => $h[7] ?: null,
                'biaya_project' => is_numeric($h[12]) ? $h[12] : 0,
                'saving' => is_numeric($h[13]) ? $h[13] : 0,
                'opp_lost' => is_numeric($h[14]) ? $h[14] : 0,
                'revenue' => is_numeric($h[15]) ? $h[15] : 0,
                'total_benefit' => is_numeric($h[16]) ? $h[16] : 0,
                'status_saat_ini' => $h[17] ?: null,
                'keterangan' => $h[18] ?: null,
                'hyperlink_dokumen' => $h[19] ?: null,
                'tahun' => $h[20] ?: null,
            ]);
            $jumlahInovasi++;

            foreach ($g['anggota'] as $a) {
                if (empty($a[8])) { continue; } // nama_personil kosong, skip
                $this->timModel->insert([
                    'inovasi_id' => $inovasiId, 'nama_personil' => $a[8],
                    'nik' => $a[9] ?: null, 'struktur_tim' => $a[10] ?: null, 'org_unit' => $a[11] ?: null,
                ]);
                $jumlahAnggota++;
            }
        }

        $db->transComplete();
        $this->logModel->catat(session()->get('user_id'), 'import_inovasi', 'inovasi', null, "{$jumlahInovasi} inovasi, {$jumlahAnggota} anggota tim");

        return redirect()->to('/admin/inovasi')->with('success', "Import selesai: {$jumlahInovasi} inovasi baru, {$jumlahAnggota} anggota tim ditambahkan. Data duplikat (nama_tim+judul sama) otomatis dilewati.");
    }
}
