<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\FotoBuktiModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenilaianController extends BaseController
{
    protected $jadwalModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $hasilModel;
    protected $detailModel;
    protected $fotoModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->jenisModel = new JenisPenilaianModel();
        $this->aspekModel = new AspekPenilaianModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->fotoModel = new FotoBuktiModel();
    }

    public function form($jadwalId)
    {
        // Get schedule details - allow both 'Terjadwal' and 'Selesai' statuses for viewing
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, COALESCE(guru_spv.nama, users.username) as nama_supervisor, COALESCE(guru_spv.nip, users.nip) as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('guru as guru_spv', 'guru_spv.user_id = users.id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->whereIn('jadwal_supervisi.status', ['Terjadwal', 'Selesai']) // Allow both statuses
            ->first();

        if (empty($schedule['nama_supervisor'])) {
            $schedule['nama_supervisor'] = get_pengaturan('nama_kepala', 'Kepala Sekolah');
        }

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan atau tidak dapat dinilai');
        }

        // Ambil jenis penilaian yang ditugaskan khusus untuk kelompok supervisi ini (jika ada)
        $kelompokModel = new \App\Models\KelompokSupervisiModel();
        $kelompok = $kelompokModel->resolveKelompokForSchedule($schedule);
        $assignedJenis = $kelompok ? $kelompokModel->getAssignedJenisPenilaian((int)$kelompok['id']) : [];

        if (!empty($assignedJenis)) {
            $assignedIds = array_map('intval', array_column($assignedJenis, 'id'));
            $allActive = $this->jenisModel->findAllActiveWithMappedColumns();
            $jenisPenilaian = array_values(array_filter($allActive, static fn ($j) => in_array((int)$j['id'], $assignedIds, true)));
        } else {
            $jenisPenilaian = $this->jenisModel->findAllActiveWithMappedColumns();
        }

        // Hanya aspek Aktif milik jenis Aktif yang ditugaskan
        $allowedJenisIds = array_map('intval', array_column($jenisPenilaian, 'id'));
        $aspekPenilaian = $this->aspekModel->findAllActiveWithJenis();

        // Group aspects by jenis penilaian
        $aspekByJenis = [];
        foreach ($aspekPenilaian as $aspek) {
            if (in_array((int)$aspek['jenis_penilaian_id'], $allowedJenisIds, true)) {
                $aspekByJenis[$aspek['jenis_penilaian_id']][] = $aspek;
            }
        }

        // Get existing results if any
        $existingResults = [];
        $existingDetails = [];
        foreach ($jenisPenilaian as $jenis) {
            $existingResults[$jenis['id']] = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenis['id'])
                ->first();
            
            // If there are existing results, get the details
            if ($existingResults[$jenis['id']]) {
                $hasilId = $existingResults[$jenis['id']]['id'];
                $details = $this->detailModel->where('hasil_supervisi_id', $hasilId)->findAll();
                foreach ($details as $detail) {
                    $existingDetails[$jenis['id']][$detail['aspek_penilaian_id']] = $detail;
                }
            }
        }

        // Ambil foto bukti supervisi yang sudah diupload
        $existingPhotos = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'jenisPenilaian' => $jenisPenilaian,
            'aspekByJenis' => $aspekByJenis,
            'existingResults' => $existingResults,
            'existingDetails' => $existingDetails,
            'existingPhotos' => $existingPhotos
        ];

        return view('kepala/penilaian/form', $data);
    }

    public function save()
    {
        // Cek jika request berasal dari AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $jadwalId = $this->request->getPost('jadwal_id');
            $jenisPenilaianId = $this->request->getPost('jenis_penilaian_id');
            $rekomendasi = $this->request->getPost('rekomendasi');

            log_message('info', 'Saving penilaian for jadwal_id: ' . $jadwalId . ', jenis_penilaian_id: ' . $jenisPenilaianId);

            // Validasi jadwal - allow both 'Terjadwal' and 'Selesai' statuses for editing
            $schedule = $this->jadwalModel
                ->where('id', $jadwalId)
                ->whereIn('status', ['Terjadwal', 'Selesai']) // Changed from where('status', 'Terjadwal') to match form() method
                ->first();

            if (!$schedule) {
                log_message('error', 'Jadwal not found or invalid status for id: ' . $jadwalId);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan atau tidak dapat dinilai'
                ]);
            }

            // Tolak penilaian baru untuk jenis yang dinonaktifkan
            if (!$this->jenisModel->isActive((int) $jenisPenilaianId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Jenis penilaian ini sedang Nonaktif dan tidak dapat dinilai.'
                ]);
            }

            // Cek apakah hasil untuk jenis penilaian ini sudah ada
            $existingHasil = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->first();

            // Simpan atau update hasil supervisi
            $hasilData = [
                'jadwal_supervisi_id' => $jadwalId,
                'jenis_penilaian_id' => $jenisPenilaianId,
                'rekomendasi' => $rekomendasi
            ];

            if ($existingHasil) {
                $this->hasilModel->update($existingHasil['id'], $hasilData);
                $hasilId = $existingHasil['id'];
            } else {
                $hasilId = $this->hasilModel->insert($hasilData);
            }

            // Jika gagal menyimpan hasil supervisi
            if (!$hasilId) {
                log_message('error', 'Failed to save hasil supervisi');
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan hasil supervisi'
                ]);
            }

            // Siapkan array untuk menyimpan detail penilaian
            $detailsToInsert = [];

            // Siapkan detail hasil penilaian (hanya aspek Aktif; BC bila kolom belum ada)
            $aspekList = $this->filterActiveAspek($jenisPenilaianId);

            foreach ($aspekList as $aspek) {
                $skorKey = 'skor_' . $aspek['id'];
                $catatanKey = 'catatan_' . $aspek['id'];

                $skor = $this->request->getPost($skorKey);
                $catatan = $this->request->getPost($catatanKey);

                // Hanya tambahkan ke array jika skor tidak kosong
                if ($skor !== null && $skor !== '') {
                    $detailsToInsert[] = [
                        'hasil_supervisi_id' => $hasilId,
                        'aspek_penilaian_id' => $aspek['id'],
                        'skor' => $skor,
                        'catatan' => $catatan
                    ];
                }
            }

            // Hapus detail hasil yang lama hanya jika ada detail baru yang akan disimpan
            if (!empty($detailsToInsert)) {
                $this->detailModel->where('hasil_supervisi_id', $hasilId)->delete();
                
                // Simpan semua detail penilaian sekaligus
                $this->detailModel->insertBatch($detailsToInsert);

                // Update skor & nilai_akhir di tabel hasil_supervisi
                $totalSkorKomponen = 0;
                $jumlahAspek = count($detailsToInsert);
                foreach ($detailsToInsert as $d) {
                    $totalSkorKomponen += (int)$d['skor'];
                }
                $skorMaksimal = $jumlahAspek * 4;
                $persen = ($skorMaksimal > 0) ? round(($totalSkorKomponen / $skorMaksimal) * 100, 2) : 0;

                if ($persen >= 86) {
                    $ketercapaian = 'Baik Sekali';
                } elseif ($persen >= 70) {
                    $ketercapaian = 'Baik';
                } elseif ($persen >= 55) {
                    $ketercapaian = 'Cukup';
                } else {
                    $ketercapaian = 'Kurang';
                }

                $this->hasilModel->update($hasilId, [
                    'total_skor'   => $totalSkorKomponen,
                    'nilai_akhir'  => $persen,
                    'ketercapaian' => $ketercapaian
                ]);
            } else {
                // Jika tidak ada detail yang disimpan, hapus detail lama saja
                $this->detailModel->where('hasil_supervisi_id', $hasilId)->delete();
                $this->hasilModel->update($hasilId, [
                    'total_skor'   => 0,
                    'nilai_akhir'  => 0,
                    'ketercapaian' => 'Kurang'
                ]);
            }

            log_message('info', 'Successfully saved penilaian');

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data penilaian berhasil disimpan',
                'csrf_token' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error saat menyimpan penilaian: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    private function filterActiveAspek($jenisPenilaianId): array
    {
        try {
            $builder = $this->aspekModel->where('jenis_penilaian_id', $jenisPenilaianId);
            $fields = \Config\Database::connect()->getFieldNames('aspek_penilaian');
            if (in_array('status', $fields, true)) {
                $builder->where('status', 'Aktif');
            }
            return $builder->findAll();
        } catch (\Throwable $e) {
            return $this->aspekModel->where('jenis_penilaian_id', $jenisPenilaianId)->findAll();
        }
    }

    public function view($jadwalId)
    {
        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        // Menambahkan nama kepala sekolah sebagai pengganti nama supervisor
        $schedule['nama_supervisor'] = get_pengaturan('nama_kepala', 'Kepala Sekolah');

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil penilaian tidak ditemukan');
        }

        // Get all assessment results (filter by assigned jenis jika ada)
        $kelompokModel = new \App\Models\KelompokSupervisiModel();
        $kelompok = $kelompokModel->resolveKelompokForSchedule($schedule);
        $assignedIds = $kelompok ? $kelompokModel->getAssignedJenisIds((int)$kelompok['id']) : [];

        $hasilQuery = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $jadwalId);

        if (!empty($assignedIds)) {
            $hasilQuery->whereIn('hasil_supervisi.jenis_penilaian_id', $assignedIds);
        }

        $hasilPenilaian = $hasilQuery->findAll();

        // Get detail results and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilPenilaian = [];
        
        foreach ($hasilPenilaian as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
            
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
            
            // Calculate nilai_akhir and ketercapaian for this jenis penilaian
            $totalSkor = 0;
            foreach ($details as $detail) {
                $totalSkor += $detail['skor'];
            }
            
            // Calculate percentage based on actual number of aspects * 4
            $detailCount = count($details);
            $maxScore = $detailCount * 4;
            $nilaiAkhir = $maxScore > 0 ? round(($totalSkor / $maxScore) * 100, 2) : 0;
            
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
            
            // Update the hasil data with calculated values
            $hasil['nilai_akhir'] = $nilaiAkhir;
            $hasil['ketercapaian'] = $ketercapaian;
            $hasil['total_skor'] = $totalSkor;
            
            $processedHasilPenilaian[] = $hasil;
        }

        // Get uploaded photos
        $fotoBuktiModel = new \App\Models\FotoBuktiModel();
        $uploadedPhotos = $fotoBuktiModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'hasilPenilaian' => $processedHasilPenilaian,
            'detailResults' => $detailResults,
            'uploadedPhotos' => $uploadedPhotos
        ];

        return view('kepala/penilaian/view', $data);
    }
    
    public function complete($jadwalId)
    {
        // Validasi jadwal - mengizinkan status 'Terjadwal' dan 'Selesai' (untuk kasus edit)
        $schedule = $this->jadwalModel
            ->where('id', $jadwalId)
            ->whereIn('status', ['Terjadwal', 'Selesai']) // Memperbaiki validasi untuk mengizinkan edit
            ->first();
            
        if (!$schedule) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Jadwal tidak ditemukan atau tidak dapat diselesaikan'
            ]);
        }
        
        try {
            // Hitung total skor dan nilai akhir untuk seluruh supervisi (disesuaikan dengan komponen kelompok)
            $kelompokModel = new \App\Models\KelompokSupervisiModel();
            $kelompok = $kelompokModel->resolveKelompokForSchedule($schedule);
            $assignedIds = $kelompok ? $kelompokModel->getAssignedJenisIds((int)$kelompok['id']) : [];

            $hasilQuery = $this->hasilModel
                ->select('hasil_supervisi.*, jenis_penilaian.skor_maksimal')
                ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
                ->where('jadwal_supervisi_id', $jadwalId);

            if (!empty($assignedIds)) {
                $hasilQuery->whereIn('hasil_supervisi.jenis_penilaian_id', $assignedIds);
            }

            $hasilData = $hasilQuery->findAll();
                
            $totalSkor = 0;
            $totalMaksimal = 0;
            
            foreach ($hasilData as $hasil) {
                $details = $this->detailModel
                    ->where('hasil_supervisi_id', $hasil['id'])
                    ->findAll();
                    
                $skorJenis = 0;
                foreach ($details as $detail) {
                    $skorJenis += $detail['skor'];
                }
                
                // Hitung skor maksimal berdasarkan jumlah aspek * 4 (skor maksimal per aspek)
                $skorMaksimal = count($details) * 4;
                
                $totalSkor += $skorJenis;
                $totalMaksimal += $skorMaksimal;
            }
            
            // Hitung nilai akhir berdasarkan rumus proporsional
            // Rumus: (total_skor / total_maksimal) * 100
            $nilaiAkhir = $totalMaksimal > 0 ? ($totalSkor / $totalMaksimal) * 100 : 0;
            
            // Tentukan ketercapaian berdasarkan nilai akhir
            $ketercapaian = '';
            if ($nilaiAkhir >= 86) {
                $ketercapaian = 'Baik Sekali';
            } elseif ($nilaiAkhir >= 70) {
                $ketercapaian = 'Baik';
            } elseif ($nilaiAkhir >= 55) {
                $ketercapaian = 'Cukup';
            } else {
                $ketercapaian = 'Kurang';
            }
            
            // Update status jadwal menjadi 'Selesai'
            $this->jadwalModel->update($jadwalId, [
                'status' => 'Selesai',
                'nilai_akhir' => $nilaiAkhir,
                'ketercapaian' => $ketercapaian
            ]);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supervisi berhasil diselesaikan',
                'redirect' => base_url('kepala/hasil')
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error saat menyelesaikan supervisi: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyelesaikan supervisi: ' . $e->getMessage()
            ]);
        }
    }

    public function print($jadwalId)
    {
        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        // Menambahkan nama kepala sekolah sebagai pengganti nama supervisor
        $schedule['nama_supervisor'] = get_pengaturan('nama_kepala', 'Kepala Sekolah');

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil penilaian tidak ditemukan');
        }

        // Get all assessment results
        $hasilPenilaian = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $jadwalId)
            ->findAll();

        // Get detail results and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilPenilaian = [];
        
        foreach ($hasilPenilaian as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
            
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
            
            // Calculate nilai_akhir and ketercapaian for this jenis penilaian
            $totalSkor = 0;
            foreach ($details as $detail) {
                $totalSkor += $detail['skor'];
            }
            
            // Calculate percentage based on actual number of aspects * 4
            $detailCount = count($details);
            $maxScore = $detailCount * 4;
            $nilaiAkhir = $maxScore > 0 ? round(($totalSkor / $maxScore) * 100, 2) : 0;
            
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
            
            // Update the hasil data with calculated values
            $hasil['nilai_akhir'] = $nilaiAkhir;
            $hasil['ketercapaian'] = $ketercapaian;
            $hasil['total_skor'] = $totalSkor;
            
            $processedHasilPenilaian[] = $hasil;
        }

        $data = [
            'schedule' => $schedule,
            'hasilPenilaian' => $processedHasilPenilaian,
            'detailResults' => $detailResults
        ];

        return view('kepala/penilaian/print', $data);
    }
}