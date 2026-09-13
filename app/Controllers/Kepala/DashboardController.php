<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\SupervisorModel;
use App\Models\HasilSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\UserModel;
use App\Models\TahunAjarModel;

class DashboardController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $supervisorModel;
    protected $hasilModel;
    protected $jenisModel;
    protected $userModel;
    protected $tahunAjarModel;
    protected $detailModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
        $this->supervisorModel = new SupervisorModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->jenisModel = new JenisPenilaianModel();
        $this->userModel = new UserModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->detailModel = new \App\Models\DetailHasilPenilaianModel();

        // Load performance helper
        helper('performance');
    }

    public function index()
    {
        // Get statistics
        $data['total_guru'] = $this->guruModel->countAll();
        $data['total_supervisor'] = $this->supervisorModel->where('status', 'Aktif')->countAllResults();

        // Get today's supervisions
        $data['today_schedules'] = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id')
            ->join('users', 'users.id = supervisor.user_id')
            ->where('jadwal_supervisi.tanggal_supervisi', date('Y-m-d'))
            ->findAll();

        // Get recent supervision results
        $data['recent_results'] = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as jenis_penilaian, guru.nama as nama_guru, users.username as nama_supervisor, jadwal_supervisi.tanggal_supervisi')
            ->join('jadwal_supervisi', 'jadwal_supervisi.id = hasil_supervisi.jadwal_supervisi_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id')
            ->join('users', 'users.id = supervisor.user_id')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->orderBy('hasil_supervisi.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Calculate dynamic scores for recent results to fix 0 values
        foreach ($data['recent_results'] as &$result) {
            $details = $this->detailModel->where('hasil_supervisi_id', $result['id'])->findAll();
            $totalSkor = 0;
            $maxScore = count($details) * 4;

            foreach ($details as $detail) {
                $totalSkor += $detail['skor'];
            }

            // Override nilai_akhir if it is 0 but we have details
            if ($result['nilai_akhir'] == 0 && $maxScore > 0) {
                $result['nilai_akhir'] = ($totalSkor / $maxScore) * 100;
            }
        }

        // Get upcoming schedules
        $data['upcoming_schedules'] = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id')
            ->join('users', 'users.id = supervisor.user_id')
            ->where('jadwal_supervisi.tanggal_supervisi >', date('Y-m-d'))
            ->where('jadwal_supervisi.status', 'Terjadwal')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')
            ->findAll(5); // Limit to 5 upcoming schedules

        // Get completed supervisions this month
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $data['completed_count'] = $this->jadwalModel
            ->where('status', 'Selesai')
            ->where('tanggal_supervisi >=', $startDate)
            ->where('tanggal_supervisi <=', $endDate)
            ->countAllResults();

        // Get assessment types
        $data['jenis_penilaian'] = $this->jenisModel->findAll();

        return view('kepala/dashboard', $data);
    }

    public function performance()
    {
        // Get filter tahun ajaran
        $tahunAjarId = $this->request->getGet('tahun_ajar_id');

        // Get tahun ajaran aktif
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        // Get all tahun ajaran for filter dropdown
        $tahunAjaranList = $this->tahunAjarModel->findAll();

        // Get all teachers
        $teachers = $this->guruModel->findAll();

        // Debug logging
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Teachers count: ' . count($teachers));
            log_message('debug', 'Active tahun ajar: ' . json_encode($tahunAjarAktif));
            log_message('debug', 'Tahun ajaran list count: ' . count($tahunAjaranList));
            if (!empty($teachers)) {
                log_message('debug', 'First teacher: ' . json_encode($teachers[0]));
            }
        }

        // Calculate performance data for each teacher
        $performanceData = [];

        foreach ($teachers as $teacher) {
            // Calculate average score using helper function
            $scoreData = calculate_teacher_average_score(
                $teacher['id'],
                $this->jadwalModel,
                $tahunAjarId ?? ($tahunAjarAktif ? $tahunAjarAktif['id'] : null)
            );

            // Get performance category using helper function
            $categoryData = get_teacher_performance_category($scoreData['rata_rata']);

            $performanceData[] = [
                'guru' => $teacher,
                'rata_rata' => $scoreData['rata_rata'],
                'kategori' => $categoryData['kategori'],
                'kategori_class' => $categoryData['kategori_class'],
                'jumlah_supervisi' => $scoreData['jumlah_supervisi'],
                'trend' => $scoreData['trend'],
                'subjects' => $scoreData['subjects']
            ];
        }

        // Debug logging
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Performance data count: ' . count($performanceData));
            if (!empty($performanceData)) {
                log_message('debug', 'First performance data: ' . json_encode($performanceData[0]));
            }
        }

        // Sort teachers by average score (descending), then by jumlah supervisi (descending)
        usort($performanceData, function ($a, $b) {
            // Jika rata-rata sama, urutkan berdasarkan jumlah supervisi
            if ($a['rata_rata'] == $b['rata_rata']) {
                return $b['jumlah_supervisi'] <=> $a['jumlah_supervisi'];
            }
            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        $data = [
            'performance_data' => $performanceData,
            'tahun_ajaran_list' => $tahunAjaranList ?? [],
            'tahun_ajar' => $tahunAjarAktif
        ];

        // Debug logging
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Final data passed to view: ' . json_encode($data));
        }

        return view('kepala/dashboard_performance', $data);
    }
}
