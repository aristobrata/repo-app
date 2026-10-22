<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\KompetisiInovasiModel;
use App\Models\KompetisiAnggotaModel;
use App\Models\ActivityLogModel;

/**
 * CRUD + Import/Export Excel untuk modul Kompetisi Inovasi.
 * File dokumen & sampul TIDAK WAJIB (permit_empty); kalau sampul kosong,
 * Library otomatis memakai gambar placeholder.
 */
class KompetisiManageController extends BaseController
{
    protected KompetisiInovasiModel $kompetisiModel;
    protected KompetisiAnggotaModel $anggotaModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->kompetisiModel = new KompetisiInovasiModel();
        $this->anggotaModel   = new KompetisiAnggotaModel();
        $this->logModel       = new ActivityLogModel();
    }

    private function opsiForm(): array
    {
        $jenisDefault = ['DIAMOND', 'PLATINUM', 'GOLD', 'SILVER', 'EXCELLENT'];
        return [
            'opsi_jenis'         => array_values(array_unique(array_merge($jenisDefault, $this->kompetisiModel->nilaiUnik('jenis_penghargaan')))),
            'opsi_event'         => $this->kompetisiModel->nilaiUnik('event_kompetisi'),
            'opsi_penyelenggara' => $this->kompetisiModel->nilaiUnik('penyelenggara'),
        ];
    }

    public function index()
    {
        $filters    = $this->request->getGet(['keyword', 'jenis', 'event', 'tahun']);
        $kompetisi  = $this->kompetisiModel->daftar($filters)->paginate(per_page_from_request(), 'kompetisi');
        $pager      = $this->kompetisiModel->pager;

        // jumlah personil per entri (1 query, bukan 1 per baris)
        $jumlah = [];
        if ($kompetisi) {
            $ids  = array_column($kompetisi, 'id');
            $rows = $this->anggotaModel->select('kompetisi_id, COUNT(id) as total')->whereIn('kompetisi_id', $ids)->groupBy('kompetisi_id')->findAll();
            foreach ($rows as $r) { $jumlah[$r['kompetisi_id']] = (int) $r['total']; }
        }

        return view('admin/kompetisi/index', [
            'kompetisi'  => $kompetisi,
            'pager'      => $pager,
            'filters'    => $filters,
            'jumlah'     => $jumlah,
            'opsi_jenis' => $this->kompetisiModel->nilaiUnik('jenis_penghargaan'),
            'opsi_event' => $this->kompetisiModel->nilaiUnik('event_kompetisi'),
            'opsi_tahun' => $this->kompetisiModel->nilaiUnik('tahun'),
        ]);
    }

    public function createForm()
    {
        return view('admin/kompetisi/form', array_merge(['kompetisi' => null, 'anggota' => []], $this->opsiForm()));
    }

    private function dataFromPost(): array
    {
        $tanggal = $this->request->getPost('tanggal_penghargaan') ?: null;
        $tahun   = trim((string) $this->request->getPost('tahun'));
        if ($tahun === '' && $tanggal) { $tahun = date('Y', strtotime($tanggal)); }
        return [
            'nama_tim'            => $this->request->getPost('nama_tim'),
            'judul_inovasi'       => $this->request->getPost('judul_inovasi'),
            'jenis_penghargaan'   => strtoupper(trim((string) $this->request->getPost('jenis_penghargaan'))) ?: null,
            'event_kompetisi'     => $this->request->getPost('event_kompetisi'),
            'penyelenggara'       => $this->request->getPost('penyelenggara'),
            'tanggal_penghargaan' => $tanggal,
            'tahun'               => $tahun ?: null,
            'keterangan'          => $this->request->getPost('keterangan'),
            'link_dokumen'        => $this->request->getPost('link_dokumen'),
        ];
    }

    private function rules(): array
    {
        return ['judul_inovasi' => 'required|min_length[3]'];
    }

    /**
     * Validasi file upload secara eksplisit. Aturan ext_in/max_size bawaan CI4
     * dikombinasikan dengan permit_empty ternyata tidak memblokir file yang salah
     * (hasil uji: .exe dan .gif lolos), jadi pengecekan dilakukan manual di sini.
     * Mengembalikan daftar pesan error (kosong jika semua file valid / tidak ada file).
     */
    private function cekUpload(): array
    {
        $errors = [];
        $aturan = [
            'file_dokumen' => ['label' => 'File dokumen', 'ext' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png'], 'kb' => 51200, 'mime' => null],
            'foto_penghargaan' => ['label' => 'Foto dokumentasi penghargaan', 'ext' => ['jpg', 'jpeg', 'png'], 'kb' => 5120, 'mime' => ['image/jpeg', 'image/png']],
            'cover'        => ['label' => 'Sampul', 'ext' => ['jpg', 'jpeg', 'png'], 'kb' => 3072, 'mime' => ['image/jpeg', 'image/png']],
        ];
        foreach ($aturan as $field => $a) {
            $f = $this->request->getFile($field);
            if (!$f || $f->getError() === UPLOAD_ERR_NO_FILE) { continue; }
            if (!$f->isValid()) { $errors[$field] = $a['label'] . ' gagal diunggah (' . $f->getErrorString() . ').'; continue; }
            if (!in_array(strtolower($f->getClientExtension()), $a['ext'], true)) {
                $errors[$field] = $a['label'] . ' harus berformat: ' . implode(', ', $a['ext']) . '.'; continue;
            }
            if ($f->getSize() > $a['kb'] * 1024) {
                $errors[$field] = $a['label'] . ' maksimal ' . round($a['kb'] / 1024) . ' MB.'; continue;
            }
            if ($a['mime'] && !in_array($f->getMimeType(), $a['mime'], true)) {
                $errors[$field] = $a['label'] . ' bukan gambar JPG/PNG yang valid.';
            }
        }
        return $errors;
    }

    private function handleUpload(string $field, string $dir): ?string
    {
        $file = $this->request->getFile($field);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $nama = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/' . $dir, $nama);
            return $nama;
        }
        return null;
    }

    private function simpanAnggota(int $kompetisiId, bool $hapusDulu = false): void
    {
        if ($hapusDulu) { $this->anggotaModel->where('kompetisi_id', $kompetisiId)->delete(); }
        $nama = $this->request->getPost('anggota_nama') ?? [];
        $nik  = $this->request->getPost('anggota_nik') ?? [];
        foreach ($nama as $i => $n) {
            if (trim((string) $n) === '') { continue; }
            $this->anggotaModel->insert(['kompetisi_id' => $kompetisiId, 'nama_karyawan' => trim($n), 'nik' => trim((string) ($nik[$i] ?? '')) ?: null]);
        }
    }

    public function store()
    {
        $valid = $this->validate($this->rules());
        $errors = array_merge($valid ? [] : $this->validator->getErrors(), $this->cekUpload());
        if ($errors) { return redirect()->back()->withInput()->with('errors', $errors); }

        $data = $this->dataFromPost();
        $data['file_dokumen']    = $this->handleUpload('file_dokumen', 'originals');
        $data['cover_thumbnail'] = $this->handleUpload('cover', 'covers');
        $data['foto_penghargaan'] = $this->handleUpload('foto_penghargaan', 'dokumentasi');
        $id = $this->kompetisiModel->insert($data);

        $this->simpanAnggota((int) $id);
        $this->logModel->catat(session()->get('user_id'), 'tambah_kompetisi', 'kompetisi_inovasi', $id, $data['judul_inovasi']);
        return redirect()->to('/admin/kompetisi')->with('success', 'Kompetisi inovasi berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $kompetisi = $this->kompetisiModel->find($id);
        if (!$kompetisi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/kompetisi/form', array_merge(['kompetisi' => $kompetisi, 'anggota' => $this->anggotaModel->getFor($id)], $this->opsiForm()));
    }

    public function update(int $id)
    {
        $kompetisi = $this->kompetisiModel->find($id);
        if (!$kompetisi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $valid = $this->validate($this->rules());
        $errors = array_merge($valid ? [] : $this->validator->getErrors(), $this->cekUpload());
        if ($errors) { return redirect()->back()->withInput()->with('errors', $errors); }

        $data = $this->dataFromPost();
        if ($baru = $this->handleUpload('file_dokumen', 'originals')) {
            $this->hapusFile('originals', $kompetisi['file_dokumen']);
            $data['file_dokumen'] = $baru;
        }
        if ($baru = $this->handleUpload('cover', 'covers')) {
            $this->hapusFile('covers', $kompetisi['cover_thumbnail']);
            $data['cover_thumbnail'] = $baru;
        }
        if ($baru = $this->handleUpload('foto_penghargaan', 'dokumentasi')) {
            $this->hapusFile('dokumentasi', $kompetisi['foto_penghargaan'] ?? null);
            $data['foto_penghargaan'] = $baru;
        }
        if ($this->request->getPost('hapus_foto_penghargaan') && empty($data['foto_penghargaan'])) {
            $this->hapusFile('dokumentasi', $kompetisi['foto_penghargaan'] ?? null);
            $data['foto_penghargaan'] = null;
        }
        if ($this->request->getPost('hapus_file_dokumen') && empty($data['file_dokumen'])) {
            $this->hapusFile('originals', $kompetisi['file_dokumen']);
            $data['file_dokumen'] = null;
        }
        if ($this->request->getPost('hapus_cover') && empty($data['cover_thumbnail'])) {
            $this->hapusFile('covers', $kompetisi['cover_thumbnail']);
            $data['cover_thumbnail'] = null;
        }

        $this->kompetisiModel->update($id, $data);
        $this->simpanAnggota($id, true);
        $this->logModel->catat(session()->get('user_id'), 'edit_kompetisi', 'kompetisi_inovasi', $id, $data['judul_inovasi']);
        return redirect()->to('/admin/kompetisi')->with('success', 'Kompetisi inovasi berhasil diperbarui.');
    }

    private function hapusFile(string $dir, ?string $nama): void
    {
        if (!empty($nama)) {
            $path = WRITEPATH . "uploads/{$dir}/" . basename($nama);
            if (is_file($path)) { unlink($path); }
        }
    }

    public function destroy(int $id)
    {
        $kompetisi = $this->kompetisiModel->find($id);
        if ($kompetisi) {
            $this->hapusFile('originals', $kompetisi['file_dokumen']);
            $this->hapusFile('covers', $kompetisi['cover_thumbnail']);
            $this->hapusFile('dokumentasi', $kompetisi['foto_penghargaan'] ?? null);
            $this->anggotaModel->where('kompetisi_id', $id)->delete();
            $this->kompetisiModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_kompetisi', 'kompetisi_inovasi', $id, $kompetisi['judul_inovasi']);
        }
        return redirect()->to('/admin/kompetisi')->with('success', 'Kompetisi inovasi dihapus.');
    }

    // ==================== IMPORT EXCEL ====================
    public function importForm() { return view('admin/kompetisi/import'); }

    /**
     * Import sheet "Data_Import" dari kompetisi_inovasi_database_ready.xlsx.
     * Kolom: id, nik, nama_karyawan, nama_tim, jenis_penghargaan, judul_inovasi,
     * event_kompetisi, penyelenggara, tanggal_penghargaan, link_dokumen.
     * 1 baris Excel = 1 personil. Baris dengan (tim + judul + event + penghargaan + tanggal)
     * yang sama digabung menjadi 1 entri kompetisi berisi banyak personil.
     * Entri yang sudah ada (kombinasi sama) dilewati, jadi aman di-import ulang.
     */
    public function import()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) { return redirect()->back()->with('error', 'File Excel tidak valid.'); }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('Data_Import') ?: $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, false);
        array_shift($rows);

        [$baru, $dilewati, $personil] = $this->kompetisiModel->importBaris($rows);

        $this->logModel->catat(session()->get('user_id'), 'import_kompetisi', 'kompetisi_inovasi', null, "{$baru} entri, {$personil} personil");
        return redirect()->to('/admin/kompetisi')->with('success', "Import selesai: {$baru} entri kompetisi baru dengan {$personil} personil. {$dilewati} entri yang sudah ada dilewati.");
    }

    // ==================== EXPORT EXCEL ====================
    /** Export dalam format yang sama dengan sheet Data_Import (1 baris = 1 personil), menghormati filter aktif. */
    public function exportExcel()
    {
        $filters = $this->request->getGet(['keyword', 'jenis', 'event', 'tahun']);
        $data    = $this->kompetisiModel->daftar($filters)->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data_Import');
        $headers = ['id', 'nik', 'nama_karyawan', 'nama_tim', 'jenis_penghargaan', 'judul_inovasi', 'event_kompetisi', 'penyelenggara', 'tanggal_penghargaan', 'link_dokumen'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D73338');
        $sheet->getStyle('A1:J1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2; $no = 1;
        foreach ($data as $d) {
            $anggota = $this->anggotaModel->getFor((int) $d['id']);
            if (!$anggota) { $anggota = [['nik' => null, 'nama_karyawan' => null]]; }
            foreach ($anggota as $a) {
                $sheet->fromArray([$no++, $a['nik'], $a['nama_karyawan'], $d['nama_tim'], $d['jenis_penghargaan'], $d['judul_inovasi'], $d['event_kompetisi'], $d['penyelenggara'], $d['tanggal_penghargaan'], $d['link_dokumen']], null, 'A' . $row);
                $sheet->setCellValueExplicit('B' . $row, (string) $a['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $row++;
            }
        }
        foreach (range('A', 'J') as $col) { $sheet->getColumnDimension($col)->setAutoSize(true); }
        $sheet->freezePane('A2');

        $this->logModel->catat(session()->get('user_id'), 'export_kompetisi', 'kompetisi_inovasi', null, count($data) . ' entri');
        if (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="kompetisi_inovasi_export_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
