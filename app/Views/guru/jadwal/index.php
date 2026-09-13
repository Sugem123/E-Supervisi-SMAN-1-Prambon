<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Jadwal Supervisi</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="alert alert-info">
                    Tidak ada jadwal supervisi yang tersedia.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Tanggal Supervisi</th>
                                <th>Jam Ke-</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Supervisor</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($jadwal as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= format_hari_indonesia($item['tanggal_supervisi']) ?></td>
                                    <td><?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?></td>
                                    <td><?= $item['jam_ke'] ?? '' ?></td>
                                    <td><?= $item['nama_kelas'] ?? '' ?></td>
                                    <td><?= $item['mata_pelajaran'] ?? '' ?></td>
                                    <td><?= $item['tahun_ajar'] ?? '' ?> - <?= $item['semester'] ?? '' ?></td>
                                    <td><?= $item['supervisor_name'] ?? '' ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'Terjadwal'): ?>
                                            <span class="badge badge-info"><?= $item['status'] ?></span>
                                        <?php elseif ($item['status'] == 'Selesai'): ?>
                                            <span class="badge badge-success"><?= $item['status'] ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= $item['status'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'Terjadwal'): ?>
                                            <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-folder-open"></i> Dokumen
                                            </a>
                                            <a href="#" class="btn btn-info btn-sm">
                                                <i class="fas fa-info-circle"></i> Detail
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('guru/hasil/' . $item['id']) ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Lihat Hasil
                                            </a>
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
<?= $this->endSection() ?>