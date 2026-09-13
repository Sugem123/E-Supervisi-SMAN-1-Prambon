<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'judul', 'pesan', 'dibaca'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}