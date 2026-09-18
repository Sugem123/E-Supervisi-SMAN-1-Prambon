<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilSupervisiModel extends Model
{
    protected $table = 'hasil_supervisi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'jadwal_supervisi_id', 
        'jenis_penilaian_id', 
        'perencanaan_skor', 
        'perencanaan_catatan',
        'pelaksanaan_skor',
        'pelaksanaan_catatan',
        'penilaian_skor',
        'penilaian_catatan',
        'total_skor',
        'nilai_akhir',
        'ketercapaian',
        'rekomendasi',
        'berita_acara_path',
        'link_video',
        'rtl'
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}