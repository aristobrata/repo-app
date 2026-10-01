<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\DokumenModel;
use App\Models\KategoriDokumenModel;
use App\Models\DokumenPreviewPageModel;
use App\Models\ActivityLogModel;

class DocumentManageController extends BaseController
{
    protected DokumenModel $dokumenModel;
    protected KategoriDokumenModel $kategoriModel;
    protected DokumenPreviewPageModel $previewModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->dokumenModel  = new DokumenModel();
        $this->kategoriModel = new KategoriDokumenModel();
        $this->previewModel  = new DokumenPreviewPageModel();
        $this->logModel      = new ActivityLogModel();
        helper('preview');
    }

    public function index()
    {
        $dokumen = $this->dokumenModel->select('dokumen.*, kategori_dokumen.nama as kategori_nama')
            ->join('kategori_dokumen', 'kategori_dokumen.id = dokumen.kategori_id')
            ->orderBy('dokumen.created_at', 'DESC')->paginate(15);
        return view('admin/documents/index', ['dokumen' => $dokumen, 'pager' => $this->dokumenModel->pager]);
    }

    public function createForm() { return view('admin/documents/create', ['kategori' => $this->kategoriModel->findAll()]); }

    private function handleCoverUpload(): ?string
    {
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $namaCover = $cover->getRandomName();
            $cover->move(WRITEPATH . 'uploads/covers', $namaCover);
            return $namaCover;
        }
        return null;
    }

    public function store()
    {
        $rules = [
            'judul' => 'required|min_length[3]', 'penulis' => 'required', 'tahun' => 'required|numeric',
            'kategori_id' => 'required|numeric',
            'file_dokumen' => 'uploaded[file_dokumen]|max_size[file_dokumen,51200]|ext_in[file_dokumen,pdf]',
            'cover' => 'permit_empty|max_size[cover,3072]|ext_in[cover,jpg,jpeg,png]',
        ];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $file = $this->request->getFile('file_dokumen');
        $namaFileAsli = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/originals', $namaFileAsli);
        $namaCover = $this->handleCoverUpload();
        $halamanPreview = (int) ($this->request->getPost('halaman_preview') ?: 5);

        $dokumenId = $this->dokumenModel->insert([
            'kategori_id' => $this->request->getPost('kategori_id'), 'judul' => $this->request->getPost('judul'),
            'penulis' => $this->request->getPost('penulis'), 'tahun' => $this->request->getPost('tahun'),
            'kata_kunci' => $this->request->getPost('kata_kunci'), 'abstrak' => $this->request->getPost('abstrak'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tanggal_berlaku' => $this->request->getPost('tanggal_berlaku') ?: null,
            'file_asli' => $namaFileAsli, 'halaman_preview' => $halamanPreview, 'cover_thumbnail' => $namaCover,
            'ukuran_file' => $file->getSize(), 'status_preview' => 'processing', 'uploaded_by' => session()->get('user_id'),
        ]);

        $hasilPreview = generate_dokumen_preview(WRITEPATH . 'uploads/originals/' . $namaFileAsli, WRITEPATH . 'uploads/previews', $halamanPreview);
        foreach ($hasilPreview as $p) {
            $this->previewModel->insert(['dokumen_id' => $dokumenId, 'halaman_ke' => $p['halaman_ke'], 'file_gambar' => $p['file_gambar']]);
        }
        $this->dokumenModel->update($dokumenId, ['status_preview' => count($hasilPreview) > 0 ? 'ready' : 'failed']);
        $this->logModel->catat(session()->get('user_id'), 'upload_dokumen', 'dokumen', $dokumenId);
        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen berhasil diupload & preview sedang/berhasil diproses.');
    }

    public function editForm(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/documents/edit', ['dokumen' => $dokumen, 'kategori' => $this->kategoriModel->findAll()]);
    }

    public function update(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $rules = ['judul' => 'required|min_length[3]', 'penulis' => 'required', 'tahun' => 'required|numeric',
            'kategori_id' => 'required|numeric', 'cover' => 'permit_empty|max_size[cover,3072]|ext_in[cover,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $dataUpdate = [
            'kategori_id' => $this->request->getPost('kategori_id'), 'judul' => $this->request->getPost('judul'),
            'penulis' => $this->request->getPost('penulis'), 'tahun' => $this->request->getPost('tahun'),
            'kata_kunci' => $this->request->getPost('kata_kunci'), 'abstrak' => $this->request->getPost('abstrak'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tanggal_berlaku' => $this->request->getPost('tanggal_berlaku') ?: null,
        ];
        $namaCoverBaru = $this->handleCoverUpload();
        if ($namaCoverBaru) {
            if (!empty($dokumen['cover_thumbnail'])) {
                $oldCoverPath = WRITEPATH . 'uploads/covers/' . $dokumen['cover_thumbnail'];
                if (is_file($oldCoverPath)) { unlink($oldCoverPath); }
            }
            $dataUpdate['cover_thumbnail'] = $namaCoverBaru;
        }
        $this->dokumenModel->update($id, $dataUpdate);
        $this->logModel->catat(session()->get('user_id'), 'edit_dokumen', 'dokumen', $id, $dataUpdate['judul']);
        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function editVisibilitas(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        $halaman = $this->previewModel->getPagesFor($id);
        return view('admin/documents/edit_visibilitas', compact('dokumen', 'halaman'));
    }

    public function hapusHalamanPreview(int $halamanId)
    {
        $halaman = $this->previewModel->find($halamanId);
        if ($halaman) {
            $path = WRITEPATH . 'uploads/previews/' . $halaman['file_gambar'];
            if (is_file($path)) { unlink($path); }
            $this->previewModel->delete($halamanId);
        }
        return redirect()->back()->with('success', 'Halaman preview dihapus.');
    }

    public function viewFull(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $path = WRITEPATH . 'uploads/originals/' . $dokumen['file_asli'];
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $this->logModel->catat(session()->get('user_id'), 'lihat_dokumen_lengkap', 'dokumen', $id, $dokumen['judul']);
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $dokumen['judul'] . '.pdf"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->setBody(file_get_contents($path));
    }

    public function download(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $path = WRITEPATH . 'uploads/originals/' . $dokumen['file_asli'];
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $this->logModel->catat(session()->get('user_id'), 'unduh_dokumen', 'dokumen', $id, $dokumen['judul']);
        return $this->response->download($path, null)->setFileName($dokumen['judul'] . '.pdf');
    }

    public function destroy(int $id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if ($dokumen) {
            $previewList = $this->previewModel->getPagesFor($id);
            hapus_file_dokumen($dokumen['file_asli'], $previewList);
            if (!empty($dokumen['cover_thumbnail'])) {
                $coverPath = WRITEPATH . 'uploads/covers/' . $dokumen['cover_thumbnail'];
                if (is_file($coverPath)) { unlink($coverPath); }
            }
            $this->dokumenModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_dokumen', 'dokumen', $id, $dokumen['judul']);
        }
        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen dihapus.');
    }
}
