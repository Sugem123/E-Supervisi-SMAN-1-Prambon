<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenAjarModel extends Model
{
    protected $table            = 'dokumen_ajar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['jadwal_id', 'nama_dokumen', 'link_drive', 'keterangan', 'status', 'feedback'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'jadwal_id'    => 'required|integer',
        'nama_dokumen' => 'required|string|max_length[255]',
        'link_drive'   => 'required|valid_url',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
