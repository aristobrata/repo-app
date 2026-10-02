<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\KmAktivitasModel;
use App\Models\KmKaryawanModel;
use App\Models\KmRekapKaryawanModel;
use App\Models\KmTargetModel;
use App\Models\ActivityLogModel;

/**
 * Kelola Knowledge Management: aktivitas (fact), karyawan (dim), rekap/leaderboard,
 * dan target tahunan. Import Excel untuk fact_aktivitas_km, dim_karyawan, rekap_karyawan.
 * file_dokumen pada aktivitas TIDAK WAJIB (lampiran materi/dokumentasi opsional).
 */
class KmManageController extends BaseController
{
    protected KmAktivitasModel $aktivitasModel;
    protected KmKaryawanModel $karyawanModel;
    protected KmRekapKaryawanModel $rekapModel;
    protected KmTargetModel $targetModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->aktivitasModel = new KmAktivitasModel();
        $this->karyawanModel  = new KmKaryawanModel();
        $this->rekapModel     = new KmRekapKaryawanModel();
        $this->targetModel    = new KmTargetModel();
        $this->logModel       = new ActivityLogModel();
    }

    // ---- Tab Aktivitas ----
    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'pillar', 'bulan', 'tahun']);
        $aktivitas = $this->aktivitasModel->daftar($filters)->paginate(25, 'aktivitas');
        return view('admin/knowledge/index', ['aktivitas' => $aktivitas, 'pager' => $this->aktivitasModel->pager, 'filters' => $filters]);
    }

    public function createForm() { return view('admin/knowledge/create'); }

    public function store()
    {
        $rules = ['judul_event' => 'required', 'nama_peserta' => 'required', 'file_dokumen' => 'permit_empty|max_size[file_dokumen,10240]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $fileDokumen = null;
        $file = $this->request->getFile('file_dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileDokumen = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/originals', $fileDokumen);
        }

        $id = $this->aktivitasModel->insert([
            'bulan' => $this->request->getPost('bulan'), 'pillar_km' => $this->request->getPost('pillar_km'),
            'aktivitas' => $this->request->getPost('aktivitas'), 'subactivity' => $this->request->getPost('subactivity'),
            'judul_event' => $this->request->getPost('judul_event'), 'tanggal_score' => $this->request->getPost('tanggal_score') ?: null,
            'tempat' => $this->request->getPost('tempat'), 'nik' => $this->request->getPost('nik'),
            'nip' => $this->request->getPost('nip'), 'nama_peserta' => $this->request->getPost('nama_peserta'),
            'direktorat' => $this->request->getPost('direktorat'), 'departemen' => $this->request->getPost('departemen'),
            'peran' => $this->request->getPost('peran'), 'poin' => $this->request->getPost('poin') ?: 0,
            'file_dokumen' => $fileDokumen, 'tahun' => $this->request->getPost('tahun'),
        ]);
        $this->logModel->catat(session()->get('user_id'), 'tambah_km_aktivitas', 'km_aktivitas', $id);
        return redirect()->to('/admin/knowledge')->with('success', 'Aktivitas KM berhasil ditambahkan.');
    }

    public function destroy(int $id)
    {
        $item = $this->aktivitasModel->find($id);
        if ($item) {
            if (!empty($item['file_dokumen'])) {
                $path = WRITEPATH . 'uploads/originals/' . $item['file_dokumen'];
                if (is_file($path)) { unlink($path); }
            }
            $this->aktivitasModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_km_aktivitas', 'km_aktivitas', $id);
        }
        return redirect()->to('/admin/knowledge')->with('success', 'Aktivitas dihapus.');
    }

    // ---- Tab Karyawan (master) ----
    public function karyawan()
    {
        $karyawan = $this->karyawanModel->orderBy('personnel_number', 'ASC')->paginate(25, 'karyawan');
        return view('admin/knowledge/karyawan', ['karyawan' => $karyawan, 'pager' => $this->karyawanModel->pager]);
    }

    // ---- Tab Rekap/Leaderboard ----
    public function rekap()
    {
        $rekap = $this->rekapModel->orderBy('total_poin', 'DESC')->paginate(25, 'rekap');
        return view('admin/knowledge/rekap', ['rekap' => $rekap, 'pager' => $this->rekapModel->pager]);
    }

    // ---- Tab Target ----
    public function target()
    {
        $daftarTarget = $this->targetModel->orderBy('tahun', 'DESC')->findAll();
        return view('admin/knowledge/target', ['daftarTarget' => $daftarTarget]);
    }

    public function storeTarget()
    {
        $tahun  = $this->request->getPost('tahun');
        $target = (int) $this->request->getPost('target_poin_tahunan');
        $existing = $this->targetModel->where('tahun', $tahun)->first();
        if ($existing) {
            $this->targetModel->update($existing['id'], ['target_poin_tahunan' => $target]);
        } else {
            $this->targetModel->insert(['tahun' => $tahun, 'target_poin_tahunan' => $target]);
        }
        return redirect()->to('/admin/knowledge/target')->with('success', "Target tahun {$tahun} disimpan.");
    }

    // ==================== IMPORT EXCEL ====================
    public function importForm() { return view('admin/knowledge/import'); }

    /** Import sheet 'fact_aktivitas_km' -- kolom A-U sesuai Database_KM_2026_Clean.xlsx */
    public function importAktivitas()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) { return redirect()->back()->with('error', 'File Excel tidak valid.'); }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('fact_aktivitas_km') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        array_shift($rows);

        $tahunDefault = $this->request->getPost('tahun') ?: date('Y');
        $jumlah = 0;
        foreach ($rows as $r) {
            if (empty($r[5])) { continue; } // judul_event kosong, skip
            $this->aktivitasModel->insert([
                'bulan' => $r[0] ?: null, 'pillar_km' => $r[1] ?: null, 'aktivitas' => $r[2] ?: null,
                'activity_type' => $r[3] ?: null, 'subactivity' => $r[4] ?: null, 'judul_event' => $r[5],
                'tanggal_score' => $this->parseTanggal($r[6]), 'tanggal_create' => $this->parseTanggal($r[7]),
                'tempat' => $r[8] ?: null, 'nik' => $r[9] ?: null, 'nip' => $r[10] ?: null, 'nama_peserta' => $r[11] ?: null,
                'direktorat' => $r[12] ?: null, 'departemen' => $r[13] ?: null, 'biro' => $r[14] ?: null,
                'org_unit' => $r[15] ?: null, 'bidang' => $r[16] ?: null, 'peran' => $r[17] ?: null,
                'poin' => is_numeric($r[18]) ? $r[18] : 0, 'jumlah_karyawan_bulanan' => is_numeric($r[19]) ? $r[19] : null,
                'poin_corporate' => is_numeric($r[20]) ? $r[20] : null, 'tahun' => $tahunDefault,
            ]);
            $jumlah++;
        }
        $this->logModel->catat(session()->get('user_id'), 'import_km_aktivitas', 'km_aktivitas', null, "{$jumlah} baris");
        return redirect()->to('/admin/knowledge')->with('success', "Import aktivitas KM selesai: {$jumlah} baris ditambahkan.");
    }

    /** Import sheet 'dim_karyawan' */
    public function importKaryawan()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) { return redirect()->back()->with('error', 'File Excel tidak valid.'); }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('dim_karyawan') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        array_shift($rows);

        $this->karyawanModel->truncate(); // dim_karyawan adalah snapshot master -> replace penuh
        $jumlah = 0;
        foreach ($rows as $r) {
            if (empty($r[2])) { continue; } // nama kosong, skip
            $this->karyawanModel->insert([
                'perner' => $r[0] ?: null, 'id_number' => $r[1] ?: null, 'personnel_number' => $r[2],
                'direktorat' => $r[3] ?: null, 'departemen' => $r[4] ?: null, 'nama_unit_kerja' => $r[5] ?: null,
                'biro' => $r[6] ?: null, 'org_unit' => $r[7] ?: null, 'bidang' => $r[8] ?: null,
            ]);
            $jumlah++;
        }
        $this->logModel->catat(session()->get('user_id'), 'import_km_karyawan', 'km_karyawan', null, "{$jumlah} baris");
        return redirect()->to('/admin/knowledge/karyawan')->with('success', "Import master karyawan selesai: {$jumlah} baris (data lama ditimpa).");
    }

    /** Import sheet 'rekap_karyawan' -- dipakai leaderboard dashboard */
    public function importRekap()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) { return redirect()->back()->with('error', 'File Excel tidak valid.'); }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('rekap_karyawan') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        array_shift($rows);

        $this->rekapModel->truncate(); // rekap adalah snapshot -> replace penuh tiap import
        $jumlah = 0;
        foreach ($rows as $r) {
            if (empty($r[2])) { continue; } // nama kosong, skip
            $this->rekapModel->insert([
                'nik' => $r[0] ?: null, 'nip' => $r[1] ?: null, 'nama' => $r[2],
                'departemen' => $r[3] ?: null, 'unit' => $r[4] ?: null, 'seksi' => $r[5] ?: null,
                'total_poin' => is_numeric($r[6]) ? $r[6] : 0, 'band' => $r[7] ?: null,
            ]);
            $jumlah++;
        }
        $this->logModel->catat(session()->get('user_id'), 'import_km_rekap', 'km_rekap_karyawan', null, "{$jumlah} baris");
        return redirect()->to('/admin/knowledge/rekap')->with('success', "Import rekap karyawan selesai: {$jumlah} baris (data lama ditimpa).");
    }

    private function parseTanggal($value): ?string
    {
        if (empty($value)) { return null; }
        if ($value instanceof \DateTime) { return $value->format('Y-m-d'); }
        $ts = strtotime((string) $value);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}
