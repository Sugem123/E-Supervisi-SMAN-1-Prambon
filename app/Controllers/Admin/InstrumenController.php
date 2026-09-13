<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;

class InstrumenController extends BaseController
{
    protected $jenisPenilaianModel;
    protected $aspekPenilaianModel;

    public function __construct()
    {
        $this->jenisPenilaianModel = new JenisPenilaianModel();
        $this->aspekPenilaianModel = new AspekPenilaianModel();
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'jenis';
        if (!in_array($activeTab, ['jenis', 'aspek'])) {
            $activeTab = 'jenis';
        }

        $data['activeTab'] = $activeTab;
        $data['jenis_penilaians'] = $this->jenisPenilaianModel->findAllWithMappedColumns();
        $data['aspek_penilaians'] = $this->aspekPenilaianModel
            ->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id')
            ->findAll();

        $db = \Config\Database::connect();
        $maxUrutanResult = $db->table('aspek_penilaian')
            ->select('jenis_penilaian_id, MAX(urutan) as max_urutan')
            ->groupBy('jenis_penilaian_id')
            ->get()
            ->getResultArray();

        $data['max_urutan'] = [];
        foreach ($maxUrutanResult as $row) {
            $data['max_urutan'][$row['jenis_penilaian_id']] = $row['max_urutan'];
        }

        return view('admin/instrumen/index', $data);
    }

    public function jenisPenilaian()
    {
        return redirect()->to('/admin/instrumen?tab=jenis');
    }

    public function aspekPenilaian()
    {
        return redirect()->to('/admin/instrumen?tab=aspek');
    }

    public function createJenisPenilaian()
    {
        $jenisPenilaianData = [
            'nama' => $this->request->getPost('nama_jenis'),
            'skor_maksimal' => $this->request->getPost('skor_maksimal') ?? 48,
            'kategori_skor' => $this->request->getPost('kategori_skor') ?? '{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}'
        ];
        
        $result = $this->jenisPenilaianModel->insert($jenisPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis penilaian');
        }
    }

    public function updateJenisPenilaian($id)
    {
        $jenisPenilaianData = [
            'nama' => $this->request->getPost('nama_jenis'),
            'skor_maksimal' => $this->request->getPost('skor_maksimal'),
            'kategori_skor' => $this->request->getPost('kategori_skor')
        ];
        
        $result = $this->jenisPenilaianModel->update($id, $jenisPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate jenis penilaian');
        }
    }

    public function deleteJenisPenilaian($id)
    {
        // Check if this jenis penilaian is being used by any aspek penilaian
        $aspekCount = $this->aspekPenilaianModel->where('jenis_penilaian_id', $id)->countAllResults();
        
        if ($aspekCount > 0) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Tidak dapat menghapus jenis penilaian ini karena masih digunakan oleh aspek penilaian');
        }
        
        $result = $this->jenisPenilaianModel->delete($id);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil dihapus');
        } else {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Gagal menghapus jenis penilaian');
        }
    }

    public function createAspekPenilaian()
    {
        $aspekPenilaianData = [
            'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'urutan' => $this->request->getPost('urutan'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->aspekPenilaianModel->insert($aspekPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan aspek penilaian');
        }
    }

    public function updateAspekPenilaian($id)
    {
        $aspekPenilaianData = [
            'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'urutan' => $this->request->getPost('urutan')
        ];
        
        $result = $this->aspekPenilaianModel->update($id, $aspekPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate aspek penilaian');
        }
    }

    public function deleteAspekPenilaian($id)
    {
        // Check if this aspek penilaian is being used in detail hasil penilaian
        $db = \Config\Database::connect();
        $detailCount = $db->table('detail_hasil_penilaian')
                          ->where('aspek_penilaian_id', $id)
                          ->countAllResults();
        
        if ($detailCount > 0) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Tidak dapat menghapus aspek penilaian ini karena masih digunakan dalam penilaian');
        }
        
        $result = $this->aspekPenilaianModel->delete($id);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil dihapus');
        } else {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Gagal menghapus aspek penilaian');
        }
    }
}