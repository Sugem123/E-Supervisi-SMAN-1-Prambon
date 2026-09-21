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
        'status',
        'status_ajuan',
        'alasan_batal',
        'usulan_tanggal',
        'usulan_hari',
        'usulan_jam_ke',
        'usulan_waktu_dari',
        'usulan_waktu_sampai',
        'usulan_kelas_id',
        'usulan_kelas',
        'catatan_supervisor',
        'tgl_respon_supervisor'
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}