<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\InovasiModel;
use App\Models\InovasiLampiranModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class InnovationManageController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected InovasiLampiranModel $lampiranModel;
    protected UserModel $userModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel  = new InovasiModel();
        $this->lampiranModel = new InovasiLampiranModel();
        $this->userModel     = new UserModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function index()
    {
        $inovasi = $this->inovasiModel->select('inovasi.*, users.nama as nama_karyawan')
            ->join('users', 'users.id = inovasi.karyawan_id')
            ->orderBy('inovasi.created_at', 'DESC')->paginate(15);
        return view('admin/innovations/index', ['inovasi' => $inovasi, 'pager' => $this->inovasiModel->pager]);
    }

    public function createForm() { return view('admin/innovations/create', ['karyawan' => $this->userModel->where('status', 'aktif')->findAll()]); }

    private function handleCoverUpload(): ?string
    {
        $cover = $this->request->getFile('foto_ilustrasi');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $namaCover = $cover->getRandomName();
            $cover->move(WRITEPATH . 'uploads/covers', $namaCover);
            return $namaCover;
        }
        return null;
    }

    public function store()
    {
        $rules = ['judul' => 'required|min_length[5]', 'karyawan_id' => 'required|numeric',
            'deskripsi_masalah' => 'required', 'solusi_inovatif' => 'required', 'dampak_manfaat' => 'required',
            'foto_ilustrasi' => 'permit_empty|max_size[foto_ilustrasi,3072]|ext_in[foto_ilustrasi,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $inovasiId = $this->inovasiModel->insert([
            'judul' => $this->request->getPost('judul'), 'karyawan_id' => $this->request->getPost('karyawan_id'),
            'divisi' => $this->request->getPost('divisi'), 'deskripsi_masalah' => $this->request->getPost('deskripsi_masalah'),
            'solusi_inovatif' => $this->request->getPost('solusi_inovatif'), 'dampak_manfaat' => $this->request->getPost('dampak_manfaat'),
            'foto_ilustrasi' => $this->handleCoverUpload(), 'status' => $this->request->getPost('status') ?: 'diajukan',
        ]);
        $this->simpanLampiran($inovasiId);
        $this->logModel->catat(session()->get('user_id'), 'tambah_inovasi', 'inovasi', $inovasiId);
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/innovations/edit', [
            'inovasi' => $inovasi, 'karyawan' => $this->userModel->where('status', 'aktif')->findAll(),
            'lampiran' => $this->lampiranModel->getFor($id),
        ]);
    }

    public function update(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $rules = ['judul' => 'required|min_length[5]', 'karyawan_id' => 'required|numeric',
            'deskripsi_masalah' => 'required', 'solusi_inovatif' => 'required', 'dampak_manfaat' => 'required',
            'foto_ilustrasi' => 'permit_empty|max_size[foto_ilustrasi,3072]|ext_in[foto_ilustrasi,jpg,jpeg,png]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }

        $dataUpdate = [
            'judul' => $this->request->getPost('judul'), 'karyawan_id' => $this->request->getPost('karyawan_id'),
            'divisi' => $this->request->getPost('divisi'), 'deskripsi_masalah' => $this->request->getPost('deskripsi_masalah'),
            'solusi_inovatif' => $this->request->getPost('solusi_inovatif'), 'dampak_manfaat' => $this->request->getPost('dampak_manfaat'),
            'status' => $this->request->getPost('status'),
        ];
        $namaCoverBaru = $this->handleCoverUpload();
        if ($namaCoverBaru) {
            if (!empty($inovasi['foto_ilustrasi'])) {
                $oldPath = WRITEPATH . 'uploads/covers/' . $inovasi['foto_ilustrasi'];
                if (is_file($oldPath)) { unlink($oldPath); }
            }
            $dataUpdate['foto_ilustrasi'] = $namaCoverBaru;
        }
        $this->inovasiModel->update($id, $dataUpdate);
        $this->simpanLampiran($id);
        $this->logModel->catat(session()->get('user_id'), 'edit_inovasi', 'inovasi', $id, $dataUpdate['judul']);
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi berhasil diperbarui.');
    }

    private function simpanLampiran(int $inovasiId): void
    {
        $files = $this->request->getFiles();
        if (!empty($files['lampiran'])) {
            foreach ($files['lampiran'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/originals', $newName);
                    $this->lampiranModel->insert(['inovasi_id' => $inovasiId, 'nama_file' => $newName, 'tipe_file' => $file->getClientMimeType()]);
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
        $inovasi = $this->inovasiModel->find($id);
        if ($inovasi) {
            if (!empty($inovasi['foto_ilustrasi'])) {
                $coverPath = WRITEPATH . 'uploads/covers/' . $inovasi['foto_ilustrasi'];
                if (is_file($coverPath)) { unlink($coverPath); }
            }
            foreach ($this->lampiranModel->getFor($id) as $l) {
                $path = WRITEPATH . 'uploads/originals/' . $l['nama_file'];
                if (is_file($path)) { unlink($path); }
            }
            $this->inovasiModel->delete($id);
            $this->logModel->catat(session()->get('user_id'), 'hapus_inovasi', 'inovasi', $id, $inovasi['judul']);
        }
        return redirect()->to('/admin/inovasi')->with('success', 'Inovasi dihapus.');
    }
}
