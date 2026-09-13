<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Daftar Dokumen Ajar</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Dokumen per Jadwal Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="alert alert-info">
                    Belum ada jadwal supervisi yang tersedia.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Supervisi</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Status Jadwal</th>
                                <th>Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($jadwal as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?></td>
                                    <td><?= $item['mata_pelajaran'] ?></td>
                                    <td><?= $item['nama_kelas'] ?></td>
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
                                        <!-- Check document count logic could be here if we joined tables, 
                                             but for now simple link is enough -->
                                        <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']) ?>" class="btn btn-sm btn-light border">
                                            <i class="fas fa-folder-open text-warning"></i> Kelola
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit Dokumen
                                        </a>
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