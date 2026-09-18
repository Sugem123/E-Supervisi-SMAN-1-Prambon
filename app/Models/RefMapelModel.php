<?php

namespace App\Models;

use CodeIgniter\Model;

class RefMapelModel extends Model
{
    protected $table = 'ref_mapel';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_mapel', 'jenjang', 'kelompok', 'status'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
