<?php

namespace App\Controllers;

use App\Models\InovasiModel;
use App\Models\KmAktivitasModel;
use App\Models\KompetisiInovasiModel;
use App\Models\KompetisiAnggotaModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Files\File;

/**
 * Modul "Library": pengganti modul Repository/Kelola Dokumen lama.
 * Tidak lagi punya tabel/upload dokumen sendiri -- isinya otomatis
 * digabung dari data Inovasi dan Knowledge Management yang sudah ada,
 * supaya admin tidak perlu upload ulang di tempat lain.
 */
class LibraryController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected KmAktivitasModel $kmModel;
    protected KompetisiInovasiModel $kompetisiModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel = new InovasiModel();
        $this->kmModel      = new KmAktivitasModel();
        $this->kompetisiModel = new KompetisiInovasiModel();
        $this->logModel     = new ActivityLogModel();
    }

    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'tahun', 'sumber']);

        $daftarInovasi = [];
        if (!in_array($filters['sumber'] ?? '', ['km', 'kompetisi'], true)) {
            $qi = $this->inovasiModel->select('id, judul_inovasi as judul, kategori_inovasi as kategori, cover_thumbnail as cover, tahun, created_at');
            if (!empty($filters['keyword'])) { $qi->like('judul_inovasi', $filters['keyword']); }
            if (!empty($filters['tahun']))   { $qi->where('tahun', $filters['tahun']); }
            $daftarInovasi = array_map(static function ($r) {
                $r['sumber'] = 'inovasi';
                return $r;
            }, $qi->findAll());
        }

        $daftarKm = [];
        if (!in_array($filters['sumber'] ?? '', ['inovasi', 'kompetisi'], true)) {
            $qk = $this->kmModel->select('id, judul_event as judul, pillar_km as kategori, cover, tahun, tanggal_create as created_at');
            if (!empty($filters['keyword'])) { $qk->like('judul_event', $filters['keyword']); }
            if (!empty($filters['tahun']))   { $qk->where('tahun', $filters['tahun']); }
            $daftarKm = array_map(static function ($r) {
                $r['sumber'] = 'km';
                return $r;
            }, $qk->findAll());
        }

        $daftarKompetisi = [];
        if (!in_array($filters['sumber'] ?? '', ['inovasi', 'km'], true)) {
            $qc = $this->kompetisiModel->select('id, judul_inovasi as judul, jenis_penghargaan as kategori, cover_thumbnail as cover, tahun, COALESCE(tanggal_penghargaan, created_at) as created_at');
            if (!empty($filters['keyword'])) { $qc->groupStart()->like('judul_inovasi', $filters['keyword'])->orLike('nama_tim', $filters['keyword'])->groupEnd(); }
            if (!empty($filters['tahun']))   { $qc->where('tahun', $filters['tahun']); }
            $daftarKompetisi = array_map(static function ($r) {
                $r['sumber'] = 'kompetisi';
                return $r;
            }, $qc->findAll());
        }

        $gabungan = array_merge($daftarInovasi, $daftarKm, $daftarKompetisi);
        usort($gabungan, static fn ($a, $b) => strtotime((string) $b['created_at']) <=> strtotime((string) $a['created_at']));

        // Dua sumber data berbeda tabel -> tidak bisa pakai $model->paginate() bawaan CI4,
        // jadi paginasi dilakukan manual atas hasil gabungan.
        $perPage     = 12;
        $currentPage = max(1, (int) ($this->request->getGet('page') ?? 1));
        $totalItem   = count($gabungan);
        $totalPage   = max(1, (int) ceil($totalItem / $perPage));
        $items       = array_slice($gabungan, ($currentPage - 1) * $perPage, $perPage);

        return view('library/index', [
            'title'       => 'Library',
            'items'       => $items,
            'filters'     => $filters,
            'currentPage' => $currentPage,
            'totalPage'   => $totalPage,
        ]);
    }

    /** Detail ringkas untuk entri Knowledge Management (entri Inovasi sudah punya halaman detail sendiri). */
    public function kmDetail(int $id)
    {
        $item = $this->kmModel->find($id);
        if (!$item) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $this->logModel->catat(session()->get('user_id'), 'lihat_km_library', 'km_aktivitas', $id, $item['judul_event']);

        return view('library/km_detail', ['title' => $item['judul_event'], 'item' => $item]);
    }

    /** Detail entri Kompetisi Inovasi di Library: info penghargaan, daftar personil, dokumen & link. */
    public function kompetisiDetail(int $id)
    {
        $item = $this->kompetisiModel->find($id);
        if (!$item) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $this->kompetisiModel->incrementView($id);
        $this->logModel->catat(session()->get('user_id'), 'lihat_kompetisi_library', 'kompetisi_inovasi', $id, $item['judul_inovasi']);

        return view('library/kompetisi_detail', [
            'title'   => $item['judul_inovasi'],
            'item'    => $item,
            'anggota' => (new KompetisiAnggotaModel())->getFor($id),
        ]);
    }

    public function downloadKompetisiFile(int $id)
    {
        $item = $this->kompetisiModel->find($id);
        if (!$item || empty($item['file_dokumen'])) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $path = WRITEPATH . 'uploads/originals/' . basename($item['file_dokumen']);
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        return $this->response->download($path, null);
    }

    public function downloadKmFile(int $id)
    {
        $item = $this->kmModel->find($id);
        if (!$item || empty($item['file_dokumen'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/originals/' . $item['file_dokumen'];
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($path, null);
    }

    /**
     * Stream sampul untuk kedua sumber (Inovasi & KM) -- nama file saja yang dibutuhkan,
     * keduanya disimpan di folder writable/uploads/covers/ yang sama.
     */
    public function streamCoverImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/covers/' . basename($filename);
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $file = new File($path);

        return $this->response->setHeader('Content-Type', $file->getMimeType())
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody(file_get_contents($path));
    }
}
