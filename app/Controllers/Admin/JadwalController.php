<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\UserModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class JadwalController extends BaseController
{
    protected $jadwalSupervisiModel;
    protected $tahunAjarModel;
    protected $guruModel;
    protected $userModel;

    public function __construct()
    {
        $this->jadwalSupervisiModel = new JadwalSupervisiModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $query = $this->jadwalSupervisiModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, kelas.nama_kelas, users.username as nama_supervisor')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left');

        // Lembaran baru per tahun: index hanya tampilkan tahun aktif.
        // Arsip tetap tersimpan; aktifkan tahun lama untuk melihatnya.
        if ($tahunAjarAktif) {
            $query->where('jadwal_supervisi.tahun_ajar_id', $tahunAjarAktif['id']);
        }

        $data['jadwals'] = $query->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')->findAll();
        $data['tahun_ajar_aktif'] = $tahunAjarAktif;
        $data['tahun_ajars'] = $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->orderBy('id', 'DESC')->findAll();
        $data['arsip_count'] = $tahunAjarAktif
            ? 0
            : $this->jadwalSupervisiModel->countAll();

        return view('admin/jadwal/index', $data);
    }

    public function create()
    {
        // Get active tahun ajar
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        
        // Get all active classes
        $kelasModel = new \App\Models\KelasModel();
        $kelases = [];
        
        if ($tahunAjarAktif) {
            $kelases = $kelasModel->where('tahun_ajar_id', $tahunAjarAktif['id'])->where('status', 'Aktif')->findAll();
        }
        
        // Get supervisors (users with role supervisor or kepala) including role info
        $supervisors = $this->userModel->select('id, username, role')->whereIn('role', ['supervisor', 'kepala'])->where('status', 'Aktif')->findAll();

        $refMapelModel = new \App\Models\RefMapelModel();
        $mapels = $refMapelModel->where('status', 'Aktif')->orderBy('nama_mapel', 'ASC')->findAll();

        $kelompokModel = new \App\Models\KelompokSupervisiModel();
        $kelompoks = $kelompokModel->orderBy('nama_kelompok', 'ASC')->findAll();
        
        $data['tahun_ajar'] = $tahunAjarAktif;
        $data['kelases'] = $kelases;
        $data['gurus'] = $this->guruModel->findAll();
        $data['supervisors'] = $supervisors;
        $data['mapels'] = $mapels;
        $data['kelompoks'] = $kelompoks;
        
        return view('admin/jadwal/create', $data);
    }

    public function store()
    {
        $tahunAjarId = $this->request->getPost('tahun_ajar_id');
        $guruId = $this->request->getPost('guru_id');
        $supervisorId = $this->request->getPost('supervisor_id');
        $kelompokId = $this->request->getPost('kelompok_id');
        $tanggalSupervisi = $this->request->getPost('tanggal_supervisi');
        $jamKe = $this->request->getPost('jam_ke');

        // Validasi fleksibel: mendukung 1 guru disupervisi oleh supervisor/kelompok berbeda
        // dengan proteksi bentrok waktu (collision guard) dan anti-duplikasi kelompok
        $dupMsg = $this->findDuplicateScheduleMessage(
            $tahunAjarId,
            $guruId,
            $supervisorId,
            $kelompokId,
            $tanggalSupervisi,
            $jamKe,
            null
        );
        if ($dupMsg !== null) {
            return redirect()->back()->withInput()->with('error', $dupMsg);
        }

        // Get class name
        $kelasModel = new \App\Models\KelasModel();
        $kelas = $kelasModel->find($this->request->getPost('kelas_id'));
        
        // Sanitize mata_pelajaran input and collapse whitespace
        $mpRaw = $this->request->getPost('mata_pelajaran');
        $mp = $mpRaw !== null ? trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $mpRaw))) : '';

        // If mata_pelajaran is empty, try to fallback to the guru's stored mata_pelajaran to avoid blanks
        if ($mp === '' && $this->request->getPost('guru_id')) {
            $guru = $this->guruModel->find($this->request->getPost('guru_id'));
            $mp = $guru['mata_pelajaran'] ?? '';
        }

        $mapelId = $this->request->getPost('mapel_id');
        $mapelId = ($mapelId === '' || $mapelId === null) ? null : (int) $mapelId;
        if ($mapelId !== null && !(new \App\Models\RefMapelModel())->find($mapelId)) {
            return redirect()->back()->withInput()->with('error', 'Mata pelajaran referensi tidak ditemukan.');
        }

        $kelompokId = $this->request->getPost('kelompok_id');
        $kelompokId = ($kelompokId === '' || $kelompokId === null) ? null : (int) $kelompokId;
        if ($kelompokId !== null && !(new \App\Models\KelompokSupervisiModel())->find($kelompokId)) {
            return redirect()->back()->withInput()->with('error', 'Kelompok supervisi tidak ditemukan.');
        }

        $jadwalData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'supervisor_id' => $this->request->getPost('supervisor_id'),
            'mata_pelajaran' => $mp,
            'mapel_id' => $mapelId,
            'kelas' => $kelas ? $kelas['nama_kelas'] : '', // Store class name for backward compatibility
            'kelas_id' => $this->request->getPost('kelas_id'),
            'kelompok_id' => $kelompokId,
            'jam_ke' => $this->request->getPost('jam_ke'),
            'hari' => $this->request->getPost('hari'),
            'tanggal_supervisi' => $this->request->getPost('tanggal_supervisi'),
            'waktu_dari' => $this->request->getPost('waktu_dari'),
            'waktu_sampai' => $this->request->getPost('waktu_sampai'),
            'materi_supervisi' => $this->request->getPost('materi_supervisi'),
            'status' => 'Terjadwal',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->jadwalSupervisiModel->insert($jadwalData);
        
        if ($result) {
            return redirect()->to('/admin/jadwal')->with('success', 'Jadwal supervisi berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal supervisi');
        }
    }

    public function show($id)
    {
        $data['jadwal'] = $this->jadwalSupervisiModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, ref_mapel.nama_mapel, kelompok_supervisi.nama_kelompok')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('ref_mapel', 'ref_mapel.id = jadwal_supervisi.mapel_id', 'left')
            ->join('kelompok_supervisi', 'kelompok_supervisi.id = jadwal_supervisi.kelompok_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->first();
            
        if (!$data['jadwal']) {
            return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }

        return view('admin/jadwal/show', $data);
    }

    public function edit($id)
    {
        $data['jadwal'] = $this->jadwalSupervisiModel->find($id);
        
        if (!$data['jadwal']) {
            return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }
        
        // Get active tahun ajar
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        
        // Get all tahun ajaran (needed for edit form)
        $tahunAjarans = $this->tahunAjarModel->findAll();
        
        // Get all active classes
        $kelasModel = new \App\Models\KelasModel();
        $kelases = [];
        
        if ($tahunAjarAktif) {
            $kelases = $kelasModel->where('tahun_ajar_id', $tahunAjarAktif['id'])->where('status', 'Aktif')->findAll();
        }
        
        // Get supervisors (users with role supervisor or kepala) including role info
        $supervisors = $this->userModel->select('id, username, role')->whereIn('role', ['supervisor', 'kepala'])->where('status', 'Aktif')->findAll();

        $refMapelModel = new \App\Models\RefMapelModel();
        $mapels = $refMapelModel->where('status', 'Aktif')->orderBy('nama_mapel', 'ASC')->findAll();

        $kelompokModel = new \App\Models\KelompokSupervisiModel();
        $kelompoks = $kelompokModel->orderBy('nama_kelompok', 'ASC')->findAll();
        
        $data['tahun_ajar'] = $tahunAjarAktif;
        $data['tahun_ajars'] = $tahunAjarans; // Perubahan ini untuk memenuhi kebutuhan view
        $data['kelases'] = $kelases;
        $data['gurus'] = $this->guruModel->findAll();
        $data['supervisors'] = $supervisors;
        $data['mapels'] = $mapels;
        $data['kelompoks'] = $kelompoks;
        
        return view('admin/jadwal/edit', $data);
    }

    public function update($id)
    {
        $existing = $this->jadwalSupervisiModel->find($id);
        if (!$existing) {
            return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }

        $tahunAjarId = $this->request->getPost('tahun_ajar_id') ?: ($existing['tahun_ajar_id'] ?? null);
        $guruId = $this->request->getPost('guru_id') ?: ($existing['guru_id'] ?? null);
        $supervisorId = $this->request->getPost('supervisor_id') ?: ($existing['supervisor_id'] ?? null);
        $kelompokId = $this->request->getPost('kelompok_id') ?: ($existing['kelompok_id'] ?? null);
        $tanggalSupervisi = $this->request->getPost('tanggal_supervisi') ?: ($existing['tanggal_supervisi'] ?? null);
        $jamKe = $this->request->getPost('jam_ke') ?: ($existing['jam_ke'] ?? null);

        $dupMsg = $this->findDuplicateScheduleMessage(
            $tahunAjarId,
            $guruId,
            $supervisorId,
            $kelompokId,
            $tanggalSupervisi,
            $jamKe,
            (int) $id
        );
        if ($dupMsg !== null) {
            return redirect()->back()->withInput()->with('error', $dupMsg);
        }

        // Get class name
        $kelasModel = new \App\Models\KelasModel();
        $kelas = $kelasModel->find($this->request->getPost('kelas_id'));
        
        // Sanitize mata_pelajaran input and only overwrite if non-empty
        $mpRaw = $this->request->getPost('mata_pelajaran');
        $mp = $mpRaw !== null ? trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $mpRaw))) : null;

        $mapelId = $this->request->getPost('mapel_id');
        $mapelId = ($mapelId === '' || $mapelId === null) ? null : (int) $mapelId;
        if ($mapelId !== null && !(new \App\Models\RefMapelModel())->find($mapelId)) {
            return redirect()->back()->withInput()->with('error', 'Mata pelajaran referensi tidak ditemukan.');
        }

        $kelompokId = $this->request->getPost('kelompok_id');
        $kelompokId = ($kelompokId === '' || $kelompokId === null) ? null : (int) $kelompokId;
        if ($kelompokId !== null && !(new \App\Models\KelompokSupervisiModel())->find($kelompokId)) {
            return redirect()->back()->withInput()->with('error', 'Kelompok supervisi tidak ditemukan.');
        }

        $jadwalData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'supervisor_id' => $this->request->getPost('supervisor_id'),
            'mapel_id' => $mapelId,
            'kelas' => $kelas ? $kelas['nama_kelas'] : '',
            'kelas_id' => $this->request->getPost('kelas_id'),
            'kelompok_id' => $kelompokId,
            'jam_ke' => $this->request->getPost('jam_ke'),
            'hari' => $this->request->getPost('hari'),
            'tanggal_supervisi' => $this->request->getPost('tanggal_supervisi'),
            'waktu_dari' => $this->request->getPost('waktu_dari'),
            'waktu_sampai' => $this->request->getPost('waktu_sampai'),
            'materi_supervisi' => $this->request->getPost('materi_supervisi'),
            'status' => $this->request->getPost('status')
        ];

        // Only set mata_pelajaran if a non-empty value was provided
        if ($mp !== null) {
            if ($mp !== '') {
                $jadwalData['mata_pelajaran'] = $mp;
            } else {
                // remove key so update won't overwrite with empty string
                unset($jadwalData['mata_pelajaran']);
            }
        }

        $result = $this->jadwalSupervisiModel->update($id, $jadwalData);
        
        if ($result) {
            return redirect()->to('/admin/jadwal')->with('success', 'Jadwal supervisi berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal supervisi');
        }
    }

    public function delete($id)
    {
        // Periksa apakah jadwal sudah digunakan dalam hasil supervisi
        $hasilSupervisiModel = new \App\Models\HasilSupervisiModel();
        $jumlahHasil = $hasilSupervisiModel->where('jadwal_supervisi_id', $id)->countAllResults();

        if ($jumlahHasil > 0) {
            return redirect()->to('/admin/jadwal')->with('error', 'Tidak dapat menghapus jadwal supervisi ini karena sudah digunakan dalam hasil supervisi');
        }

        $result = $this->jadwalSupervisiModel->delete($id);

        if ($result) {
            return redirect()->to('/admin/jadwal')->with('success', 'Jadwal supervisi berhasil dihapus');
        }

        return redirect()->to('/admin/jadwal')->with('error', 'Gagal menghapus jadwal supervisi');
    }

    /**
     * Validasi fleksibel:
     * - Memperbolehkan guru disupervisi lebih dari 1x oleh supervisor berbeda atau dalam kelompok berbeda.
     * - Mencegah duplikasi ganda dalam kelompok yang sama.
     * - Mencegah bentrok waktu (collision guard) di tanggal & jam yang sama untuk guru maupun supervisor.
     */
    private function findDuplicateScheduleMessage(
        $tahunAjarId,
        $guruId,
        $supervisorId = null,
        $kelompokId = null,
        $tanggalSupervisi = null,
        $jamKe = null,
        ?int $excludeId = null
    ): ?string {
        $tahunAjarId = (int) ($tahunAjarId ?? 0);
        $guruId = (int) ($guruId ?? 0);
        $supervisorId = (int) ($supervisorId ?? 0);
        $kelompokId = !empty($kelompokId) ? (int) $kelompokId : null;

        if ($tahunAjarId <= 0 || $guruId <= 0) {
            return 'Tahun ajaran dan guru wajib dipilih.';
        }

        $tahun = $this->tahunAjarModel->find($tahunAjarId);
        if (!$tahun) {
            return 'Tahun ajaran tidak ditemukan.';
        }
        if (($tahun['status_aktif'] ?? 'Nonaktif') !== 'Aktif') {
            return 'Jadwal hanya boleh dibuat pada tahun ajaran yang Aktif. Aktifkan dulu tahun ajaran ini.';
        }

        // 1. Cek duplikasi persis dalam kelompok yang sama
        if ($kelompokId !== null && $kelompokId > 0) {
            $sameKelompok = $this->jadwalSupervisiModel
                ->where('kelompok_id', $kelompokId)
                ->where('guru_id', $guruId);
            if ($excludeId !== null && $excludeId > 0) {
                $sameKelompok->where('id !=', $excludeId);
            }
            if ($sameKelompok->first()) {
                return 'Guru ini sudah memiliki jadwal supervisi dalam kelompok supervisi ini.';
            }
        } elseif ($supervisorId > 0 && !empty($tanggalSupervisi)) {
            // Jika tanpa kelompok, cegah duplikasi guru dengan supervisor yang sama di tanggal yang sama
            $sameSupervisor = $this->jadwalSupervisiModel
                ->where('tahun_ajar_id', $tahunAjarId)
                ->where('guru_id', $guruId)
                ->where('supervisor_id', $supervisorId)
                ->where('tanggal_supervisi', $tanggalSupervisi);
            if ($excludeId !== null && $excludeId > 0) {
                $sameSupervisor->where('id !=', $excludeId);
            }
            if ($sameSupervisor->first()) {
                return 'Guru ini sudah memiliki jadwal supervisi dengan supervisor tersebut pada tanggal yang sama.';
            }
        }

        // 2. Proteksi bentrok waktu (Time Collision Guard):
        // Guru tidak boleh memiliki 2 jadwal supervisi pada tanggal dan jam_ke yang sama
        if (!empty($tanggalSupervisi) && !empty($jamKe)) {
            $conflictGuru = $this->jadwalSupervisiModel
                ->where('guru_id', $guruId)
                ->where('tanggal_supervisi', $tanggalSupervisi)
                ->where('jam_ke', $jamKe);
            if ($excludeId !== null && $excludeId > 0) {
                $conflictGuru->where('id !=', $excludeId);
            }
            if ($conflictGuru->first()) {
                return "Jadwal bentrok: Guru ini sudah memiliki agenda supervisi lain pada tanggal {$tanggalSupervisi} pada jam ke-{$jamKe}.";
            }
        }

        // Supervisor tidak boleh memiliki 2 jadwal supervisi dengan guru berbeda pada tanggal dan jam_ke yang sama
        if (!empty($tanggalSupervisi) && !empty($jamKe) && $supervisorId > 0) {
            $conflictSup = $this->jadwalSupervisiModel
                ->where('supervisor_id', $supervisorId)
                ->where('tanggal_supervisi', $tanggalSupervisi)
                ->where('jam_ke', $jamKe);
            if ($excludeId !== null && $excludeId > 0) {
                $conflictSup->where('id !=', $excludeId);
            }
            if ($conflictSup->first()) {
                return "Jadwal bentrok: Supervisor ini sudah memiliki jadwal supervisi guru lain pada tanggal {$tanggalSupervisi} pada jam ke-{$jamKe}.";
            }
        }

        return null;
    }

    /**
     * Menghapus banyak jadwal supervisi sekaligus (Bulk Delete) dengan proteksi hasil supervisi
     */
    public function bulkDelete()
    {
        $ids = (array)$this->request->getPost('selected_ids');
        
        if (empty($ids)) {
            return redirect()->to(base_url('/admin/jadwal'))->with('error', 'Pilih minimal satu jadwal yang akan dihapus.');
        }

        // Ambil ID jadwal yang sudah digunakan dalam hasil supervisi
        $hasilSupervisiModel = new \App\Models\HasilSupervisiModel();
        $usedHasil = $hasilSupervisiModel->whereIn('jadwal_supervisi_id', $ids)->findAll();
        $usedIds = !empty($usedHasil) ? array_column($usedHasil, 'jadwal_supervisi_id') : [];

        // Filter jadwal yang aman untuk dihapus (belum ada penilaian/hasil)
        $deletableIds = array_diff($ids, $usedIds);

        if (empty($deletableIds)) {
            return redirect()->to(base_url('/admin/jadwal'))->with('error', 'Semua jadwal yang Anda pilih tidak dapat dihapus karena sudah memiliki data hasil supervisi.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $this->jadwalSupervisiModel->whereIn('id', $deletableIds)->delete();
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(base_url('/admin/jadwal'))->with('error', 'Terjadi kesalahan sistem saat menghapus data jadwal supervisi.');
        }

        $deletedCount = count($deletableIds);
        $skippedCount = count($usedIds);

        if ($skippedCount > 0) {
            return redirect()->to(base_url('/admin/jadwal'))->with('warning', "Berhasil menghapus {$deletedCount} jadwal. Sebanyak {$skippedCount} jadwal dilewati karena sudah memiliki data hasil supervisi.");
        }

        return redirect()->to(base_url('/admin/jadwal'))->with('success', "Berhasil menghapus {$deletedCount} jadwal supervisi secara massal.");
    }

    public function cetakPdf()
    {
        try {
            helper(['setting', 'date']);

            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
            $status = $this->request->getGet('status');

            $query = $this->jadwalSupervisiModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, kelas.nama_kelas, users.username as nama_supervisor, users.nip as nip_supervisor')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left');

            if (!empty($tahun_ajar_id)) {
                $query->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            if (!empty($status)) {
                $query->where('jadwal_supervisi.status', $status);
            }

            $jadwals = $query->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')->findAll();
                
            $data = [
                'jadwals'       => $jadwals,
                // NOTE: blank SMA placeholders until set via Pengaturan Identitas Sekolah.
                'nama_kepala'   => get_pengaturan('nama_kepala', ''),
                'nip_kepala'    => get_pengaturan('nip_kepala', ''),
                'kota_madrasah' => get_pengaturan('kecamatan', ''),
                'tanggal_cetak' => date('Y-m-d')
            ];
            
            // Bersihkan semua output buffering agar tidak mengotori output stream binary PDF
            while (ob_get_level()) {
                ob_end_clean();
            }

            $html = view('admin/jadwal/pdf_view', $data);
            
            // Setup Dompdf dengan opsi aman untuk hosting
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('chroot', [ROOTPATH, FCPATH]);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'laporan-jadwal-supervisi-' . date('Y-m-d') . '.pdf';

            // Set header PDF secara eksplisit
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            
            // Output PDF dan langsung akhiri proses agar CI4 tidak mengirim output tambahan
            $dompdf->stream($filename, ['Attachment' => 0]);
            exit();
        } catch (\Throwable $e) {
            log_message('error', 'Cetak PDF Jadwal Error: ' . $e->getMessage());
            throw $e;
        }
    }
}