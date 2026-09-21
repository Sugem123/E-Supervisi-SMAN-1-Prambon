<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelompokSupervisiModel;
use App\Models\KelompokAnggotaModel;
use App\Models\KelompokJenisPenilaianModel;
use App\Models\JenisPenilaianModel;
use App\Models\JadwalSupervisiModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\UserModel;
use App\Models\TahunAjarModel;
use App\Models\AuditLogModel;

class KelompokSupervisiController extends BaseController
{
    protected $kelompokModel;
    protected $anggotaModel;
    protected $kelompokJenisModel;
    protected $jenisModel;
    protected $jadwalModel;
    protected $kelasModel;
    protected $guruModel;
    protected $userModel;
    protected $tahunAjarModel;
    protected $auditLogModel;
    protected $db;

    public function __construct()
    {
        $this->kelompokModel      = new KelompokSupervisiModel();
        $this->anggotaModel       = new KelompokAnggotaModel();
        $this->kelompokJenisModel = new KelompokJenisPenilaianModel();
        $this->jenisModel         = new JenisPenilaianModel();
        $this->jadwalModel        = new JadwalSupervisiModel();
        $this->kelasModel         = new KelasModel();
        $this->guruModel          = new GuruModel();
        $this->userModel          = new UserModel();
        $this->tahunAjarModel     = new TahunAjarModel();
        $this->auditLogModel      = new AuditLogModel();
        $this->db                 = \Config\Database::connect();
    }

