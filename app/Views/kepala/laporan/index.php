<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Supervisi</h1>
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
                                <?= $total_guru ?? 0 ?>
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
                                <?= $total_supervisor ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Supervisions Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Supervisi Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= isset($completed_supervisions) ? count($completed_supervisions) : (isset($jadwalSelesai) ? count($jadwalSelesai) : 0) ?>
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

    <!-- Chart Row -->
    <?php if (isset($totalScheduled, $totalCompleted, $totalPending)): ?>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Supervisi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="supervisiChart"></canvas>
                    </div>
                    <hr>
                    Statistik pelaksanaan supervisi berdasarkan jadwal yang telah dibuat.
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi Supervisi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Guru</th>
                            <th>Supervisor</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (isset($completed_supervisions)): ?>
                            <?php foreach ($completed_supervisions as $schedule): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                <td><?= $schedule['nama_guru'] ?></td>
                                <td><?= $schedule['nama_supervisor'] ?? 'N/A' ?></td>
                                <!-- Debug: Uncomment below to check full data -->
                                <!-- <pre><?= print_r($schedule, true) ?></pre> -->
                                <td><?= $schedule['mata_pelajaran'] ?></td>
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
                        <?php elseif (isset($jadwalSelesai)): ?>
                            <?php foreach ($jadwalSelesai as $schedule): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                <td><?= $schedule['nama_guru'] ?></td>
                                <td><?= $schedule['nama_supervisor'] ?? 'N/A' ?></td>
                                <td><?= $schedule['mata_pelajaran'] ?></td>
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($totalScheduled, $totalCompleted, $totalPending)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('supervisiChart').getContext('2d');
    var supervisiChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jadwal Supervisi', 'Sudah Dilaksanakan', 'Belum Dilaksanakan'],
            datasets: [{
                label: 'Jumlah',
                data: [<?= $totalScheduled ?>, <?= $totalCompleted ?>, <?= $totalPending ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
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
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>