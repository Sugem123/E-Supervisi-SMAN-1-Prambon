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

    private function hasStatusColumn(string $table): bool
    {
        try {
            return in_array('status', \Config\Database::connect()->getFieldNames($table), true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'jenis';
        if (!in_array($activeTab, ['jenis', 'aspek'])) {
            $activeTab = 'jenis';
        }

        $data['activeTab'] = $activeTab;
        $data['hasJenisStatus'] = $this->hasStatusColumn('jenis_penilaian');
        $data['hasAspekStatus'] = $this->hasStatusColumn('aspek_penilaian');
        $data['jenis_penilaians'] = $this->jenisPenilaianModel->findAllWithMappedColumns();
        // Admin lihat semua (Aktif + Nonaktif); histori tidak hilang.
        // Tambahkan status jenis agar badge parent bisa tampil di tab aspek.
        $aspekBuilder = $this->aspekPenilaianModel
            ->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis' . ($data['hasJenisStatus'] ? ', jenis_penilaian.status as jenis_status' : ''))
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id');
        $data['aspek_penilaians'] = $aspekBuilder->findAll();
        // Dropdown "tambah aspek" hanya tawarkan jenis Aktif (bila kolom ada).
        $data['jenis_aktif'] = $data['hasJenisStatus']
            ? array_values(array_filter($data['jenis_penilaians'], static fn ($j) => ($j['status'] ?? 'Aktif') === 'Aktif'))
            : $data['jenis_penilaians'];

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

    private function resolveStatusInput(): string
    {
        $status = $this->request->getPost('status');
        return in_array($status, ['Aktif', 'Nonaktif'], true) ? $status : 'Aktif';
    }

    public function createJenisPenilaian()
    {
        $jenisPenilaianData = [
            'nama' => $this->request->getPost('nama_jenis'),
            'skor_maksimal' => $this->request->getPost('skor_maksimal') ?? 48,
            'kategori_skor' => $this->request->getPost('kategori_skor') ?? '{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}'
        ];
        if ($this->hasStatusColumn('jenis_penilaian')) {
            $jenisPenilaianData['status'] = $this->resolveStatusInput();
        }
        
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
        if ($this->hasStatusColumn('jenis_penilaian')) {
            $jenisPenilaianData['status'] = $this->resolveStatusInput();
        }
        
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

    public function toggleJenisStatus($id)
    {
        $row = $this->jenisPenilaianModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Jenis penilaian tidak ditemukan');
        }
        // BC: migrasi belum jalan -> jangan fatal, arahkan dengan pesan jelas
        if (!$this->hasStatusColumn('jenis_penilaian')) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Kolom status belum tersedia. Jalankan migrasi database dulu.');
        }
        $current = $row['status'] ?? 'Aktif';
        $next = $current === 'Aktif' ? 'Nonaktif' : 'Aktif';

        // Peringatan bila menonaktifkan jenis yang masih punya aspek Aktif
        $warning = '';
        if ($next === 'Nonaktif' && $this->hasStatusColumn('aspek_penilaian')) {
            $aktifAspek = $this->aspekPenilaianModel
                ->where('jenis_penilaian_id', $id)
                ->where('status', 'Aktif')
                ->countAllResults();
            if ($aktifAspek > 0) {
                $warning = " ({$aktifAspek} aspek Aktif ikut disembunyikan dari form penilaian)";
            }
        }

        $this->jenisPenilaianModel->update($id, ['status' => $next]);

        return redirect()->to('/admin/instrumen?tab=jenis')->with('success', "Jenis penilaian {$next}{$warning}");
    }

    public function toggleAspekStatus($id)
    {
        $row = $this->aspekPenilaianModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Aspek penilaian tidak ditemukan');
        }
        if (!$this->hasStatusColumn('aspek_penilaian')) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Kolom status belum tersedia. Jalankan migrasi database dulu.');
        }
        $current = $row['status'] ?? 'Aktif';
        $next = $current === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $this->aspekPenilaianModel->update($id, ['status' => $next]);

        return redirect()->to('/admin/instrumen?tab=aspek')->with('success', "Aspek penilaian {$next}");
    }

    public function createAspekPenilaian()
    {
        $aspekPenilaianData = [
            'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'urutan' => $this->request->getPost('urutan'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($this->hasStatusColumn('aspek_penilaian')) {
            $aspekPenilaianData['status'] = $this->resolveStatusInput();
        }
        
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
        if ($this->hasStatusColumn('aspek_penilaian')) {
            $aspekPenilaianData['status'] = $this->resolveStatusInput();
        }
        
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