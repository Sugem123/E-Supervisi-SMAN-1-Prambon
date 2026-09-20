<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Kelompok: <?= esc($kelompok['nama_kelompok']); ?></h1>
            <p class="text-muted small mb-0">
                Supervisor: <strong><?= esc($kelompok['nama_supervisor'] ?? '-'); ?></strong> &bull; Tahun: <?= esc(($kelompok['tahun_ajar'] ?? '-') . (isset($kelompok['semester']) ? ' (' . $kelompok['semester'] . ')' : '')); ?>
            </p>
        </div>
        <div class="mt-3 mt-sm-0">
            <button type="button" class="btn btn-success btn-sm shadow-sm mr-1" data-toggle="modal" data-target="#generateModal">
                <i class="fas fa-magic mr-1"></i> Generate Jadwal Kelompok
            </button>
            <a href="<?= base_url('admin/kelompok/' . $kelompok['id'] . '/edit'); ?>" class="btn btn-warning btn-sm shadow-sm mr-1">
                <i class="fas fa-edit mr-1"></i> Edit Kelompok
            </a>
            <a href="<?= base_url('admin/kelompok'); ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$totalAnggota; ?> Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sudah Dijadwalkan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$totalTerjadwal; ?> Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Dijadwalkan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$belumTerjadwalCount; ?> Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Supervisi Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$totalSelesai; ?> Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Informasi Kelompok & Jenis Penilaian -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-1"></i> Informasi Kelompok</h6>
                    <span class="badge badge-<?= ($kelompok['status_aktif'] ?? '') === 'Aktif' ? 'success' : 'secondary' ?>">
                        <?= esc($kelompok['status_aktif'] ?? 'Nonaktif'); ?>
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-3">
                        <tr>
                            <td width="42%" class="text-muted">Nama Kelompok</td>
                            <td width="3%">:</td>
                            <td><strong><?= esc($kelompok['nama_kelompok']); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Supervisor Pembina</td>
                            <td>:</td>
                            <td>
                                <strong><?= esc($kelompok['nama_supervisor'] ?? '-'); ?></strong>
                                <?php if (!empty($kelompok['role_supervisor'])): ?>
                                    <span class="badge badge-light border ml-1"><?= esc($kelompok['role_supervisor']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Ajaran</td>
                            <td>:</td>
                            <td><?= esc(($kelompok['tahun_ajar'] ?? '-') . (isset($kelompok['semester']) ? ' (' . $kelompok['semester'] . ')' : '')); ?></td>
                        </tr>
                    </table>

                    <hr>

                    <!-- Jenis Penilaian yang Ditugaskan -->
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-tasks text-primary mr-1"></i> Jenis Penilaian Ditugaskan:
                    </h6>
                    <div class="mb-3">
                        <?php if (!empty($assignedJenis)): ?>
                            <ul class="list-group list-group-flush small">
                                <?php foreach ($assignedJenis as $idx => $aj): ?>
                                    <li class="list-group-item px-0 py-1 d-flex align-items-center justify-content-between">
                                        <span>
                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                            <strong><?= esc($aj['nama']); ?></strong>
                                        </span>
                                        <span class="badge badge-light border">Maks: <?= esc($aj['skor_maksimal'] ?? 100); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Supervisor ini hanya akan menilai <?= count($assignedJenis); ?> komponen di atas saat melakukan supervisi.
                            </small>
                        <?php else: ?>
                            <div class="alert alert-light border small text-muted mb-0">
                                <i class="fas fa-asterisk text-info mr-1"></i> Belum dikonfigurasi secara spesifik. Sistem menggunakan <strong>Semua Komponen Aktif</strong> secara default.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="text-center pt-2 border-top">
                        <button type="button" class="btn btn-outline-success btn-sm btn-block" data-toggle="modal" data-target="#generateModal">
                            <i class="fas fa-magic mr-1"></i> Generate Jadwal Anggota
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Card Jadwal Pelaksanaan Supervisi -->
            <div class="card shadow mb-4" id="section-jadwal">
                <div class="card-header py-3 bg-white d-flex flex-wrap align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-1"></i> Jadwal Supervisi Anggota Kelompok
                    </h6>
                    <?php if ($belumTerjadwalCount > 0): ?>
                        <span class="badge badge-warning">
                            <i class="fas fa-exclamation-circle mr-1"></i> <?= $belumTerjadwalCount; ?> guru belum dijadwalkan
                        </span>
                    <?php else: ?>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i> Semua guru sudah dijadwalkan
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($jadwals)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover small" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="4%" class="text-center">No</th>
                                        <th>Nama Guru</th>
                                        <th>Tanggal</th>
                                        <th>Hari / Jam</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th width="10%" class="text-center">Status</th>
                                        <th width="12%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($jadwals as $j): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $no++; ?></td>
                                            <td class="align-middle">
                                                <strong><?= esc($j['nama_guru']); ?></strong>
                                                <div class="text-muted"><?= esc($j['nip_guru'] ?: '-'); ?></div>
                                            </td>
                                            <td class="align-middle">
                                                <?= !empty($j['tanggal_supervisi']) ? date('d M Y', strtotime($j['tanggal_supervisi'])) : '-'; ?>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?= esc($j['hari']); ?></strong>
                                                <div>Jam ke-<?= esc($j['jam_ke']); ?> (<?= esc(substr($j['waktu_dari'], 0, 5) . ' - ' . substr($j['waktu_sampai'], 0, 5)); ?>)</div>
                                            </td>
                                            <td class="align-middle"><?= esc($j['kelas'] ?: '-'); ?></td>
                                            <td class="align-middle"><?= esc($j['mata_pelajaran'] ?: '-'); ?></td>
                                            <td class="text-center align-middle">
                                                <?php if ($j['status'] === 'Selesai'): ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i>Selesai</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i>Terjadwal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('admin/jadwal/' . $j['id']); ?>" class="btn btn-info" title="Lihat Jadwal"><i class="fas fa-eye"></i></a>
                                                    <a href="<?= base_url('admin/penilaian/form/' . $j['id']); ?>" class="btn btn-success" title="Form Penilaian"><i class="fas fa-clipboard-check"></i></a>
                                                    <a href="<?= base_url('admin/jadwal/' . $j['id'] . '/edit'); ?>" class="btn btn-warning" title="Edit Jadwal"><i class="fas fa-edit"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-2 text-gray-300"></i>
                            <p class="mb-2">Belum ada jadwal supervisi yang dibuat untuk kelompok ini.</p>
                            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#generateModal">
                                <i class="fas fa-magic mr-1"></i> Generate Jadwal Otomatis Sekarang
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Anggota Guru Kelompok -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-1"></i> Anggota Guru (<?= count($anggota); ?>)
                    </h6>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addAnggotaModal">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Anggota
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover small" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%" class="text-center">No</th>
                                    <th>Nama Guru</th>
                                    <th>NIP</th>
                                    <th>Mata Pelajaran</th>
                                    <th width="14%" class="text-center">Status Jadwal</th>
                                    <th width="8%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($anggota as $a): ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $no++; ?></td>
                                        <td class="align-middle">
                                            <strong><?= esc($a['nama_guru']); ?></strong>
                                            <span class="badge badge-light border ml-1"><?= esc($a['jenis_ptk'] ?? 'Guru'); ?></span>
                                        </td>
                                        <td class="align-middle"><?= esc($a['nip'] ?: '-'); ?></td>
                                        <td class="align-middle"><?= esc($a['nama_mapel_ref'] ?? $a['mata_pelajaran'] ?? '-'); ?></td>
                                        <td class="text-center align-middle">
                                            <?php if ($a['has_jadwal']): ?>
                                                <span class="badge badge-<?= $a['jadwal']['status'] === 'Selesai' ? 'success' : 'info' ?> px-2 py-1">
                                                    <?= esc($a['jadwal']['status']); ?>: <?= date('d/m/Y', strtotime($a['jadwal']['tanggal_supervisi'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-warning px-2 py-1">Belum ada jadwal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <form action="<?= base_url('admin/kelompok/' . $kelompok['id'] . '/delete-anggota/' . $a['guru_id']); ?>" method="post" class="d-inline" onsubmit="return confirm('Keluarkan guru <?= esc(addslashes($a['nama_guru'])); ?> dari kelompok ini?')">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus dari Kelompok">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($anggota)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada anggota di kelompok ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Jadwal Kelompok -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/kelompok/' . $kelompok['id'] . '/generate-jadwal'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="generateModalLabel">
                        <i class="fas fa-magic mr-1"></i> Generate Jadwal Otomatis - <?= esc($kelompok['nama_kelompok']); ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Fitur ini akan otomatis membuat jadwal supervisi untuk anggota kelompok ini dengan supervisor <strong><?= esc($kelompok['nama_supervisor'] ?? '-'); ?></strong>. Guru yang sudah memiliki jadwal dalam kelompok ini akan otomatis dilewati agar tidak terjadi jadwal ganda.
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_mulai" class="font-weight-bold small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_selesai" class="font-weight-bold small">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= date('Y-m-d', strtotime('+1 month')); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sesi_mulai" class="font-weight-bold small d-flex justify-content-between align-items-center">
                                <span>Jam Pelajaran Mulai:</span>
                                <a href="<?= base_url('admin/pengaturan?tab=jam-pelajaran'); ?>" target="_blank" class="small text-primary font-weight-normal" title="Ubah jam pelajaran di Pengaturan Sistem">
                                    <i class="fas fa-cog mr-1"></i>Atur Jam
                                </a>
                            </label>
                            <select class="form-control" id="sesi_mulai" name="sesi_mulai">
                                <?php 
                                $kbmSlots = $jamPelajaranKbm ?? get_jam_pelajaran_kbm();
                                foreach ($kbmSlots as $jk => $slot): 
                                ?>
                                    <option value="<?= esc($jk); ?>">
                                        <?= esc($slot['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="max_per_day" class="font-weight-bold small">Maksimal Guru per Hari</label>
                            <select class="form-control" id="max_per_day" name="max_per_day">
                                <option value="1" selected>1 Guru per Hari</option>
                                <option value="2">2 Guru per Hari</option>
                                <option value="3">3 Guru per Hari</option>
                                <option value="4">4 Guru per Hari</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small d-flex justify-content-between align-items-center mb-1">
                            <span>Pilih Anggota Guru yang Dijadwalkan:</span>
                            <span class="btn btn-link btn-sm p-0 text-primary" id="btnToggleAllGuru" style="font-size: 0.8rem; text-decoration: none; cursor: pointer;">
                                Centang Semua
                            </span>
                        </label>
                        <?php if (!empty($anggota)): ?>
                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="searchJadwalGuru" class="form-control border-left-0" placeholder="Cari nama guru atau mata pelajaran..." autocomplete="off">
                                <div class="input-group-append" id="wrapperClearSearchJadwal" style="display: none;">
                                    <button class="btn btn-outline-secondary" type="button" id="btnClearSearchJadwal" title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="border rounded p-2 bg-light" id="listJadwalGuru" style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($anggota)): ?>
                                <?php foreach ($anggota as $a): ?>
                                    <div class="custom-control custom-checkbox mb-2 item-jadwal-guru" data-search="<?= strtolower(esc($a['nama_guru'] . ' ' . ($a['nama_mapel_ref'] ?? $a['mata_pelajaran'] ?? ''))); ?>">
                                        <input type="checkbox" class="custom-control-input check-guru-item" id="chk_guru_<?= $a['guru_id']; ?>" name="guru_ids[]" value="<?= $a['guru_id']; ?>" <?= !$a['has_jadwal'] ? 'checked' : ''; ?>>
                                        <label class="custom-control-label font-weight-bold text-gray-800" for="chk_guru_<?= $a['guru_id']; ?>">
                                            <?= esc($a['nama_guru']); ?>
                                            <span class="text-muted font-weight-normal">(<?= esc($a['nama_mapel_ref'] ?? $a['mata_pelajaran'] ?? '-'); ?>)</span>
                                            <?php if ($a['has_jadwal']): ?>
                                                <span class="badge badge-secondary ml-1 font-weight-normal">Sudah ada jadwal</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning ml-1 font-weight-normal">Belum ada jadwal</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <div id="noMatchJadwalGuru" class="text-muted small py-3 text-center" style="display: none;">
                                    <i class="fas fa-search mr-1"></i> Anggota guru tidak ditemukan.
                                </div>
                            <?php else: ?>
                                <div class="text-muted small py-2 text-center">Belum ada anggota di kelompok ini.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mulai generate jadwal otomatis untuk guru yang dipilih?')">
                        <i class="fas fa-magic mr-1"></i> Mulai Generate Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Anggota Kelompok -->
<div class="modal fade" id="addAnggotaModal" tabindex="-1" role="dialog" aria-labelledby="addAnggotaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/kelompok/' . $kelompok['id'] . '/add-anggota'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="addAnggotaModalLabel">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Anggota Guru ke <?= esc($kelompok['nama_kelompok']); ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Pilih satu atau beberapa guru yang akan ditambahkan ke kelompok ini. Guru yang sudah menjadi anggota tidak ditampilkan di daftar ini.
                    </p>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small d-flex justify-content-between align-items-center mb-1">
                            <span>Daftar Guru Tersedia:</span>
                            <?php if (!empty($availableGurus)): ?>
                                <span class="btn btn-link btn-sm p-0 text-primary" id="btnToggleAllAvailable" style="font-size: 0.8rem; text-decoration: none; cursor: pointer;">
                                    Centang Semua
                                </span>
                            <?php endif; ?>
                        </label>
                        <?php if (!empty($availableGurus)): ?>
                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="searchAvailableGuru" class="form-control border-left-0" placeholder="Cari nama guru, mata pelajaran, atau NIP..." autocomplete="off">
                                <div class="input-group-append" id="wrapperClearSearchAvail" style="display: none;">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="btnClearSearchAvail" title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="border rounded p-2 bg-light" id="listAvailableGuru" style="max-height: 280px; overflow-y: auto;">
                            <?php if (!empty($availableGurus)): ?>
                                <?php foreach ($availableGurus as $ag): ?>
                                    <div class="custom-control custom-checkbox mb-2 item-available-guru" data-search="<?= strtolower(esc($ag['nama'] . ' ' . ($ag['mata_pelajaran'] ?? '') . ' ' . ($ag['nip'] ?? ''))); ?>">
                                        <input type="checkbox" class="custom-control-input check-available-item" id="chk_avail_<?= $ag['id']; ?>" name="guru_ids[]" value="<?= $ag['id']; ?>">
                                        <label class="custom-control-label font-weight-bold text-gray-800" for="chk_avail_<?= $ag['id']; ?>">
                                            <?= esc($ag['nama']); ?>
                                            <span class="text-muted font-weight-normal">(<?= esc($ag['mata_pelajaran'] ?: 'Umum'); ?> &bull; NIP: <?= esc($ag['nip'] ?: '-'); ?>)</span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <div id="noMatchAvailableGuru" class="text-muted small py-3 text-center" style="display: none;">
                                    <i class="fas fa-search mr-1"></i> Guru dengan nama/kata kunci tersebut tidak ditemukan.
                                </div>
                            <?php else: ?>
                                <div class="text-muted small py-3 text-center">Seluruh data guru sudah terdaftar sebagai anggota di kelompok ini.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" <?= empty($availableGurus) ? 'disabled' : ''; ?>>
                        <i class="fas fa-plus mr-1"></i> Tambahkan ke Kelompok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Pencarian Guru Tersedia di Modal Tambah Anggota
    var searchAvailInput = document.getElementById('searchAvailableGuru');
    var btnClearAvail = document.getElementById('btnClearSearchAvail');
    var wrapperClearAvail = document.getElementById('wrapperClearSearchAvail');
    var itemsAvail = document.querySelectorAll('.item-available-guru');
    var noMatchAvail = document.getElementById('noMatchAvailableGuru');

    if (searchAvailInput) {
        searchAvailInput.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            if (wrapperClearAvail) {
                wrapperClearAvail.style.display = q !== '' ? 'block' : 'none';
            }
            var visibleCount = 0;
            itemsAvail.forEach(function(el) {
                var text = el.getAttribute('data-search') || '';
                if (q === '' || text.indexOf(q) !== -1) {
                    el.style.display = '';
                    visibleCount++;
                } else {
                    el.style.display = 'none';
                }
            });
            if (noMatchAvail) {
                noMatchAvail.style.display = (visibleCount === 0 && itemsAvail.length > 0) ? 'block' : 'none';
            }
        });

        if (btnClearAvail) {
            btnClearAvail.addEventListener('click', function() {
                searchAvailInput.value = '';
                searchAvailInput.dispatchEvent(new Event('input'));
                searchAvailInput.focus();
            });
        }
    }

    // 2. Pencarian Anggota di Modal Generate Jadwal
    var searchJadwalInput = document.getElementById('searchJadwalGuru');
    var btnClearJadwal = document.getElementById('btnClearSearchJadwal');
    var wrapperClearJadwal = document.getElementById('wrapperClearSearchJadwal');
    var itemsJadwal = document.querySelectorAll('.item-jadwal-guru');
    var noMatchJadwal = document.getElementById('noMatchJadwalGuru');

    if (searchJadwalInput) {
        searchJadwalInput.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            if (wrapperClearJadwal) {
                wrapperClearJadwal.style.display = q !== '' ? 'block' : 'none';
            }
            var visibleCount = 0;
            itemsJadwal.forEach(function(el) {
                var text = el.getAttribute('data-search') || '';
                if (q === '' || text.indexOf(q) !== -1) {
                    el.style.display = '';
                    visibleCount++;
                } else {
                    el.style.display = 'none';
                }
            });
            if (noMatchJadwal) {
                noMatchJadwal.style.display = (visibleCount === 0 && itemsJadwal.length > 0) ? 'block' : 'none';
            }
        });

        if (btnClearJadwal) {
            btnClearJadwal.addEventListener('click', function() {
                searchJadwalInput.value = '';
                searchJadwalInput.dispatchEvent(new Event('input'));
                searchJadwalInput.focus();
            });
        }
    }

    // 3. Toggle Centang Semua (Hanya yang sedang terlihat/visible jika ada filter)
    var toggleBtn = document.getElementById('btnToggleAllGuru');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var visibleItems = [];
            document.querySelectorAll('.item-jadwal-guru').forEach(function(wrapper) {
                if (wrapper.style.display !== 'none') {
                    var chk = wrapper.querySelector('.check-guru-item');
                    if (chk) visibleItems.push(chk);
                }
            });
            if (visibleItems.length === 0) return;

            var anyUnchecked = visibleItems.some(function(el) { return !el.checked; });
            visibleItems.forEach(function(el) { el.checked = anyUnchecked; });
            toggleBtn.textContent = anyUnchecked ? 'Hapus Semua Centang' : 'Centang Semua';
        });
    }

    var toggleAvailBtn = document.getElementById('btnToggleAllAvailable');
    if (toggleAvailBtn) {
        toggleAvailBtn.addEventListener('click', function() {
            var visibleItems = [];
            document.querySelectorAll('.item-available-guru').forEach(function(wrapper) {
                if (wrapper.style.display !== 'none') {
                    var chk = wrapper.querySelector('.check-available-item');
                    if (chk) visibleItems.push(chk);
                }
            });
            if (visibleItems.length === 0) return;

            var anyUnchecked = visibleItems.some(function(el) { return !el.checked; });
            visibleItems.forEach(function(el) { el.checked = anyUnchecked; });
            toggleAvailBtn.textContent = anyUnchecked ? 'Hapus Semua Centang' : 'Centang Semua';
        });
    }

    // 4. Reset pencarian saat modal ditutup
    if (window.jQuery) {
        $('#addAnggotaModal').on('hidden.bs.modal', function() {
            if (searchAvailInput) {
                searchAvailInput.value = '';
                searchAvailInput.dispatchEvent(new Event('input'));
            }
        });
        $('#generateJadwalModal').on('hidden.bs.modal', function() {
            if (searchJadwalInput) {
                searchJadwalInput.value = '';
                searchJadwalInput.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>
