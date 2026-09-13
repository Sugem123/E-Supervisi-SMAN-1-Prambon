<?php

namespace App\Models;

use CodeIgniter\Model;

class AspekPenilaianModel extends Model
{
    protected $table = 'aspek_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jenis_penilaian_id', 'nama_aspek', 'urutan', 'created_at'];
    protected $useTimestamps = false;
    
    // Ensure we're getting the proper data with joins
    public function getAspekWithJenis()
    {
        return $this->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
                    ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id')
                    ->findAll();
    }
}