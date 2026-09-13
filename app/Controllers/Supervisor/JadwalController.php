<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        $supervisorId = session()->get('id');

        // Get all schedules for this supervisor
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data = [
            'schedules' => $schedules
        ];

        return view('supervisor/jadwal/index', $data);
    }

    public function detail($id)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }

        $data = [
            'schedule' => $schedule
        ];

        return view('supervisor/jadwal/detail', $data);
    }
}