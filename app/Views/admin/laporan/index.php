<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading & Actions Bar -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Laporan & Analitik Hasil Supervisi</h1>
            <p class="text-muted small mb-0">Dashboard rekapitulasi capaian akademik, evaluasi pembelajaran, dan cetak laporan resmi.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('admin/laporan/cetak-rekap-detail?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm mr-2">
                <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> Cetak PDF Rekap Detail
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-clipboard-list fa-sm text-white-50 mr-1"></i> Kelola Hasil Supervisi
            </a>
        </div>
    </div>

    <!-- Filter Tahun Ajaran Card -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-body py-3">
            <form method="get" action="<?= base_url('admin/laporan') ?>" class="form-inline d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <label class="mr-3 font-weight-bold text-gray-700 mb-0">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Periode Tahun Ajaran:
                    </label>
                    <select name="tahun_ajar_id" class="form-control form-control-sm mr-2" style="min-width: 220px;" onchange="this.form.submit()">
                        <?php foreach ($tahun_ajars as $ta): ?>
                            <option value="<?= $ta['id'] ?>" <?= ($ta['id'] == $tahun_ajar_id) ? 'selected' : '' ?>>
                                <?= esc($ta['tahun_ajar']) ?> - Semester <?= esc($ta['semester']) ?> <?= (!empty($ta['is_active'])) ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter mr-1"></i> Terapkan
                    </button>
                </div>
                <div>
                    <span class="badge badge-light border px-3 py-2 text-dark font-weight-normal">
                        Tahun Ajaran Terpilih: <strong><?= esc($selectedTahunAjar['tahun_ajar'] ?? '-') ?> (<?= esc($selectedTahunAjar['semester'] ?? '-') ?>)</strong>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="row">
        <!-- Total Guru Disupervisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Guru Disupervisi (Selesai)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_selesai'] ?> <span class="text-muted small">/ <?= $metrics['total_jadwal'] ?> Jadwal</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <?= ($metrics['total_jadwal'] > 0) ? round(($metrics['total_selesai'] / $metrics['total_jadwal']) * 100) : 0 ?>% terlaksana
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Rata-Rata Supervisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Rata-Rata Nilai Akhir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($metrics['rata_rata_nilai'], 2, ',', '.') ?>
                            </div>
                            <div class="small text-success mt-1 font-weight-bold">
                                <?php
                                    $avg = $metrics['rata_rata_nilai'];
                                    if ($avg >= 86) echo 'Predikat: Baik Sekali';
                                    elseif ($avg >= 70) echo 'Predikat: Baik';
                                    elseif ($avg >= 55) echo 'Predikat: Cukup';
                                    elseif ($avg > 0) echo 'Predikat: Kurang';
                                    else echo 'Belum Ada Penilaian';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tingkat Ketercapaian Kualitas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Capaian Mutu (Baik/BS)
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?= $metrics['persen_kualitas'] ?>%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?= $metrics['persen_kualitas'] ?>%"
                                            aria-valuenow="<?= $metrics['persen_kualitas'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="small text-muted mt-1">
                                <?= ($metrics['predikat_counts']['Baik Sekali'] + $metrics['predikat_counts']['Baik']) ?> dari <?= $metrics['total_selesai'] ?> guru tuntas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guru Perlu Pembinaan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Membutuhkan Pembinaan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= ($metrics['predikat_counts']['Cukup'] + $metrics['predikat_counts']['Kurang']) ?> <span class="text-muted small">Guru</span>
                            </div>
                            <div class="small text-muted mt-1">
                                Cukup: <?= $metrics['predikat_counts']['Cukup'] ?> | Kurang: <?= $metrics['predikat_counts']['Kurang'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Bar Chart: Nilai Capaian per Jenis Penilaian -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-1"></i> Rata-Rata Capaian Berdasarkan Jenis Penilaian
                    </h6>
                    <span class="badge badge-primary px-2 py-1">Skala 100%</span>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="position: relative; height: 260px;">
                        <canvas id="jenisPenilaianBarChart"></canvas>
                    </div>
                    <hr>
                    <div class="row text-center pt-1">
                        <?php foreach ($jenisPenilaians as $jp): ?>
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted small"><?= esc($jp['nama']) ?>:</span>
                                <strong class="ml-1 text-primary"><?= number_format($metrics['avg_per_jenis'][$jp['id']] ?? 0, 1) ?>%</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart: Distribusi Predikat Kualifikasi -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-1"></i> Distribusi Kualifikasi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-2 pb-2" style="position: relative; height: 220px;">
                        <canvas id="predikatDonutChart"></canvas>
                    </div>
                    <div class="mt-3 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Baik Sekali (<?= $metrics['predikat_counts']['Baik Sekali'] ?>)</span>
                        <span class="mr-2"><i class="fas fa-circle text-primary"></i> Baik (<?= $metrics['predikat_counts']['Baik'] ?>)</span>
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Cukup (<?= $metrics['predikat_counts']['Cukup'] ?>)</span>
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Kurang (<?= $metrics['predikat_counts']['Kurang'] ?>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Supervision Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-1"></i> Rekapitulasi Rincian Supervisi Guru (Tahun Ajaran <?= esc($selectedTahunAjar['tahun_ajar'] ?? '-') ?>)
            </h6>
            <div>
                <a href="<?= base_url('admin/laporan/cetak-rekap-detail?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak Dokumen Rekap Landscape
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-dark" id="dataTableLaporan" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">No</th>
                            <th>Nama Guru & NIP</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <?php foreach ($jenisPenilaians as $jp): ?>
                                <th class="text-center" style="min-width: 110px;"><?= esc($jp['nama']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center" style="width: 90px;">Nilai Akhir</th>
                            <th class="text-center" style="width: 100px;">Kualifikasi</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwals)): ?>
                            <tr>
                                <td colspan="<?= 7 + count($jenisPenilaians) ?>" class="text-center py-4 text-muted">
                                    <em>Tidak ada data jadwal supervisi pada tahun ajaran ini.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <strong class="text-dark"><?= esc($jadwal['nama_guru']) ?></strong>
                                    <?php if (!empty($jadwal['nip_guru'])): ?>
                                        <div class="text-muted small">NIP. <?= esc($jadwal['nip_guru']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($jadwal['nama_supervisor'])): ?>
                                        <div class="small text-muted mt-1"><i class="fas fa-user-tie text-info mr-1"></i>Spv: <?= esc($jadwal['nama_supervisor']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?= esc($jadwal['mata_pelajaran'] ?? '-') ?></td>
                                <td class="align-middle text-center">
                                    <?php if (empty($jadwal['kelas']) || $jadwal['kelas'] === '-' || ($jadwal['jenis_ptk'] ?? '') === 'Tendik'): ?>
                                        <span class="badge badge-light border text-muted px-2 py-1">Non-KBM</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary font-weight-bold px-2 py-1"><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= !empty($jadwal['tanggal_supervisi']) ? date('d/m/Y', strtotime($jadwal['tanggal_supervisi'])) : '-' ?>
                                </td>

                                <!-- Kolom Rincian Nilai per Jenis Penilaian -->
                                <?php foreach ($jenisPenilaians as $jp): ?>
                                    <?php 
                                        $item = $jadwal['nilai_per_jenis'][$jp['id']] ?? null;
                                    ?>
                                    <td class="text-center align-middle">
                                        <?php if ($item !== null): ?>
                                            <span class="badge badge-light border font-weight-bold px-2 py-1" style="font-size: 85%;">
                                                <?= number_format($item['nilai'], 1, ',', '.') ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>

                                <!-- Nilai Akhir -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['nilai_akhir'] !== null): ?>
                                        <h6 class="font-weight-bold mb-0 text-primary">
                                            <?= number_format($jadwal['nilai_akhir'], 2, ',', '.') ?>
                                        </h6>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Belum Dinilai</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Ketercapaian / Predikat -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['ketercapaian'] !== null): ?>
                                        <span class="badge badge-<?= $jadwal['predikat_badge'] ?> px-2 py-1 font-weight-bold">
                                            <?= esc($jadwal['ketercapaian']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted">
                                            <?= esc($jadwal['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['status'] === 'Selesai'): ?>
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $jadwal['id']) ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/penilaian/form/' . $jadwal['id']) ?>" class="btn btn-sm btn-warning" title="Edit Hasil Penilaian">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/laporan/hasil-supervisi/cetak-detail/' . $jadwal['id']) ?>" target="_blank" class="btn btn-sm btn-danger" title="Cetak Dokumen PDF">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Belum Selesai</em></span>
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
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    if ($('#dataTableLaporan').length) {
        $('#dataTableLaporan').DataTable({
            "language": {
                "search": "Cari Guru / Mapel:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ guru",
                "infoEmpty": "Tidak ada data yang tersedia",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 25,
            "order": [[ 0, "asc" ]]
        });
    }

    // Chart.js - Bar Chart Rata-rata Jenis Penilaian
    var ctxBar = document.getElementById("jenisPenilaianBarChart");
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_jenis['labels']) ?>,
                datasets: [{
                    label: "Rata-Rata Capaian (%)",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: <?= json_encode($chart_jenis['data']) ?>,
                    maxBarThickness: 50
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 15, top: 15, bottom: 0 }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 6 }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 100,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) { return value + '%'; }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: { display: false },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel + '%';
                        }
                    }
                }
            }
        });
    }

    // Chart.js - Donut Chart Predikat Kualifikasi
    var ctxDonut = document.getElementById("predikatDonutChart");
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chart_predikat['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chart_predikat['data']) ?>,
                    backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#2e59d9', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                }]
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
                    caretPadding: 10
                },
                legend: { display: false },
                cutoutPercentage: 70
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>