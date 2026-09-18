<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<link href="<?= base_url('assets/css/supervisi-premium.css') ?>" rel="stylesheet">
<div class="sup-dashboard container-fluid">
    <?php $tahunAktifLabel = isset($tahun_ajar_aktif) && $tahun_ajar_aktif ? esc($tahun_ajar_aktif['tahun_ajar'] . ' · ' . $tahun_ajar_aktif['semester']) : 'Belum ada tahun Aktif'; ?>
    <div class="sup-page-head">
        <div><h1>Dashboard Supervisi Tahunan</h1><p>Lembaran aktif <strong><?= $tahunAktifLabel ?></strong> · 1 guru 1 supervisi · guru otomatis lanjut · kelompok via carry-over.</p></div>
        <span class="sup-year-pill"><span class="live" aria-hidden="true"></span><?= $tahunAktifLabel ?></span>
    </div>

    <!-- Custom CSS for Premium Look -->
    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.2);
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero h2 {
            color: white !important;
        }

        .dashboard-hero p {
            color: white !important;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: 10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .bg-soft-primary {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }

        .bg-soft-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .bg-soft-info {
            background-color: rgba(54, 185, 204, 0.1);
            color: #36b9cc;
        }

        .bg-soft-warning {
            background-color: rgba(246, 194, 62, 0.1);
            color: #f6c23e;
        }

        .bg-soft-danger {
            background-color: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }

        .card-header-premium {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 700;
            color: #4e73df;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .premium-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            color: white !important;
        }

        .premium-card h2 {
            color: white !important;
        }

        .action-btn {
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            transition: all 0.2s;
            border: 1px solid #eee;
            background: white;
            color: #5a5c69;
            text-decoration: none !important;
        }

        .action-btn:hover {
            background: #f8f9fc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            color: #4e73df;
        }

        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .table-premium thead th {
            border-top: none;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            color: #858796;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table-premium td {
            vertical-align: middle;
            border-top: 1px solid #f8f9fc;
            padding: 1rem 0.75rem;
        }

        .progress-thin {
            height: 6px;
            border-radius: 3px;
        }
    </style>

    <!-- Hero Section -->
    <div class="dashboard-hero text-light p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="font-weight-bold mb-2 text-light">Selamat Datang, Admin!</h2>
                <p class="mb-0 opacity-80">Ringkasan lembaran aktif <?= $tahunAktifLabel ?>: jadwal tahun ini, carry-over kelompok, dan progres supervisi.</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <div class="h4 font-weight-bold mb-0" style="color: #f6c23e !important;" id="waktuWIB"><?= format_waktu_indonesia(date('Y-m-d H:i:s')) ?></div>
                <div class="small opacity-80"><?= format_tanggal_indonesia(date('Y-m-d')) ?></div>
            </div>
        </div>
    </div>

    <!-- Real-time Stats Row -->
    <div class="row mb-4">
        <!-- Today's Supervisions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Supervisi Hari Ini</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800" id="todaySupervisions"><?= $today_stats['today_supervisions'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted small">Jadwal hari ini</span>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Pending Approval</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800" id="pendingApprovals"><?= $today_stats['pending_approvals'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted small">Menunggu konfirmasi</span>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Pengguna Aktif</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800" id="activeUsers"><?= $today_stats['active_users'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-info">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted small">Online sekarang</span>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Status Sistem</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $health = $today_stats['system_health'] ?? 'good';
                            $healthLabel = [
                                'good' => 'Normal',
                                'warning' => 'Peringatan',
                                'critical' => 'Kritis'
                            ];
                            echo $healthLabel[$health];
                            ?>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper <?= $health === 'good' ? 'bg-soft-success' : ($health === 'warning' ? 'bg-soft-warning' : 'bg-soft-danger') ?>">
                        <i class="fas <?= $health === 'good' ? 'fa-check-circle' : ($health === 'warning' ? 'fa-exclamation-circle' : 'fa-times-circle') ?>"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted small">Database ready</span>
                </div>
            </div>
        </div>
    </div>


    <!-- Main Stats Grid -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Pengguna</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_users ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success small font-weight-bold"><i class="fas fa-arrow-up"></i> Active</span>
                    <span class="text-muted small ml-1">users in system</span>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Guru</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_guru ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-success">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Terdaftar dalam sistem</span>
                </div>
            </div>
        </div>

        <!-- Supervisor -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Supervisor Aktif</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_supervisor ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-info">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Siap melakukan supervisi</span>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Total Kelas</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_kelas ?? 0 ?></div>
                    </div>
                    <div class="stat-icon-wrapper bg-soft-warning">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Ruang belajar aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="row mb-4">
        <!-- Completion Rate Line Chart -->
        <div class="col-lg-6 mb-4">
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-chart-line mr-2"></i> Completion Rate (6 Bulan)</span>
                </div>
                <div class="card-body">
                    <canvas id="completionRateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Supervisor Workload Bar Chart -->
        <div class="col-lg-6 mb-4">
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-chart-bar mr-2"></i> Beban Kerja Supervisor</span>
                </div>
                <div class="card-body">
                    <canvas id="supervisorWorkloadChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Activity Chart -->
    <div class="row mb-4">
        <div class="col-lg-12 mb-4">
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-chart-area mr-2"></i> Aktivitas Pengguna Bulanan</span>
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats & Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-8 col-lg-7">

            <!-- Jadwal Overview -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-card p-3 d-flex align-items-center">
                        <div class="stat-icon-wrapper bg-soft-success mb-0 mr-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <div class="small text-muted font-weight-bold">Total Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold"><?= $total_jadwal ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-card p-3 d-flex align-items-center">
                        <div class="stat-icon-wrapper bg-soft-warning mb-0 mr-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="small text-muted font-weight-bold">Terjadwal</div>
                            <div class="h5 mb-0 font-weight-bold"><?= $jadwal_terjadwal ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card p-3 d-flex align-items-center">
                        <div class="stat-icon-wrapper bg-soft-primary mb-0 mr-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="small text-muted font-weight-bold">Selesai</div>
                            <div class="h5 mb-0 font-weight-bold"><?= $jadwal_selesai ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics Chart/Bars -->
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-chart-pie mr-2"></i> Statistik Pengguna</span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="myPieChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-primary"></i> Admin
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Guru
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-info"></i> Supervisor
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-warning"></i> Kepala
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-4">Distribusi Role</h5>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                    <span>Guru</span>
                                    <span><?= $user_stats['guru'] ?? 0 ?></span>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($total_users > 0) ? ($user_stats['guru'] / $total_users * 100) : 0 ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                    <span>Supervisor</span>
                                    <span><?= $user_stats['supervisor'] ?? 0 ?></span>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= ($total_users > 0) ? ($user_stats['supervisor'] / $total_users * 100) : 0 ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                    <span>Admin</span>
                                    <span><?= $user_stats['admin'] ?? 0 ?></span>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($total_users > 0) ? ($user_stats['admin'] / $total_users * 100) : 0 ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                    <span>Kepala Sekolah</span>
                                    <span><?= $user_stats['kepala'] ?? 0 ?></span>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= ($total_users > 0) ? ($user_stats['kepala'] / $total_users * 100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-history mr-2"></i> Aktivitas Login Terbaru</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="table-responsive">
                            <table class="table table-premium mb-0">
                                <thead>
                                    <tr>
                                        <th class="pl-4">Pengguna</th>
                                        <th>Role</th>
                                        <th>Waktu Login</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-primary rounded-circle p-2 mr-3">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800"><?= esc($activity['username']) ?></div>
                                                        <div class="small text-muted"><?= esc($activity['email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border"><?= ucfirst($activity['role']) ?></span>
                                            </td>
                                            <td>
                                                <div class="small text-gray-600">
                                                    <i class="far fa-clock mr-1"></i>
                                                    <?= date('d M Y, H:i', strtotime($activity['last_login'])) ?>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-success">Online</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-info-circle mb-2"></i><br>
                            Tidak ada aktivitas terbaru
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4 col-lg-5">

            <!-- Active Academic Year -->
            <div class="premium-card card bg-gradient-primary text-white" style="background: linear-gradient(45deg, #00b11dff, #054902ff);">
                <div class="card-body p-4 text-center">
                    <h6 class="text-uppercase mb-3 opacity-80 text-white">Tahun Ajaran Aktif</h6>
                    <?php if ($tahun_ajar_aktif): ?>
                        <h2 class="font-weight-bold mb-1 text-white"><?= $tahun_ajar_aktif['tahun_ajar'] ?></h2>
                        <p class="mb-3 opacity-80 text-white"><?= $tahun_ajar_aktif['semester'] ?></p>
                        <span class="badge badge-light text-primary px-3 py-2">Status: Aktif</span>
                    <?php else: ?>
                        <p class="mb-0">Tidak ada tahun ajaran aktif</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-bolt mr-2"></i> Aksi Cepat</span>
                </div>
                <div class="card-body p-3">
                    <div class="row no-gutters">
                        <div class="col-6 p-2">
                            <a href="<?= base_url('/admin/pengguna/create') ?>" class="action-btn">
                                <i class="fas fa-user-plus text-primary"></i>
                                <span class="small font-weight-bold">Tambah User</span>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="<?= base_url('/admin/pengaturan?tab=tahun-ajar') ?>" class="action-btn">
                                <i class="fas fa-calendar-alt text-success"></i>
                                <span class="small font-weight-bold">Tahun Ajaran</span>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="<?= base_url('/admin/kelompok') ?>" class="action-btn">
                                <i class="fas fa-copy text-info"></i>
                                <span class="small font-weight-bold">Carry-over Kelompok</span>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="<?= base_url('/admin/jadwal/create') ?>" class="action-btn">
                                <i class="fas fa-calendar-plus text-info"></i>
                                <span class="small font-weight-bold">Jadwal Baru</span>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="<?= base_url('/admin/laporan') ?>" class="action-btn">
                                <i class="fas fa-chart-bar text-warning"></i>
                                <span class="small font-weight-bold">Laporan</span>
                            </a>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="<?= base_url('/admin/laporan/hasil-supervisi') ?>" class="btn btn-primary btn-block py-2 shadow-sm">
                            <i class="fas fa-clipboard-check mr-2"></i> Laporan Hasil Supervisi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Academic Year Stats -->
            <div class="premium-card card">
                <div class="card-header-premium">
                    <span><i class="fas fa-history mr-2"></i> Riwayat Tahun Ajaran</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($tahun_ajar_stats)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($tahun_ajar_stats as $tahun => $stats): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="font-weight-bold text-gray-800"><?= $tahun ?></div>
                                        <div class="small text-muted"><?= $stats['kelas_count'] ?> Kelas Terdaftar</div>
                                    </div>
                                    <?php if ($stats['status'] === 'Aktif'): ?>
                                        <span class="badge badge-success badge-pill">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary badge-pill">Nonaktif</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">Tidak ada data</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
    // Fungsi untuk memperbarui waktu WIB setiap detik
    function updateWaktuWIB() {
        const options = {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const now = new Date();
        const waktuWIB = now.toLocaleTimeString('id-ID', options);

        const waktuWIBElement = document.getElementById('waktuWIB');
        if (waktuWIBElement) {
            waktuWIBElement.textContent = waktuWIB + ' WIB';
        }
    }

    setInterval(updateWaktuWIB, 1000);
    updateWaktuWIB();

    // Pie Chart Logic
    var ctx = document.getElementById("myPieChart");
    if (ctx) {
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Admin", "Guru", "Supervisor", "Kepala"],
                datasets: [{
                    data: [
                        <?= $user_stats['admin'] ?? 0 ?>,
                        <?= $user_stats['guru'] ?? 0 ?>,
                        <?= $user_stats['supervisor'] ?? 0 ?>,
                        <?= $user_stats['kepala'] ?? 0 ?>
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    }

    // Completion Rate Line Chart
    var completionCtx = document.getElementById("completionRateChart");
    if (completionCtx) {
        new Chart(completionCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($completion_rate['labels']) ?>,
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: <?= json_encode($completion_rate['data']) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderColor: '#1cc88a',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
        completionCtx.style.height = '300px';
    }

    // Supervisor Workload Bar Chart
    var workloadCtx = document.getElementById("supervisorWorkloadChart");
    if (workloadCtx) {
        new Chart(workloadCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($supervisor_workload['labels']) ?>,
                datasets: [{
                    label: 'Total Supervisi',
                    data: <?= json_encode($supervisor_workload['data']) ?>,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        workloadCtx.style.height = '300px';
    }

    // Monthly Activity Chart
    var activityCtx = document.getElementById("monthlyActivityChart");
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthly_activity['labels']) ?>,
                datasets: [{
                    label: 'Active Users',
                    data: <?= json_encode($monthly_activity['data']) ?>,
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    borderColor: '#36b9cc',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#36b9cc',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        activityCtx.style.height = '250px';
    }
</script>
<?= $this->endSection(); ?>