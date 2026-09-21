<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Jadwal Supervisi</h1>
        <a href="<?= base_url('/admin/jadwal') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Jadwal</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>            <?php endif; ?>
            
            <?php if (!isset($tahun_ajar)): ?>
                <div class="alert alert-warning">
                    Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.
                </div>
            <?php else: ?>
                <form action="<?= base_url('/admin/jadwal/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tahun_ajar_id" value="<?= $tahun_ajar['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tahun_ajar">Tahun Ajaran</label>
                                <div class="form-control-plaintext">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="guru_id">Guru</label>
                                <select class="form-control" id="guru_id" name="guru_id" required>
                                    <option value="">Pilih Guru</option>
                                    <?php foreach ($gurus as $guru): ?>
                                        <option value="<?= $guru['id'] ?>">
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
                                        <option value="<?= $supervisor['id'] ?>">
                                            <?= $supervisor['username'] ?> 
                                            <?= $supervisor['role'] == 'kepala' ? '(Kepala)' : '(Supervisor)' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="mata_pelajaran">Mata Pelajaran</label>
                                <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" required>
                            </div>

                            <div class="form-group">
                                <label for="mapel_id">Referensi Mapel SMA (Opsional)</label>
                                <select class="form-control" id="mapel_id" name="mapel_id">
                                    <option value="">-- Tanpa referensi mapel --</option>
                                    <?php foreach (($mapels ?? []) as $mapel): ?>
                                        <option value="<?= $mapel['id'] ?>"><?= esc($mapel['nama_mapel']) ?><?= !empty($mapel['kelompok']) ? ' (Kel. ' . esc($mapel['kelompok']) . ')' : '' ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="kelompok_id">Kelompok Supervisi (Opsional)</label>
                                <select class="form-control" id="kelompok_id" name="kelompok_id">
                                    <option value="">-- Tanpa kelompok --</option>
                                    <?php foreach (($kelompoks ?? []) as $kelompok): ?>
                                        <option value="<?= $kelompok['id'] ?>"><?= esc($kelompok['nama_kelompok']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="kelas_id">Kelas <small class="text-muted">(Kosongkan jika Tenaga Teknis / Non-KBM)</small></label>
                                <select class="form-control" id="kelas_id" name="kelas_id">
                                    <option value="">-- Non-KBM / Tendik (Tanpa Kelas) --</option>
                                    <?php foreach ($kelases as $kelas): ?>
                                        <option value="<?= $kelas['id'] ?>">
                                            <?= $kelas['nama_kelas'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="materi_supervisi">Materi yang Disupervisi</label>
                                <textarea class="form-control" id="materi_supervisi" name="materi_supervisi" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_ke">Jam Ke-</label>
                                <input type="number" class="form-control" id="jam_ke" name="jam_ke" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_supervisi">Tanggal Supervisi</label>
                                <input type="date" class="form-control" id="tanggal_supervisi" name="tanggal_supervisi" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <input type="text" class="form-control" id="hari" name="hari" readonly>
                                <small class="form-text text-muted">Hari akan otomatis terisi setelah memilih tanggal</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="waktu_dari">Waktu</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="time" class="form-control" id="waktu_dari" name="waktu_dari" placeholder="Dari">
                                    </div>
                                    <div class="col">
                                        <input type="time" class="form-control" id="waktu_sampai" name="waktu_sampai" placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalInput = document.getElementById('tanggal_supervisi');
    const hariInput = document.getElementById('hari');
    
    function getHariIndonesia(date) {
        const hari = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 
            'Kamis', 'Jumat', 'Sabtu'
        ];
        return hari[date.getDay()];
    }
    
    function updateHari() {
        const tanggalValue = tanggalInput.value;
        if (!tanggalValue) {
            hariInput.value = '';
            return;
        }

        // Parse the date parts
        const [year, month, day] = tanggalValue.split('-').map(num => parseInt(num, 10));
        
        // Create date object (month - 1 because months are 0-indexed in JavaScript)
        const tanggal = new Date(year, month - 1, day);
        
        // Validate date and update hari input
        if (tanggal instanceof Date && !isNaN(tanggal)) {
            hariInput.value = getHariIndonesia(tanggal);
        } else {
            hariInput.value = '';
            console.error('Invalid date');
        }
    }

    if (tanggalInput && hariInput) {
        // Update hari when date changes
        tanggalInput.addEventListener('change', updateHari);
        
        // Initialize if there's a pre-selected date
        if (tanggalInput.value) {
            updateHari();
        }
    }
});

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