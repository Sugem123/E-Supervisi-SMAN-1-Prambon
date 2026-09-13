<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'nip', 'foto_profil', 'telepon', 'alamat', 'password', 'role', 'status', 'last_login'];
    protected $useTimestamps = false; // Disable automatic timestamps
    
    // Add updated_at field
    protected $updatedField = 'updated_at';
}