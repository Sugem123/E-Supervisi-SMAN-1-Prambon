<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Jadwal Supervisi</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">List Jadwal Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Guru</th>
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
                                    <?php if ($schedule['status'] == 'Terjadwal' && $schedule['tanggal_supervisi'] <= date('Y-m-d')): ?>
                                        <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-primary">Mulai Penilaian</a>
                                        <a href="<?= base_url('supervisor/dokumen-ajar/view/' . $schedule['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-folder-open"></i> Dokumen
                                        </a>
                                    <?php elseif ($schedule['status'] == 'Terjadwal'): ?>
                                        <a href="<?= base_url('supervisor/dokumen-ajar/view/' . $schedule['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-folder-open"></i> Dokumen
                                        </a>
                                    <?php elseif ($schedule['status'] == 'Selesai'): ?>
                                        <a href="<?= base_url('supervisor/penilaian/view/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-info"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                                            </svg> Lihat Hasil</a>
                                        <!-- Edit button that redirects to penilaian form for already completed evaluations -->
                                        <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-warning"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg> Edit</a>
                                        <!-- Button to upload evidence photos -->
                                        <a href="<?= base_url('supervisor/foto-bukti/upload/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                <path d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0" />
                                            </svg> Upload Foto</a>
                                    <?php else: ?>
                                        <a href="<?= base_url('supervisor/jadwal/detail/' . $schedule['id']) ?>"
                                            class="btn btn-sm btn-secondary">Lihat Detail</a>
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