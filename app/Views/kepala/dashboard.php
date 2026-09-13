<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kepala Sekolah</h1>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <!-- Total Guru Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $total_guru ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supervisor Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Supervisor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $total_supervisor ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Supervisions Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Supervisi Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($today_schedules) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed This Month Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Penilaian Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $completed_count ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Supervisions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Supervisi Hari Ini</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($today_schedules)): ?>
                        <p class="text-center text-muted">Tidak ada supervisi hari ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Kelas</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($today_schedules as $schedule): ?>
                                        <tr>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['nama_supervisor'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                            <td><?= $schedule['jam_ke'] ?></td>
                                            <td>
                                                <?php if ($schedule['status'] == 'Terjadwal'): ?>
                                                    <span class="badge badge-info"><?= $schedule['status'] ?></span>
                                                <?php elseif ($schedule['status'] == 'Selesai'): ?>
                                                    <span class="badge badge-success"><?= $schedule['status'] ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?= $schedule['status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Supervisions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Mendatang</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($upcoming_schedules)): ?>
                        <p class="text-center text-muted">Tidak ada supervisi mendatang.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_schedules as $schedule): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['nama_supervisor'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Results -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Supervisi Terbaru</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_results)): ?>
                        <p class="text-center text-muted">Belum ada hasil supervisi.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Jenis</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_results as $result): ?>
                                        <tr>
                                            <td><?= $result['nama_guru'] ?></td>
                                            <td><?= $result['nama_supervisor'] ?></td>
                                            <td><?= $result['jenis_penilaian'] ?></td>
                                            <td><?= number_format($result['nilai_akhir'], 2) ?></td>
                                            <td><?= format_tanggal_indonesia($result['tanggal_supervisi']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>