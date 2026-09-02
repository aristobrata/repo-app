<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriDokumenModel;
use App\Models\DokumenModel;
use App\Models\ActivityLogModel;

class KategoriManageController extends BaseController
{
    protected KategoriDokumenModel $kategoriModel;
    protected DokumenModel $dokumenModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriDokumenModel();
        $this->dokumenModel  = new DokumenModel();
        $this->logModel      = new ActivityLogModel();
    }

    public function index()
    {
        // Tampilkan juga jumlah dokumen per kategori agar admin tahu dampak sebelum hapus
        $kategori = $this->kategoriModel
            ->select('kategori_dokumen.*, COUNT(dokumen.id) as jumlah_dokumen')
            ->join('dokumen', 'dokumen.kategori_id = kategori_dokumen.id', 'left')
            ->groupBy('kategori_dokumen.id')
            ->findAll();

        return view('admin/categories/index', ['kategori' => $kategori]);
    }

    public function createForm()
    {
        return view('admin/categories/create');
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|is_unique[kategori_dokumen.nama]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = $this->request->getPost('nama');
        $slug = url_title($nama, '-', true);

        $id = $this->kategoriModel->insert([
            'nama'      => $nama,
            'slug'      => $slug,
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        $this->logModel->catat(session()->get('user_id'), 'tambah_kategori', 'kategori', $id, $nama);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editForm(int $id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('admin/categories/edit', ['kategori' => $kategori]);
    }

    public function update(int $id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nama' => "required|min_length[3]|is_unique[kategori_dokumen.nama,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = $this->request->getPost('nama');

        $this->kategoriModel->update($id, [
            'nama'      => $nama,
            'slug'      => url_title($nama, '-', true),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        $this->logModel->catat(session()->get('user_id'), 'edit_kategori', 'kategori', $id, $nama);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $jumlahDokumen = $this->dokumenModel->where('kategori_id', $id)->countAllResults();

        if ($jumlahDokumen > 0) {
            return redirect()->back()->with('error',
                "Kategori tidak bisa dihapus karena masih dipakai oleh {$jumlahDokumen} dokumen. Pindahkan dokumen ke kategori lain terlebih dahulu."
            );
        }

        $kategori = $this->kategoriModel->find($id);
        $this->kategoriModel->delete($id);

        $this->logModel->catat(session()->get('user_id'), 'hapus_kategori', 'kategori', $id, $kategori['nama'] ?? null);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori dihapus.');
    }
}
