<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Guru</h1>
        <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
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
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Guru</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/admin/pengguna/guru/' . $user['id'] . '/update') ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="_method" value="POST">
                <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Aktif" <?= old('status', $user['status']) == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= old('status', $user['status']) == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $guru['nama'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="<?= old('nip', $guru['nip'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="pangkat_golongan">Pangkat/Golongan</label>
                            <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" value="<?= old('pangkat_golongan', $guru['pangkat_golongan'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis_ptk">Jenis PTK</label>
                            <select class="form-control" id="jenis_ptk" name="jenis_ptk">
                                <option value="Guru" <?= old('jenis_ptk', $guru['jenis_ptk'] ?? 'Guru') == 'Guru' ? 'selected' : '' ?>>Guru</option>
                                <option value="Tendik" <?= old('jenis_ptk', $guru['jenis_ptk'] ?? '') == 'Tendik' ? 'selected' : '' ?>>Tendik</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mapel_id">Mata Pelajaran (Master SMA)</label>
                            <select class="form-control" id="mapel_id" name="mapel_id">
                                <option value="">Pilih Mapel</option>
                                <?php foreach (($mapels ?? []) as $mapel): ?>
                                    <option value="<?= $mapel['id'] ?>" <?= (string) old('mapel_id', $guru['mapel_id'] ?? '') === (string) $mapel['id'] ? 'selected' : '' ?>><?= esc($mapel['nama_mapel']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran (Teks/Legacy)</label>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" value="<?= old('mata_pelajaran', $guru['mata_pelajaran'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="status_kepegawaian">Status Kepegawaian</label>
                            <select class="form-control" id="status_kepegawaian" name="status_kepegawaian">
                                <option value="">Pilih Status</option>
                                <option value="PNS" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'PNS' ? 'selected' : '' ?>>PNS</option>
                                <option value="PPPK" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'PPPK' ? 'selected' : '' ?>>PPPK</option>
                                <option value="GTT" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'GTT' ? 'selected' : '' ?>>GTT</option>
                                <option value="PTT" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'PTT' ? 'selected' : '' ?>>PTT</option>
                                <option value="Honorer" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                                <option value="Kontrak" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_supervisor" name="is_supervisor" value="1" <?= old('is_supervisor', $guru['is_supervisor'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_supervisor">
                                    <?= ($guru['is_supervisor'] ?? 0) ? 'Sudah menjadi Supervisor' : 'Jadikan sebagai Supervisor' ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>