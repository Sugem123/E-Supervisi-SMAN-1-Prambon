<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailHasilPenilaianModel extends Model
{
    protected $table = 'detail_hasil_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['hasil_supervisi_id', 'aspek_penilaian_id', 'skor', 'catatan'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}