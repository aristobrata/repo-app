<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\InovasiKaryawanModel;
use App\Models\ActivityLogModel;

/**
 * CRUD + Import/Export Excel untuk Master Karyawan Inovasi.
 *
 * Sumber data awal: "KOMPOSISI_SEPTEMBER_Diklat_SHE.xlsx" sheet "SEPTEMBER",
 * kolom (A-P): No, Pers/No, ID Number, Personnel Number, Band, Position,
 * Gender Key, Tempat Lahir, Birth date, TXT_DIR, TXT_DEPT, DEPARTEMEN,
 * TXT_BIRO, UNIT, Nama Unit Kerja, Seksi.
 *
 * CATATAN PENTING: di file Excel sumber, kolom berjudul "Personnel Number"
 * (kolom D) sebenarnya BERISI NAMA KARYAWAN, bukan nomor -- ini bawaan dari
 * file aslinya. Supaya tidak membingungkan, di sistem ini kolom tsb dipetakan
 * ke field "nama" (bukan "personnel_number").
 */
class InovasiKaryawanManageController extends BaseController
{
    protected InovasiKaryawanModel $karyawanModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->karyawanModel = new InovasiKaryawanModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function index()
    {
        $filters  = $this->request->getGet(['keyword', 'departemen', 'band']);
        $karyawan = $this->karyawanModel->daftar($filters)->paginate(20, 'inovasi_karyawan');
        return view('admin/inovasi_karyawan/index', [
            'karyawan'   => $karyawan,
            'pager'      => $this->karyawanModel->pager,
            'filters'    => $filters,
            'departemen' => $this->karyawanModel->daftarDepartemen(),
        ]);
    }

    public function createForm()
    {
        return view('admin/inovasi_karyawan/form', ['karyawan' => null]);
    }

    private function dataFromPost(): array
    {
        return [
            'perner'          => $this->request->getPost('perner'),
            'id_number'       => $this->request->getPost('id_number'),
            'nama'            => $this->request->getPost('nama'),
            'band'            => $this->request->getPost('band'),
            'jabatan'         => $this->request->getPost('jabatan'),
            'gender'          => $this->request->getPost('gender'),
            'tempat_lahir'    => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'   => $this->request->getPost('tanggal_lahir') ?: null,
            'direktorat'      => $this->request->getPost('direktorat'),
            'departemen_en'   => $this->request->getPost('departemen_en'),
            'departemen'      => $this->request->getPost('departemen'),
            'biro'            => $this->request->getPost('biro'),
            'unit_kerja_en'   => $this->request->getPost('unit_kerja_en'),
            'nama_unit_kerja' => $this->request->getPost('nama_unit_kerja'),
            'seksi'           => $this->request->getPost('seksi'),
        ];
    }

