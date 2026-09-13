<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'foto_profil', 'telepon', 'alamat', 'nama', 'nip', 'pangkat_golongan', 'mata_pelajaran', 'status_kepegawaian', 'is_supervisor'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}