<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\TahunAjarModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        $userId = session()->get('id');
        
        // Get guru data by user ID
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }
        
        // Get jadwal where guru_id matches
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as supervisor_name')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.user_id = jadwal_supervisi.supervisor_id')
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->findAll();
        
        $data = [
            'jadwal' => $jadwal,
            'guru' => $guru
        ];

        return view('guru/jadwal/index', $data);
    }
}