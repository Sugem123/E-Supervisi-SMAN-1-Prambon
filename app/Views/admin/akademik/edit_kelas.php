<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kelas</h1>
        <a href="<?= base_url('/admin/akademik/kelas') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Edit Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Kelas</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/update') ?>" method="post">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" placeholder="Contoh: X IPA 1"
                            value="<?= esc($kelas['nama_kelas']) ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tahun Ajaran</label>
                        <select class="form-control" name="tahun_ajar_id" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php foreach ($tahun_ajars as $tahun): ?>
                                <option value="<?= $tahun['id'] ?>" <?= $kelas['tahun_ajar_id'] == $tahun['id'] ? 'selected' : '' ?>>
                                    <?= $tahun['tahun_ajar'] ?> - <?= $tahun['semester'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Wali Kelas <small class="text-muted">(Total: <?= count($gurus) ?>)</small></label>
                        <select class="form-control" name="wali_kelas" required>
                            <option value="">Pilih Wali Kelas</option>
                            <?php foreach ($gurus as $guru): ?>
                                <option value="<?= $guru['id'] ?>" <?= $kelas['wali_kelas'] == $guru['id'] ? 'selected' : '' ?>>
                                    <?= $guru['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="Aktif" <?= $kelas['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= $kelas['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>