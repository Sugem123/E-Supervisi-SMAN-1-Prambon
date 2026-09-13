<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjarModel extends Model
{
    protected $table = 'tahun_ajar';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tahun_ajar', 'semester', 'status_aktif'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}