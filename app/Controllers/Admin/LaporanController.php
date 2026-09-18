<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\LaporanModel;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\JenisPenilaianModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanController extends BaseController
{
    public function index()
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $tahunAjarModel = new TahunAjarModel();
        $jenisPenilaianModel = new JenisPenilaianModel();
        $db = \Config\Database::connect();

        // 1. Tahun Ajar Filter
        $tahunAjars = $tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll();

        $activeTahunAjar = null;
        foreach ($tahunAjars as $ta) {
            if (!empty($ta['is_active'])) {
                $activeTahunAjar = $ta;
                break;
            }
        }
        if (!$activeTahunAjar && !empty($tahunAjars)) {
            $activeTahunAjar = $tahunAjars[0];
        }

        $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
        if (empty($tahun_ajar_id) && $activeTahunAjar) {
            $tahun_ajar_id = $activeTahunAjar['id'];
        }

        $selectedTahunAjar = null;
        if ($tahun_ajar_id) {
            foreach ($tahunAjars as $ta) {
                if ($ta['id'] == $tahun_ajar_id) {
                    $selectedTahunAjar = $ta;
                    break;
                }
            }
        }

        // 2. Daftar Jenis Penilaian untuk kolom skor dinamis
        $jenisPenilaians = $jenisPenilaianModel->findAll();

        // 3. Query Jadwal Supervisi pada tahun ajaran yang dipilih
        $jadwalQuery = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran, kelas.nama_kelas, supervisor.username as nama_supervisor')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users as supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id', 'left');

        if ($tahun_ajar_id) {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
        }

        $jadwals = $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')->findAll();

        // 4. Hitung Skor per Guru dan per Jenis Penilaian
        $totalJadwal = count($jadwals);
        $selesaiList = [];
        $totalNilaiSum = 0;
        $predikatCounts = [
            'Baik Sekali' => 0,
            'Baik'        => 0,
            'Cukup'       => 0,
            'Kurang'      => 0
        ];

        $skorPerJenisSum = [];
        $countPerJenis = [];
        foreach ($jenisPenilaians as $jp) {
            $skorPerJenisSum[$jp['id']] = 0;
            $countPerJenis[$jp['id']] = 0;
        }

        foreach ($jadwals as &$jadwal) {
            $jadwal['nilai_per_jenis'] = [];
            $jadwal['nilai_akhir'] = null;
            $jadwal['ketercapaian'] = null;
            $jadwal['predikat_badge'] = 'secondary';

            if ($jadwal['status'] === 'Selesai') {
                $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();

                $grandTotalSkor = 0;
                $grandTotalMaks = 0;

                foreach ($hasilRecords as $hasil) {
                    $detailHasil = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->selectSum('skor')
                        ->get()
                        ->getRow();

                    $detailCount = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->countAllResults();

                    $skor = (float)($detailHasil->skor ?? 0);
                    $max = $detailCount * 4;
                    $persen = ($max > 0) ? round(($skor / $max) * 100, 2) : 0;

                    $jadwal['nilai_per_jenis'][$hasil['jenis_penilaian_id']] = [
                        'skor' => $skor,
                        'maksimal' => $max,
                        'nilai' => $persen
                    ];

                    $grandTotalSkor += $skor;
                    $grandTotalMaks += $max;

                    if (isset($skorPerJenisSum[$hasil['jenis_penilaian_id']])) {
                        $skorPerJenisSum[$hasil['jenis_penilaian_id']] += $persen;
                        $countPerJenis[$hasil['jenis_penilaian_id']]++;
                    }
                }

                if ($grandTotalMaks > 0) {
                    $nilaiAkhir = round(($grandTotalSkor / $grandTotalMaks) * 100, 2);
                    $jadwal['nilai_akhir'] = $nilaiAkhir;

                    if ($nilaiAkhir >= 86) {
                        $ketercapaian = 'Baik Sekali';
                        $predikatBadge = 'success';
                    } elseif ($nilaiAkhir >= 70) {
                        $ketercapaian = 'Baik';
                        $predikatBadge = 'primary';
                    } elseif ($nilaiAkhir >= 55) {
                        $ketercapaian = 'Cukup';
                        $predikatBadge = 'warning';
                    } else {
                        $ketercapaian = 'Kurang';
                        $predikatBadge = 'danger';
                    }

                    $jadwal['ketercapaian'] = $ketercapaian;
                    $jadwal['predikat_badge'] = $predikatBadge;
                    $predikatCounts[$ketercapaian]++;
                    $totalNilaiSum += $nilaiAkhir;
                    $selesaiList[] = $jadwal;
                }
            }
        }
        unset($jadwal);

        $totalSelesai = count($selesaiList);
        $totalBelumSelesai = $totalJadwal - $totalSelesai;
        $rataRataNilai = $totalSelesai > 0 ? round($totalNilaiSum / $totalSelesai, 2) : 0;

        // Persentase Ketercapaian Kualitas (Baik Sekali + Baik)
        $kualitasBaik = $predikatCounts['Baik Sekali'] + $predikatCounts['Baik'];
        $persenKualitas = $totalSelesai > 0 ? round(($kualitasBaik / $totalSelesai) * 100, 1) : 0;

        // Rata-rata per Jenis Penilaian
        $avgPerJenis = [];
        $chartJenisLabels = [];
        $chartJenisData = [];
        foreach ($jenisPenilaians as $jp) {
            $val = ($countPerJenis[$jp['id']] > 0) 
                ? round($skorPerJenisSum[$jp['id']] / $countPerJenis[$jp['id']], 2) 
                : 0;
            $avgPerJenis[$jp['id']] = $val;
            $chartJenisLabels[] = $jp['nama'] ?? ('Jenis ' . $jp['id']);
            $chartJenisData[] = $val;
        }

        $data = [
            'title' => 'Laporan dan Analisis Hasil Supervisi',
            'tahun_ajars' => $tahunAjars,
            'tahun_ajar_id' => $tahun_ajar_id,
            'selectedTahunAjar' => $selectedTahunAjar,
            'jenisPenilaians' => $jenisPenilaians,
            'jadwals' => $jadwals,
            'metrics' => [
                'total_jadwal' => $totalJadwal,
                'total_selesai' => $totalSelesai,
                'total_belum' => $totalBelumSelesai,
                'rata_rata_nilai' => $rataRataNilai,
                'persen_kualitas' => $persenKualitas,
                'predikat_counts' => $predikatCounts,
                'avg_per_jenis' => $avgPerJenis
            ],
            'chart_predikat' => [
                'labels' => ['Baik Sekali (86-100)', 'Baik (70-85)', 'Cukup (55-69)', 'Kurang (<55)'],
                'data'   => [
                    $predikatCounts['Baik Sekali'],
                    $predikatCounts['Baik'],
                    $predikatCounts['Cukup'],
                    $predikatCounts['Kurang']
                ]
            ],
            'chart_jenis' => [
                'labels' => $chartJenisLabels,
                'data'   => $chartJenisData
            ]
        ];

        return view('admin/laporan/index', $data);
    }

    public function exportPdfRekapDetail()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $hasilModel = new HasilSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $jenisPenilaianModel = new JenisPenilaianModel();
            $userModel = new UserModel();
            $db = \Config\Database::connect();

            helper('setting');
            helper('date');

            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');

            // Find selected tahun ajar
            $tahunAjars = $tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll();
            $selectedTahunAjar = null;
            if ($tahun_ajar_id) {
                $selectedTahunAjar = $tahunAjarModel->find($tahun_ajar_id);
            } else {
                foreach ($tahunAjars as $ta) {
                    if (!empty($ta['is_active'])) {
                        $selectedTahunAjar = $ta;
                        $tahun_ajar_id = $ta['id'];
                        break;
                    }
                }
                if (!$selectedTahunAjar && !empty($tahunAjars)) {
                    $selectedTahunAjar = $tahunAjars[0];
                    $tahun_ajar_id = $selectedTahunAjar['id'];
                }
            }

            // Get all jenis penilaian
            $jenisPenilaians = $jenisPenilaianModel->findAll();

            // Query completed schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran, kelas.nama_kelas, supervisor.username as nama_supervisor, supervisor.nip as nip_supervisor')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->join('users as supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id', 'left')
                ->where('jadwal_supervisi.status', 'Selesai');

            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }

            $jadwals = $jadwalQuery->orderBy('guru.nama', 'ASC')->findAll();

            // Calculate detailed score per teacher & per jenis penilaian
            $rekapData = [];
            $predikatStats = [
                'Baik Sekali' => ['count' => 0, 'range' => '86.00 - 100.00', 'color' => '#155724', 'bg' => '#d4edda'],
                'Baik'        => ['count' => 0, 'range' => '70.00 - 85.99',  'color' => '#004085', 'bg' => '#cce7ff'],
                'Cukup'       => ['count' => 0, 'range' => '55.00 - 69.99',  'color' => '#856404', 'bg' => '#fff3cd'],
                'Kurang'      => ['count' => 0, 'range' => '< 55.00',        'color' => '#721c24', 'bg' => '#f8d7da']
            ];
            $totalNilaiSum = 0;

            foreach ($jadwals as $jadwal) {
                $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();

                $nilaiPerJenis = [];
                $grandTotalSkor = 0;
                $grandTotalMaks = 0;

                foreach ($hasilRecords as $hasil) {
                    $detailHasil = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->selectSum('skor')
                        ->get()
                        ->getRow();

                    $detailCount = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->countAllResults();

                    $skor = (float)($detailHasil->skor ?? 0);
                    $max = $detailCount * 4;
                    $persen = ($max > 0) ? round(($skor / $max) * 100, 2) : 0;

                    $nilaiPerJenis[$hasil['jenis_penilaian_id']] = $persen;
                    $grandTotalSkor += $skor;
                    $grandTotalMaks += $max;
                }

                $nilaiAkhir = ($grandTotalMaks > 0) ? round(($grandTotalSkor / $grandTotalMaks) * 100, 2) : 0;
                if ($nilaiAkhir >= 86) {
                    $predikat = 'Baik Sekali';
                } elseif ($nilaiAkhir >= 70) {
                    $predikat = 'Baik';
                } elseif ($nilaiAkhir >= 55) {
                    $predikat = 'Cukup';
                } else {
                    $predikat = 'Kurang';
                }

                $predikatStats[$predikat]['count']++;
                $totalNilaiSum += $nilaiAkhir;

                $jadwal['nilai_per_jenis'] = $nilaiPerJenis;
                $jadwal['nilai_akhir'] = $nilaiAkhir;
                $jadwal['predikat'] = $predikat;
                $rekapData[] = $jadwal;
            }

            $totalGuru = count($rekapData);
            $rataRata = $totalGuru > 0 ? round($totalNilaiSum / $totalGuru, 2) : 0;

            // Get Signatures Data (SMA placeholders blank until set via Pengaturan).
            $namaKepala = get_pengaturan('nama_kepala', '');
            $nipKepala = get_pengaturan('nip_kepala', '');
            $kotaMadrasah = get_pengaturan('kecamatan', '');

            // Default supervisor or first supervisor found in system
            $firstSupervisor = $userModel->where('role', 'supervisor')->first();
            $namaSupervisor = $firstSupervisor['username'] ?? 'Supervisor Pembina';
            $nipSupervisor = $firstSupervisor['nip'] ?? '-';

            $data = [
                'selectedTahunAjar' => $selectedTahunAjar,
                'jenisPenilaians'   => $jenisPenilaians,
                'rekapData'         => $rekapData,
                'totalGuru'         => $totalGuru,
                'rataRata'          => $rataRata,
                'predikatStats'     => $predikatStats,
                'namaKepala'        => $namaKepala,
                'nipKepala'         => $nipKepala,
                'namaSupervisor'    => $namaSupervisor,
                'nipSupervisor'     => $nipSupervisor,
                'kotaMadrasah'      => $kotaMadrasah,
                'tanggalCetak'      => date('Y-m-d')
            ];

            // Clear buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            $html = view('admin/laporan/pdf_rekap_detail', $data);

            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $filename = 'Rekapitulasi-Detail-Supervisi-' . ($selectedTahunAjar ? preg_replace('/[^A-Za-z0-9]/', '-', $selectedTahunAjar['tahun_ajar'] . '-' . $selectedTahunAjar['semester']) : date('Ymd')) . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');

            $dompdf->stream($filename, ['Attachment' => 0]);
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Rekap Detail PDF Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function statistikPengguna()
    {
        $laporanModel = new LaporanModel();

        $role = $this->request->getGet('role');
        $status = $this->request->getGet('status');

        $data['users'] = $laporanModel->getUsers($role, $status);
        $data['roleCounts'] = $laporanModel->getUserRoleCounts();
        $data['statusCounts'] = $laporanModel->getUserStatusCounts();
        $data['role'] = $role;
        $data['status'] = $status;

        return view('admin/laporan/statistik_pengguna', $data);
    }
    
    public function hasilSupervisi()
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $tahunAjarModel = new TahunAjarModel();
        $guruModel = new GuruModel();
        $db = \Config\Database::connect();
        
        // Get filter parameters
        $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
        $status = $this->request->getGet('status');
        
        // Get tahun ajaran list for filter
        $data['tahun_ajars'] = $tahunAjarModel->findAll();
        
        // Build query for supervision schedules
        $jadwalQuery = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id');
            
        // Apply filters
        if ($tahun_ajar_id) {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
        }
        
        if ($status) {
            $jadwalQuery->where('jadwal_supervisi.status', $status);
        }
        
        // Get supervision schedules
        $data['jadwals'] = $jadwalQuery->findAll();
        
        // Add hasil information to each schedule
        foreach ($data['jadwals'] as &$jadwal) {
            if ($jadwal['status'] == 'Selesai') {
                // Get all hasil records for this schedule
                $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();
                
                if (!empty($hasilRecords)) {
                    // Calculate nilai_akhir dynamically based on detail_hasil_penilaian
                    $totalSkor = 0;
                    $totalMaksimal = 0;
                    
                    foreach ($hasilRecords as $hasil) {
                        // Get detail hasil penilaian for this hasil record
                        $detailHasil = $db->table('detail_hasil_penilaian')
                            ->where('hasil_supervisi_id', $hasil['id'])
                            ->selectSum('skor')
                            ->get()
                            ->getRow();
                        
                        // Get count of detail penilaian to calculate max possible score
                        $detailCount = $db->table('detail_hasil_penilaian')
                            ->where('hasil_supervisi_id', $hasil['id'])
                            ->countAllResults();
                        
                        // Add to totals
                        $totalSkor += $detailHasil->skor ?? 0;
                        // Assuming max score per item is 4 (based on system specs)
                        $totalMaksimal += $detailCount * 4;
                    }
                    
                    // Calculate nilai_akhir: (actual score sum / max possible score) × 100
                    $nilaiAkhir = ($totalMaksimal > 0) ? ($totalSkor / $totalMaksimal) * 100 : 0;
                    
                    // Determine ketercapaian based on nilai_akhir
                    if ($nilaiAkhir >= 86) {
                        $ketercapaian = 'Baik Sekali';
                    } elseif ($nilaiAkhir >= 70) {
                        $ketercapaian = 'Baik';
                    } elseif ($nilaiAkhir >= 55) {
                        $ketercapaian = 'Cukup';
                    } else {
                        $ketercapaian = 'Kurang';
                    }
                    
                    // Create a mock hasil record with calculated nilai_akhir
                    $jadwal['hasil'] = [
                        'nilai_akhir' => $nilaiAkhir,
                        'ketercapaian' => $ketercapaian,
                        'total_skor' => $totalSkor,
                        'total_maksimal' => $totalMaksimal
                    ];
                } else {
                    $jadwal['hasil'] = null;
                }
            } else {
                $jadwal['hasil'] = null;
            }
        }
        
        // Count statistics
        $data['total_jadwal'] = count($data['jadwals']);
        $data['selesai'] = array_filter($data['jadwals'], function($jadwal) {
            return $jadwal['status'] == 'Selesai';
        });
        $data['belum_selesai'] = array_filter($data['jadwals'], function($jadwal) {
            return $jadwal['status'] != 'Selesai';
        });
        
        $data['tahun_ajar_id'] = $tahun_ajar_id;
        $data['status'] = $status;
        
        return view('admin/laporan/hasil_supervisi', $data);
    }
    
    public function detail($id)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $db = \Config\Database::connect();
        
        // Get schedule with related data
        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil supervisi tidak ditemukan');
        }
        
        // Get hasil records for this schedule
        $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
        
        // Get detail results grouped by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek')
                ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id', 'left')
                ->where('dhp.hasil_supervisi_id', $hasil['id'])
                ->get()
                ->getResultArray();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }
        
        // Get photo evidence
        $fotoBukti = $db->table('foto_bukti')
            ->where('jadwal_supervisi_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];
        
        return view('admin/laporan/detail', $data);
    }
    
    public function cetakDetail($id)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();
        
        // Get schedule with related data
        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil supervisi tidak ditemukan');
        }
        
        // Get hasil records for this schedule
        $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
        
        // Get detail results grouped by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek')
                ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id', 'left')
                ->where('dhp.hasil_supervisi_id', $hasil['id'])
                ->get()
                ->getResultArray();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }
        
        // Get photo evidence
        $fotoBukti = $db->table('foto_bukti')
            ->where('jadwal_supervisi_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
            
        // Get kepala sekolah data
        $kepalaSekolah = $userModel
            ->select('username, nip')
            ->where('role', 'kepala')
            ->first();
            
        // Set nama dan nip kepala sekolah: prefer Pengaturan Identitas, fallback akun role kepala.
        $schedule['nama_kepala'] = get_pengaturan('nama_kepala', '');
        $schedule['nip_kepala'] = get_pengaturan('nip_kepala', '');

        if ($schedule['nama_kepala'] === '' && $kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'] ?? '';
            $schedule['nip_kepala'] = $kepalaSekolah['nip'] ?? '';
        }
        
        // Get supervisor data if not already in schedule
        if (empty($schedule['nama_supervisor']) && !empty($schedule['supervisor_id'])) {
            $supervisor = $userModel
                ->select('username, nip')
                ->where('id', $schedule['supervisor_id'])
                ->first();
                
            if ($supervisor) {
                $schedule['nama_supervisor'] = $supervisor['username'] ?? 'Supervisor';
                $schedule['nip_supervisor'] = $supervisor['nip'] ?? '';
            }
        } else if (empty($schedule['nama_supervisor'])) {
            $schedule['nama_supervisor'] = 'Supervisor';
            $schedule['nip_supervisor'] = '';
        }
        
        // Ensure nip fields exist
        if (!isset($schedule['nip_supervisor'])) {
            $schedule['nip_supervisor'] = '';
        }

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];
        
        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $html = view('supervisor/hasil/pdf_view', $data);
        
        // Setup Dompdf
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        $filename = 'detail_hasil_supervisi_' . ($schedule['nama_guru'] ?? 'guru') . '_' . date('Y-m-d');
        $dompdf->stream($filename . ".pdf", ['Attachment' => false]);
        exit();
    }
    
    public function exportExcel()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
            $db = \Config\Database::connect();
            
            // Get filter parameters
            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
            $status = $this->request->getGet('status');
            
            // Build query for completed schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left');
                
            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if ($status) {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            // Get results for each schedule
            $completedJadwals = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal['status'] == 'Selesai') {
                    // Get average nilai_akhir for this schedule
                    $hasilQuery = $db->table('hasil_supervisi')
                        ->select('AVG(nilai_akhir) as avg_nilai')
                        ->where('jadwal_supervisi_id', $jadwal['id']);
                        
                    $result = $hasilQuery->get()->getRow();
                    
                    if ($result) {
                        $jadwal['hasil']['nilai_akhir'] = $result->avg_nilai;
                        
                        // Determine ketercapaian based on nilai
                        if ($result->avg_nilai >= 3.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Sangat Baik';
                        } elseif ($result->avg_nilai >= 2.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Baik';
                        } elseif ($result->avg_nilai >= 1.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Cukup';
                        } else {
                            $jadwal['hasil']['ketercapaian'] = 'Kurang';
                        }
                    }
                }
                $completedJadwals[] = $jadwal;
            }
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Laporan Hasil Supervisi")
                ->setSubject("Laporan Hasil Supervisi")
                ->setDescription("Laporan hasil supervisi guru");
                
            // Header
            $sheet->setCellValue('A1', 'LAPORAN HASIL SUPERVISI GURU');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // School name (SMA, legacy wrapper kept in helper).
            $sheet->setCellValue('A2', get_nama_sekolah());
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Filter info
            $filterText = 'Filter: ';
            if ($tahun_ajar_id) {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $filterText .= 'Tahun Ajaran ' . $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            }
            
            if ($status) {
                $filterText .= ($tahun_ajar_id ? ', ' : '') . 'Status ' . $status;
            }
            
            if (!$tahun_ajar_id && !$status) {
                $filterText .= 'Semua Data';
            }
            
            $sheet->setCellValue('A3', $filterText);
            $sheet->mergeCells('A3:H3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Table header
            $headerRow = 5;
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Tahun Ajaran');
            $sheet->setCellValue('C' . $headerRow, 'Nama Guru');
            $sheet->setCellValue('D' . $headerRow, 'Mata Pelajaran');
            $sheet->setCellValue('E' . $headerRow, 'Kelas');
            $sheet->setCellValue('F' . $headerRow, 'Tanggal Supervisi');
            $sheet->setCellValue('G' . $headerRow, 'Nilai Akhir');
            $sheet->setCellValue('H' . $headerRow, 'Ketercapaian');
            
            // Style table header
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4e73df');
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            
            // Table data
            $row = $headerRow + 1;
            $no = 1;
            foreach ($completedJadwals as $jadwal) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $jadwal['tahun_ajar'] . ' - ' . $jadwal['semester']);
                $sheet->setCellValue('C' . $row, $jadwal['nama_guru']);
                $sheet->setCellValue('D' . $row, $jadwal['mata_pelajaran']);
                $sheet->setCellValue('E' . $row, $jadwal['nama_kelas'] ?? $jadwal['kelas']);
                $sheet->setCellValue('F' . $row, format_tanggal_indonesia($jadwal['tanggal_supervisi']));
                
                if (isset($jadwal['hasil']['nilai_akhir'])) {
                    $sheet->setCellValue('G' . $row, number_format((float)$jadwal['hasil']['nilai_akhir'], 2, '.', ''));
                    $sheet->setCellValue('H' . $row, $jadwal['hasil']['ketercapaian'] ?? '');
                } else {
                    $sheet->setCellValue('G' . $row, '-');
                    $sheet->setCellValue('H' . $row, '-');
                }
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Freeze the first row
            $sheet->freezePane('A' . ($headerRow + 1));
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-hasil-supervisi-' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            // Clear any previous output
            ob_end_clean();
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function exportPdf()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get filter parameters
            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
            $status = $this->request->getGet('status');
            
            // Build query for completed schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left');
                
            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if ($status) {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            // Get results for each schedule (consistent with hasilSupervisi function)
            $completedJadwals = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal['status'] == 'Selesai') {
                    // Get all hasil records for this schedule
                    $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();
                    
                    if (!empty($hasilRecords)) {
                        // Calculate nilai_akhir dynamically based on detail_hasil_penilaian
                        $totalSkor = 0;
                        $totalMaksimal = 0;
                        
                        foreach ($hasilRecords as $hasil) {
                            // Get detail hasil penilaian for this hasil record
                            $detailHasil = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->selectSum('skor')
                                ->get()
                                ->getRow();
                            
                            // Get count of detail penilaian to calculate max possible score
                            $detailCount = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->countAllResults();
                            
                            // Add to totals
                            $totalSkor += $detailHasil->skor ?? 0;
                            // Assuming max score per item is 4 (based on system specs)
                            $totalMaksimal += $detailCount * 4;
                        }
                        
                        // Calculate nilai_akhir: (actual score sum / max possible score) × 100
                        $nilaiAkhir = ($totalMaksimal > 0) ? ($totalSkor / $totalMaksimal) * 100 : 0;
                        
                        // Determine ketercapaian based on nilai_akhir
                        if ($nilaiAkhir >= 86) {
                            $ketercapaian = 'Baik Sekali';
                        } elseif ($nilaiAkhir >= 70) {
                            $ketercapaian = 'Baik';
                        } elseif ($nilaiAkhir >= 55) {
                            $ketercapaian = 'Cukup';
                        } else {
                            $ketercapaian = 'Kurang';
                        }
                        
                        // Create a mock hasil record with calculated nilai_akhir
                        $jadwal['hasil'] = [
                            'nilai_akhir' => $nilaiAkhir,
                            'ketercapaian' => $ketercapaian,
                            'total_skor' => $totalSkor,
                            'total_maksimal' => $totalMaksimal
                        ];
                    } else {
                        $jadwal['hasil'] = null;
                    }
                } else {
                    $jadwal['hasil'] = null;
                }
                $completedJadwals[] = $jadwal;
            }
            
            $data = [
                'jadwals' => $completedJadwals,
                'tahun_ajar_id' => $tahun_ajar_id,
                'status' => $status,
                'tahun_ajars' => $tahunAjarModel->findAll(),
                // NOTE: keep legacy 'nama_madrasah' view key; add canonical 'nama_sekolah'.
                'nama_madrasah' => get_nama_sekolah(),
                'nama_sekolah' => get_nama_sekolah()
            ];
            
            // Get tahun ajar filter info
            if ($tahun_ajar_id) {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $data['filter_tahun_ajar'] = $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            }
            
            // Clear any previous output
            ob_end_clean();
            
            // Load the view and generate HTML
            $html = view('admin/laporan/pdf_hasil_supervisi', $data);
            
            // Setup Dompdf
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            // Set proper headers for PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="laporan-hasil-supervisi-' . date('Y-m-d') . '.pdf"');
            
            // Output the PDF
            $dompdf->stream('laporan-hasil-supervisi-' . date('Y-m-d') . '.pdf', ['Attachment' => 0]);
            exit();
        } catch (\Exception $e) {
            log_message('error', 'PDF Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function batchExcel()
    {
        try {
            $selectedIds = $this->request->getPost('selected_ids');
            $tahun_ajar_id = $this->request->getPost('tahun_ajar_id');
            $status = $this->request->getPost('status');
            
            if (empty($selectedIds) || !is_array($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diexport');
            }
            
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get selected schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->whereIn('jadwal_supervisi.id', $selectedIds);
                
            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if ($status) {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            // Process results for each schedule
            $completedJadwals = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal['status'] == 'Selesai') {
                    // Get all hasil records for this schedule
                    $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();
                    
                    if (!empty($hasilRecords)) {
                        // Calculate nilai_akhir dynamically based on detail_hasil_penilaian
                        $totalSkor = 0;
                        $totalMaksimal = 0;
                        
                        foreach ($hasilRecords as $hasil) {
                            // Get detail hasil penilaian for this hasil record
                            $detailHasil = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->selectSum('skor')
                                ->get()
                                ->getRow();
                            
                            // Get count of detail penilaian to calculate max possible score
                            $detailCount = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->countAllResults();
                            
                            // Add to totals
                            $totalSkor += $detailHasil->skor ?? 0;
                            // Assuming max score per item is 4 (based on system specs)
                            $totalMaksimal += $detailCount * 4;
                        }
                        
                        // Calculate nilai_akhir: (actual score sum / max possible score) × 100
                        $nilaiAkhir = ($totalMaksimal > 0) ? ($totalSkor / $totalMaksimal) * 100 : 0;
                        
                        // Determine ketercapaian based on nilai_akhir
                        if ($nilaiAkhir >= 86) {
                            $ketercapaian = 'Baik Sekali';
                        } elseif ($nilaiAkhir >= 70) {
                            $ketercapaian = 'Baik';
                        } elseif ($nilaiAkhir >= 55) {
                            $ketercapaian = 'Cukup';
                        } else {
                            $ketercapaian = 'Kurang';
                        }
                        
                        // Create a mock hasil record with calculated nilai_akhir
                        $jadwal['hasil'] = [
                            'nilai_akhir' => $nilaiAkhir,
                            'ketercapaian' => $ketercapaian,
                            'total_skor' => $totalSkor,
                            'total_maksimal' => $totalMaksimal
                        ];
                    } else {
                        $jadwal['hasil'] = null;
                    }
                } else {
                    $jadwal['hasil'] = null;
                }
                $completedJadwals[] = $jadwal;
            }
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Laporan Hasil Supervisi")
                ->setSubject("Laporan Hasil Supervisi")
                ->setDescription("Laporan hasil supervisi guru");
                
            // Header
            $sheet->setCellValue('A1', 'LAPORAN HASIL SUPERVISI');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Filter info
            $filterText = 'Filter: ';
            if ($tahun_ajar_id) {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $filterText .= 'Tahun Ajaran ' . $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            }
            
            if ($status) {
                $filterText .= ($tahun_ajar_id ? ', ' : '') . 'Status ' . $status;
            }
            
            if (!$tahun_ajar_id && !$status) {
                $filterText .= 'Semua Data';
            }
            
            $sheet->setCellValue('A2', $filterText);
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Table header
            $headerRow = 4;
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Tahun Ajaran');
            $sheet->setCellValue('C' . $headerRow, 'Nama Guru');
            $sheet->setCellValue('D' . $headerRow, 'Mata Pelajaran');
            $sheet->setCellValue('E' . $headerRow, 'Kelas');
            $sheet->setCellValue('F' . $headerRow, 'Tanggal Supervisi');
            $sheet->setCellValue('G' . $headerRow, 'Nilai Akhir');
            $sheet->setCellValue('H' . $headerRow, 'Ketercapaian');
            
            // Style table header
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4e73df');
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            
            // Table data
            $row = $headerRow + 1;
            $no = 1;
            foreach ($completedJadwals as $jadwal) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $jadwal['tahun_ajar'] . ' - ' . $jadwal['semester']);
                $sheet->setCellValue('C' . $row, $jadwal['nama_guru']);
                $sheet->setCellValue('D' . $row, $jadwal['mata_pelajaran']);
                $sheet->setCellValue('E' . $row, $jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-');
                $sheet->setCellValue('F' . $row, format_tanggal_indonesia($jadwal['tanggal_supervisi']));
                
                if (isset($jadwal['hasil']['nilai_akhir'])) {
                    $sheet->setCellValue('G' . $row, number_format((float)$jadwal['hasil']['nilai_akhir'], 2, '.', ''));
                    $sheet->setCellValue('H' . $row, $jadwal['hasil']['ketercapaian'] ?? '');
                } else {
                    $sheet->setCellValue('G' . $row, '-');
                    $sheet->setCellValue('H' . $row, '-');
                }
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Freeze the first row
            $sheet->freezePane('A' . ($headerRow + 1));
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-hasil-supervisi-batch.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            // Clear any previous output
            ob_end_clean();
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Batch Excel Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }
    
    public function exportExcelDetail($id)
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get schedule with related data
            $schedule = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->find($id);
                
            if (!$schedule) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            
            // Get hasil records for this schedule
            $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
            
            // Get detail results grouped by jenis_penilaian_id
            $detailResults = [];
            foreach ($hasilList as $hasil) {
                $details = $db->table('detail_hasil_penilaian dhp')
                    ->select('dhp.*, ap.nama_aspek')
                    ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id')
                    ->where('dhp.hasil_supervisi_id', $hasil['id'])
                    ->get()
                    ->getResultArray();
                    
                $detailResults[$hasil['jenis_penilaian_id']] = $details;
            }
            
            // Clean output buffer to prevent issues
            if (ob_get_length()) {
                ob_clean();
            }
            
            // Set headers for Excel export
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = 'detail-hasil-supervisi-' . ($schedule['nama_guru'] ?? 'guru') . '-' . date('Y-m-d-H-i-s') . '.xlsx';
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            // Create new PHPExcel object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Detail Hasil Supervisi")
                ->setSubject("Detail Hasil Supervisi")
                ->setDescription("Detail hasil supervisi guru");
            
            // Set sheet title
            $sheet->setTitle('Detail Hasil Supervisi');
            
            // Informasi Guru section
            $sheet->setCellValue('A1', 'DETAIL HASIL SUPERVISI');
            $sheet->mergeCells('A1:D1');
            
            $sheet->setCellValue('A3', 'Nama Guru');
            $sheet->setCellValue('B3', ': ' . ($schedule['nama_guru'] ?? ''));
            
            $sheet->setCellValue('A4', 'NIP');
            $sheet->setCellValue('B4', ': ' . ($schedule['nip_guru'] ?? ''));
            
            $sheet->setCellValue('A5', 'Mata Pelajaran');
            $sheet->setCellValue('B5', ': ' . ($schedule['mata_pelajaran'] ?? ''));
            
            $sheet->setCellValue('A6', 'Tanggal Supervisi');
            $sheet->setCellValue('B6', ': ' . format_tanggal_indonesia($schedule['tanggal_supervisi']));
            
            $sheet->setCellValue('A7', 'Kelas');
            $sheet->setCellValue('B7', ': ' . ($schedule['kelas'] ?? ''));
            
            // Style for header
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style for labels
            $sheet->getStyle('A3:A7')->getFont()->setBold(true);
            
            // Assessment Results section
            $currentRow = 9;
            if (!empty($hasilList)) {
                foreach ($hasilList as $hasil) {
                    $jenisId = $hasil['jenis_penilaian_id'];
                    $jenisNama = '';
                    switch($jenisId) {
                        case 1: $jenisNama = 'Administrasi Guru'; break;
                        case 2: $jenisNama = 'Proses Pembelajaran'; break;
                        case 3: $jenisNama = 'Evaluasi Pembelajaran'; break;
                        case 4: $jenisNama = 'Pengembangan Diri'; break;
                        default: $jenisNama = 'Komponen Lain';
                    }
                    
                    // Section header
                    $sheet->setCellValue('A' . $currentRow, $jenisNama);
                    $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $currentRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD3D3D3');
                    $currentRow++;
                    
                    // Table header for this section
                    $sheet->setCellValue('A' . $currentRow, 'No');
                    $sheet->setCellValue('B' . $currentRow, 'Aspek Penilaian');
                    $sheet->setCellValue('C' . $currentRow, 'Skor');
                    $sheet->setCellValue('D' . $currentRow, 'Catatan');
                    $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE0E0E0');
                    $currentRow++;
                    
                    // Table data for this section
                    if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                        $no = 1;
                        foreach ($detailResults[$jenisId] as $detail) {
                            $sheet->setCellValue('A' . $currentRow, $no++);
                            $sheet->setCellValue('B' . $currentRow, $detail['nama_aspek'] ?? '');
                            $sheet->setCellValue('C' . $currentRow, $detail['skor'] ?? '0');
                            $sheet->setCellValue('D' . $currentRow, $detail['catatan'] ?? '');
                            $currentRow++;
                        }
                    } else {
                        $sheet->setCellValue('A' . $currentRow, '');
                        $sheet->setCellValue('B' . $currentRow, 'Tidak ada detail penilaian');
                        $sheet->mergeCells('B' . $currentRow . ':D' . $currentRow);
                        $currentRow++;
                    }
                    
                    // Add some space between sections
                    $currentRow++;
                }
            } else {
                $sheet->setCellValue('A' . $currentRow, 'Belum ada hasil penilaian');
                $currentRow++;
            }
            
            // Auto-size columns
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Create Excel writer
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Detail Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
}