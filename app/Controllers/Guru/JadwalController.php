<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\TahunAjarModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->jadwalModel    = new JadwalSupervisiModel();
        $this->guruModel      = new GuruModel();
        $this->kelasModel     = new KelasModel();
        $this->tahunAjarModel = new TahunAjarModel();
        helper(['setting', 'date']);
    }

    public function index()
    {
        $userId = session()->get('id');

        // Ambil data profil guru berdasarkan user ID yang sedang login
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            // Fallback cari berdasarkan NIP atau Email jika user_id belum terisi
            $nip = session()->get('nip');
            $email = session()->get('email');
            if (!empty($nip)) {
                $guru = $this->guruModel->where('nip', $nip)->first();
            }
            if (!$guru && !empty($email)) {
                $guru = $this->guruModel->where('email', $email)->first();
            }
        }

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Profil data Guru/Tendik Anda belum terhubung dengan akun login ini.');
        }

        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        // Ambil jadwal supervisi milik guru ini (Gunakan LEFT JOIN agar Tendik tanpa kelas tetap tampil)
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, tahun_ajar.tahun_ajar, tahun_ajar.semester, COALESCE(spv_guru.nama, spv_user.username) as supervisor_name, spv_guru.nip as supervisor_nip')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users as spv_user', 'spv_user.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('guru as spv_guru', 'spv_guru.user_id = spv_user.id', 'left')
            ->where('jadwal_supervisi.guru_id', (int) $guru['id'])
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->findAll();

        // Daftar kelas aktif untuk dipilih/diedit oleh guru pengajar
        $kelases = $this->kelasModel
            ->where('status', 'Aktif')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        $data = [
            'title'           => 'Jadwal Supervisi Saya',
            'jadwal'          => $jadwal,
            'guru'            => $guru,
            'tahunAktif'      => $tahunAktif,
            'kelases'         => $kelases,
            'jamPelajaranKbm' => get_jam_pelajaran_kbm(),
        ];

        return view('guru/jadwal/index', $data);
    }

    /**
     * Guru memilih / mengubah kelas yang digunakan untuk supervisi.
     */
    public function updateKelas($id)
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal || (int)$jadwal['guru_id'] !== (int)$guru['id']) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak valid atau bukan milik Anda.');
        }

        if ($jadwal['status'] === 'Selesai') {
            return redirect()->back()->with('error', 'Jadwal supervisi yang sudah selesai tidak dapat diubah kelasnya.');
        }

        $kelasId = $this->request->getPost('kelas_id');
        $namaKelas = '-';

        if (!empty($kelasId)) {
            $kelasRow = $this->kelasModel->find($kelasId);
            if ($kelasRow) {
                $namaKelas = $kelasRow['nama_kelas'];
            }
        }

        $this->jadwalModel->update($id, [
            'kelas_id' => !empty($kelasId) ? (int)$kelasId : null,
            'kelas'    => $namaKelas
        ]);

        return redirect()->to('/guru/jadwal')->with('success', "Kelas untuk supervisi Anda berhasil diperbarui menjadi: {$namaKelas}.");
    }

    /**
     * Guru mengajukan pembatalan jadwal supervisi disertai alasan dan usulan jadwal pengganti.
     */
    public function ajukanBatal($id)
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal || (int)$jadwal['guru_id'] !== (int)$guru['id']) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak valid atau bukan milik Anda.');
        }

        if ($jadwal['status'] === 'Selesai') {
            return redirect()->back()->with('error', 'Jadwal supervisi yang sudah selesai tidak dapat diajukan pembatalan.');
        }

        $rules = [
            'alasan_batal'   => 'required|min_length[5]',
            'usulan_tanggal' => 'required|valid_date',
            'usulan_jam_ke'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Harap isi alasan pembatalan dan pilih tanggal serta jam usulan pengganti.');
        }

        $alasan        = trim($this->request->getPost('alasan_batal'));
        $usulanTanggal = trim($this->request->getPost('usulan_tanggal'));
        $usulanJamKe   = trim($this->request->getPost('usulan_jam_ke'));
        $usulanKelasId = $this->request->getPost('usulan_kelas_id');

        // Hari dalam Bahasa Indonesia
        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        $dayEnglish = date('l', strtotime($usulanTanggal));
        $usulanHari = $hariMap[$dayEnglish] ?? 'Senin';

        // Slot waktu KBM
        $kbmSlots = get_jam_pelajaran_kbm();
        $waktuDari = '07:00';
        $waktuSampai = '07:45';
        if (isset($kbmSlots[$usulanJamKe])) {
            $waktuDari   = $kbmSlots[$usulanJamKe]['waktu_dari'];
            $waktuSampai = $kbmSlots[$usulanJamKe]['waktu_sampai'];
        }

        $usulanKelasNama = null;
        if (!empty($usulanKelasId)) {
            $kRow = $this->kelasModel->find($usulanKelasId);
            if ($kRow) {
                $usulanKelasNama = $kRow['nama_kelas'];
            }
        }

        $this->jadwalModel->update($id, [
            'status_ajuan'          => 'Diajukan',
            'alasan_batal'          => $alasan,
            'usulan_tanggal'        => $usulanTanggal,
            'usulan_hari'           => $usulanHari,
            'usulan_jam_ke'         => $usulanJamKe,
            'usulan_waktu_dari'     => $waktuDari,
            'usulan_waktu_sampai'   => $waktuSampai,
            'usulan_kelas_id'       => !empty($usulanKelasId) ? (int)$usulanKelasId : null,
            'usulan_kelas'          => $usulanKelasNama,
            'catatan_supervisor'    => null,
            'tgl_respon_supervisor' => null,
        ]);

        return redirect()->to('/guru/jadwal')->with('success', 'Pengajuan pembatalan dan usulan jadwal pengganti berhasil dikirim ke Supervisor. Mohon menunggu konfirmasi persetujuan.');
    }

    /**
     * Guru membatalkan / menarik kembali pengajuan reschedule sebelum direspon.
     */
    public function batalkanAjuan($id)
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal || (int)$jadwal['guru_id'] !== (int)$guru['id']) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak valid.');
        }

        $this->jadwalModel->update($id, [
            'status_ajuan'          => 'Tidak Ada',
            'alasan_batal'          => null,
            'usulan_tanggal'        => null,
            'usulan_hari'           => null,
            'usulan_jam_ke'         => null,
            'usulan_waktu_dari'     => null,
            'usulan_waktu_sampai'   => null,
            'usulan_kelas_id'       => null,
            'usulan_kelas'          => null,
        ]);

        return redirect()->to('/guru/jadwal')->with('success', 'Pengajuan pembatalan jadwal supervisi telah dibatalkan.');
    }
}
