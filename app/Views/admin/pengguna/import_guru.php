<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Data Guru</h1>
        <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Import Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload File Excel</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/admin/pengguna/import-guru/process') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                
                <div class="form-group">
                    <label for="excel_file">Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                        <label class="custom-file-label" for="excel_file">Pilih file...</label>
                    </div>
                    <small class="form-text text-muted">Format yang didukung: .xls, .xlsx (Maksimal 10MB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Import Data
                </button>
                <a href="<?= base_url('/admin/pengguna/import-guru/template') ?>" class="btn btn-info">
                    <i class="fas fa-download"></i> Unduh Template
                </a>
            </form>
        </div>
    </div>

    <!-- Template Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Template Excel</h6>
        </div>
        <div class="card-body">
            <h5>Format Kolom yang Diperlukan:</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Label</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A</td>
                            <td>USERNAME</td>
                            <td>Harus unik, tidak boleh ada spasi</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>PASSWORD</td>
                            <td>Minimal 6 karakter</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td>EMAIL</td>
                            <td>Format email valid</td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>ROLE</td>
                            <td>Pilih: guru/supervisor/kepala/admin</td>
                        </tr>
                        <tr>
                            <td>E</td>
                            <td>NAMA_LENGKAP</td>
                            <td>Nama lengkap guru</td>
                        </tr>
                        <tr>
                            <td>F</td>
                            <td>NIP</td>
                            <td>18 digit (jika PNS/PPPK)</td>
                        </tr>
                        <tr>
                            <td>G</td>
                            <td>PANGKAT_GOLONGAN</td>
                            <td>Contoh: Penata Muda/III d</td>
                        </tr>
                        <tr>
                            <td>H</td>
                            <td>MATA_PELAJARAN</td>
                            <td>Mapel yang diampu</td>
                        </tr>
                        <tr>
                            <td>I</td>
                            <td>STATUS_KEPEGAWAIAN</td>
                            <td>Pilih: PNS/PPPK/GTT/PTT/Honorer/Kontrak</td>
                        </tr>
                        <tr>
                            <td>J</td>
                            <td>JENIS_PTK</td>
                            <td>Pilih: Guru/Tendik (default Guru)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h5>Contoh Validasi:</h5>
            <ul>
                <li>Username harus unik dan tidak ada spasi</li>
                <li>Password minimal 6 karakter</li>
                <li>Email harus dalam format yang valid</li>
                <li>NIP harus 18 digit untuk PNS/PPPK</li>
                <li>Status kepegawaian harus dipilih dari: PNS/PPPK/GTT/PTT/Honorer/Kontrak</li>
                <li>Jenis PTK harus Guru atau Tendik</li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
// Update file input label with selected file name
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = document.getElementById("excel_file").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>
<?= $this->endSection(); ?>