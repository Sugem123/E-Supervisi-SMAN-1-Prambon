<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Guru</h1>
        <div>
            <a href="<?= base_url('/admin/pengguna/import-guru') ?>" class="btn btn-info btn-sm">
                <i class="fas fa-file-excel"></i> Import dari Excel
            </a>
            <a href="<?= base_url('/admin/pengguna/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Guru
            </a>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Guru</h6>
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
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Pangkat/Golongan</th>
                            <th>Jenis PTK</th>
                            <th>Mata Pelajaran</th>
                            <th>Status Kepegawaian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gurus as $guru): ?>
                            <tr>
                                <td><?= $guru['nama'] ?></td>
                                <td><?= $guru['nip'] ?></td>
                                <td><?= $guru['username'] ?></td>
                                <td><?= $guru['email'] ?></td>
                                <td><?= $guru['pangkat_golongan'] ?></td>
                                <td><?= esc($guru['jenis_ptk'] ?? 'Guru') ?></td>
                                <td><?= esc($guru['nama_mapel_ref'] ?? $guru['mata_pelajaran']) ?></td>
                                <td><?= $guru['status_kepegawaian'] ?></td>
                                <td>
                                    <?php if (isset($guru['is_supervisor']) && $guru['is_supervisor'] == 1): ?>
                                        <span class="badge badge-warning">Supervisor</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Guru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <?php if (!empty($guru['account_id'])): ?>
                                        <a href="<?= base_url('/admin/pengguna/guru/' . $guru['account_id'] . '/edit') ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if (isset($guru['is_supervisor']) && $guru['is_supervisor'] == 1): ?>
                                            <a href="<?= base_url('/admin/guru/' . $guru['id'] . '/toggle-supervisor') ?>" class="btn btn-info btn-sm" title="Turunkan sebagai Guru">
                                                <i class="fas fa-arrow-down"></i> Turunkan
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('/admin/pengguna/guru/' . $guru['id'] . '/make-supervisor') ?>" class="btn btn-success btn-sm" title="Angkat sebagai Supervisor">
                                                <i class="fas fa-arrow-up"></i> Jadikan Supervisor
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('/admin/pengguna/' . $guru['account_id'] . '/delete') ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus guru ini? Semua data terkait akan terhapus.')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-danger mb-2">User Hilang</span>
                                        <a href="<?= base_url('/admin/guru/' . $guru['id'] . '/delete') ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Data user tidak ditemukan. Apakah Anda yakin ingin menghapus data guru ini?')">
                                            <i class="fas fa-trash"></i> Hapus Data Guru
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
<?= $this->endSection(); ?>