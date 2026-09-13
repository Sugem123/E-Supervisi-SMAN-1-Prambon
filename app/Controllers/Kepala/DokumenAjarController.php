<?php

namespace App\Controllers\Kepala;

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
        // Kepala can access any schedule
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, kelas.nama_kelas')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('jadwal_id', $jadwalId)->findAll();

        $data = [
            'title' => 'Dokumen Ajar - ' . $jadwal['nama_guru'],
            'jadwal' => $jadwal,
            'dokumen' => $dokumen
        ];

        return view('kepala/dokumen_ajar/index', $data);
    }

    public function verify($id)
    {
        $status = $this->request->getPost('status');
        $feedback = $this->request->getPost('feedback');

        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $this->dokumenModel->update($id, [
            'status' => $status,
            'feedback' => $feedback
        ]);

        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
