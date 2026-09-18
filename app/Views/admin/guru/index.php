<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Guru</h1>
        <a href="<?= base_url('admin/guru/create'); ?>" class="btn btn-primary btn-icon-split btn-sm">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Guru</span>
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Guru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Jenis PTK</th>
                            <th>Mata Pelajaran</th>
                            <th>Status Kepegawaian</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($gurus as $guru) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($guru['nama']); ?></td>
                                <td><?= esc($guru['nip'] ?? '-'); ?></td>
                                <td><?= esc($guru['email'] ?? '-'); ?></td>
                                <td><?= esc($guru['jenis_ptk'] ?? 'Guru'); ?></td>
                                <td><?= esc($guru['nama_mapel_ref'] ?? $guru['mata_pelajaran'] ?? '-'); ?></td>
                                <td><?= esc($guru['status_kepegawaian'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($guru['user_status'] == 'Aktif') : ?>
                                        <span class="badge badge-success"><?= $guru['user_status']; ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-secondary"><?= $guru['user_status']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guru['is_supervisor'] == 1) : ?>
                                        <span class="badge badge-warning">Supervisor</span>
                                    <?php else : ?>
                                        <span class="badge badge-info">Guru</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/guru/' . $guru['id'] . '/edit'); ?>" class="btn btn-warning btn-circle btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($guru['is_supervisor'] == 1) : ?>
                                        <a href="<?= base_url('admin/guru/' . $guru['id'] . '/toggle-supervisor'); ?>" class="btn btn-info btn-circle btn-sm" title="Turunkan sebagai Guru">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?= base_url('admin/guru/' . $guru['id'] . '/toggle-supervisor'); ?>" class="btn btn-success btn-circle btn-sm" title="Angkat sebagai Supervisor">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('admin/guru/' . $guru['id'] . '/delete'); ?>" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
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
<?= $this->endSection(); ?>