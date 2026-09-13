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

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];
        
        $errors = [
            'email' => [
                'required' => 'Email is required',
                'valid_email' => 'Please enter a valid email address'
            ],
            'password' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 8 characters long'
            ]
        ];
        
        if (!$this->validate($rules, $errors)) {
            return view('auth/login', [
                'validation' => $this->validator
            ]);
        }
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        // Get user model
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();
        
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
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
            $allowedSettings = [
                'nama_madrasah', 'alamat', 'telepon', 'email', 
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
            session()->setFlashdata('error', 'Invalid email or password');
            return redirect()->route('login');
        }
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->route('login');
    }
}