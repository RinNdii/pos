<?php
namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, arahkan sesuai role agar tidak perlu login ulang
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard'); 
        }
        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $model   = new UserModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user) {
            // Verifikasi kecocokan hash password
            if (password_verify($password, $user['password'])) {
                $sessionData = [
                    'id'        => $user['id'],
                    'username'  => $user['username'],
                    'role'      => $user['role'],
                    'logged_in' => TRUE
                ];
                $session->set($sessionData);
                
                // Redirect ke halaman utama setelah login berhasil
                return redirect()->to('/dashboard'); 
            } else {
                $session->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->to('/auth');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}