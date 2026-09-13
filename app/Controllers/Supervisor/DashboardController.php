<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\JenisPenilaianModel;

class DashboardController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->jenisModel = new JenisPenilaianModel();
    }

    public function index()
    {
        $supervisorId = session()->get('id'); // Assuming supervisor ID is stored in session
        
        // Get today's schedules
        $todaySchedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.tanggal_supervisi', date('Y-m-d'))
            ->where('jadwal_supervisi.status', 'Terjadwal')
            ->findAll();

        // Get upcoming schedules
        $upcomingSchedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.tanggal_supervisi >', date('Y-m-d'))
            ->where('jadwal_supervisi.status', 'Terjadwal')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->findAll(5); // Limit to 5 upcoming schedules

        // Get completed supervisions this month
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        $completedCount = $this->jadwalModel
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'Selesai')
            ->where('tanggal_supervisi >=', $startDate)
            ->where('tanggal_supervisi <=', $endDate)
            ->countAllResults();

        // Get assessment types
        $jenisPenilaian = $this->jenisModel->findAll();

        $data = [
            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,
            'completedCount' => $completedCount,
            'jenisPenilaian' => $jenisPenilaian
        ];

        return view('supervisor/dashboard', $data);
    }
}