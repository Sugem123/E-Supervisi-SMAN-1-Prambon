<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Hasil Supervisi</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Hasil Supervisi</h6>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($jadwalSelesai as $jadwal): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                                <td><?= $jadwal['nama_guru'] ?></td>
                                <td><?= isset($jadwal['nama_supervisor']) && !empty($jadwal['nama_supervisor']) ? $jadwal['nama_supervisor'] : 'Tidak ditentukan' ?></td>
                                <td><?= $jadwal['mata_pelajaran'] ?></td>
                                <td><?= $jadwal['kelas'] ?></td>
                                <td>
                                    <a href="<?= base_url('kepala/penilaian/form/' . $jadwal['id']) ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('kepala/hasil/detail/' . $jadwal['id']) ?>"
                                        class="btn btn-sm btn-primary">Lihat Detail</a>
                                    <a href="<?= base_url('kepala/foto-bukti/upload/' . $jadwal['id']) ?>"
                                        class="btn btn-sm btn-info">
                                        Upload <i class="fas fa-camera"></i>
                                    </a>
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