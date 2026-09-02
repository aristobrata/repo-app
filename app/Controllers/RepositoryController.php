<?php

namespace App\Controllers;

use App\Models\DokumenModel;
use App\Models\KategoriDokumenModel;
use App\Models\DokumenPreviewPageModel;
use App\Models\DocumentViewModel;
use App\Models\ActivityLogModel;

class RepositoryController extends BaseController
{
    protected DokumenModel $dokumenModel;
    protected KategoriDokumenModel $kategoriModel;
    protected DokumenPreviewPageModel $previewModel;
    protected DocumentViewModel $viewModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->dokumenModel  = new DokumenModel();
        $this->kategoriModel = new KategoriDokumenModel();
        $this->previewModel  = new DokumenPreviewPageModel();
        $this->viewModel     = new DocumentViewModel();
        $this->logModel      = new ActivityLogModel();
    }

    /**
     * Halaman katalog + filter & pencarian cerdas.
     * GET params: keyword, judul, penulis, tahun, kategori_id, sort, page
     */
    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'judul', 'penulis', 'tahun', 'kategori_id', 'sort']);

        $dokumen = $this->dokumenModel->searchDokumen($filters)->paginate(12, 'dokumen');

        return view('repository/index', [
            'dokumen'   => $dokumen,
            'pager'     => $this->dokumenModel->pager,
            'kategori'  => $this->kategoriModel->findAll(),
            'filters'   => $filters,
        ]);
    }

    /** Katalog per kategori (Laporan Internal, E-Book, SOP, Jurnal Inovasi, Dokumen Teknis) */
    public function kategori(string $slug)
    {
        $kategori = $this->kategoriModel->where('slug', $slug)->first();
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filters = array_merge($this->request->getGet(['keyword', 'sort']), ['kategori_id' => $kategori['id']]);
        $dokumen = $this->dokumenModel->searchDokumen($filters)->paginate(12, 'dokumen');

        return view('repository/kategori', [
            'kategori' => $kategori,
            'dokumen'  => $dokumen,
            'pager'    => $this->dokumenModel->pager,
        ]);
    }

    /** Halaman detail: metadata + abstrak + tombol "Baca Preview" */
    public function detail(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('repository/detail', ['dokumen' => $dokumen]);
    }

    /**
     * In-Browser Viewer: menampilkan daftar gambar preview (bukan file PDF asli).
     * Klik kanan & print di-disable lewat JS di view.
     */
    public function preview(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $halaman = $this->previewModel->getPagesFor($id);

        // catat statistik & activity log
        $this->dokumenModel->incrementView($id);
        $userId = session()->get('user_id');
        $this->viewModel->catat($id, $userId);
        $this->logModel->catat($userId, 'lihat_dokumen', 'dokumen', $id, $dokumen['judul']);

        return view('repository/preview', [
            'dokumen' => $dokumen,
            'halaman' => $halaman,
        ]);
    }

    /**
     * Streaming gambar preview satu per satu.
     * PENTING: gambar dikirim lewat controller (bukan link langsung ke folder writable),
     * sehingga tetap bisa dicek session/otorisasi sebelum file dikirim ke browser.
     */
    public function streamPreviewImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/previews/' . basename($filename); // basename() cegah path traversal

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            // Content-Disposition inline agar tampil di browser, bukan ter-download otomatis
            ->setHeader('Content-Disposition', 'inline; filename="preview.jpg"')
            ->setBody(file_get_contents($path));
    }

    /**
     * Streaming gambar sampul/cover dokumen lewat controller (bukan link langsung
     * ke folder writable), konsisten dengan pola keamanan streamPreviewImage().
     */
    public function streamCoverImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/covers/' . basename($filename);

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody(file_get_contents($path));
    }

    /** AJAX autocomplete untuk search bar */
    public function suggest()
    {
        $keyword = $this->request->getGet('q');
        if (strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }

        $hasil = $this->dokumenModel
            ->select('id, judul, penulis')
            ->like('judul', $keyword)
            ->limit(8)
            ->findAll();

        return $this->response->setJSON($hasil);
    }
}
