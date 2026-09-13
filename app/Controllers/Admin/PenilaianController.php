<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\AuditLogModel;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\TahunAjarModel;

class PenilaianController extends BaseController
{
    protected $jadwalModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $hasilModel;
    protected $detailModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->jenisModel = new JenisPenilaianModel();
        $this->aspekModel = new AspekPenilaianModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        return redirect()->to('/admin/laporan/hasil-supervisi');
    }

    public function form($jadwalId)
    {
        // Admin dapat melihat dan mengedit jadwal dengan status apapun
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor, users.nip as nip_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi ID ' . $jadwalId . ' tidak ditemukan');
        }

        // Ambil semua jenis penilaian
        $jenisPenilaian = $this->jenisModel->findAllWithMappedColumns();

        // Ambil semua aspek penilaian terurut
        $aspekPenilaian = $this->aspekModel
            ->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id')
            ->orderBy('aspek_penilaian.jenis_penilaian_id', 'ASC')
            ->orderBy('aspek_penilaian.urutan', 'ASC')
            ->findAll();

        // Kelompokkan aspek per jenis penilaian
        $aspekByJenis = [];
        foreach ($aspekPenilaian as $aspek) {
            $aspekByJenis[$aspek['jenis_penilaian_id']][] = $aspek;
        }

        // Ambil hasil & detail yang sudah tersimpan
        $existingResults = [];
        $existingDetails = [];
        foreach ($jenisPenilaian as $jenis) {
            $existingResults[$jenis['id']] = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenis['id'])
                ->first();

            if ($existingResults[$jenis['id']]) {
                $hasilId = $existingResults[$jenis['id']]['id'];
                $details = $this->detailModel->where('hasil_supervisi_id', $hasilId)->findAll();
                foreach ($details as $detail) {
                    $existingDetails[$jenis['id']][$detail['aspek_penilaian_id']] = $detail;
                }
            }
        }

        $activeTab = $this->request->getGet('tab') ?? ($jenisPenilaian[0]['id'] ?? 1);

        $fotoModel = new \App\Models\FotoBuktiModel();
        $existingPhotos = $fotoModel->where('jadwal_supervisi_id', $jadwalId)->findAll();

        $data = [
            'title'           => 'Edit Hasil Penilaian Supervisi',
            'schedule'        => $schedule,
            'jenisPenilaian'  => $jenisPenilaian,
            'aspekByJenis'    => $aspekByJenis,
            'existingResults' => $existingResults,
            'existingDetails' => $existingDetails,
            'existingPhotos'  => $existingPhotos,
            'activeTab'       => $activeTab,
        ];

        return view('admin/penilaian/form', $data);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $jadwalId = $this->request->getPost('jadwal_id');
            $jenisPenilaianId = $this->request->getPost('jenis_penilaian_id');
            $rekomendasi = $this->request->getPost('rekomendasi');

            // Validasi jadwal
            $schedule = $this->jadwalModel
                ->select('jadwal_supervisi.*, guru.nama as nama_guru')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
                ->where('jadwal_supervisi.id', $jadwalId)
                ->first();

            if (!$schedule) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Jadwal tidak ditemukan',
                    'token'   => csrf_hash()
                ]);
            }

            // Cek apakah hasil untuk jenis penilaian ini sudah ada
            $existingHasil = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->first();

            $hasilData = [
                'jadwal_supervisi_id' => $jadwalId,
                'jenis_penilaian_id' => $jenisPenilaianId,
                'rekomendasi'        => $rekomendasi
            ];

            if ($existingHasil) {
                $this->hasilModel->update($existingHasil['id'], $hasilData);
                $hasilId = $existingHasil['id'];
            } else {
                $hasilId = $this->hasilModel->insert($hasilData);
            }

            if (!$hasilId) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan hasil supervisi',
                    'token'   => csrf_hash()
                ]);
            }

            // Siapkan detail penilaian
            $aspekList = $this->aspekModel
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->findAll();

            $detailsToInsert = [];
            $totalSkor = 0;
            $jumlahAspek = 0;

            foreach ($aspekList as $aspek) {
                $skorKey = 'skor_' . $aspek['id'];
                $catatanKey = 'catatan_' . $aspek['id'];

                $skor = $this->request->getPost($skorKey);
                $catatan = $this->request->getPost($catatanKey);

                if ($skor !== null && $skor !== '') {
                    $detailsToInsert[] = [
                        'hasil_supervisi_id' => $hasilId,
                        'aspek_penilaian_id' => $aspek['id'],
                        'skor'               => (int)$skor,
                        'catatan'            => $catatan
                    ];
                    $totalSkor += (int)$skor;
                    $jumlahAspek++;
                }
            }

            // Update details
            $this->detailModel->where('hasil_supervisi_id', $hasilId)->delete();
            if (!empty($detailsToInsert)) {
                $this->detailModel->insertBatch($detailsToInsert);
            }

            // Update skor & nilai_akhir di tabel hasil_supervisi jika ada skor
            $skorMaksimal = $jumlahAspek * 4;
            $persen = ($skorMaksimal > 0) ? round(($totalSkor / $skorMaksimal) * 100, 2) : 0;

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
                'total_skor'   => $totalSkor,
                'nilai_akhir'  => $persen,
                'ketercapaian' => $ketercapaian
            ]);

            // Audit log
            $userId = session()->get('id');
            $guruNama = $schedule['nama_guru'] ?? ('ID ' . $schedule['guru_id']);
            $this->auditLogModel->logActivity(
                $userId,
                'update_penilaian',
                "Admin memperbarui penilaian komponen ID {$jenisPenilaianId} untuk guru {$guruNama} (Jadwal #{$jadwalId})"
            );

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data penilaian komponen berhasil disimpan',
                'token'   => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error saat menyimpan penilaian admin: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }

    public function quickUpdate()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $jadwalId = $this->request->getPost('jadwal_id');
            $jenisPenilaianId = $this->request->getPost('jenis_penilaian_id');
            $aspekId = $this->request->getPost('aspek_id');
            $skor = (int)$this->request->getPost('skor');
            $catatan = $this->request->getPost('catatan');

            $schedule = $this->jadwalModel
                ->select('jadwal_supervisi.*, guru.nama as nama_guru')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
                ->where('jadwal_supervisi.id', $jadwalId)
                ->first();

            if (!$schedule) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Jadwal supervisi tidak ditemukan',
                    'token'   => csrf_hash()
                ]);
            }

            // Temukan atau buat hasil_supervisi
            $hasil = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->first();

            if (!$hasil) {
                $hasilId = $this->hasilModel->insert([
                    'jadwal_supervisi_id' => $jadwalId,
                    'jenis_penilaian_id'  => $jenisPenilaianId,
                    'rekomendasi'         => ''
                ]);
            } else {
                $hasilId = $hasil['id'];
            }

            // Temukan atau update detail_hasil_penilaian
            $existingDetail = $this->detailModel
                ->where('hasil_supervisi_id', $hasilId)
                ->where('aspek_penilaian_id', $aspekId)
                ->first();

            if ($existingDetail) {
                $this->detailModel->update($existingDetail['id'], [
                    'skor'    => $skor,
                    'catatan' => $catatan
                ]);
            } else {
                $this->detailModel->insert([
                    'hasil_supervisi_id' => $hasilId,
                    'aspek_penilaian_id' => $aspekId,
                    'skor'               => $skor,
                    'catatan'            => $catatan
                ]);
            }

            // Hitung ulang total untuk komponen ini
            $db = \Config\Database::connect();
            $detailHasil = $db->table('detail_hasil_penilaian')
                ->where('hasil_supervisi_id', $hasilId)
                ->selectSum('skor')
                ->get()
                ->getRow();

            $detailCount = $db->table('detail_hasil_penilaian')
                ->where('hasil_supervisi_id', $hasilId)
                ->countAllResults();

            $totalSkorKomponen = (float)($detailHasil->skor ?? 0);
            $maxKomponen = $detailCount * 4;
            $persenKomponen = ($maxKomponen > 0) ? round(($totalSkorKomponen / $maxKomponen) * 100, 2) : 0;

            if ($persenKomponen >= 86) {
                $kategoriKomponen = 'Baik Sekali';
            } elseif ($persenKomponen >= 70) {
                $kategoriKomponen = 'Baik';
            } elseif ($persenKomponen >= 55) {
                $kategoriKomponen = 'Cukup';
            } else {
                $kategoriKomponen = 'Kurang';
            }

            $this->hasilModel->update($hasilId, [
                'total_skor'   => $totalSkorKomponen,
                'nilai_akhir'  => $persenKomponen,
                'ketercapaian' => $kategoriKomponen
            ]);

            // Audit log
            $userId = session()->get('id');
            $guruNama = $schedule['nama_guru'] ?? ('ID ' . $schedule['guru_id']);
            $this->auditLogModel->logActivity(
                $userId,
                'quick_update_aspek',
                "Admin mengubah skor aspek #{$aspekId} menjadi {$skor} untuk guru {$guruNama} (Jadwal #{$jadwalId})"
            );

            return $this->response->setJSON([
                'status'           => 'success',
                'message'          => 'Aspek penilaian berhasil diperbarui',
                'skor'             => $skor,
                'catatan'          => $catatan,
                'persen_komponen'  => $persenKomponen,
                'kategori_komponen'=> $kategoriKomponen,
                'token'            => csrf_hash()
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memperbarui aspek: ' . $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }

    public function complete($jadwalId)
    {
        try {
            $schedule = $this->jadwalModel
                ->select('jadwal_supervisi.*, guru.nama as nama_guru')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
                ->where('jadwal_supervisi.id', $jadwalId)
                ->first();

            if (!$schedule) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Jadwal tidak ditemukan',
                    'token'   => csrf_hash()
                ]);
            }

            $db = \Config\Database::connect();
            $hasilData = $this->hasilModel->where('jadwal_supervisi_id', $jadwalId)->findAll();

            $grandTotalSkor = 0;
            $grandTotalMaks = 0;

            foreach ($hasilData as $hasil) {
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

                $grandTotalSkor += $skor;
                $grandTotalMaks += $max;
            }

            $nilaiAkhir = ($grandTotalMaks > 0) ? round(($grandTotalSkor / $grandTotalMaks) * 100, 2) : 0;

            if ($nilaiAkhir >= 86) {
                $ketercapaian = 'Baik Sekali';
            } elseif ($nilaiAkhir >= 70) {
                $ketercapaian = 'Baik';
            } elseif ($nilaiAkhir >= 55) {
                $ketercapaian = 'Cukup';
            } else {
                $ketercapaian = 'Kurang';
            }

            // Pertahankan/pastikan status tetap 'Selesai'
            $this->jadwalModel->update($jadwalId, [
                'status'       => 'Selesai',
                'nilai_akhir'  => $nilaiAkhir,
                'ketercapaian' => $ketercapaian
            ]);

            // Audit log
            $userId = session()->get('id');
            $guruNama = $schedule['nama_guru'] ?? ('ID ' . $schedule['guru_id']);
            $this->auditLogModel->logActivity(
                $userId,
                'complete_penilaian',
                "Admin menyelesaikan edit hasil penilaian supervisi guru {$guruNama} (Jadwal #{$jadwalId}) dengan nilai akhir {$nilaiAkhir}% ({$ketercapaian})"
            );

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Perubahan hasil supervisi berhasil disimpan!',
                'redirect' => base_url('admin/laporan/hasil-supervisi/detail/' . $jadwalId),
                'token'    => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error complete admin penilaian: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }

    public function detail($jadwalId)
    {
        return redirect()->to('/admin/laporan/hasil-supervisi/detail/' . $jadwalId);
    }

    public function edit($jadwalId)
    {
        return $this->form($jadwalId);
    }

    public function cetakPdf($jadwalId)
    {
        return redirect()->to('/admin/laporan/hasil-supervisi/cetak-detail/' . $jadwalId);
    }
}