    public function index()
    {
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $query = $this->kelompokModel
            ->select('kelompok_supervisi.*, COALESCE(guru_spv.nama, users.username) as nama_supervisor, guru_spv.nip as nip_supervisor, users.username as username_supervisor, users.role as role_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('users', 'users.id = kelompok_supervisi.supervisor_id', 'left')
            ->join('guru as guru_spv', 'guru_spv.user_id = users.id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = kelompok_supervisi.tahun_ajar_id', 'left');

        if ($tahunAktif) {
            $query->where('kelompok_supervisi.tahun_ajar_id', $tahunAktif['id']);
        }

        $kelompoks = $query->orderBy('kelompok_supervisi.id', 'DESC')->findAll();

        if (!empty($kelompoks)) {
            $ids = array_column($kelompoks, 'id');

            // 1. Hitung total anggota per kelompok
            $memberCounts = [];
            $mRows = $this->db->table('kelompok_anggota')
                ->select('kelompok_id, COUNT(*) as total')
                ->whereIn('kelompok_id', $ids)
                ->groupBy('kelompok_id')
                ->get()->getResultArray();
            foreach ($mRows as $row) {
                $memberCounts[$row['kelompok_id']] = (int) $row['total'];
            }

            // 2. Ambil jenis penilaian yang ditugaskan ke masing-masing kelompok
            $jenisMap = [];
            $jRows = $this->db->table('kelompok_jenis_penilaian kjp')
                ->select('kjp.kelompok_id, jp.id, jp.nama')
                ->join('jenis_penilaian jp', 'jp.id = kjp.jenis_penilaian_id')
                ->whereIn('kjp.kelompok_id', $ids)
                ->orderBy('jp.id', 'ASC')
                ->get()->getResultArray();
            foreach ($jRows as $j) {
                $jenisMap[$j['kelompok_id']][] = $j;
            }

            // 3. Hitung status jadwal supervisi per kelompok
            $jadwalStats = [];
            $sRows = $this->db->table('jadwal_supervisi')
                ->select('kelompok_id, status, COUNT(*) as total')
                ->whereIn('kelompok_id', $ids)
                ->groupBy('kelompok_id, status')
                ->get()->getResultArray();
            foreach ($sRows as $s) {
                $kId = $s['kelompok_id'];
                if (!isset($jadwalStats[$kId])) {
                    $jadwalStats[$kId] = ['terjadwal' => 0, 'selesai' => 0, 'total' => 0];
                }
                if ($s['status'] === 'Terjadwal') {
                    $jadwalStats[$kId]['terjadwal'] += (int) $s['total'];
                } elseif ($s['status'] === 'Selesai') {
                    $jadwalStats[$kId]['selesai'] += (int) $s['total'];
                }
                $jadwalStats[$kId]['total'] += (int) $s['total'];
            }

            foreach ($kelompoks as &$k) {
                $kId = $k['id'];
                $k['total_anggota']    = $memberCounts[$kId] ?? 0;
                $k['assigned_jenis']   = $jenisMap[$kId] ?? [];
                $k['total_jadwal']     = $jadwalStats[$kId]['total'] ?? 0;
                $k['jadwal_terjadwal'] = $jadwalStats[$kId]['terjadwal'] ?? 0;
                $k['jadwal_selesai']   = $jadwalStats[$kId]['selesai'] ?? 0;
            }
            unset($k);
        }

        return view('admin/kelompok/index', [
            'title'       => 'Kelompok Supervisi',
            'kelompoks'   => $kelompoks,
            'tahunAktif'  => $tahunAktif,
            'tahunAjars'  => $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->orderBy('id', 'DESC')->findAll(),
            'arsipCounts' => $this->getArsipCountsByTahun(),
        ]);
    }

    public function show($id)
    {
        $kelompok = $this->kelompokModel
            ->select('kelompok_supervisi.*, COALESCE(guru_spv.nama, users.username) as nama_supervisor, guru_spv.nip as nip_supervisor, users.username as username_supervisor, users.role as role_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester, tahun_ajar.status_aktif')
            ->join('users', 'users.id = kelompok_supervisi.supervisor_id', 'left')
            ->join('guru as guru_spv', 'guru_spv.user_id = users.id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = kelompok_supervisi.tahun_ajar_id', 'left')
            ->where('kelompok_supervisi.id', $id)->first();

        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        // Jenis penilaian yang ditugaskan ke kelompok ini
        $assignedJenis = $this->kelompokJenisModel->getJenisByKelompok($id);

        // Anggota guru dalam kelompok ini
        $anggota = $this->anggotaModel
            ->select('kelompok_anggota.*, guru.nama as nama_guru, guru.nip, guru.mata_pelajaran, guru.status_kepegawaian, guru.jenis_ptk, ref_mapel.nama_mapel as nama_mapel_ref')
            ->join('guru', 'guru.id = kelompok_anggota.guru_id')
            ->join('ref_mapel', 'ref_mapel.id = guru.mapel_id', 'left')
            ->where('kelompok_anggota.kelompok_id', $id)
            ->orderBy('guru.nama', 'ASC')
            ->findAll();

        $tahunAjarId = (int) ($kelompok['tahun_ajar_id'] ?? 0);

        // Ambil jadwal supervisi yang terkait dengan kelompok ini (atau guru dalam kelompok pada tahun terkait)
        $jadwals = [];
        $scheduledGuruMap = [];
        if (!empty($anggota)) {
            $guruIds = array_column($anggota, 'guru_id');
            $jBuilder = $this->db->table('jadwal_supervisi js')
                ->select('js.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.jenis_ptk, users.username as supervisor_nama')
                ->join('guru', 'guru.id = js.guru_id', 'left')
                ->join('users', 'users.id = js.supervisor_id', 'left')
                ->whereIn('js.guru_id', $guruIds);

            if ($tahunAjarId > 0) {
                $jBuilder->groupStart()
                    ->where('js.kelompok_id', $id)
                    ->orWhere('js.tahun_ajar_id', $tahunAjarId)
                ->groupEnd();
            } else {
                $jBuilder->where('js.kelompok_id', $id);
            }

            $jadwalRows = $jBuilder->orderBy('js.tanggal_supervisi', 'ASC')->get()->getResultArray();
            foreach ($jadwalRows as $jr) {
                $jadwals[] = $jr;
                $scheduledGuruMap[$jr['guru_id']] = $jr;
            }
        }

        // Hitung statistik jadwal
        $totalAnggota = count($anggota);
        $totalTerjadwal = 0;
        $totalSelesai = 0;
        $belumTerjadwalCount = 0;

        foreach ($anggota as &$a) {
            $gId = $a['guru_id'];
            if (isset($scheduledGuruMap[$gId])) {
                $a['has_jadwal'] = true;
                $a['jadwal'] = $scheduledGuruMap[$gId];
                if ($scheduledGuruMap[$gId]['status'] === 'Selesai') {
                    $totalSelesai++;
                } else {
                    $totalTerjadwal++;
                }
            } else {
                $a['has_jadwal'] = false;
                $a['jadwal'] = null;
                $belumTerjadwalCount++;
            }
        }
        unset($a);

        // Ambil kelas aktif untuk keperluan generate
        $kelases = $this->kelasModel
            ->where('status', 'Aktif')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        // Ambil daftar guru yang belum menjadi anggota di kelompok ini
        $existingGuruIds = !empty($anggota) ? array_column($anggota, 'guru_id') : [];
        $availableGurusBuilder = $this->guruModel->orderBy('nama', 'ASC');
        if (!empty($existingGuruIds)) {
            $availableGurusBuilder->whereNotIn('id', $existingGuruIds);
        }
        $availableGurus = $availableGurusBuilder->findAll();

        return view('admin/kelompok/show', [
            'title'               => 'Detail Kelompok Supervisi',
            'kelompok'            => $kelompok,
            'assignedJenis'       => $assignedJenis,
            'allJenisPenilaians'  => $this->jenisModel->findAllActiveWithMappedColumns(),
            'selectedJenisIds'    => $this->kelompokJenisModel->getJenisIdsByKelompok($id),
            'anggota'             => $anggota,
            'jadwals'             => $jadwals,
            'kelases'             => $kelases,
            'availableGurus'      => $availableGurus,
            'totalAnggota'        => $totalAnggota,
            'totalTerjadwal'      => $totalTerjadwal,
            'totalSelesai'        => $totalSelesai,
            'belumTerjadwalCount' => $belumTerjadwalCount,
            'jamPelajaranKbm'     => get_jam_pelajaran_kbm(),
        ]);
    }

    public function create()
    {
        return view('admin/kelompok/create', [
            'title'           => 'Tambah Kelompok Supervisi',
            'supervisors'     => $this->getSupervisors(),
            'gurus'           => $this->guruModel->orderBy('nama', 'ASC')->findAll(),
            'tahunAjars'      => $this->tahunAjarModel->findAll(),
            'tahunAktif'      => $this->tahunAjarModel->where('status_aktif', 'Aktif')->first(),
            'jenisPenilaians' => $this->jenisModel->findAllActiveWithMappedColumns(),
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_kelompok' => 'required|max_length[100]',
            'supervisor_id' => 'required|integer',
            'tahun_ajar_id' => 'permit_empty|integer',
            'anggota_ids'   => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Validasi gagal: periksa nama kelompok dan supervisor.');
        }

        $supervisorId = (int) $this->request->getPost('supervisor_id');
        if (!$this->isSupervisor($supervisorId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Supervisor tidak valid. Pilih user dengan role supervisor/kepala yang Aktif.');
        }

        $tahunAjarId = $this->request->getPost('tahun_ajar_id');
        $tahunAjarId = ($tahunAjarId === '' || $tahunAjarId === null) ? null : (int) $tahunAjarId;
        $anggotaIds = $this->normalizeAnggotaIds($this->request->getPost('anggota_ids'));
        $jenisIds = (array) $this->request->getPost('jenis_penilaian_ids');

        if (!empty($anggotaIds) && !$this->allGuruExist($anggotaIds)) {
            return redirect()->back()->withInput()
                ->with('error', 'Salah satu anggota guru tidak ditemukan di database.');
        }

        $this->db->transStart();
        try {
            $kelompokId = $this->kelompokModel->insert([
                'nama_kelompok' => trim($this->request->getPost('nama_kelompok')),
                'supervisor_id' => $supervisorId,
                'tahun_ajar_id' => $tahunAjarId,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            if (!$kelompokId) {
                throw new \RuntimeException('Gagal menyimpan kelompok supervisi.');
            }

            $this->insertAnggota($kelompokId, $anggotaIds);
            $this->kelompokJenisModel->syncJenis($kelompokId, $jenisIds);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kelompok. Transaksi dibatalkan.');
            }

            $this->auditLogModel->logActivity(
                session()->get('id'),
                'create_kelompok',
                "Admin membuat kelompok supervisi #{$kelompokId} dengan " . count($anggotaIds) . ' anggota dan ' . count($jenisIds) . ' jenis penilaian'
            );

            return redirect()->to('/admin/kelompok')->with('success', 'Kelompok supervisi berhasil ditambahkan.');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Store kelompok gagal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $selected = array_column(
            $this->anggotaModel->where('kelompok_id', $id)->findAll(),
            'guru_id'
        );

        $selectedJenisIds = $this->kelompokJenisModel->getJenisIdsByKelompok($id);

        return view('admin/kelompok/edit', [
            'title'            => 'Edit Kelompok Supervisi',
            'kelompok'         => $kelompok,
            'selectedIds'      => $selected,
            'selectedJenisIds' => $selectedJenisIds,
            'supervisors'      => $this->getSupervisors(),
            'gurus'            => $this->guruModel->orderBy('nama', 'ASC')->findAll(),
            'tahunAjars'       => $this->tahunAjarModel->findAll(),
            'jenisPenilaians'  => $this->jenisModel->findAllActiveWithMappedColumns(),
        ]);
    }

    public function update($id)
    {
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $rules = [
            'nama_kelompok' => 'required|max_length[100]',
            'supervisor_id' => 'required|integer',
            'tahun_ajar_id' => 'permit_empty|integer',
            'anggota_ids'   => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Validasi gagal: periksa nama kelompok dan supervisor.');
        }

        $supervisorId = (int) $this->request->getPost('supervisor_id');
        if (!$this->isSupervisor($supervisorId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Supervisor tidak valid. Pilih user dengan role supervisor/kepala yang Aktif.');
        }

        $tahunAjarId = $this->request->getPost('tahun_ajar_id');
        $tahunAjarId = ($tahunAjarId === '' || $tahunAjarId === null) ? null : (int) $tahunAjarId;
        $anggotaIds = $this->normalizeAnggotaIds($this->request->getPost('anggota_ids'));
        $jenisIds = (array) $this->request->getPost('jenis_penilaian_ids');

        if (!empty($anggotaIds) && !$this->allGuruExist($anggotaIds)) {
            return redirect()->back()->withInput()
                ->with('error', 'Salah satu anggota guru tidak ditemukan di database.');
        }

        $this->db->transStart();
        try {
            $this->kelompokModel->update($id, [
                'nama_kelompok' => trim($this->request->getPost('nama_kelompok')),
                'supervisor_id' => $supervisorId,
                'tahun_ajar_id' => $tahunAjarId,
            ]);

            // Sinkronisasi anggota
            $this->anggotaModel->where('kelompok_id', $id)->delete();
            $this->insertAnggota($id, $anggotaIds);

            // Sinkronisasi jenis penilaian kelompok
            $this->kelompokJenisModel->syncJenis($id, $jenisIds);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelompok. Transaksi dibatalkan.');
            }

            $this->auditLogModel->logActivity(
                session()->get('id'),
                'update_kelompok',
                "Admin memperbarui kelompok supervisi #{$id} dengan " . count($anggotaIds) . ' anggota dan ' . count($jenisIds) . ' jenis penilaian'
            );

            return redirect()->to('/admin/kelompok')->with('success', 'Kelompok supervisi berhasil diperbarui.');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Update kelompok gagal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function carryOver()
    {
        $fromId = (int) ($this->request->getPost('from_tahun_ajar_id') ?? 0);
        $toId = (int) ($this->request->getPost('to_tahun_ajar_id') ?? 0);
        $copyJadwal = $this->request->getPost('copy_jadwal') === '1';

        $from = $fromId > 0 ? $this->tahunAjarModel->find($fromId) : null;
        $to = $toId > 0 ? $this->tahunAjarModel->find($toId) : null;
        if (!$from || !$to) {
            return redirect()->to('/admin/kelompok')->with('error', 'Pilih tahun sumber dan tahun tujuan carry-over.');
        }
        if ($fromId === $toId) {
            return redirect()->to('/admin/kelompok')->with('error', 'Tahun sumber dan tujuan tidak boleh sama.');
        }
        if (($to['status_aktif'] ?? 'Nonaktif') !== 'Aktif') {
            return redirect()->to('/admin/kelompok')->with('error', 'Tahun tujuan harus berstatus Aktif agar menjadi lembaran baru.');
        }

        $sources = $this->kelompokModel->where('tahun_ajar_id', $fromId)->findAll();
        if (empty($sources)) {
            return redirect()->to('/admin/kelompok')->with('error', 'Tidak ada kelompok pada tahun sumber yang bisa dilanjutkan.');
        }

        $this->db->transStart();
        try {
            $kelompokBaru = 0;
            $anggotaBaru = 0;
            $jadwalBaru = 0;
            $jadwalDilewati = 0;

            foreach ($sources as $src) {
                if (!$this->isSupervisor((int) $src['supervisor_id'])) {
                    continue;
                }
                $exists = $this->kelompokModel
                    ->where('tahun_ajar_id', $toId)
                    ->where('nama_kelompok', $src['nama_kelompok'])
                    ->first();
                if ($exists) {
                    $newKelompokId = (int) $exists['id'];
                } else {
                    $newKelompokId = (int) $this->kelompokModel->insert([
                        'nama_kelompok' => $src['nama_kelompok'],
                        'supervisor_id' => (int) $src['supervisor_id'],
                        'tahun_ajar_id' => $toId,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                    if (!$newKelompokId) {
                        throw new \RuntimeException('Gagal menyalin kelompok ' . $src['nama_kelompok']);
                    }
                    $kelompokBaru++;
                }

                // Salin anggota kelompok
                $members = $this->anggotaModel->where('kelompok_id', (int) $src['id'])->findAll();
                foreach ($members as $m) {
                    $guru = $this->guruModel->find((int) $m['guru_id']);
                    if (!$guru) {
                        continue;
                    }
                    $dup = $this->anggotaModel
                        ->where('kelompok_id', $newKelompokId)
                        ->where('guru_id', (int) $m['guru_id'])
                        ->first();
                    if (!$dup) {
                        $this->anggotaModel->insert([
                            'kelompok_id' => $newKelompokId,
                            'guru_id'     => (int) $m['guru_id'],
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]);
                        $anggotaBaru++;
                    }
                }

                // Salin jenis penilaian kelompok
                $this->kelompokJenisModel->copyJenis((int) $src['id'], $newKelompokId);

                // Salin jadwal jika dicentang
                if ($copyJadwal) {
                    $jadwals = $this->db->table('jadwal_supervisi')
                        ->where('kelompok_id', (int) $src['id'])
                        ->where('tahun_ajar_id', $fromId)
                        ->get()->getResultArray();
                    foreach ($jadwals as $j) {
                        // Cegah duplikasi dalam kelompok tujuan yang sama
                        $already = $this->db->table('jadwal_supervisi')
                            ->where('kelompok_id', $newKelompokId)
                            ->where('guru_id', (int) $j['guru_id'])
                            ->countAllResults();
                        if ($already > 0) {
                            $jadwalDilewati++;
                            continue;
                        }
                        unset($j['id']);
                        $j['kelompok_id'] = $newKelompokId;
                        $j['tahun_ajar_id'] = $toId;
                        $j['status'] = 'Terjadwal';
                        $j['created_at'] = date('Y-m-d H:i:s');
                        $this->db->table('jadwal_supervisi')->insert($j);
                        $jadwalBaru++;
                    }
                }
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return redirect()->to('/admin/kelompok')->with('error', 'Carry-over gagal. Transaksi dibatalkan.');
            }

            $this->auditLogModel->logActivity(
                session()->get('id'),
                'carry_over_kelompok',
                "Admin carry-over {$kelompokBaru} kelompok + {$anggotaBaru} anggota dari tahun #{$fromId} ke #{$toId}"
            );

            $msg = "Carry-over selesai: {$kelompokBaru} kelompok diperbarui beserta jenis penilaiannya.";
            if ($copyJadwal) {
                $msg .= " {$jadwalBaru} jadwal disalin ({$jadwalDilewati} dilewati agar 1 guru tetap 1x).";
            }
            return redirect()->to('/admin/kelompok')->with('success', $msg);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Carry-over kelompok gagal: ' . $e->getMessage());
            return redirect()->to('/admin/kelompok')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate Jadwal Otomatis khusus untuk anggota dalam Kelompok Supervisi ini.
     */
    public function generateJadwal($id)
    {
        $kelompok = $this->kelompokModel
            ->select('kelompok_supervisi.*, users.username as nama_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester, tahun_ajar.status_aktif')
            ->join('users', 'users.id = kelompok_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = kelompok_supervisi.tahun_ajar_id', 'left')
            ->where('kelompok_supervisi.id', $id)
            ->first();

        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok supervisi tidak ditemukan.');
        }

        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $sesiMulai = (int) ($this->request->getPost('sesi_mulai') ?? 1);
        $maxPerDay = (int) ($this->request->getPost('max_per_day') ?? 1);
        $selectedGuruIds = (array) $this->request->getPost('guru_ids');

        if (empty($tanggalMulai) || empty($tanggalSelesai) || $tanggalMulai > $tanggalSelesai) {
            return redirect()->back()->with('error', 'Rentang tanggal pelaksanaan supervisi tidak valid.');
        }

        if (empty($selectedGuruIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu guru anggota yang akan dijadwalkan.');
        }

        $tahunAjarId = (int) ($kelompok['tahun_ajar_id'] ?? 0);
        if ($tahunAjarId <= 0) {
            $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
            $tahunAjarId = $tahunAktif ? (int) $tahunAktif['id'] : 0;
        }

        if ($tahunAjarId <= 0) {
            return redirect()->back()->with('error', 'Tahun ajaran belum ditentukan untuk kelompok ini dan belum ada tahun aktif.');
        }

        $tahunAjar = $this->tahunAjarModel->find($tahunAjarId);
        if (!$tahunAjar || ($tahunAjar['status_aktif'] ?? 'Nonaktif') !== 'Aktif') {
            return redirect()->back()->with('error', 'Generate jadwal hanya diperbolehkan untuk Tahun Ajaran yang sedang Aktif.');
        }

        // Ambil guru terpilih yang memang anggota kelompok ini
        $validMembers = $this->anggotaModel
            ->select('guru.id, guru.nama, guru.nip, guru.mata_pelajaran')
            ->join('guru', 'guru.id = kelompok_anggota.guru_id')
            ->where('kelompok_anggota.kelompok_id', $id)
            ->whereIn('guru.id', $selectedGuruIds)
            ->orderBy('guru.nama', 'ASC')
            ->findAll();

        if (empty($validMembers)) {
            return redirect()->back()->with('error', 'Tidak ada anggota guru valid yang dipilih untuk dijadwalkan.');
        }

        // Kumpulkan hari kerja (Senin s.d. Sabtu)
        $start = new \DateTime($tanggalMulai);
        $end = new \DateTime($tanggalSelesai);
        $end->modify('+1 day');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        $workingDays = [];
        foreach ($period as $dt) {
            if ($dt->format('N') != 7) {
                $workingDays[] = $dt->format('Y-m-d');
            }
        }
        if (empty($workingDays)) {
            return redirect()->back()->with('error', 'Tidak ada hari kerja (Senin–Sabtu) dalam rentang tanggal yang dipilih.');
        }

        // Ambil kelas aktif
        $kelases = $this->kelasModel
            ->where('tahun_ajar_id', $tahunAjarId)
            ->where('status', 'Aktif')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();
        if (empty($kelases)) {
            $kelases = $this->kelasModel->where('status', 'Aktif')->findAll();
        }

        // Slot jam pelajaran diambil dinamis dari Pengaturan Sistem
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

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        $queueGurus = $validMembers;
        $insertedCount = 0;
        $skippedCount = 0;
        $kelasCount = count($kelases);
        $kelasIdx = 0;

        $this->db->transStart();

        foreach ($workingDays as $currentDate) {
            $dayEnglish = date('l', strtotime($currentDate));
            $hariIndo = $hariMap[$dayEnglish] ?? 'Senin';

            for ($slot = 0; $slot < $maxPerDay; $slot++) {
                if (empty($queueGurus)) {
                    break 2;
                }

                $guru = array_shift($queueGurus);
                $guruId = (int) $guru['id'];

                // Cek apakah guru sudah memiliki jadwal di KELOMPOK ini
                $alreadyInThisKelompok = $this->db->table('jadwal_supervisi')
                    ->where('kelompok_id', $id)
                    ->where('guru_id', $guruId)
                    ->countAllResults();

                if ($alreadyInThisKelompok > 0) {
                    $skippedCount++;
                    continue;
                }

                // Cek apakah anggota adalah Tendik / Tenaga Teknis / Tata Usaha (tidak memiliki kelas KBM)
                $isTendik = (
                    ($guru['jenis_ptk'] ?? '') === 'Tendik' ||
                    stripos($guru['mata_pelajaran'] ?? '', 'tata usaha') !== false ||
                    stripos($guru['mata_pelajaran'] ?? '', 'administrasi') !== false ||
                    stripos($kelompok['nama_kelompok'] ?? '', 'teknis') !== false ||
                    stripos($kelompok['nama_kelompok'] ?? '', 'tata usaha') !== false
                );

                if ($isTendik) {
                    $selectedKelas   = null;
                    $kelasName       = '-';
                    $kelasId         = null;
                    $materiSupervisi = 'Supervisi Administrasi & Layanan Kependidikan';
                } else {
                    $selectedKelas   = !empty($kelases) ? $kelases[$kelasIdx % $kelasCount] : null;
                    $kelasName       = $selectedKelas ? $selectedKelas['nama_kelas'] : 'Semua Kelas';
                    $kelasId         = $selectedKelas ? $selectedKelas['id'] : null;
                    $materiSupervisi = 'Supervisi Akademik Proses Pembelajaran';
                    $kelasIdx++;
                }

                $currentSlotIdx = ($startSlotIdx + $slot) % count($availableSlots);
                $slotNumber = $availableSlots[$currentSlotIdx];
                $timeInfo = $slotWaktu[$slotNumber] ?? [
                    'jam_ke' => (string)$slotNumber,
                    'waktu_dari' => '07:00',
                    'waktu_sampai' => '07:45'
                ];

                // Proteksi bentrok waktu: cek apakah guru sudah punya jadwal di hari dan jam yang sama
                $bentrokGuru = $this->db->table('jadwal_supervisi')
                    ->where('guru_id', $guruId)
                    ->where('tanggal_supervisi', $currentDate)
                    ->where('jam_ke', $timeInfo['jam_ke'])
                    ->countAllResults();

                // Proteksi bentrok supervisor: cek apakah supervisor ada jadwal guru lain di hari dan jam yang sama
                $bentrokSupervisor = $this->db->table('jadwal_supervisi')
                    ->where('supervisor_id', $kelompok['supervisor_id'])
                    ->where('tanggal_supervisi', $currentDate)
                    ->where('jam_ke', $timeInfo['jam_ke'])
                    ->countAllResults();

                if ($bentrokGuru > 0 || $bentrokSupervisor > 0) {
                    // Masukkan kembali guru ke antrian agar dijadwalkan pada slot/hari berikutnya
                    array_unshift($queueGurus, $guru);
                    continue;
                }

                $dataInsert = [
                    'kelompok_id'       => $id,
                    'tahun_ajar_id'     => $tahunAjarId,
                    'guru_id'           => $guruId,
                    'supervisor_id'     => $kelompok['supervisor_id'],
                    'mata_pelajaran'    => !empty($guru['mata_pelajaran']) ? $guru['mata_pelajaran'] : 'Mata Pelajaran Umum',
                    'kelas'             => $kelasName,
                    'kelas_id'          => $kelasId,
                    'jam_ke'            => $timeInfo['jam_ke'],
                    'hari'              => $hariIndo,
                    'tanggal_supervisi' => $currentDate,
                    'waktu_dari'        => $timeInfo['waktu_dari'],
                    'waktu_sampai'      => $timeInfo['waktu_sampai'],
                    'materi_supervisi'  => $materiSupervisi,
                    'status'            => 'Terjadwal',
                    'created_at'        => date('Y-m-d H:i:s')
                ];

                $this->db->table('jadwal_supervisi')->insert($dataInsert);
                $insertedCount++;
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal membuat jadwal supervisi kelompok. Transaksi dibatalkan.');
        }

        $this->auditLogModel->logActivity(
            session()->get('id'),
            'generate_jadwal_kelompok',
            "Admin men-generate {$insertedCount} jadwal supervisi untuk kelompok #{$id} ({$kelompok['nama_kelompok']}) - {$skippedCount} guru dilewati karena sudah ada jadwal."
        );

        $msg = "Berhasil membuat {$insertedCount} jadwal supervisi untuk {$kelompok['nama_kelompok']}!";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} guru dilewati karena sudah memiliki jadwal di tahun ajaran ini).";
        }

        return redirect()->to('/admin/kelompok/' . $id)->with('success', $msg);
    }

    /**
     * Tambah satu atau beberapa guru ke dalam anggota kelompok.
     */
    public function addAnggota($id)
    {
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $guruIds = $this->normalizeAnggotaIds($this->request->getPost('guru_ids'));
        if (empty($guruIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu guru untuk ditambahkan ke kelompok.');
        }

        $now = date('Y-m-d H:i:s');
        $addedCount = 0;
        foreach ($guruIds as $gId) {
            $guru = $this->guruModel->find($gId);
            if (!$guru) {
                continue;
            }
            $exists = $this->anggotaModel
                ->where('kelompok_id', $id)
                ->where('guru_id', $gId)
                ->first();
            if (!$exists) {
                $this->anggotaModel->insert([
                    'kelompok_id' => $id,
                    'guru_id'     => $gId,
                    'created_at'  => $now,
                ]);
                $addedCount++;
            }
        }

        $this->auditLogModel->logActivity(
            session()->get('id'),
            'add_anggota_kelompok',
            "Admin menambahkan {$addedCount} guru ke kelompok #{$id} ({$kelompok['nama_kelompok']})"
        );

        return redirect()->to('/admin/kelompok/' . $id)->with('success', "Berhasil menambahkan {$addedCount} guru ke kelompok.");
    }

    /**
     * Hapus satu anggota guru dari kelompok.
     */
    public function deleteAnggota($id, $guruId)
    {
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $guru = $this->guruModel->find((int) $guruId);
        $namaGuru = $guru['nama'] ?? "Guru #{$guruId}";

        $this->anggotaModel
            ->where('kelompok_id', $id)
            ->where('guru_id', (int) $guruId)
            ->delete();

        $this->auditLogModel->logActivity(
            session()->get('id'),
            'delete_anggota_kelompok',
            "Admin menghapus guru {$namaGuru} dari kelompok #{$id} ({$kelompok['nama_kelompok']})"
        );

        return redirect()->to('/admin/kelompok/' . $id)->with('success', "Guru {$namaGuru} berhasil dihapus dari kelompok ini.");
    }

    /**
     * Update penugasan jenis penilaian untuk suatu kelompok.
     */
    public function updateJenis($id)
    {
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $jenisIds = (array) $this->request->getPost('jenis_penilaian_ids');
        $this->kelompokJenisModel->syncJenis((int) $id, $jenisIds);

        $count = count(array_filter($jenisIds));
        $msg = $count > 0 
            ? "Jenis penilaian untuk kelompok '{$kelompok['nama_kelompok']}' berhasil diperbarui ({$count} jenis instrumen ditugaskan)."
            : "Jenis penilaian untuk kelompok '{$kelompok['nama_kelompok']}' diatur ke Semua Komponen Aktif (default).";

        $this->auditLogModel->logActivity(
            session()->get('id'),
            'update_jenis_kelompok',
            "Admin memperbarui penugasan jenis penilaian kelompok #{$id} ({$kelompok['nama_kelompok']})"
        );

        return redirect()->to('/admin/kelompok/' . $id)->with('success', $msg);
    }

    public function delete($id)
    {
        $kelompok = $this->kelompokModel
            ->select('kelompok_supervisi.*, users.username as nama_supervisor')
            ->join('users', 'users.id = kelompok_supervisi.supervisor_id', 'left')
            ->where('kelompok_supervisi.id', $id)->first();

        if (!$kelompok) {
            return redirect()->to('/admin/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        $jadwalCount = $this->db->table('jadwal_supervisi')->where('kelompok_id', $id)->countAllResults();

        $this->db->transStart();
        try {
            if ($jadwalCount > 0 && $this->db->fieldExists('kelompok_id', 'jadwal_supervisi')) {
                $this->db->table('jadwal_supervisi')->where('kelompok_id', $id)->update(['kelompok_id' => null]);
            }
            $this->anggotaModel->where('kelompok_id', $id)->delete();
            $this->kelompokJenisModel->where('kelompok_id', $id)->delete();
            $this->kelompokModel->delete($id);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->to('/admin/kelompok')->with('error', 'Gagal menghapus kelompok.');
            }

            $this->auditLogModel->logActivity(
                session()->get('id'),
                'delete_kelompok',
                "Admin menghapus kelompok supervisi #{$id} ({$kelompok['nama_kelompok']}); {$jadwalCount} jadwal dilepas"
            );

            $msg = 'Kelompok supervisi berhasil dihapus.';
            if ($jadwalCount > 0) {
                $msg .= " {$jadwalCount} jadwal dilepas dari kelompok ini.";
            }

            return redirect()->to('/admin/kelompok')->with('success', $msg);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Delete kelompok gagal: ' . $e->getMessage());
            return redirect()->to('/admin/kelompok')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getArsipCountsByTahun(): array
    {
        $rows = $this->db->table('kelompok_supervisi')
            ->select('tahun_ajar_id, COUNT(*) as total')
            ->groupBy('tahun_ajar_id')
            ->get()->getResultArray();
        $out = [];
        foreach ($rows as $row) {
            $key = $row['tahun_ajar_id'] === null ? 'tanpa_tahun' : (int) $row['tahun_ajar_id'];
            $out[$key] = (int) $row['total'];
        }

        return $out;
    }

    // ---------- Helpers ----------

    private function getSupervisors(): array
    {
        return $this->userModel
            ->select('users.id, users.username, users.role, COALESCE(guru.nama, users.username) as nama_lengkap, guru.nip')
            ->join('guru', 'guru.user_id = users.id', 'left')
            ->whereIn('users.role', ['supervisor', 'kepala'])
            ->where('users.status', 'Aktif')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    }

    private function isSupervisor(int $userId): bool
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return false;
        }

        return in_array($user['role'] ?? '', ['supervisor', 'kepala'], true)
            && ($user['status'] ?? '') === 'Aktif';
    }

    private function normalizeAnggotaIds($input): array
    {
        if (empty($input)) {
            return [];
        }
        if (!is_array($input)) {
            $input = [$input];
        }
        $ids = [];
        foreach ($input as $v) {
            if ($v === '' || $v === null) {
                continue;
            }
            $ids[] = (int) $v;
        }

        return array_values(array_unique(array_filter($ids, static fn ($v) => $v > 0)));
    }

    private function allGuruExist(array $ids): bool
    {
        $found = $this->guruModel->whereIn('id', $ids)->findAll();

        return count($found) === count($ids);
    }

    private function insertAnggota(int $kelompokId, array $anggotaIds): void
    {
        if (empty($anggotaIds)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $batch = [];
        foreach ($anggotaIds as $guruId) {
            $batch[] = [
                'kelompok_id' => $kelompokId,
                'guru_id'     => $guruId,
                'created_at'  => $now,
            ];
        }
        $this->anggotaModel->insertBatch($batch);
    }
}
