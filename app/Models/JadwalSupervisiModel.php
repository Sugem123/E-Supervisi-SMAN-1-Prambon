<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalSupervisiModel extends Model
{
    protected $table = 'jadwal_supervisi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tahun_ajar_id',
        'guru_id',
        'supervisor_id',
        'mata_pelajaran',
        'mapel_id',
        'kelas',
        'kelas_id',
        'kelompok_id',
        'jam_ke',
        'hari',
        'tanggal_supervisi',
        'waktu_dari',
        'waktu_sampai',
        'materi_supervisi',
        'status'
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}