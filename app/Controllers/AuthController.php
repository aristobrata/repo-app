<?php
namespace App\Controllers;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected ActivityLogModel $logModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
    }
    public function loginForm()
    {
        if (session()->get('logged_in')) { return redirect()->to('/dashboard'); }
        return view('auth/login');
    }
    public function login()
    {
        $rules = ['email' => 'required|valid_email', 'password' => 'required'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = $this->userModel->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }
        if ($user['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Akun Anda nonaktif. Hubungi administrator.');
        }
        session()->set([
            'user_id'   => $user['id'],
            'nama'      => $user['nama'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'status'    => $user['status'],
            'logged_in' => true,
        ]);
        $this->logModel->catat($user['id'], 'login', 'user', $user['id']);
        return redirect()->to('/dashboard');
    }
    public function logout()
    {
        $this->logModel->catat(session()->get('user_id'), 'logout', 'user', session()->get('user_id'));
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
    public function forgotPasswordForm() { return view('auth/forgot_password'); }
    public function forgotPassword()
    {
        $email = $this->request->getPost('email');
        $user  = $this->userModel->where('email', $email)->first();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], ['reset_token' => $token]);
        }
        return redirect()->to('/login')->with('success', 'Jika email terdaftar, instruksi reset password telah dikirim.');
    }
}
