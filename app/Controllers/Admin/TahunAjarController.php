<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjarModel;

class TahunAjarController extends BaseController
{
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        return redirect()->to('/admin/pengaturan?tab=tahun-ajar');
    }

    public function store()
    {
        if (!$this->validate([
            'tahun_ajar' => 'required',
            'semester' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $statusAktif = $this->request->getPost('status_aktif');
        if ($statusAktif == 'Aktif') {
            $this->tahunAjarModel->set('status_aktif', 'Nonaktif')->where('status_aktif', 'Aktif')->update();
        } else {
            // Ensure default is Nonaktif if not provided or invalid
            $statusAktif = 'Nonaktif';
        }

        $this->tahunAjarModel->save([
            'tahun_ajar' => $this->request->getPost('tahun_ajar'),
            'semester' => $this->request->getPost('semester'),
            'status_aktif' => $statusAktif
        ]);

        return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('success', 'Data berhasil ditambahkan');
    }

    public function update($id)
    {
        if (!$this->validate([
            'tahun_ajar' => 'required',
            'semester' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $statusAktif = $this->request->getPost('status_aktif');

        $data = [
            'tahun_ajar' => $this->request->getPost('tahun_ajar'),
            'semester' => $this->request->getPost('semester'),
        ];

        if ($statusAktif == 'Aktif') {
            $this->tahunAjarModel->set('status_aktif', 'Nonaktif')->where('status_aktif', 'Aktif')->update();
            $data['status_aktif'] = 'Aktif';
        } elseif ($statusAktif == 'Nonaktif') {
            $data['status_aktif'] = 'Nonaktif';
        }

        $this->tahunAjarModel->update($id, $data);

        return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        // Check if active
        $item = $this->tahunAjarModel->find($id);
        if (!$item) {
            return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('error', 'Data tahun ajaran tidak ditemukan.');
        }
        if ($item['status_aktif'] == 'Aktif') {
            return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang Aktif!');
        }

        // Proteksi arsip: tahun Nonaktif yang masih punya jadwal/kelas/kelompok tidak boleh dihapus.
        $db = \Config\Database::connect();
        $jadwalCount = $db->table('jadwal_supervisi')->where('tahun_ajar_id', $id)->countAllResults();
        $kelasCount = $db->table('kelas')->where('tahun_ajar_id', $id)->countAllResults();
        $kelompokCount = $db->table('kelompok_supervisi')->where('tahun_ajar_id', $id)->countAllResults();
        if (($jadwalCount + $kelasCount + $kelompokCount) > 0) {
            return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('error', 'Arsip tahun ini masih dipakai (' . $jadwalCount . ' jadwal, ' . $kelasCount . ' kelas, ' . $kelompokCount . ' kelompok). Hapus arsip hanya bila data historis sudah tidak dibutuhkan.');
        }

        $this->tahunAjarModel->delete($id);
        return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('success', 'Data berhasil dihapus');
    }

    public function activate($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Deactivate all
        $db->table('tahun_ajar')->update(['status_aktif' => 'Nonaktif']);

        // Activate selected
        $this->tahunAjarModel->update($id, ['status_aktif' => 'Aktif']);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('error', 'Gagal mengaktifkan data');
        }

        return redirect()->to('/admin/pengaturan?tab=tahun-ajar')->with('success', 'Tahun Ajaran & Semester berhasil diaktifkan');
    }
}
