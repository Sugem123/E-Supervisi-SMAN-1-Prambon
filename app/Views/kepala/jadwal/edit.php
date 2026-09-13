<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Edit Jadwal Supervisi</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Jadwal</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('kepala/jadwal/update/' . $jadwal['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="tahun_ajar_id">Tahun Ajar</label>
                            <select class="form-control" id="tahun_ajar_id" name="tahun_ajar_id" required>
                                <option value="">Pilih Tahun Ajar</option>
                                <?php foreach ($tahun_ajars as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= $jadwal['tahun_ajar_id'] == $tahun['id'] ? 'selected' : '' ?>>
                                        <?= $tahun['tahun_ajar'] ?> - Semester <?= $tahun['semester'] ?>
                                        <?= $tahun['status_aktif'] == 'Aktif' ? '(Aktif)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="guru_id">Guru</label>
                            <select class="form-control" id="guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= $guru['id'] ?>" <?= $jadwal['guru_id'] == $guru['id'] ? 'selected' : '' ?>>
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
                                    <option value="<?= $supervisor['id'] ?>" <?= $jadwal['supervisor_id'] == $supervisor['id'] ? 'selected' : '' ?>>
                                        <?= $supervisor['username'] ?> (Supervisor)
                                    </option>
                                <?php endforeach; ?>
                                <?php foreach ($kepala_users as $kepala): ?>
                                    <option value="<?= $kepala['id'] ?>" <?= $jadwal['supervisor_id'] == $kepala['id'] ? 'selected' : '' ?>>
                                        <?= $kepala['username'] ?> (Kepala)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" 
                                   value="<?= $jadwal['mata_pelajaran'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kelas_id">Kelas</label>
                            <select class="form-control" id="kelas_id" name="kelas_id">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelases as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>" <?= $jadwal['kelas_id'] == $kelas['id'] ? 'selected' : '' ?>>
                                        <?= $kelas['nama_kelas'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="hari">Hari</label>
                            <select class="form-control" id="hari" name="hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin" <?= $jadwal['hari'] == 'Senin' ? 'selected' : '' ?>>Senin</option>
                                <option value="Selasa" <?= $jadwal['hari'] == 'Selasa' ? 'selected' : '' ?>>Selasa</option>
                                <option value="Rabu" <?= $jadwal['hari'] == 'Rabu' ? 'selected' : '' ?>>Rabu</option>
                                <option value="Kamis" <?= $jadwal['hari'] == 'Kamis' ? 'selected' : '' ?>>Kamis</option>
                                <option value="Jumat" <?= $jadwal['hari'] == 'Jumat' ? 'selected' : '' ?>>Jumat</option>
                                <option value="Sabtu" <?= $jadwal['hari'] == 'Sabtu' ? 'selected' : '' ?>>Sabtu</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_supervisi">Tanggal Supervisi</label>
                            <input type="date" class="form-control" id="tanggal_supervisi" name="tanggal_supervisi" 
                                   value="<?= $jadwal['tanggal_supervisi'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="jam_ke">Jam Ke-</label>
                            <input type="number" class="form-control" id="jam_ke" name="jam_ke" 
                                   value="<?= $jadwal['jam_ke'] ?>" min="1" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('/kepala/jadwal') ?>" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Jadwal</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                                    <span class="badge badge-info"><?= $jadwal['status'] ?></span>
                                <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                                    <span class="badge badge-success"><?= $jadwal['status'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= $jadwal['status'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>