<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Tambah Jadwal Supervisi</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Jadwal</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('kepala/jadwal/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <?php if ($tahun_ajar): ?>
                            <input type="hidden" name="tahun_ajar_id" value="<?= $tahun_ajar['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="guru_id">Guru</label>
                            <select class="form-control" id="guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= $guru['id'] ?>" <?= old('guru_id') == $guru['id'] ? 'selected' : '' ?>>
                                        <?= $guru['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="supervisor_id">Supervisor</label>
                            <select class="form-control" id="supervisor_id" name="supervisor_id" required>
                                <option value="">Pilih Supervisor</option>
                                <?php foreach ($supervisors as $supervisor): ?>
                                    <option value="<?= $supervisor['id'] ?>" <?= old('supervisor_id') == $supervisor['id'] ? 'selected' : '' ?>>
                                        <?= $supervisor['username'] ?> (Supervisor)
                                    </option>
                                <?php endforeach; ?>
                                <?php foreach ($kepala_users as $kepala): ?>
                                    <option value="<?= $kepala['id'] ?>" <?= old('supervisor_id') == $kepala['id'] ? 'selected' : '' ?>>
                                        <?= $kepala['username'] ?> (Kepala)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" 
                                   value="<?= old('mata_pelajaran') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kelas_id">Kelas</label>
                            <select class="form-control" id="kelas_id" name="kelas_id">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelases as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>" <?= old('kelas_id') == $kelas['id'] ? 'selected' : '' ?>>
                                        <?= $kelas['nama_kelas'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="hari">Hari</label>
                            <select class="form-control" id="hari" name="hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin" <?= old('hari') == 'Senin' ? 'selected' : '' ?>>Senin</option>
                                <option value="Selasa" <?= old('hari') == 'Selasa' ? 'selected' : '' ?>>Selasa</option>
                                <option value="Rabu" <?= old('hari') == 'Rabu' ? 'selected' : '' ?>>Rabu</option>
                                <option value="Kamis" <?= old('hari') == 'Kamis' ? 'selected' : '' ?>>Kamis</option>
                                <option value="Jumat" <?= old('hari') == 'Jumat' ? 'selected' : '' ?>>Jumat</option>
                                <option value="Sabtu" <?= old('hari') == 'Sabtu' ? 'selected' : '' ?>>Sabtu</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_supervisi">Tanggal Supervisi</label>
                            <input type="date" class="form-control" id="tanggal_supervisi" name="tanggal_supervisi" 
                                   value="<?= old('tanggal_supervisi') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="jam_ke">Jam Ke-</label>
                            <input type="number" class="form-control" id="jam_ke" name="jam_ke" 
                                   value="<?= old('jam_ke') ?>" min="1" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('/kepala/jadwal') ?>" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tahun Ajar</h6>
                </div>
                <div class="card-body">
                    <?php if ($tahun_ajar): ?>
                        <table class="table table-borderless">
                            <tr>
                                <th>Tahun Ajar</th>
                                <td><?= $tahun_ajar['tahun_ajar'] ?></td>
                            </tr>
                            <tr>
                                <th>Semester</th>
                                <td><?= $tahun_ajar['semester'] ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span class="badge badge-success">Aktif</span></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada tahun ajar aktif.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>