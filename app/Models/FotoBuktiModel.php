<?php

namespace App\Models;

use CodeIgniter\Model;

class FotoBuktiModel extends Model
{
    protected $table = 'foto_bukti';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jadwal_supervisi_id', 'file_path', 'jenis_bukti', 'keterangan', 'created_at'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}