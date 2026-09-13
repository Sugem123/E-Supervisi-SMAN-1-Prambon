<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function edit()
    {
        $userId = session()->get('id');
        
        // Get user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }
        
        $data = [
            'user' => $user
        ];

        return view('kepala/profile/edit', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        
        // Validate input
        $rules = [
            'email' => 'required|valid_email',
            'telepon' => 'permit_empty|regex_match[/^(\+62|62|0)[0-9]{8,15}$/]',
        ];
        
        // Check if a file is uploaded
        if ($this->request->getFile('foto_profil') && $this->request->getFile('foto_profil')->isValid()) {
            $rules['foto_profil'] = 'uploaded[foto_profil]|max_size[foto_profil,2048]|mime_in[foto_profil,image/jpg,image/jpeg,image/png]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $email = $this->request->getPost('email');
        $telepon = $this->request->getPost('telepon');
        $alamat = $this->request->getPost('alamat');
        
        // Check if email is unique (excluding current user)
        $existingUser = $this->userModel
            ->where('email', $email)
            ->where('id !=', $userId)
            ->first();
            
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan');
        }
        
        // Handle photo upload
        $fotoProfil = $user['foto_profil'] ?? null;
        if ($this->request->getFile('foto_profil') && $this->request->getFile('foto_profil')->isValid()) {
            $file = $this->request->getFile('foto_profil');
            
            // Generate random name for the file
            $fileName = $file->getRandomName();
            
            // Move file to target directory
            if ($file->move(ROOTPATH . 'public/uploads/profile', $fileName)) {
                // Delete old file if exists
                if ($fotoProfil && file_exists(ROOTPATH . 'public/uploads/profile/' . $fotoProfil)) {
                    unlink(ROOTPATH . 'public/uploads/profile/' . $fotoProfil);
                }
                $fotoProfil = $fileName;
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto profil');
            }
        }
        
        // Handle password update
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        
        $userData = [
            'email' => $email,
            'foto_profil' => $fotoProfil,
            'telepon' => $telepon,
            'alamat' => $alamat,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($password)) {
            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('error', 'Konfirmasi password tidak cocok');
            }
            
            // Update user with password
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Update user data
        if (!$this->userModel->update($userId, $userData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna');
        }
        
        // Update session data if email changed
        session()->set([
            'username' => $email,
            'email' => $email
        ]);
        
        return redirect()->to('/kepala')->with('success', 'Profil berhasil diperbarui');
    }
    
    public function updatePhoto()
    {
        $userId = session()->get('id');
        
        // Get user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }
        
        // Validate photo upload
        $rules = [
            'foto_profil' => 'uploaded[foto_profil]|max_size[foto_profil,2048]|mime_in[foto_profil,image/jpg,image/jpeg,image/png]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Handle photo upload
        $fotoProfil = $user['foto_profil'] ?? null;
        if ($this->request->getFile('foto_profil') && $this->request->getFile('foto_profil')->isValid()) {
            $file = $this->request->getFile('foto_profil');
            
            // Generate random name for the file
            $fileName = $file->getRandomName();
            
            // Move file to target directory
            if ($file->move(ROOTPATH . 'public/uploads/profile', $fileName)) {
                // Delete old file if exists
                if ($fotoProfil && file_exists(ROOTPATH . 'public/uploads/profile/' . $fotoProfil)) {
                    unlink(ROOTPATH . 'public/uploads/profile/' . $fotoProfil);
                }
                $fotoProfil = $fileName;
            } else {
                return redirect()->back()->with('error', 'Gagal mengunggah foto profil');
            }
        }
        
        // Update user photo
        if (!$this->userModel->update($userId, ['foto_profil' => $fotoProfil])) {
            return redirect()->back()->with('error', 'Gagal memperbarui foto profil');
        }
        
        // Update session data
        session()->set(['foto_profil' => $fotoProfil]);
        
        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
    }

    public function deletePhoto()
    {
        $userId = session()->get('id');
        
        // Get user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }
        
        $fotoProfil = $user['foto_profil'];
        
        if ($fotoProfil && file_exists(ROOTPATH . 'public/uploads/profile/' . $fotoProfil)) {
            unlink(ROOTPATH . 'public/uploads/profile/' . $fotoProfil);
        }
        
        // Update user photo to null
        if (!$this->userModel->update($userId, ['foto_profil' => null])) {
            return redirect()->back()->with('error', 'Gagal menghapus foto profil');
        }
        
        // Update session data
        session()->set(['foto_profil' => null]);
        
        return redirect()->back()->with('success', 'Foto profil berhasil dihapus');
    }
}