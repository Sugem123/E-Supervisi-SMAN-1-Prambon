<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Guru</h1>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <!-- Upcoming Supervisions Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Supervisi Mendatang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($upcomingSchedules) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Supervisions Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Supervisi Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($completedSchedules) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Average Score Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Nilai Rata-rata</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                // Calculate average from recent hasil with calculated nilai_akhir
                                $totalNilai = 0;
                                $countNilai = 0;
                                
                                foreach($recentHasil as $jadwal) {
                                    if (!empty($jadwal['hasil_items'])) {
                                        foreach ($jadwal['hasil_items'] as $hasil) {
                                            if (isset($hasil['nilai_akhir']) && !is_null($hasil['nilai_akhir'])) {
                                                $totalNilai += $hasil['nilai_akhir'];
                                                $countNilai++;
                                            }
                                        }
                                    }
                                }
                                
                                echo $countNilai > 0 ? number_format($totalNilai / $countNilai, 2) : '0.00';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-yellow-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Information Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Supervisi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Jadwal Supervisi Mendatang</h5>
                            <?php if (empty($upcomingSchedules)): ?>
                                <p class="text-muted">Tidak ada jadwal supervisi mendatang.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Kelas</th>
                                                <th>Jam</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcomingSchedules as $schedule): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                                    <td><?= $schedule['mata_pelajaran'] ?></td>
                                                    <td><?= $schedule['kelas'] ?></td>
                                                    <td><?= $schedule['jam_ke'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Hasil Supervisi Terbaru</h5>
                            <?php if (empty($recentHasil)): ?>
                                <p class="text-muted">Belum ada hasil supervisi yang tersedia.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Supervisi</th>
                                                <th>Administrasi</th>
                                                <th>Proses</th>
                                                <th>Evaluasi</th>
                                                <th>Pengembangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentHasil as $jadwal): ?>
                                                <tr>
                                                    <td><?= format_tanggal_indonesia($jadwal['tanggal_supervisi']) ?></td>
                                                    <?php 
                                                    // Initialize nilai for each category
                                                    $nilai = [
                                                        1 => '-', // Administrasi Guru
                                                        2 => '-', // Proses Pembelajaran
                                                        3 => '-', // Evaluasi Pembelajaran
                                                        4 => '-'  // Pengembangan Diri
                                                    ];
                                                    
                                                    // Fill in actual nilai if available
                                                    if (!empty($jadwal['hasil_items'])) {
                                                        foreach ($jadwal['hasil_items'] as $hasil) {
                                                            if (isset($hasil['nilai_akhir']) && isset($hasil['jenis_penilaian_id'])) {
                                                                $nilai[$hasil['jenis_penilaian_id']] = number_format($hasil['nilai_akhir'], 2);
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $nilai[1] ?></td>
                                                    <td><?= $nilai[2] ?></td>
                                                    <td><?= $nilai[3] ?></td>
                                                    <td><?= $nilai[4] ?></td>
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
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jenis Penilaian</h6>
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