<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Tahun Ajaran</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tahun Ajaran</h6>
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
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($tahun_ajars as $tahun): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $tahun['tahun_ajar'] ?></td>
                            <td><?= $tahun['semester'] ?></td>
                            <td>
                                <?php if ($tahun['status_aktif'] == 'Aktif'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $tahun['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $tahun['id'] ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('/admin/akademik/tahun-ajar/' . $tahun['id'] . '/update') ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Tahun Ajaran</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Tahun Ajaran</label>
                                                        <input type="text" class="form-control" name="tahun_ajar" value="<?= $tahun['tahun_ajar'] ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Semester</label>
                                                        <select class="form-control" name="semester" required>
                                                            <option value="Ganjil" <?= $tahun['semester'] == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                                            <option value="Genap" <?= $tahun['semester'] == 'Genap' ? 'selected' : '' ?>>Genap</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select class="form-control" name="status_aktif" required>
                                                            <option value="Aktif" <?= $tahun['status_aktif'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                                            <option value="Nonaktif" <?= $tahun['status_aktif'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
            <h6 class="m-0 font-weight-bold text-primary">Tambah Tahun Ajaran</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/admin/akademik/tahun-ajar/create') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Tahun Ajaran</label>
                        <input type="text" class="form-control" name="tahun_ajar" placeholder="Contoh: 2025/2026" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Semester</label>
                        <select class="form-control" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select class="form-control" name="status_aktif" required>
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