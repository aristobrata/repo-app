<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DokumenModel;
use App\Models\KategoriModel;

class DokumenManageController extends BaseController
{
    public function index()
    {
        $model   = new DokumenModel();
        $dokumen = $model->daftar()->paginate(10, 'dokumen');

        return view('admin/dokumen/index', [
            'title'   => 'Kelola Dokumen',
            'dokumen' => $dokumen,
            'pager'   => $model->pager,
        ]);
    }

    public function createForm()
    {
        $kategoriModel = new KategoriModel();

        return view('admin/dokumen/create', [
            'title'    => 'Tambah Dokumen',
            'kategori' => $kategoriModel->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'judul'       => 'required|min_length[3]|max_length[255]',
            'kategori_id' => 'permit_empty|numeric',
            'file_asli'   => 'uploaded[file_asli]|max_size[file_asli,20480]|ext_in[file_asli,pdf,doc,docx,xls,xlsx]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model   = new DokumenModel();
        $fileAsli = $this->request->getFile('file_asli');
        $namaAsli = $fileAsli->getRandomName();
        $fileAsli->move(WRITEPATH . 'uploads/originals', $namaAsli);

        $cover = $this->handleCoverUpload();

        $model->insert([
            'judul'          => $this->request->getPost('judul'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'kategori_id'    => $this->request->getPost('kategori_id') ?: null,
            'file_asli'      => $namaAsli,
            'cover'          => $cover,
            'status_preview' => 'pending',
            'uploaded_by'    => session()->get('user_id'),
        ]);

        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $model   = new DokumenModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $kategoriModel = new KategoriModel();

        return view('admin/dokumen/edit', [
            'title'    => 'Edit Dokumen',
            'dokumen'  => $dokumen,
            'kategori' => $kategoriModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $model   = new DokumenModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'judul'     => 'required|min_length[3]|max_length[255]',
            'file_asli' => 'permit_empty|max_size[file_asli,20480]|ext_in[file_asli,pdf,doc,docx,xls,xlsx]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'judul'       => $this->request->getPost('judul'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
        ];

        $fileAsli = $this->request->getFile('file_asli');
        if ($fileAsli && $fileAsli->isValid() && ! $fileAsli->hasMoved()) {
            $namaAsli = $fileAsli->getRandomName();
            $fileAsli->move(WRITEPATH . 'uploads/originals', $namaAsli);
            $updateData['file_asli']      = $namaAsli;
            $updateData['status_preview'] = 'pending';
        }

        $cover = $this->handleCoverUpload();
        if ($cover) {
            $updateData['cover'] = $cover;
        }

        $model->update($id, $updateData);

        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $model = new DokumenModel();
        $model->delete($id);

        return redirect()->to('/admin/dokumen')->with('success', 'Dokumen berhasil dihapus.');
    }

    private function handleCoverUpload(): ?string
    {
        $cover = $this->request->getFile('cover');

        if ($cover && $cover->isValid() && ! $cover->hasMoved()) {
            $namaCover = $cover->getRandomName();
            $cover->move(WRITEPATH . 'uploads/covers', $namaCover);

            return $namaCover;
        }

        return null;
    }
}
