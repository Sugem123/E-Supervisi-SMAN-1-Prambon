<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Guru</h1>
        <a href="<?= base_url('admin/guru'); ?>" class="btn btn-secondary btn-icon-split btn-sm">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Guru</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/guru/store'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="user_id">User<span class="text-danger">*</span></label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">Pilih User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id']; ?>" <?= old('user_id') == $user['id'] ? 'selected' : ''; ?>>
                                    <?= $user['username'] . ' (' . $user['email'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Lengkap<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama'); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nip">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" value="<?= old('nip'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pangkat_golongan">Pangkat/Golongan</label>
                        <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" value="<?= old('pangkat_golongan'); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="mata_pelajaran">Mata Pelajaran</label>
                        <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" value="<?= old('mata_pelajaran'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status_kepegawaian">Status Kepegawaian</label>
                        <select class="form-control" id="status_kepegawaian" name="status_kepegawaian">
                            <option value="">Pilih Status</option>
                            <option value="PNS" <?= old('status_kepegawaian') == 'PNS' ? 'selected' : ''; ?>>PNS</option>
                            <option value="PPPK" <?= old('status_kepegawaian') == 'PPPK' ? 'selected' : ''; ?>>PPPK</option>
                            <option value="Honorer" <?= old('status_kepegawaian') == 'Honorer' ? 'selected' : ''; ?>>Honorer</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_supervisor" name="is_supervisor" value="1" <?= old('is_supervisor') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_supervisor">
                            Jadikan sebagai Supervisor
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>