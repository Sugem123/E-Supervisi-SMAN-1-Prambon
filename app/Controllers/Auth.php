<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SystemSettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->route('dashboard');
        }

        helper('setting');

        return view('auth/login', [
            'namaSekolah' => \get_nama_sekolah(),
            'identitas'   => \get_identitas_publik(),
        ]);
    }

    public function attemptLogin()
    {
        $validation = \Config\Services::validation();

        // Admin boleh masuk pakai username tanpa format email.
        // Semua role boleh pakai username ATAU email pada satu kolom `login`.
        // `email` tetap diterima sebagai alias lama agar form/bookmark lama tidak rusak.
        $rules = [
            'login' => 'permit_empty|min_length[3]',
            'email' => 'permit_empty|min_length[3]',
            'password' => 'required|min_length[8]'
        ];

        $errors = [
            'password' => [
                'required' => 'Kata sandi wajib diisi',
                'min_length' => 'Kata sandi minimal 8 karakter'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            helper('setting');
            return view('auth/login', [
                'validation' => $this->validator,
                'namaSekolah' => \get_nama_sekolah(),
                'identitas'   => \get_identitas_publik(),
            ]);
        }

        $login = trim((string) ($this->request->getPost('login') ?? $this->request->getPost('email') ?? ''));
        $password = (string) $this->request->getPost('password');

        if ($login === '') {
            session()->setFlashdata('error', 'Username atau email wajib diisi');
            return redirect()->route('login')->withInput();
        }

        // Get user model
        $userModel = new \App\Models\UserModel();
        $user = $userModel->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();
        
        // Check if user exists and password is correct
        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            // Get system settings
            $settingModel = new SystemSettingModel();
            $settings = $settingModel->getAllSettings();
            
            // Set session
            $sessionData = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'foto_profil' => $user['foto_profil'],
                'role' => $user['role'],
                'logged_in' => TRUE
            ];
            
            // Add specific system settings to session (whitelist)
            // NOTE: 'nama_sekolah' (new) + 'nama_madrasah' (legacy fallback) both whitelisted.
            $allowedSettings = [
                'nama_sekolah', 'nama_madrasah', 'npsn', 'alamat', 'kecamatan', 'kabupaten', 'provinsi',
                'telepon', 'email', 
                'logo', 'kop_surat', 'sidebar_logo', 
                'nama_kepala', 'nip_kepala'
            ];
            
            foreach ($settings as $key => $value) {
                if (in_array($key, $allowedSettings)) {
                    $sessionData[$key] = $value;
                }
            }
            
            session()->set($sessionData);
            
            // Update last login
            $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Redirect based on user role
            switch ($user['role']) {
                case 'admin':
                    return redirect()->route('admin/dashboard');
                case 'supervisor':
                    return redirect()->route('supervisor/dashboard');
                case 'guru':
                    return redirect()->route('guru/dashboard');
                case 'kepala':
                    return redirect()->route('kepala/dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        } else {
            session()->setFlashdata('error', 'Username/email atau kata sandi salah');
            return redirect()->route('login')->withInput();
        }
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->route('login');
    }
}