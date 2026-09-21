<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kelompok Supervisi</h1>
        <a href="<?= base_url('admin/kelompok'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Form Edit Kelompok</h6></div>
        <div class="card-body">
            <form action="<?= base_url('admin/kelompok/' . $kelompok['id'] . '/update'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_kelompok">Nama Kelompok</label>
                            <input type="text" class="form-control" id="nama_kelompok" name="nama_kelompok" value="<?= esc(old('nama_kelompok', $kelompok['nama_kelompok'])); ?>" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label for="supervisor_id">Supervisor</label>
                            <select class="form-control" id="supervisor_id" name="supervisor_id" required>
                                <option value="">Pilih Supervisor</option>
                                <?php foreach ($supervisors as $s): ?>
                                    <option value="<?= $s['id']; ?>" <?= old('supervisor_id', $kelompok['supervisor_id']) == $s['id'] ? 'selected' : ''; ?>>
                                        <?= esc($s['nama_lengkap'] ?? $s['username']); ?><?= !empty($s['nip']) ? ' (NIP: ' . esc($s['nip']) . ')' : '' ?> (<?= esc($s['role']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun_ajar_id">Tahun Ajaran</label>
                            <select class="form-control" id="tahun_ajar_id" name="tahun_ajar_id">
                                <option value="">Tanpa Tahun Ajaran</option>
                                <?php foreach ($tahunAjars as $t): ?>
                                    <option value="<?= $t['id']; ?>" <?= old('tahun_ajar_id', $kelompok['tahun_ajar_id']) == $t['id'] ? 'selected' : ''; ?>>
                                        <?= esc($t['tahun_ajar']); ?> - <?= esc($t['semester']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Jenis Penilaian yang Ditugaskan -->
                        <div class="form-group mt-4">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-clipboard-check text-primary mr-1"></i> Jenis Penilaian yang Dilakukan:
                            </label>
                            <small class="form-text text-muted mb-2">
                                Tentukan jenis penilaian yang menjadi tugas supervisor ini. Boleh memilih lebih dari satu jenis penilaian.
                            </small>
                            <div class="border rounded p-3 bg-light shadow-sm">
                                <?php if (!empty($jenisPenilaians)): ?>
                                    <?php foreach ($jenisPenilaians as $jp): ?>
                                        <?php $isChecked = in_array((int)$jp['id'], array_map('intval', (array)old('jenis_penilaian_ids', $selectedJenisIds ?? []))); ?>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="jp_<?= $jp['id'] ?>" name="jenis_penilaian_ids[]" value="<?= $jp['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-gray-800" for="jp_<?= $jp['id'] ?>">
                                                <?= esc($jp['nama_jenis'] ?? $jp['nama']) ?>
                                            </label>
                                            <span class="badge badge-light border ml-1">Maks: <?= esc($jp['skor_maksimal'] ?? 100) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small">Belum ada jenis penilaian aktif di sistem.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anggota_ids">Anggota Guru <small class="text-muted">(tahan Ctrl untuk pilih banyak)</small></label>
                            <select class="form-control" id="anggota_ids" name="anggota_ids[]" multiple size="12">
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id']; ?>" <?= in_array($g['id'], old('anggota_ids', $selectedIds ?? [])) ? 'selected' : ''; ?>><?= esc($g['nama']); ?><?= !empty($g['mata_pelajaran']) ? ' - ' . esc($g['mata_pelajaran']) : ''; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Menyimpan akan mengganti seluruh anggota kelompok ini.</small>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui Kelompok</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
