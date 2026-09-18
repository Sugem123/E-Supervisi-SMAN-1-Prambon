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
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" placeholder="Contoh: X IPA 1"
                            value="<?= esc($kelas['nama_kelas']) ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Tingkat</label>
                        <select class="form-control" name="tingkat">
                            <option value="">Pilih Tingkat</option>
                            <option value="X" <?= ($kelas['tingkat'] ?? '') == 'X' ? 'selected' : '' ?>>X</option>
                            <option value="XI" <?= ($kelas['tingkat'] ?? '') == 'XI' ? 'selected' : '' ?>>XI</option>
                            <option value="XII" <?= ($kelas['tingkat'] ?? '') == 'XII' ? 'selected' : '' ?>>XII</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Jurusan</label>
                        <input type="text" class="form-control" name="jurusan" placeholder="Contoh: IPA/IPS/Bahasa"
                            value="<?= esc($kelas['jurusan'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tahun Ajaran <span class="badge badge-success ml-1"><i class="fas fa-lock mr-1"></i>Terkunci</span></label>
                        <input type="text" class="form-control bg-light font-weight-bold" 
                            value="<?= esc(($kelas['tahun_ajar'] ?? '-') . ' - ' . ($kelas['semester'] ?? '-')) ?>" readonly title="Tahun ajaran terkunci">
                        <input type="hidden" name="tahun_ajar_id" value="<?= esc($kelas['tahun_ajar_id']) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Wali Kelas <small class="text-muted">(Total: <?= count($gurus) ?>)</small></label>
                        <select class="form-control" name="wali_kelas">
                            <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                            <?php foreach ($gurus as $guru): ?>
                                <option value="<?= $guru['id'] ?>" <?= $kelas['wali_kelas'] == $guru['id'] ? 'selected' : '' ?>>
                                    <?= esc($guru['nama']) ?><?= !empty($guru['nip']) ? ' (' . esc($guru['nip']) . ')' : '' ?>
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