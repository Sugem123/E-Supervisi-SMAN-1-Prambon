<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\UserModel;

class GuruController extends BaseController
{
    protected $guruModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $gurus = $this->guruModel->select('guru.*, users.email, users.status as user_status')
            ->join('users', 'guru.user_id = users.id', 'left')
            ->findAll();

        $data = [
            'title' => 'Data Guru',
            'gurus' => $gurus
        ];

        return view('admin/guru/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Guru',
        ];

        return view('admin/guru/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Create the user first
            $userData = [
                'email' => $this->request->getPost('email'),
                'password' => password_hash('123456', PASSWORD_DEFAULT), // Default password
                'role' => $this->request->getPost('is_supervisor') ? 'supervisor' : 'guru',
                'status' => 'Aktif'
            ];

            $this->userModel->save($userData);
            $userId = $this->userModel->getInsertID();

            // Create the guru record
            $guruData = [
                'user_id' => $userId,
                'nama' => $this->request->getPost('nama'),
                'nip' => $this->request->getPost('nip'),
                'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
                'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
                'is_supervisor' => $this->request->getPost('is_supervisor') ? 1 : 0,
            ];

            $this->guruModel->save($guruData);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
            }

            return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $guru = $this->guruModel->select('guru.*, users.email, users.status as user_status')
            ->join('users', 'guru.user_id = users.id', 'left')
            ->where('guru.id', $id)
            ->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Data Guru',
            'guru' => $guru
        ];

        return view('admin/guru/edit', $data);
    }

    public function update($id)
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'nama' => 'required',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $guru['user_id'] . ']',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Update guru data
            $guruData = [
                'nama' => $this->request->getPost('nama'),
                'nip' => $this->request->getPost('nip'),
                'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
                'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
                'is_supervisor' => $this->request->getPost('is_supervisor') ? 1 : 0,
            ];

            $this->guruModel->update($id, $guruData);

            // Update user data
            $userData = [
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('is_supervisor') ? 'supervisor' : 'guru'
            ];

            $this->userModel->update($guru['user_id'], $userData);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Silakan coba lagi.');
            }

            return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Delete guru (user will remain but won't be associated with a guru)
        $this->guruModel->delete($id);

        return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil dihapus.');
    }

    public function toggleSupervisor($id)
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Toggle supervisor status
        $newStatus = $guru['is_supervisor'] == 1 ? 0 : 1;
        $this->guruModel->update($id, ['is_supervisor' => $newStatus]);

        // Update user role
        $role = $newStatus == 1 ? 'supervisor' : 'guru';
        $this->userModel->update($guru['user_id'], ['role' => $role]);

        $statusText = $newStatus == 1 ? 'supervisor' : 'guru';
        return redirect()->back()->with('success', "Status berhasil diubah menjadi {$statusText}.");
    }
}
