<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table = 'users'; // Sesuaikan dengan nama tabel pengguna Anda

    public function getUserRoleCounts()
    {
        return $this->select('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->findAll();
    }

    public function getUserStatusCounts()
    {
        return $this->select('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->findAll();
    }

    public function getUsers($role = null, $status = null)
    {
        $builder = $this->db->table('users');
        $builder->select('username, email, role, status');

        if ($role) {
            $builder->where('role', $role);
        }

        if ($status !== null) {
            $builder->where('status', $status);
        }

        return $builder->get()->getResultArray();
    }
}
