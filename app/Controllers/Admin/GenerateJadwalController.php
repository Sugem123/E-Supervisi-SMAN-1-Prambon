<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\UserModel;
use App\Models\KelasModel;

class GenerateJadwalController extends BaseController
{
    protected $jadwalSupervisiModel;
    protected $tahunAjarModel;
    protected $guruModel;
    protected $userModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->jadwalSupervisiModel = new JadwalSupervisiModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();
        $this->kelasModel = new KelasModel();
    }

    /**
     * Menampilkan form parameter generate jadwal
     */
    public function index()
    {
        // Ambil tahun ajaran aktif atau tahun ajaran terbaru
        $tahunAjar = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        if (!$tahunAjar) {
            $tahunAjar = $this->tahunAjarModel->orderBy('id', 'DESC')->first();
        }

        // Ambil daftar supervisor aktif (supervisor dan kepala sekolah)
        $supervisors = $this->userModel
            ->select('id, username, role')
            ->whereIn('role', ['supervisor', 'kepala'])
            ->where('status', 'Aktif')
            ->orderBy('username', 'ASC')
            ->findAll();

        // Ambil daftar semua guru
        $gurus = $this->guruModel->orderBy('nama', 'ASC')->findAll();

        // Cek guru yang sudah memiliki jadwal pada tahun ajaran ini
        $scheduledGuruIds = [];
        if ($tahunAjar) {
            $existingJadwals = $this->jadwalSupervisiModel
                ->select('guru_id')
                ->where('tahun_ajar_id', $tahunAjar['id'])
                ->findAll();
            $scheduledGuruIds = array_column($existingJadwals, 'guru_id');
        }

        // Tambahkan flag has_schedule ke data guru
        foreach ($gurus as &$g) {
            $g['has_schedule'] = in_array($g['id'], $scheduledGuruIds);
        }

        // Default rentang tanggal: Tanggal 1 bulan ini s.d. akhir 2 bulan ke depan
        $defaultStartDate = date('Y-m-01');
        $defaultEndDate = date('Y-m-t', strtotime('+2 months'));

        $data = [
            'title' => 'Generate Jadwal Supervisi Otomatis',
            'tahun_ajar' => $tahunAjar,
            'supervisors' => $supervisors,
            'gurus' => $gurus,
            'default_start_date' => $defaultStartDate,
            'default_end_date' => $defaultEndDate
        ];

        return view('admin/jadwal/generate', $data);
    }

    /**
     * Menghitung simulasi penjadwalan otomatis dan menampilkan halaman preview
     */
    public function preview()
    {
        $tahunAjarId = $this->request->getPost('tahun_ajar_id');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $selectedSupervisorIds = (array)$this->request->getPost('supervisor_ids');
        $selectedGuruIds = (array)$this->request->getPost('guru_ids');
        $maxPerDay = (int)($this->request->getPost('max_per_day') ?? 1);
        $sesiMulai = (int)($this->request->getPost('sesi_mulai') ?? 1);

        // Validasi input
        if (empty($tahunAjarId)) {
            return redirect()->back()->withInput()->with('error', 'Tahun ajaran tidak valid.');
        }

        if (empty($tanggalMulai) || empty($tanggalSelesai) || $tanggalMulai > $tanggalSelesai) {
            return redirect()->back()->withInput()->with('error', 'Rentang tanggal pelaksanaan supervisi tidak valid.');
        }

        if (empty($selectedSupervisorIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu supervisor yang ditugaskan.');
        }

        if (empty($selectedGuruIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu guru yang akan dijadwalkan.');
        }

        // Ambil data tahun ajaran
        $tahunAjar = $this->tahunAjarModel->find($tahunAjarId);
        if (!$tahunAjar) {
            return redirect()->back()->withInput()->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        // Ambil data supervisor terpilih
        $supervisors = $this->userModel
            ->select('id, username, role')
            ->whereIn('id', $selectedSupervisorIds)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($supervisors)) {
            return redirect()->back()->withInput()->with('error', 'Supervisor terpilih tidak aktif atau tidak ditemukan.');
        }

        // Ambil data guru terpilih
        $gurus = $this->guruModel
            ->whereIn('id', $selectedGuruIds)
            ->orderBy('nama', 'ASC')
            ->findAll();

        if (empty($gurus)) {
            return redirect()->back()->withInput()->with('error', 'Data guru yang dipilih tidak valid.');
        }

        // Aturan arsip: hanya tahun Aktif yang boleh di-generate + tolak guru yang sudah 1x tahun ini.
        if (($tahunAjar['status_aktif'] ?? 'Nonaktif') !== 'Aktif') {
            return redirect()->back()->withInput()->with('error', 'Generate hanya boleh pada tahun ajaran yang Aktif. Aktifkan dulu tahun ajaran ini.');
        }
        $blocked = $this->findAlreadyScheduledTeachers($tahunAjar, array_column($gurus, 'id'));
        if (!empty($blocked)) {
            return redirect()->back()->withInput()->with('error', 'Ada ' . count($blocked) . ' guru yang sudah memiliki jadwal tahun ' . $tahunAjar['tahun_ajar'] . ' (' . implode(', ', array_slice($blocked, 0, 5)) . (count($blocked) > 5 ? ', ...' : '') . '). Satu guru hanya 1x supervisi per tahun.');
        }

        // Ambil daftar kelas aktif
        $kelases = $this->kelasModel
            ->where('tahun_ajar_id', $tahunAjarId)
            ->where('status', 'Aktif')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        if (empty($kelases)) {
            // Fallback jika tidak ada filter tahun_ajar_id pada kelas
            $kelases = $this->kelasModel->where('status', 'Aktif')->findAll();
        }

        // Kumpulkan hari kerja (Senin s.d. Sabtu, abaikan Minggu / Day 7)
        $start = new \DateTime($tanggalMulai);
        $end = new \DateTime($tanggalSelesai);
        $end->modify('+1 day'); // Inklusif

        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        $workingDays = [];

        foreach ($period as $dt) {
            // 1 (Senin) s.d. 6 (Sabtu), 7 adalah Minggu
            if ($dt->format('N') != 7) {
                $workingDays[] = $dt->format('Y-m-d');
            }
        }

        if (empty($workingDays)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada hari kerja (Senin–Sabtu) dalam rentang tanggal yang dipilih.');
        }

        // Konfigurasi jam dan slot waktu pelajaran (diambil dinamis dari Pengaturan Jam Pelajaran)
        $slotWaktu = get_jam_pelajaran_kbm();
        $availableSlots = array_keys($slotWaktu);
        if (empty($availableSlots)) {
            $availableSlots = ['1'];
            $slotWaktu = ['1' => ['jam_ke' => '1', 'waktu_dari' => '07:00', 'waktu_sampai' => '07:45']];
        }

        $startSlotIdx = array_search((string)$sesiMulai, $availableSlots, true);
        if ($startSlotIdx === false) {
            $startSlotIdx = 0;
        }

        // Mapping hari dalam bahasa Indonesia
        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        // ALGORITMA PENJADWALAN OTOMATIS
        $queueGurus = $gurus;
        $generatedJadwals = [];
        $kelasCount = count($kelases);
        $kelasIdx = 0;

        foreach ($workingDays as $currentDate) {
            $dayEnglish = date('l', strtotime($currentDate));
            $hariIndo = $hariMap[$dayEnglish] ?? 'Senin';

            foreach ($supervisors as $supervisor) {
                for ($slot = 0; $slot < $maxPerDay; $slot++) {
                    if (empty($queueGurus)) {
                        break 3; // Seluruh guru telah terjadwalkan
                    }

                    $guru = array_shift($queueGurus);
                    
                    // Cek apakah Tendik / Tata Usaha (tidak dialokasikan ke kelas KBM siswa)
                    $isTendik = (
                        ($guru['jenis_ptk'] ?? '') === 'Tendik' ||
                        stripos($guru['mata_pelajaran'] ?? '', 'tata usaha') !== false ||
                        stripos($guru['mata_pelajaran'] ?? '', 'administrasi') !== false
                    );

                    if ($isTendik) {
                        $selectedKelas   = null;
                        $kelasId         = null;
                        $namaKelas       = '-';
                        $materiSupervisi = 'Supervisi Administrasi & Layanan Kependidikan';
                    } else {
                        $selectedKelas   = !empty($kelases) ? $kelases[$kelasIdx % $kelasCount] : null;
                        $kelasId         = $selectedKelas ? $selectedKelas['id'] : null;
                        $namaKelas       = $selectedKelas ? $selectedKelas['nama_kelas'] : 'Semua Kelas';
                        $materiSupervisi = 'Supervisi Akademik Proses Pembelajaran';
                        $kelasIdx++;
                    }

                    // Tentukan slot jam ke-
                    $currentSlotIdx = ($startSlotIdx + $slot) % count($availableSlots);
                    $slotNumber = $availableSlots[$currentSlotIdx];
                    $timeInfo = $slotWaktu[$slotNumber] ?? [
                        'jam_ke' => (string)$slotNumber,
                        'waktu_dari' => '07:00',
                        'waktu_sampai' => '07:45'
                    ];

                    $generatedJadwals[] = [
                        'tahun_ajar_id'     => $tahunAjar['id'],
                        'guru_id'           => $guru['id'],
                        'nama_guru'         => $guru['nama'],
                        'nip'               => $guru['nip'] ?? '-',
                        'supervisor_id'     => $supervisor['id'],
                        'nama_supervisor'   => $supervisor['username'],
                        'mata_pelajaran'    => !empty($guru['mata_pelajaran']) ? $guru['mata_pelajaran'] : 'Mata Pelajaran Umum',
                        'kelas_id'          => $kelasId,
                        'nama_kelas'        => $namaKelas,
                        'tanggal_supervisi' => $currentDate,
                        'hari'              => $hariIndo,
                        'jam_ke'            => $timeInfo['jam_ke'],
                        'waktu_dari'        => $timeInfo['waktu_dari'],
                        'waktu_sampai'      => $timeInfo['waktu_sampai'],
                        'materi_supervisi'  => $materiSupervisi
                    ];
                }
            }
        }

        $totalGuruDipilih = count($selectedGuruIds);
        $totalTerjadwalkan = count($generatedJadwals);
        $sisaBelumTerjadwalkan = count($queueGurus);

        $data = [
            'title'                 => 'Pratinjau Hasil Generate Jadwal Otomatis',
            'tahun_ajar'            => $tahunAjar,
            'generatedJadwals'      => $generatedJadwals,
            'kelases'               => $kelases,
            'supervisors'           => $supervisors,
            'totalGuruDipilih'      => $totalGuruDipilih,
            'totalTerjadwalkan'     => $totalTerjadwalkan,
            'sisaBelumTerjadwalkan' => $sisaBelumTerjadwalkan,
            'tanggalMulai'          => $tanggalMulai,
            'tanggalSelesai'        => $tanggalSelesai,
            'totalHariKerja'        => count($workingDays)
        ];

        return view('admin/jadwal/generate_preview', $data);
    }

    /**
     * Guard arsip: nama guru yang sudah punya jadwal pada label tahun yang sama.
     */
    private function findAlreadyScheduledTeachers(array $tahunAjar, array $guruIds): array
    {
        $guruIds = array_values(array_unique(array_filter(array_map('intval', $guruIds))));
        if (empty($guruIds) || empty($tahunAjar['tahun_ajar'])) {
            return [];
        }

        $rows = $this->jadwalSupervisiModel
            ->select('jadwal_supervisi.guru_id, guru.nama as nama_guru')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->where('tahun_ajar.tahun_ajar', $tahunAjar['tahun_ajar'])
            ->whereIn('jadwal_supervisi.guru_id', $guruIds)
            ->groupBy('jadwal_supervisi.guru_id, guru.nama')
            ->findAll();

        $names = [];
        foreach ($rows as $row) {
            $names[] = $row['nama_guru'] ?? ('Guru #' . $row['guru_id']);
        }

        return $names;
    }

    /**
     * Menyimpan seluruh jadwal yang telah dikonfirmasi dari halaman preview ke database
     */
    public function save()
    {
        $jadwalsJson = $this->request->getPost('jadwals_json');
        
        if (empty($jadwalsJson)) {
            return redirect()->to(base_url('admin/jadwal/generate'))->with('error', 'Tidak ada data jadwal yang akan disimpan.');
        }

        $jadwals = json_decode($jadwalsJson, true);
        if (!is_array($jadwals) || empty($jadwals)) {
            return redirect()->to(base_url('admin/jadwal/generate'))->with('error', 'Format data jadwal tidak valid.');
        }

        // Ambil override kelas, waktu, dan tanggal dari form jika user mengubahnya di tabel preview
        $overrideKelas = (array)$this->request->getPost('override_kelas');
        $overrideJam = (array)$this->request->getPost('override_jam');
        $overrideTanggal = (array)$this->request->getPost('override_tanggal');
        $excludeIndices = (array)$this->request->getPost('exclude_index');

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];

        $insertedCount = 0;
        $skippedCount = 0;
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($jadwals as $idx => $item) {
            // Lewati jika user mencentang hapus/kecualikan baris ini
            if (in_array((string)$idx, $excludeIndices, true)) {
                continue;
            }

            // Cegah duplikasi persis: guru yang sama dengan supervisor yang sama di tanggal yang sama
            $dup = $this->jadwalSupervisiModel
                ->where('tahun_ajar_id', (int) ($item['tahun_ajar_id'] ?? 0))
                ->where('guru_id', (int) ($item['guru_id'] ?? 0))
                ->where('supervisor_id', (int) ($item['supervisor_id'] ?? 0))
                ->where('tanggal_supervisi', $tanggal)
                ->first();
            if ($dup) {
                $skippedCount++;
                continue;
            }

            $kelasId = $overrideKelas[$idx] ?? $item['kelas_id'];
            $kelasName = $item['nama_kelas'];
            
            if ($kelasId && $kelasId != $item['kelas_id']) {
                $kelasRow = $this->kelasModel->find($kelasId);
                if ($kelasRow) {
                    $kelasName = $kelasRow['nama_kelas'];
                }
            }

            $jamKe = $overrideJam[$idx] ?? $item['jam_ke'];

            // Konfigurasi waktu berdasarkan jam pelajaran (diambil dinamis dari Pengaturan Jam Pelajaran)
            $slotWaktuSync = get_jam_pelajaran_kbm();
            $waktuDari = isset($slotWaktuSync[(string)$jamKe]) ? $slotWaktuSync[(string)$jamKe]['waktu_dari'] : $item['waktu_dari'];
            $waktuSampai = isset($slotWaktuSync[(string)$jamKe]) ? $slotWaktuSync[(string)$jamKe]['waktu_sampai'] : $item['waktu_sampai'];

            // Tentukan tanggal dan nama hari (mendukung penyesuaian/override tanggal)
            $tanggalSupervisi = !empty($overrideTanggal[$idx]) ? $overrideTanggal[$idx] : $item['tanggal_supervisi'];
            $dayEnglish = date('l', strtotime($tanggalSupervisi));
            $hariIndo = $hariMap[$dayEnglish] ?? $item['hari'];

            $dataInsert = [
                'tahun_ajar_id'     => $item['tahun_ajar_id'],
                'guru_id'           => $item['guru_id'],
                'supervisor_id'     => $item['supervisor_id'],
                'mata_pelajaran'    => $item['mata_pelajaran'],
                'kelas'             => $kelasName,
                'kelas_id'          => $kelasId,
                'jam_ke'            => $jamKe,
                'hari'              => $hariIndo,
                'tanggal_supervisi' => $tanggalSupervisi,
                'waktu_dari'        => $waktuDari,
                'waktu_sampai'      => $waktuSampai,
                'materi_supervisi'  => $item['materi_supervisi'] ?? 'Supervisi Akademik Proses Pembelajaran',
                'status'            => 'Terjadwal',
                'created_at'        => date('Y-m-d H:i:s')
            ];

            $this->jadwalSupervisiModel->insert($dataInsert);
            $insertedCount++;
        }

        $db->transComplete();

        if ($db->transStatus() === false || $insertedCount === 0) {
            $msg = 'Gagal menyimpan jadwal supervisi otomatis.';
            if ($skippedCount > 0) {
                $msg .= ' ' . $skippedCount . ' baris dilewati karena duplikat atau jadwal bentrok.';
            }
            return redirect()->to(base_url('admin/jadwal/generate'))->with('error', $msg);
        }

        $msg = 'Berhasil membuat ' . $insertedCount . ' jadwal supervisi otomatis!';
        if ($skippedCount > 0) {
            $msg .= ' ' . $skippedCount . ' baris dilewati (duplikat jadwal yang sudah ada).';
        }

        return redirect()->to(base_url('admin/jadwal'))->with('success', $msg);
    }
}
