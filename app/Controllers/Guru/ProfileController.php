<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $guruModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
    }

    public function edit()
    {
        $userId = session()->get('id');
        
        // Get user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }
        
        // Get guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        $data = [
            'user' => $user,
            'guru' => $guru
        ];

        return view('guru/profile/edit', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        
        // Validate input
        $rules = [
            'email' => 'required|valid_email',
            'nama' => 'required',
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
        $nama = $this->request->getPost('nama');
        $telepon = $this->request->getPost('telepon');
        $alamat = $this->request->getPost('alamat');
        $mataPelajaran = $this->request->getPost('mata_pelajaran');
        
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
        
        // Update guru data
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if ($guru) {
            $guruData = [
                'nama' => $nama,
                'telepon' => $telepon,
                'alamat' => $alamat,
                'mata_pelajaran' => $mataPelajaran,
                'foto_profil' => $fotoProfil
            ];
            
            if (!$this->guruModel->update($guru['id'], $guruData)) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data guru');
            }
        }
        
        // Update session data if name or email changed
        session()->set([
            'username' => $email,
            'email' => $email
        ]);
        
        return redirect()->to('/guru')->with('success', 'Profil berhasil diperbarui');
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
        
        // Update guru photo if exists
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if ($guru) {
            $this->guruModel->update($guru['id'], ['foto_profil' => $fotoProfil]);
        }
        
        // Update session data
        session()->set(['foto_profil' => $fotoProfil]);
        
        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
    }
}