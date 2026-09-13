<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\DokumenAjarModel;
use App\Models\JadwalSupervisiModel;

class DokumenAjarController extends BaseController
{
    protected $dokumenModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->dokumenModel = new DokumenAjarModel();
        $this->jadwalModel = new JadwalSupervisiModel();
    }

    public function index($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Verify access: Schedule must be assigned to this supervisor
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, kelas.nama_kelas')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau akses ditolak.');
        }

        $dokumen = $this->dokumenModel->where('jadwal_id', $jadwalId)->findAll();

        $data = [
            'title' => 'Dokumen Ajar - ' . $jadwal['nama_guru'],
            'jadwal' => $jadwal,
            'dokumen' => $dokumen
        ];

        return view('supervisor/dokumen_ajar/index', $data);
    }
}
