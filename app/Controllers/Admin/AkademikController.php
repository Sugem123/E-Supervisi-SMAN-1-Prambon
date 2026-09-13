<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjarModel;
use App\Models\KelasModel;
use App\Models\GuruModel;

class AkademikController extends BaseController
{
    protected $tahunAjarModel;
    protected $kelasModel;
    protected $guruModel;

    public function __construct()
    {
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
    }

    public function tahunAjar()
    {
        return redirect()->to('/admin/pengaturan/tahun-ajar');
    }

    public function kelas()
    {
        $data['kelases'] = $this->kelasModel
            ->select('kelas.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_wali')
            ->join('tahun_ajar', 'tahun_ajar.id = kelas.tahun_ajar_id')
            ->join('guru', 'guru.id = kelas.wali_kelas', 'left')
            ->findAll();

        $data['tahun_ajars'] = $this->tahunAjarModel->findAll();
        $data['gurus'] = $this->guruModel->findAll();

        return view('admin/akademik/kelas', $data);
    }

    public function createKelas()
    {
        $kelasData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'wali_kelas' => $this->request->getPost('wali_kelas'),
            'status' => $this->request->getPost('status'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->kelasModel->insert($kelasData);

        if ($result) {
            return redirect()->to('/admin/akademik/kelas')->with('success', 'Kelas berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kelas');
        }
    }

    public function editKelas($id)
    {
        $data['kelas'] = $this->kelasModel->find($id);

        if (!$data['kelas']) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        $data['tahun_ajars'] = $this->tahunAjarModel->findAll();
        $data['gurus'] = $this->guruModel->findAll();

        return view('admin/akademik/edit_kelas', $data);
    }

    public function updateKelas($id)
    {
        $kelasData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'wali_kelas' => $this->request->getPost('wali_kelas'),
            'status' => $this->request->getPost('status')
        ];

        $result = $this->kelasModel->update($id, $kelasData);

        if ($result) {
            return redirect()->to('/admin/akademik/kelas')->with('success', 'Kelas berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate kelas');
        }
    }
}
