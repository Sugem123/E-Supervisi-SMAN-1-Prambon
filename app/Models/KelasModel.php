<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tahun_ajar_id', 'nama_kelas', 'wali_kelas', 'status'];
    protected $useTimestamps = false;
}