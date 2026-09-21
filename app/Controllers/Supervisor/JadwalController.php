<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\TahunAjarModel;
use App\Models\KelompokSupervisiModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tahunAjarModel;
    protected $kelompokModel;
    protected $db;

    public function __construct()
    {
        $this->jadwalModel    = new JadwalSupervisiModel();
        $this->guruModel      = new GuruModel();
        $this->kelasModel     = new KelasModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelompokModel  = new KelompokSupervisiModel();
        $this->db             = \Config\Database::connect();
        helper(['setting', 'date']);
    }

    public function index()
    {
        $supervisorId = (int) session()->get('id');

        // Ambil semua jadwal supervisi yang dibina oleh supervisor ini
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.jenis_ptk, guru.mata_pelajaran as guru_mata_pelajaran, kelas.nama_kelas, kelompok_supervisi.nama_kelompok')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('kelompok_supervisi', 'kelompok_supervisi.id = jadwal_supervisi.kelompok_id', 'left')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->orderBy("CASE WHEN jadwal_supervisi.status_ajuan = 'Diajukan' THEN 0 ELSE 1 END", 'ASC')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->orderBy('jadwal_supervisi.jam_ke', 'ASC')
            ->findAll();

        $pendingAjuanCount = 0;
        foreach ($schedules as $s) {
            if (($s['status_ajuan'] ?? '') === 'Diajukan') {
                $pendingAjuanCount++;
            }
        }

        // Ambil data kelompok yang dibina oleh supervisor ini
        $myKelompoks = $this->kelompokModel
            ->where('supervisor_id', $supervisorId)
            ->findAll();

        // Ambil daftar guru/pegawai binaan supervisor (berdasarkan anggota kelompok yang dibina)
        $binaanGurus = [];
        if (!empty($myKelompoks)) {
            $kelompokIds = array_column($myKelompoks, 'id');
            $binaanGurus = $this->guruModel
                ->select('guru.*, kelompok_anggota.kelompok_id')
                ->join('kelompok_anggota', 'kelompok_anggota.guru_id = guru.id')
                ->whereIn('kelompok_anggota.kelompok_id', $kelompokIds)
                ->orderBy('guru.nama', 'ASC')
                ->findAll();
        } else {
            // Jika belum ada kelompok terdaftar, ambil semua guru aktif
            $binaanGurus = $this->guruModel->orderBy('nama', 'ASC')->findAll();
        }

        // Ambil kelas aktif untuk dropdown
        $kelases = $this->kelasModel
            ->where('status', 'Aktif')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        $data = [
            'title'             => 'Jadwal Supervisi Pembinaan',
            'schedules'         => $schedules,
            'pendingAjuanCount' => $pendingAjuanCount,
            'binaanGurus'       => $binaanGurus,
            'kelases'           => $kelases,
            'myKelompoks'       => $myKelompoks,
            'tahunAktif'        => $tahunAktif,
            'jamPelajaranKbm'   => get_jam_pelajaran_kbm(),
        ];

        return view('supervisor/jadwal/index', $data);
    }

    public function detail($id)
    {
        $supervisorId = (int) session()->get('id');

        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, guru.jenis_ptk, kelas.nama_kelas, kelompok_supervisi.nama_kelompok')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('kelompok_supervisi', 'kelompok_supervisi.id = jadwal_supervisi.kelompok_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Jadwal Supervisi',
            'schedule' => $schedule
        ];

        return view('supervisor/jadwal/detail', $data);
    }

    /**
     * Supervisor merespon ajuan pembatalan & jadwal pengganti (Setujui / Tolak).
     */
    public function responAjuan($id)
    {
        $supervisorId = (int) session()->get('id');

        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak ditemukan atau bukan binaan Anda.');
        }

        if ($jadwal['status_ajuan'] !== 'Diajukan') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan jadwal pengganti yang aktif untuk jadwal ini.');
        }

        $aksi    = $this->request->getPost('aksi'); // 'setujui' atau 'tolak'
        $catatan = trim((string) $this->request->getPost('catatan_supervisor'));
        $namaGuru = $jadwal['nama_guru'] ?? 'Guru';

        if ($aksi === 'setujui') {
            // Perbarui tanggal, hari, jam, dan kelas sesuai usulan pengganti
            $updateData = [
                'tanggal_supervisi'     => $jadwal['usulan_tanggal'],
                'hari'                  => $jadwal['usulan_hari'],
                'jam_ke'                => $jadwal['usulan_jam_ke'],
                'waktu_dari'            => $jadwal['usulan_waktu_dari'],
                'waktu_sampai'          => $jadwal['usulan_waktu_sampai'],
                'status'                => 'Terjadwal',
                'status_ajuan'          => 'Disetujui',
                'catatan_supervisor'    => $catatan ?: 'Pengajuan jadwal pengganti telah disetujui oleh Supervisor.',
                'tgl_respon_supervisor' => date('Y-m-d H:i:s'),
            ];

            // Jika ada usulan kelas pengganti
            if (!empty($jadwal['usulan_kelas_id'])) {
                $updateData['kelas_id'] = $jadwal['usulan_kelas_id'];
                $updateData['kelas']    = $jadwal['usulan_kelas'];
            }

            $this->jadwalModel->update($id, $updateData);

            $tglIndo = format_tanggal_indonesia($jadwal['usulan_tanggal'], false);
            return redirect()->to('/supervisor/jadwal')->with('success', "Pengajuan pembatalan & jadwal pengganti untuk {$namaGuru} berhasil DISETUJUI. Jadwal supervisi telah dialihkan ke tanggal {$tglIndo} (Jam Ke-{$jadwal['usulan_jam_ke']}).");
        } else {
            // Tolak ajuan
            $this->jadwalModel->update($id, [
                'status_ajuan'          => 'Ditolak',
                'catatan_supervisor'    => $catatan ?: 'Pengajuan pembatalan jadwal supervisi belum dapat disetujui.',
                'tgl_respon_supervisor' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/supervisor/jadwal')->with('success', "Pengajuan pembatalan jadwal untuk {$namaGuru} telah DITOLAK. Jadwal supervisi tetap pada waktu semula.");
        }
    }

    /**
     * Supervisor membuat jadwal supervisi baru langsung untuk pegawai binaannya.
     */
    public function createJadwal()
    {
        $supervisorId = (int) session()->get('id');

        $rules = [
            'guru_id'           => 'required|integer',
            'tanggal_supervisi' => 'required|valid_date',
            'jam_ke'            => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Harap lengkapi pegawai yang disupervisi, tanggal, dan jam pelaksanaan.');
        }

        $guruId           = (int) $this->request->getPost('guru_id');
        $tanggalSupervisi = trim($this->request->getPost('tanggal_supervisi'));
        $jamKe            = trim($this->request->getPost('jam_ke'));
        $kelasId          = $this->request->getPost('kelas_id');
        $kelompokId       = $this->request->getPost('kelompok_id');
        $materiSupervisi  = trim((string)$this->request->getPost('materi_supervisi'));

        $guru = $this->guruModel->find($guruId);
        if (!$guru) {
            return redirect()->back()->withInput()->with('error', 'Pegawai yang dipilih tidak valid.');
        }

        // Tanggal dan Hari
        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        $dayEnglish = date('l', strtotime($tanggalSupervisi));
        $hariIndo = $hariMap[$dayEnglish] ?? 'Senin';

        // Slot waktu KBM
        $kbmSlots = get_jam_pelajaran_kbm();
        $waktuDari = '07:00';
        $waktuSampai = '07:45';
        if (isset($kbmSlots[$jamKe])) {
            $waktuDari   = $kbmSlots[$jamKe]['waktu_dari'];
            $waktuSampai = $kbmSlots[$jamKe]['waktu_sampai'];
        }

        // Deteksi Tendik vs Guru KBM
        $isTendik = (
            ($guru['jenis_ptk'] ?? '') === 'Tendik' ||
            stripos($guru['mata_pelajaran'] ?? '', 'tata usaha') !== false ||
            stripos($guru['mata_pelajaran'] ?? '', 'administrasi') !== false
        );

        $kelasNama = '-';
        $targetKelasId = null;
        if (!$isTendik && !empty($kelasId)) {
            $kRow = $this->kelasModel->find($kelasId);
            if ($kRow) {
                $kelasNama = $kRow['nama_kelas'];
                $targetKelasId = (int)$kelasId;
            }
        }

        // Tahun Ajaran Aktif
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $tahunAjarId = $tahunAktif ? (int)$tahunAktif['id'] : 1;

        if (empty($materiSupervisi)) {
            $materiSupervisi = $isTendik 
                ? 'Supervisi Administrasi & Layanan Kependidikan' 
                : 'Supervisi Akademik Proses Pembelajaran';
        }

        $dataInsert = [
            'tahun_ajar_id'     => $tahunAjarId,
            'guru_id'           => $guruId,
            'supervisor_id'     => $supervisorId,
            'kelompok_id'       => !empty($kelompokId) ? (int)$kelompokId : null,
            'mata_pelajaran'    => !empty($guru['mata_pelajaran']) ? $guru['mata_pelajaran'] : 'Umum',
            'kelas'             => $kelasNama,
            'kelas_id'          => $targetKelasId,
            'jam_ke'            => $jamKe,
            'hari'              => $hariIndo,
            'tanggal_supervisi' => $tanggalSupervisi,
            'waktu_dari'        => $waktuDari,
            'waktu_sampai'      => $waktuSampai,
            'materi_supervisi'  => $materiSupervisi,
            'status'            => 'Terjadwal',
            'status_ajuan'      => 'Tidak Ada',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->jadwalModel->insert($dataInsert);

        return redirect()->to('/supervisor/jadwal')->with('success', "Jadwal supervisi baru untuk {$guru['nama']} berhasil diterbitkan.");
    }
}
