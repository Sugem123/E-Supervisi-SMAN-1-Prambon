<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jadwal Supervisi</h1>
        <a href="<?= base_url('/admin/jadwal') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

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
            
            <?php if (!isset($tahun_ajars) || empty($tahun_ajars)): ?>
                <div class="alert alert-warning">
                    Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.
                </div>
            <?php else: ?>
                <form action="<?= base_url('/admin/jadwal/' . $jadwal['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tahun_ajar_id" value="<?= $jadwal['tahun_ajar_id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tahun_ajar">Tahun Ajaran</label>
                                <div class="form-control-plaintext">
                                    <?php 
                                    $tahunAjarAktif = null;
                                    foreach ($tahun_ajars as $tahun_ajar) {
                                        if ($jadwal['tahun_ajar_id'] == $tahun_ajar['id']) {
                                            $tahunAjarAktif = $tahun_ajar;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?= $tahunAjarAktif ? $tahunAjarAktif['tahun_ajar'] . ' - Semester ' . $tahunAjarAktif['semester'] : 'Tidak ditemukan' ?>
                                </div>
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
                                            <?= $supervisor['username'] ?> 
                                            <?= $supervisor['role'] == 'kepala' ? '(Kepala)' : '(Supervisor)' ?>
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
                                <select class="form-control" id="kelas_id" name="kelas_id" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($kelases as $kelas): ?>
                                        <option value="<?= $kelas['id'] ?>" <?= $jadwal['kelas_id'] == $kelas['id'] ? 'selected' : '' ?>>
                                            <?= $kelas['nama_kelas'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="materi_supervisi">Materi yang Disupervisi</label>
                                <textarea class="form-control" id="materi_supervisi" name="materi_supervisi" rows="3"><?= $jadwal['materi_supervisi'] ?? '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_ke">Jam Ke-</label>
                                <input type="number" class="form-control" id="jam_ke" name="jam_ke" 
                                       value="<?= $jadwal['jam_ke'] ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_supervisi">Tanggal Supervisi</label>
                                <input type="date" class="form-control" id="tanggal_supervisi" name="tanggal_supervisi" 
                                       value="<?= $jadwal['tanggal_supervisi'] ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <input type="text" class="form-control" id="hari" name="hari" 
                                       value="<?= $jadwal['hari'] ?>" readonly>
                                <small class="form-text text-muted">Hari akan otomatis terisi setelah memilih tanggal</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="waktu_dari">Waktu</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="time" class="form-control" id="waktu_dari" name="waktu_dari" 
                                               value="<?= $jadwal['waktu_dari'] ?? '' ?>" placeholder="Dari">
                                    </div>
                                    <div class="col">
                                        <input type="time" class="form-control" id="waktu_sampai" name="waktu_sampai" 
                                               value="<?= $jadwal['waktu_sampai'] ?? '' ?>" placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Terjadwal" <?= $jadwal['status'] == 'Terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                                    <option value="Selesai" <?= $jadwal['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Dibatalkan" <?= $jadwal['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="<?= base_url('/admin/jadwal') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('tanggal_supervisi').addEventListener('change', function() {
    const tanggal = this.value;
    if (tanggal) {
        const hari = new Date(tanggal).toLocaleDateString('id-ID', { weekday: 'long' });
        document.getElementById('hari').value = hari.charAt(0).toUpperCase() + hari.slice(1);
    } else {
        document.getElementById('hari').value = '';
    }
});
</script>
<?= $this->endSection(); ?>