    public function store()
    {
        $rules = ['nama' => 'required|min_length[3]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->karyawanModel->insert($this->dataFromPost());
        $this->logModel->catat(session()->get('user_id'), 'tambah_karyawan_inovasi', 'inovasi_karyawan', $id, $this->request->getPost('nama'));
        return redirect()->to('/admin/inovasi-karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/inovasi_karyawan/form', ['karyawan' => $karyawan]);
    }

    public function update(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $rules = ['nama' => 'required|min_length[3]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = $this->dataFromPost();
        $this->karyawanModel->update($id, $dataUpdate);
        $this->logModel->catat(session()->get('user_id'), 'edit_karyawan_inovasi', 'inovasi_karyawan', $id, $dataUpdate['nama']);
        return redirect()->to('/admin/inovasi-karyawan')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if ($karyawan) {
            $this->karyawanModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_karyawan_inovasi', 'inovasi_karyawan', $id, $karyawan['nama']);
        }
        return redirect()->to('/admin/inovasi-karyawan')->with('success', 'Data karyawan dihapus.');
    }

    // ==================== IMPORT EXCEL ====================
    public function importForm()
    {
        return view('admin/inovasi_karyawan/import');
    }

    /**
     * Import massal dari Excel dengan struktur sama seperti sheet "SEPTEMBER"
     * pada KOMPOSISI_SEPTEMBER_Diklat_SHE.xlsx (16 kolom, A-P).
     * Mode: TIMPA SEMUA (truncate lalu insert ulang) -- paling sesuai untuk
     * file "komposisi per periode" seperti ini, konsisten dengan cara import
     * Master Karyawan KM yang sudah ada.
     */
    public function import()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel tidak valid.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('SEPTEMBER') ?: $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        array_shift($rows); // buang baris header

        $db = \Config\Database::connect();
        $db->transStart();

        $this->karyawanModel->truncate();

        $jumlah = 0;
        foreach ($rows as $r) {
            $nama = trim((string) ($r[3] ?? ''));
            if ($nama === '') { continue; } // kolom D (Personnel Number) = nama, wajib ada

            $tanggalLahir = null;
            if (!empty($r[8])) {
                if ($r[8] instanceof \DateTimeInterface) {
                    $tanggalLahir = $r[8]->format('Y-m-d');
                } elseif (is_numeric($r[8])) {
                    $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($r[8])->format('Y-m-d');
                } else {
                    $ts = strtotime((string) $r[8]);
                    $tanggalLahir = $ts ? date('Y-m-d', $ts) : null;
                }
            }

            $this->karyawanModel->insert([
                'perner'          => $r[1] ?? null,
                'id_number'       => $r[2] ?? null,
                'nama'            => $nama,
                'band'            => $r[4] ?? null,
                'jabatan'         => $r[5] ?? null,
                'gender'          => $r[6] ?? null,
                'tempat_lahir'    => $r[7] ?? null,
                'tanggal_lahir'   => $tanggalLahir,
                'direktorat'      => $r[9] ?? null,
                'departemen_en'   => $r[10] ?? null,
                'departemen'      => $r[11] ?? null,
                'biro'            => $r[12] ?? null,
                'unit_kerja_en'   => $r[13] ?? null,
                'nama_unit_kerja' => $r[14] ?? null,
                'seksi'           => $r[15] ?? null,
            ]);
            $jumlah++;
        }

        $db->transComplete();
        $this->logModel->catat(session()->get('user_id'), 'import_karyawan_inovasi', 'inovasi_karyawan', null, "{$jumlah} karyawan");

        return redirect()->to('/admin/inovasi-karyawan')->with('success', "Import selesai: {$jumlah} karyawan dimuat. Data lama otomatis diganti dengan data baru dari file ini.");
    }

    // ==================== EXPORT EXCEL ====================
    public function exportExcel()
    {
        $filters = $this->request->getGet(['keyword', 'departemen', 'band']);
        $data    = $this->karyawanModel->daftar($filters)->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Karyawan Inovasi');

        $headers = [
            'No', 'Pers/No', 'ID Number', 'Nama', 'Band', 'Jabatan', 'Gender', 'Tempat Lahir', 'Tanggal Lahir',
            'Direktorat', 'Departemen (EN)', 'Departemen', 'Biro', 'Unit (EN)', 'Nama Unit Kerja', 'Seksi',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D73338');
        $sheet->getStyle('A1:P1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        foreach ($data as $i => $d) {
            $sheet->fromArray([
                $i + 1,
                $d['perner'],
                $d['id_number'],
                $d['nama'],
                $d['band'],
                $d['jabatan'],
                $d['gender'],
                $d['tempat_lahir'],
                $d['tanggal_lahir'],
                $d['direktorat'],
                $d['departemen_en'],
                $d['departemen'],
                $d['biro'],
                $d['unit_kerja_en'],
                $d['nama_unit_kerja'],
                $d['seksi'],
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $this->logModel->catat(session()->get('user_id'), 'export_karyawan_inovasi', 'inovasi_karyawan', null, count($data) . ' baris');

        $filename = 'karyawan_inovasi_export_' . date('Ymd_His') . '.xlsx';

        if (ob_get_level()) { ob_end_clean(); }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
