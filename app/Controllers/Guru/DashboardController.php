<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\GuruModel;

class DashboardController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $jenisPenilaianModel;
    protected $detailHasilModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->jenisPenilaianModel = new JenisPenilaianModel();
        $this->detailHasilModel = new DetailHasilPenilaianModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        $userId = session()->get('id'); // Get user ID from session
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];

        // Get upcoming supervisions
        $upcomingSchedules = $this->jadwalModel
            ->where('guru_id', $guruId)
            ->where('tanggal_supervisi >=', date('Y-m-d'))
            ->where('status', 'Terjadwal')
            ->orderBy('tanggal_supervisi', 'ASC')
            ->findAll(5); // Limit to 5 upcoming schedules

        // Get completed supervisions by joining with hasil_supervisi table
        $completedSchedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, hasil_supervisi.id as hasil_id, hasil_supervisi.perencanaan_skor, hasil_supervisi.pelaksanaan_skor, hasil_supervisi.penilaian_skor, hasil_supervisi.nilai_akhir')
            ->join('hasil_supervisi', 'jadwal_supervisi.id = hasil_supervisi.jadwal_supervisi_id')
            ->where('jadwal_supervisi.guru_id', $guruId)
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll(5); // Limit to 5 latest completed schedules

        // Get evaluation types
        $jenisPenilaian = $this->jenisPenilaianModel->findAll();

        // Get all completed supervisions for chart data
        $allCompletedSupervisions = $this->jadwalModel
            ->select('jadwal_supervisi.*, hasil_supervisi.perencanaan_skor, hasil_supervisi.pelaksanaan_skor, hasil_supervisi.penilaian_skor, hasil_supervisi.nilai_akhir, jenis_penilaian.nama as jenis_nama')
            ->join('hasil_supervisi', 'jadwal_supervisi.id = hasil_supervisi.jadwal_supervisi_id')
            ->join('jenis_penilaian', 'hasil_supervisi.jenis_penilaian_id = jenis_penilaian.id')
            ->where('jadwal_supervisi.guru_id', $guruId)
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->findAll();

        // Get recent hasil supervisi with detailed scores
        $recentHasil = $this->getRecentHasilWithDetails($guruId);

        // Group data by supervision type
        $supervisionTypes = [
            'Perencanaan Pembelajaran' => [],
            'Pelaksanaan Pembelajaran' => [],
            'Penilaian Pembelajaran' => [],
            'Pengembangan Diri Guru' => []
        ];

        foreach ($allCompletedSupervisions as $supervisi) {
            $type = $supervisi['jenis_nama'];
            if (isset($supervisionTypes[$type])) {
                $supervisionTypes[$type][] = $supervisi;
            }
        }

        // Prepare data for the chart
        $chartData = [];
        foreach ($supervisionTypes as $type => $data) {
            $chartData[] = [
                'label' => $type,
                'data' => array_map(function($item) {
                    return $item['perencanaan_skor'] ?? 0;
                }, $data),
                'borderColor' => $this->getBorderColor($type),
                'fill' => false
            ];
        }

        $data = [
            'upcomingSchedules' => $upcomingSchedules,
            'completedSchedules' => $completedSchedules,
            'jenisPenilaian' => $jenisPenilaian,
            'allCompletedSupervisions' => $allCompletedSupervisions,
            'recentHasil' => $recentHasil,
            'chartLabels' => array_map(function($item) {
                return $item['jenis_nama'] ?? '';
            }, $allCompletedSupervisions),
            'chartData' => $chartData,
            'guru' => $guru
        ];

        return view('guru/dashboard', $data);
    }

    private function getRecentHasilWithDetails($guruId)
    {
        // Get recent jadwal with status 'Selesai'
        $recentJadwal = $this->jadwalModel
            ->where('guru_id', $guruId)
            ->where('status', 'Selesai')
            ->orderBy('tanggal_supervisi', 'DESC')
            ->limit(3) // Get 3 most recent
            ->findAll();

        $hasilList = [];
        foreach ($recentJadwal as $jadwal) {
            // Get all hasil supervisi for this jadwal
            $hasilItems = $this->hasilModel
                ->select('hasil_supervisi.*, jadwal_supervisi.tanggal_supervisi, jadwal_supervisi.jam_ke, users.username as supervisor_name')
                ->join('jadwal_supervisi', 'jadwal_supervisi.id = hasil_supervisi.jadwal_supervisi_id')
                ->join('users', 'users.id = jadwal_supervisi.supervisor_id')
                ->where('jadwal_supervisi.id', $jadwal['id'])
                ->findAll();

            if (!empty($hasilItems)) {
                // Process each hasil to calculate nilai_akhir and ketercapaian
                $processedHasilItems = [];
                foreach ($hasilItems as $hasil) {
                    $details = $this->detailHasilModel
                        ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                        ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                        ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                        ->findAll();

                    // Calculate nilai_akhir and ketercapaian for this jenis penilaian
                    $totalSkor = 0;
                    foreach ($details as $detail) {
                        $totalSkor += $detail['skor'];
                    }

                    // Calculate percentage (maximum score per aspect is 4)
                    $maxScore = count($details) * 4;
                    $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;

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
                    $hasil['detail_results'] = $details;

                    $processedHasilItems[] = $hasil;
                }

                $jadwal['hasil_items'] = $processedHasilItems;
                $hasilList[] = $jadwal;
            }
        }

        return $hasilList;
    }

    private function getBorderColor($type)
    {
        $colors = [
            'Perencanaan Pembelajaran' => '#4e73df',
            'Pelaksanaan Pembelajaran' => '#1cc88a',
            'Penilaian Pembelajaran' => '#36b9cc',
            'Pengembangan Diri Guru' => '#f6c23e'
        ];
        
        return $colors[$type] ?? '#858796';
    }
}