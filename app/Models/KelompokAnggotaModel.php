<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokAnggotaModel extends Model
{
    protected $table = 'kelompok_anggota';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kelompok_id', 'guru_id'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
