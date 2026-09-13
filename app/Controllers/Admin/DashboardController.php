<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SupervisorModel;
use App\Models\TahunAjarModel;
use App\Models\JadwalSupervisiModel;
use App\Models\KelasModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $supervisorModel;
    protected $tahunAjarModel;
    protected $jadwalSupervisiModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->supervisorModel = new SupervisorModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->jadwalSupervisiModel = new JadwalSupervisiModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        // Get system overview data
        $data['total_users'] = $this->userModel->countAll();
        $data['total_guru'] = $this->guruModel->countAll();
        $data['total_supervisor'] = $this->supervisorModel->where('status', 'Aktif')->countAllResults();
        $data['total_kelas'] = $this->kelasModel->countAll();

        // Get jadwal statistics
        $data['total_jadwal'] = $this->jadwalSupervisiModel->countAll();
        $data['jadwal_terjadwal'] = $this->jadwalSupervisiModel->where('status', 'Terjadwal')->countAllResults();
        $data['jadwal_selesai'] = $this->jadwalSupervisiModel->where('status', 'Selesai')->countAllResults();

        // Get tahun ajaran aktif
        $data['tahun_ajar_aktif'] = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        // Get recent activities (last 5 users logged in)
        $data['recent_activities'] = $this->userModel
            ->where('last_login IS NOT NULL')
            ->orderBy('last_login', 'DESC')
            ->limit(5)
            ->findAll();

        // Get user statistics
        $data['user_stats'] = [
            'admin' => $this->userModel->where('role', 'admin')->countAllResults(),
            'kepala' => $this->userModel->where('role', 'kepala')->countAllResults(),
            'supervisor' => $this->userModel->where('role', 'supervisor')->countAllResults(),
            'guru' => $this->userModel->where('role', 'guru')->countAllResults(),
        ];

        // Get tahun ajaran statistics
        $tahunAjarList = $this->tahunAjarModel->findAll();
        $tahunAjarStats = [];
        foreach ($tahunAjarList as $tahun) {
            $tahunAjarStats[$tahun['tahun_ajar'] . ' ' . $tahun['semester']] = [
                'status' => $tahun['status_aktif'],
                'kelas_count' => $this->kelasModel->where('tahun_ajar_id', $tahun['id'])->countAllResults()
            ];
        }
        $data['tahun_ajar_stats'] = $tahunAjarStats;

        // Get analytics data
        $data['completion_rate'] = $this->getCompletionRateData();
        $data['supervisor_workload'] = $this->getSupervisorWorkloadData();
        $data['monthly_activity'] = $this->getMonthlyActivityData();
        $data['today_stats'] = $this->getTodayStats();

        return view('admin/dashboard', $data);
    }

    /**
     * Get completion rate data for last 6 months
     */
    private function getCompletionRateData()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                DATE_FORMAT(tanggal_supervisi, '%Y-%m') as month,
                COUNT(CASE WHEN status='Selesai' THEN 1 END) as selesai,
                COUNT(*) as total
            FROM jadwal_supervisi
            WHERE tanggal_supervisi >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");

        $results = $query->getResultArray();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = date('M Y', strtotime($row['month'] . '-01'));
            $percentage = $row['total'] > 0 ? ($row['selesai'] / $row['total']) * 100 : 0;
            $data[] = round($percentage, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get supervisor workload distribution
     */
    private function getSupervisorWorkloadData()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                g.nama as supervisor_name,
                COUNT(js.id) as total_supervisi
            FROM supervisor s
            JOIN guru g ON s.user_id = g.user_id
            LEFT JOIN jadwal_supervisi js ON s.id = js.supervisor_id
            WHERE s.status = 'Aktif'
            GROUP BY s.id, g.nama
            ORDER BY total_supervisi DESC
            LIMIT 10
        ");

        $results = $query->getResultArray();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $row['supervisor_name'];
            $data[] = (int)$row['total_supervisi'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get monthly activity data
     */
    private function getMonthlyActivityData()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                DATE_FORMAT(last_login, '%Y-%m') as month,
                COUNT(DISTINCT id) as active_users
            FROM users
            WHERE last_login >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");

        $results = $query->getResultArray();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = date('M Y', strtotime($row['month'] . '-01'));
            $data[] = (int)$row['active_users'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats()
    {
        // Today's supervisions
        $todaySupervisions = $this->jadwalSupervisiModel
            ->where('DATE(tanggal_supervisi)', date('Y-m-d'))
            ->countAllResults();

        // Pending approvals (Terjadwal status)
        $pendingApprovals = $this->jadwalSupervisiModel
            ->where('status', 'Terjadwal')
            ->countAllResults();

        // Active users (logged in within last 15 minutes)
        $activeUsers = $this->userModel
            ->where('last_login >=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->countAllResults();

        // System health (simple check based on database connection)
        $systemHealth = 'good';
        try {
            $db = \Config\Database::connect();
            if (!$db->connID) {
                $systemHealth = 'critical';
            }
        } catch (\Exception $e) {
            $systemHealth = 'critical';
        }

        return [
            'today_supervisions' => $todaySupervisions,
            'pending_approvals' => $pendingApprovals,
            'active_users' => $activeUsers,
            'system_health' => $systemHealth
        ];
    }
}
