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
        $rules = [
            'judul_event'  => 'required',
            'nama_peserta' => 'required',
            'file_dokumen' => 'permit_empty|max_size[file_dokumen,10240]',
            'cover'        => 'permit_empty|max_size[cover,3072]|ext_in[cover,jpg,jpeg,png]',
        ];
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
            'file_dokumen' => $fileDokumen, 'cover' => $this->handleCoverUpload(), 'tahun' => $this->request->getPost('tahun'),
        ]);
        $this->logModel->catat(session()->get('user_id'), 'tambah_km_aktivitas', 'km_aktivitas', $id);
        return redirect()->to('/admin/knowledge')->with('success', 'Aktivitas KM berhasil ditambahkan.');
    }

    /** Upload sampul TIDAK WAJIB -- kalau kosong, modul Library akan otomatis pakai gambar placeholder */
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
        $filters  = $this->request->getGet(['keyword', 'departemen', 'band']);
        $karyawan = $this->karyawanModel->daftar($filters)->paginate(25, 'karyawan');
        return view('admin/knowledge/karyawan', [
            'karyawan'   => $karyawan,
            'pager'      => $this->karyawanModel->pager,
            'filters'    => $filters,
            'departemen' => $this->karyawanModel->daftarDepartemen(),
            'total'      => $this->karyawanModel->pager->getTotal('karyawan'),
        ]);
    }

    /** Isi kolom Band master karyawan dari data Rekap/Leaderboard (cocokkan NIK, lalu NIP). */
    public function sinkronBand()
    {
        $terisi = $this->karyawanModel->sinkronBandDariRekap();
        $sisa   = $this->karyawanModel->where('band IS NULL')->orWhere('band', '')->countAllResults();
        $this->logModel->catat(session()->get('user_id'), 'sinkron_band_km_karyawan', 'km_karyawan', null, "{$terisi} terisi, {$sisa} belum cocok");
        return redirect()->to('/admin/knowledge/karyawan')->with('success', "Sinkron Band selesai: {$terisi} karyawan terisi dari Rekap/Leaderboard. {$sisa} karyawan belum punya padanan di Rekap (NIK/NIP tidak ditemukan).");
    }

    public function karyawanCreateForm()
    {
        return view('admin/knowledge/karyawan_form', ['karyawan' => null]);
    }

    private function karyawanDataFromPost(): array
    {
        return [
            'perner'           => $this->request->getPost('perner'),
            'id_number'        => $this->request->getPost('id_number'),
            'personnel_number' => $this->request->getPost('personnel_number'),
            'direktorat'       => $this->request->getPost('direktorat'),
            'departemen'       => $this->request->getPost('departemen'),
            'nama_unit_kerja'  => $this->request->getPost('nama_unit_kerja'),
            'biro'             => $this->request->getPost('biro'),
            'org_unit'         => $this->request->getPost('org_unit'),
            'bidang'           => $this->request->getPost('bidang'),
            'band'             => $this->request->getPost('band'),
        ];
    }

    public function karyawanStore()
    {
        $rules = ['personnel_number' => 'required|min_length[3]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $id = $this->karyawanModel->insert($this->karyawanDataFromPost());
        $this->logModel->catat(session()->get('user_id'), 'tambah_km_karyawan', 'km_karyawan', $id, $this->request->getPost('personnel_number'));
        return redirect()->to('/admin/knowledge/karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function karyawanEditForm(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/knowledge/karyawan_form', ['karyawan' => $karyawan]);
    }

    public function karyawanUpdate(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $rules = ['personnel_number' => 'required|min_length[3]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $dataUpdate = $this->karyawanDataFromPost();
        $this->karyawanModel->update($id, $dataUpdate);
        $this->logModel->catat(session()->get('user_id'), 'edit_km_karyawan', 'km_karyawan', $id, $dataUpdate['personnel_number']);
        return redirect()->to('/admin/knowledge/karyawan')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function karyawanDestroy(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if ($karyawan) {
            $this->karyawanModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_km_karyawan', 'km_karyawan', $id, $karyawan['personnel_number'] ?? null);
        }
        return redirect()->to('/admin/knowledge/karyawan')->with('success', 'Data karyawan dihapus.');
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
                'band' => $r[9] ?: null,
            ]);
            $jumlah++;
        }
        $this->logModel->catat(session()->get('user_id'), 'import_km_karyawan', 'km_karyawan', null, "{$jumlah} baris");
        $band = $this->karyawanModel->sinkronBandDariRekap();
        return redirect()->to('/admin/knowledge/karyawan')->with('success', "Import master karyawan selesai: {$jumlah} baris (data lama ditimpa). Band otomatis terisi dari Rekap untuk {$band} karyawan.");
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
        $band = $this->karyawanModel->sinkronBandDariRekap();
        return redirect()->to('/admin/knowledge/rekap')->with('success', "Import rekap karyawan selesai: {$jumlah} baris (data lama ditimpa). Band master karyawan ikut terisi untuk {$band} karyawan.");
    }

    private function parseTanggal($value): ?string
    {
        if (empty($value)) { return null; }
        if ($value instanceof \DateTime) { return $value->format('Y-m-d'); }
        $ts = strtotime((string) $value);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    // ==================== EXPORT EXCEL ====================

    /** Helper umum: tulis header + data ke sheet aktif lalu stream sebagai .xlsx */
    private function streamXlsx(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, string $filename): void
    {
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function beriJudulHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $headers): void
    {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E3A8A');
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->getColor()->setRGB('FFFFFF');
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');
    }

    /** Export tab Aktivitas, menghormati filter aktif (keyword, pillar, bulan, tahun) */
    public function exportAktivitas()
    {
        $filters   = $this->request->getGet(['keyword', 'pillar', 'bulan', 'tahun']);
        $data      = $this->aktivitasModel->daftar($filters)->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aktivitas KM');

        $headers = [
            'No', 'Bulan', 'Pillar KM', 'Aktivitas', 'Activity Type', 'Subactivity', 'Judul Event',
            'Tanggal Score', 'Tanggal Create', 'Tempat', 'NIK', 'NIP', 'Nama Peserta',
            'Direktorat', 'Departemen', 'Biro', 'Org Unit', 'Bidang', 'Peran', 'Poin',
            'Jumlah Karyawan Bulanan', 'Poin Corporate', 'Tahun',
        ];
        $this->beriJudulHeader($sheet, $headers);

        $row = 2;
        foreach ($data as $i => $d) {
            $sheet->fromArray([
                $i + 1, $d['bulan'], $d['pillar_km'], $d['aktivitas'], $d['activity_type'], $d['subactivity'],
                $d['judul_event'], $d['tanggal_score'], $d['tanggal_create'], $d['tempat'], $d['nik'], $d['nip'],
                $d['nama_peserta'], $d['direktorat'], $d['departemen'], $d['biro'], $d['org_unit'], $d['bidang'],
                $d['peran'], $d['poin'], $d['jumlah_karyawan_bulanan'], $d['poin_corporate'], $d['tahun'],
            ], null, 'A' . $row);
            $row++;
        }

        $this->logModel->catat(session()->get('user_id'), 'export_km_aktivitas', 'km_aktivitas', null, count($data) . ' baris');
        $this->streamXlsx($spreadsheet, 'km_aktivitas_export_' . date('Ymd_His') . '.xlsx');
    }

    /** Export tab Master Karyawan, menghormati pencarian/filter yang sedang aktif */
    public function exportKaryawan()
    {
        $filters = $this->request->getGet(['keyword', 'departemen', 'band']);
        $data = $this->karyawanModel->daftar($filters)->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Karyawan');

        $headers = ['No', 'Perner', 'ID Number', 'Personnel Number', 'Direktorat', 'Departemen', 'Nama Unit Kerja', 'Biro', 'Org Unit', 'Bidang', 'Band'];
        $this->beriJudulHeader($sheet, $headers);

        $row = 2;
        foreach ($data as $i => $d) {
            $sheet->fromArray([
                $i + 1, $d['perner'], $d['id_number'], $d['personnel_number'], $d['direktorat'],
                $d['departemen'], $d['nama_unit_kerja'], $d['biro'], $d['org_unit'], $d['bidang'], $d['band'] ?? null,
            ], null, 'A' . $row);
            $row++;
        }

        $this->logModel->catat(session()->get('user_id'), 'export_km_karyawan', 'km_karyawan', null, count($data) . ' baris');
        $this->streamXlsx($spreadsheet, 'km_master_karyawan_export_' . date('Ymd_His') . '.xlsx');
    }

    /** Export tab Rekap/Leaderboard (seluruh data, urut poin tertinggi) */
    public function exportRekap()
    {
        $data = $this->rekapModel->orderBy('total_poin', 'DESC')->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Leaderboard');

        $headers = ['Rank', 'NIK', 'NIP', 'Nama', 'Departemen', 'Unit', 'Seksi', 'Total Poin', 'Band'];
        $this->beriJudulHeader($sheet, $headers);

        $row = 2;
        foreach ($data as $i => $d) {
            $sheet->fromArray([
                $i + 1, $d['nik'], $d['nip'], $d['nama'], $d['departemen'], $d['unit'], $d['seksi'],
                $d['total_poin'], $d['band'],
            ], null, 'A' . $row);
            $row++;
        }

        $this->logModel->catat(session()->get('user_id'), 'export_km_rekap', 'km_rekap_karyawan', null, count($data) . ' baris');
        $this->streamXlsx($spreadsheet, 'km_rekap_leaderboard_export_' . date('Ymd_His') . '.xlsx');
    }
}
