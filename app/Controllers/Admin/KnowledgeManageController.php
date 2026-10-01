<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\KnowledgeModel;
use App\Models\KnowledgeLampiranModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

/**
 * CRUD lengkap untuk Knowledge Management Hub. Struktur & alur SAMA seperti
 * Admin\InnovationManageController -- best practice / lesson learned / tips teknis
 * yang di-input Admin/Super Admin, bukan pengajuan mandiri karyawan.
 */
class KnowledgeManageController extends BaseController
{
    protected KnowledgeModel $knowledgeModel;
    protected KnowledgeLampiranModel $lampiranModel;
    protected UserModel $userModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->knowledgeModel = new KnowledgeModel();
        $this->lampiranModel  = new KnowledgeLampiranModel();
        $this->userModel      = new UserModel();
        $this->logModel       = new ActivityLogModel();
    }

    public function index()
    {
        $knowledge = $this->knowledgeModel->select('knowledge_hub.*, users.nama as nama_penulis')
            ->join('users', 'users.id = knowledge_hub.penulis_id')
            ->orderBy('knowledge_hub.created_at', 'DESC')->paginate(15);
        return view('admin/knowledge/index', ['knowledge' => $knowledge, 'pager' => $this->knowledgeModel->pager]);
    }

    public function createForm() { return view('admin/knowledge/create', ['penulis' => $this->userModel->where('status', 'aktif')->findAll()]); }

    private function handleCoverUpload(): ?string
    {
        $cover = $this->request->getFile('foto_sampul');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $namaCover = $cover->getRandomName();
            $cover->move(WRITEPATH . 'uploads/covers', $namaCover);
            return $namaCover;
        }
        return null;
    }

    public function store()
    {
        $rules = ['judul' => 'required|min_length[5]', 'penulis_id' => 'required|numeric',
            'ringkasan' => 'required', 'konten' => 'required',
            'foto_sampul' => 'permit_empty|max_size[foto_sampul,3072]|ext_in[foto_sampul,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $id = $this->knowledgeModel->insert([
            'judul' => $this->request->getPost('judul'), 'penulis_id' => $this->request->getPost('penulis_id'),
            'divisi' => $this->request->getPost('divisi'), 'topik' => $this->request->getPost('topik'),
            'ringkasan' => $this->request->getPost('ringkasan'), 'konten' => $this->request->getPost('konten'),
            'referensi' => $this->request->getPost('referensi'), 'foto_sampul' => $this->handleCoverUpload(),
            'status' => $this->request->getPost('status') ?: 'dipublikasikan',
        ]);
        $this->simpanLampiran($id);
        $this->logModel->catat(session()->get('user_id'), 'tambah_knowledge', 'knowledge', $id);
        return redirect()->to('/admin/knowledge')->with('success', 'Knowledge item berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $item = $this->knowledgeModel->find($id);
        if (!$item) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/knowledge/edit', [
            'item' => $item, 'penulis' => $this->userModel->where('status', 'aktif')->findAll(),
            'lampiran' => $this->lampiranModel->getFor($id),
        ]);
    }

    public function update(int $id)
    {
        $item = $this->knowledgeModel->find($id);
        if (!$item) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $rules = ['judul' => 'required|min_length[5]', 'penulis_id' => 'required|numeric',
            'ringkasan' => 'required', 'konten' => 'required',
            'foto_sampul' => 'permit_empty|max_size[foto_sampul,3072]|ext_in[foto_sampul,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $dataUpdate = [
            'judul' => $this->request->getPost('judul'), 'penulis_id' => $this->request->getPost('penulis_id'),
            'divisi' => $this->request->getPost('divisi'), 'topik' => $this->request->getPost('topik'),
            'ringkasan' => $this->request->getPost('ringkasan'), 'konten' => $this->request->getPost('konten'),
            'referensi' => $this->request->getPost('referensi'), 'status' => $this->request->getPost('status'),
        ];
        $namaCoverBaru = $this->handleCoverUpload();
        if ($namaCoverBaru) {
            if (!empty($item['foto_sampul'])) {
                $oldPath = WRITEPATH . 'uploads/covers/' . $item['foto_sampul'];
                if (is_file($oldPath)) { unlink($oldPath); }
            }
            $dataUpdate['foto_sampul'] = $namaCoverBaru;
        }
        $this->knowledgeModel->update($id, $dataUpdate);
        $this->simpanLampiran($id);
        $this->logModel->catat(session()->get('user_id'), 'edit_knowledge', 'knowledge', $id, $dataUpdate['judul']);
        return redirect()->to('/admin/knowledge')->with('success', 'Knowledge item berhasil diperbarui.');
    }

    private function simpanLampiran(int $knowledgeId): void
    {
        $files = $this->request->getFiles();
        if (!empty($files['lampiran'])) {
            foreach ($files['lampiran'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/originals', $newName);
                    $this->lampiranModel->insert(['knowledge_id' => $knowledgeId, 'nama_file' => $newName, 'tipe_file' => $file->getClientMimeType()]);
                }
            }
        }
    }

    public function hapusLampiran(int $lampiranId)
    {
        $lampiran = $this->lampiranModel->find($lampiranId);
        if ($lampiran) {
            $path = WRITEPATH . 'uploads/originals/' . $lampiran['nama_file'];
            if (is_file($path)) { unlink($path); }
            $this->lampiranModel->delete($lampiranId);
        }
        return redirect()->back()->with('success', 'Lampiran dihapus.');
    }

    public function destroy(int $id)
    {
        $item = $this->knowledgeModel->find($id);
        if ($item) {
            if (!empty($item['foto_sampul'])) {
                $coverPath = WRITEPATH . 'uploads/covers/' . $item['foto_sampul'];
                if (is_file($coverPath)) { unlink($coverPath); }
            }
            foreach ($this->lampiranModel->getFor($id) as $l) {
                $path = WRITEPATH . 'uploads/originals/' . $l['nama_file'];
                if (is_file($path)) { unlink($path); }
            }
            $this->knowledgeModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_knowledge', 'knowledge', $id, $item['judul']);
        }
        return redirect()->to('/admin/knowledge')->with('success', 'Knowledge item dihapus.');
    }
}
