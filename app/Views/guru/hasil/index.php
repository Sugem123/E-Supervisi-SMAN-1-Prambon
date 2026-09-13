<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Hasil Supervisi Guru</h1>
            <p class="mb-0 text-muted small">Rekapitulasi riwayat penilaian dan hasil pelaksanaan supervisi akademik Anda.</p>
        </div>
        <div>
            <?php if (!empty($activeTahunAjar)): ?>
                <span class="badge badge-light border px-3 py-2 text-gray-700 shadow-sm">
                    <i class="fas fa-calendar-check text-primary mr-1"></i> TA Aktif: <strong><?= esc($activeTahunAjar['tahun_ajar']) ?> (<?= esc($activeTahunAjar['semester']) ?>)</strong>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Supervisi -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Supervisi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total_supervisi'] ?? 0 ?> <small class="text-muted font-weight-normal">Selesai</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Rata-rata Nilai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= ($stats['supervisi_dinilai'] ?? 0) > 0 ? number_format($stats['rata_rata_nilai'], 2) : '-' ?>
                                <small class="text-muted font-weight-normal">/ 100</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kualifikasi Predikat -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kualifikasi Rata-rata
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                    $predikat = $stats['predikat_umum'] ?? '-';
                                    $predBadge = 'secondary';
                                    if ($predikat === 'Baik Sekali') $predBadge = 'success';
                                    elseif ($predikat === 'Baik') $predBadge = 'info';
                                    elseif ($predikat === 'Cukup') $predBadge = 'warning';
                                    elseif ($predikat === 'Kurang') $predBadge = 'danger';
                                ?>
                                <span class="badge badge-<?= $predBadge ?> px-2 py-1"><?= esc($predikat) ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-award fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tahun Ajar / Periode -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tahun Ajaran
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                <?= !empty($activeTahunAjar) ? esc($activeTahunAjar['tahun_ajar']) : '-' ?>
                            </div>
                            <small class="text-muted"><?= !empty($activeTahunAjar) ? esc($activeTahunAjar['semester']) : '' ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-alt mr-1"></i> Riwayat Hasil Supervisi
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($hasil)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-700 font-weight-bold">Belum Ada Hasil Supervisi</h5>
                    <p class="text-muted mb-0">Hasil supervisi yang telah selesai dinilai oleh supervisor akan muncul secara otomatis di sini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Tanggal & Jam</th>
                                <th>Mata Pelajaran & Kelas</th>
                                <th>Supervisor</th>
                                <th>Tahun Ajaran</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Kualifikasi</th>
                                <th class="text-center" width="16%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($hasil as $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-gray-800">
                                            <?= format_hari_indonesia($item['tanggal_supervisi']) ?>, <?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?>
                                        </div>
                                        <small class="text-muted">
                                            <i class="far fa-clock mr-1"></i> Jam Ke-<?= esc($item['jam_ke'] ?? '-') ?>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-primary">
                                            <?= esc($item['mata_pelajaran'] ?? '-') ?>
                                        </div>
                                        <span class="badge badge-light border text-gray-700 mt-1">
                                            <i class="fas fa-chalkboard mr-1"></i> <?= esc($item['nama_kelas'] ?? ($item['kelas'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-gray-800">
                                            <i class="fas fa-user-tie text-muted mr-1"></i> <?= esc($item['supervisor_name'] ?? '-') ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="small font-weight-bold text-gray-800">
                                            <?= esc($item['tahun_ajaran'] ?? '-') ?>
                                        </div>
                                        <small class="text-muted"><?= esc($item['semester'] ?? '-') ?></small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if (!is_null($item['nilai_akhir'])): ?>
                                            <span class="badge badge-primary px-2 py-1" style="font-size: 0.95rem;">
                                                <?= number_format($item['nilai_akhir'], 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if (!is_null($item['ketercapaian'])): ?>
                                            <?php 
                                                $kBadge = 'secondary';
                                                if ($item['ketercapaian'] === 'Baik Sekali') $kBadge = 'success';
                                                elseif ($item['ketercapaian'] === 'Baik') $kBadge = 'info';
                                                elseif ($item['ketercapaian'] === 'Cukup') $kBadge = 'warning';
                                                elseif ($item['ketercapaian'] === 'Kurang') $kBadge = 'danger';
                                            ?>
                                            <span class="badge badge-<?= $kBadge ?> px-2 py-1">
                                                <?= esc($item['ketercapaian']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('guru/hasil/' . $item['id']) ?>" class="btn btn-primary btn-sm shadow-sm" title="Lihat Rincian Penilaian">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                            <a href="<?= base_url('guru/hasil/cetak/' . $item['id']) ?>" target="_blank" class="btn btn-outline-danger btn-sm shadow-sm" title="Cetak Format PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
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
<?= $this->endSection() ?>