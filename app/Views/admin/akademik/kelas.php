<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Kelas</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas</h6>
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
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Wali Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($kelases as $kelas): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $kelas['nama_kelas'] ?></td>
                                <td><?= $kelas['tahun_ajar'] ?></td>
                                <td><?= $kelas['semester'] ?></td>
                                <td><?= $kelas['nama_wali'] ?? '-' ?></td>
                                <td>
                                    <?php if ($kelas['status'] == 'Aktif'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/edit') ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Kelas</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/admin/akademik/kelas/create') ?>" method="post">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" placeholder="Contoh: X IPA 1" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tahun Ajaran</label>
                        <select class="form-control" name="tahun_ajar_id" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php foreach ($tahun_ajars as $tahun): ?>
                                <option value="<?= $tahun['id'] ?>">
                                    <?= $tahun['tahun_ajar'] ?> - <?= $tahun['semester'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Wali Kelas <small class="text-muted">(Total: <?= count($gurus) ?>)</small></label>
                        <select class="form-control" name="wali_kelas" id="wali_kelas_select" required>
                            <option value="">Pilih Wali Kelas</option>
                            <?php if (empty($gurus)): ?>
                                <option value="" disabled>Data Guru Kosong</option>
                            <?php else: ?>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= $guru['id'] ?>"><?= $guru['nama'] ?? 'Tanpa Nama (' . $guru['id'] . ')' ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>