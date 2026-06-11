<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register(): string
    {
        return view('layouts/header', ['title' => 'Registrasi - SIMPEL-MAS'])
            . view('auth/register')
            . view('layouts/footer');
    }

    public function doRegister()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'permit_empty|min_length[10]|max_length[20]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userData  = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'role'     => 'warga',
        ];

        $userId = $userModel->insert($userData);

        if (! $userId) {
            return redirect()->back()->withInput()->with('error', 'Registrasi gagal. Silakan coba lagi.');
        }

        $user = $userModel->find($userId);

        $this->session->set([
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang, ' . $user['name'] . '.');
    }

    public function login(): string
    {
        return view('layouts/header', ['title' => 'Login - SIMPEL-MAS'])
            . view('auth/login')
            . view('layouts/footer');
    }

    public function doLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $email     = $this->request->getPost('email');
        $password  = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        $this->session->set([
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ]);

        if (in_array($user['role'], ['admin_instansi', 'super_admin'])) {
            return redirect()->to('/admin')->with('success', 'Selamat datang, ' . $user['name'] . '.');
        }

        return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user['name'] . '.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/')->with('success', 'Anda telah logout.');
    }
}
