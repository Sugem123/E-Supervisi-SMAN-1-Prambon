<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Supervisor</h1>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <!-- Today's Schedule Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jadwal Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($todaySchedules) ?>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Penilaian Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $completedCount ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Types Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jenis Penilaian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($jenisPenilaian) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Akses Cepat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Jadwal
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-link fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Schedules -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Supervisi Hari Ini</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($todaySchedules)): ?>
                        <p class="text-center text-muted">Tidak ada jadwal supervisi hari ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Jam</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todaySchedules as $schedule): ?>
                                        <tr>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['mata_pelajaran'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                            <td><?= $schedule['jam_ke'] ?></td>
                                            <td>
                                                <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>" 
                                                   class="btn btn-sm btn-primary">Mulai Penilaian</a>
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

        <!-- Upcoming Schedules -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Mendatang</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingSchedules)): ?>
                        <p class="text-center text-muted">Tidak ada jadwal supervisi mendatang.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingSchedules as $schedule): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['mata_pelajaran'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                            <td>
                                                <a href="<?= base_url('supervisor/jadwal/detail/' . $schedule['id']) ?>" 
                                                   class="btn btn-sm btn-info">Lihat Detail</a>
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
    </div>

    <!-- Assessment Types Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jenis Penilaian Supervisi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($jenisPenilaian as $jenis): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card border-left-secondary h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                    <?= $jenis['nama'] ?>
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    Skor Maksimal: <?= $jenis['skor_maksimal'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>