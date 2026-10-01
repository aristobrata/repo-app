<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class UserManageController extends BaseController
{
    protected UserModel $userModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('created_at', 'DESC')->paginate(20);
        return view('admin/users/index', ['users' => $users, 'pager' => $this->userModel->pager]);
    }

    public function createForm() { return view('admin/users/create'); }

    public function store()
    {
        $rules = ['nama' => 'required|min_length[3]', 'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]', 'role' => 'required|in_list[super_admin,admin,karyawan]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }
        $userId = $this->userModel->insert([
            'nip' => $this->request->getPost('nip'), 'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'), 'password' => $this->request->getPost('password'),
            'divisi' => $this->request->getPost('divisi'), 'role' => $this->request->getPost('role'), 'status' => 'aktif',
        ]);
        $this->logModel->catat(session()->get('user_id'), 'tambah_user', 'user', $userId);
        return redirect()->to('/admin/users')->with('success', 'Akun karyawan berhasil dibuat.');
    }

    public function editForm(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return view('admin/users/edit', ['user' => $user]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $rules = ['nama' => 'required|min_length[3]', 'email' => "required|valid_email|is_unique[users.email,id,{$id}]", 'role' => 'required|in_list[super_admin,admin,karyawan]'];
        if (!$this->validate($rules)) { return redirect()->back()->withInput()->with('errors', $this->validator->getErrors()); }
        $this->userModel->update($id, [
            'nip' => $this->request->getPost('nip'), 'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'), 'divisi' => $this->request->getPost('divisi'),
            'role' => $this->request->getPost('role'),
        ]);
        $this->logModel->catat(session()->get('user_id'), 'edit_user', 'user', $id);
        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(int $id)
    {
        $passwordBaru = bin2hex(random_bytes(4));
        $this->userModel->update($id, ['password' => $passwordBaru]);
        $this->logModel->catat(session()->get('user_id'), 'reset_password', 'user', $id);
        return redirect()->back()->with('success', "Password berhasil direset. Password sementara: {$passwordBaru}");
    }

    public function toggleStatus(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $statusBaru = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->userModel->update($id, ['status' => $statusBaru]);
        $this->logModel->catat(session()->get('user_id'), 'ubah_status_user', 'user', $id, "status baru: {$statusBaru}");
        return redirect()->back()->with('success', "Status akun diubah menjadi {$statusBaru}.");
    }
}
