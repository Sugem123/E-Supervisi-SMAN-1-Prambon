<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Supervisi</h1>
        <div>
            <a href="<?= base_url('/kepala/jadwal/export-excel') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('/kepala/jadwal/export-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('/kepala/jadwal/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">List Jadwal Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

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
                            <th>Jam Ke</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                <td><?= $schedule['nama_guru'] ?></td>
                                <td><?= isset($schedule['nama_supervisor']) && !empty($schedule['nama_supervisor']) ? $schedule['nama_supervisor'] : 'Tidak ditentukan' ?></td>
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
                                <td>
                                    <?php if ($schedule['status'] == 'Selesai'): ?>
                                        <a href="<?= base_url('kepala/hasil/detail/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-info">Lihat Hasil</a>
                                        <a href="<?= base_url('kepala/dokumen-ajar/view/' . $schedule['id']) ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-folder-open"></i> Dokumen
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('kepala/jadwal/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-primary">Detail</a>
                                        <a href="<?= base_url('kepala/jadwal/edit/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <a href="<?= base_url('kepala/dokumen-ajar/view/' . $schedule['id']) ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-folder-open"></i> Dokumen
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>