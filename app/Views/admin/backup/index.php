<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Cadangan & Pemulihan Database</h1>
            <p class="text-muted small mb-0">Kelola arsip cadangan (backup) dan pemulihan data (restore) sistem supervisi madrasah.</p>
        </div>
        <div>
            <a href="<?= base_url('/admin/pengaturan') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-cogs mr-1"></i> Pengaturan Sistem
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php
        $totalFiles = count($backups);
        $totalSizeBytes = array_sum(array_column($backups, 'size'));
        $sizeUnits = ['B', 'KB', 'MB', 'GB'];
        $sIdx = 0;
        $formattedTotalSize = $totalSizeBytes;
        while ($formattedTotalSize >= 1024 && $sIdx < count($sizeUnits) - 1) {
            $formattedTotalSize /= 1024;
            $sIdx++;
        }
        $latestBackupTime = !empty($backups) ? $backups[0]['created_at'] : null;
    ?>

    <!-- Metric Cards -->
    <div class="row mb-4">
        <!-- Total File Backup -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Arsip Cadangan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalFiles ?> Berkas</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ukuran Penyimpanan -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ruang Penyimpanan Terpakai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $totalFiles > 0 ? round($formattedTotalSize, 2) . ' ' . $sizeUnits[$sIdx] : '0 MB' ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Terakhir -->
        <div class="col-xl-4 col-md-12 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cadangan Terakhir</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                <?php if ($latestBackupTime): ?>
                                    <?= date('d M Y, H:i', strtotime($latestBackupTime)) ?> WIB
                                <?php else: ?>
                                    <span class="text-muted font-italic font-weight-normal">Belum pernah dibuat</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards: Backup & Restore -->
    <div class="row">
        <!-- Card Buat Backup -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white p-2 mr-2" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-primary">Buat Cadangan Database (Backup)</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <p class="text-gray-700 mb-3">
                            Sistem akan mengekspor seluruh struktur tabel, data penilaian, jadwal supervisi, instrumen, akun pengguna, dan konfigurasi madrasah ke dalam format arsip terkompresi <code>.zip</code>.
                        </p>
                        <div class="alert alert-light border small text-muted mb-4">
                            <i class="fas fa-lightbulb text-warning mr-1"></i>
                            <strong>Saran Praktis:</strong> Lakukan pencadangan secara berkala sebelum pergantian semester atau sebelum melakukan perubahan data besar.
                        </div>
                    </div>

                    <form action="<?= base_url('admin/backup/create') ?>" method="post" onsubmit="return confirm('Mulai proses pembuatan backup database sekarang?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-block shadow-sm py-2 font-weight-bold">
                            <i class="fas fa-download mr-1"></i> Buat Cadangan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Pulihkan Database -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center">
                    <div class="rounded-circle bg-warning text-dark p-2 mr-2" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-gray-850">Pulihkan Database (Restore)</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <p class="text-gray-700 mb-2">
                            Pulihkan database dari file cadangan sebelumnya (format <code>.sql</code> atau <code>.zip</code>).
                        </p>
                        <div class="alert alert-danger small mb-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Peringatan Penting:</strong> Proses restore akan <strong>menimpa data yang sedang berjalan</strong> dengan isi file cadangan. Pastikan Anda telah membuat cadangan terbaru sebelum melanjutkan.
                        </div>
                    </div>

                    <form action="<?= base_url('admin/backup/restore') ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN TINGKAT TINGGI: Seluruh data saat ini akan ditimpa oleh data dari berkas cadangan terpilih. Apakah Anda yakin ingin melanjutkan?')">
                        <?= csrf_field() ?>
                        <div class="form-group mb-3">
                            <label for="backup_file" class="small font-weight-bold text-gray-700">Pilih Berkas Cadangan (.sql / .zip)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql,.zip" required>
                                <label class="custom-file-label text-truncate" for="backup_file">Pilih berkas arsip...</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block shadow-sm py-2 font-weight-bold text-dark">
                            <i class="fas fa-undo-alt mr-1"></i> Pulihkan Database Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Cadangan -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-alt mr-1"></i> Riwayat Berkas Cadangan di Server
            </h6>
            <span class="badge badge-light border text-muted small">
                Tersimpan di: <code>writable/backups/</code>
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($backups)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                    <p class="mb-1 font-weight-bold">Belum ada berkas cadangan database yang tersimpan.</p>
                    <small>Klik tombol <strong>"Buat Cadangan Sekarang"</strong> di atas untuk membuat cadangan pertama.</small>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th width="32%">Nama Berkas Cadangan</th>
                                <th width="12%">Ukuran</th>
                                <th width="18%">Waktu Dibuat</th>
                                <th width="14%">Pembuat</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($backups as $backup): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2 text-primary">
                                                <i class="fas fa-file-archive fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="text-gray-800"><?= esc($backup['filename']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                            $size = $backup['size'];
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $i = 0;
                                            while ($size >= 1024 && $i < count($units) - 1) {
                                                $size /= 1024;
                                                $i++;
                                            }
                                        ?>
                                        <span class="badge badge-light border text-gray-800 font-weight-bold">
                                            <?= round($size, 2) . ' ' . $units[$i] ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <i class="far fa-clock mr-1 text-muted"></i>
                                        <?= date('d M Y, H:i', strtotime($backup['created_at'])) ?> WIB
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-user-shield mr-1"></i> <?= esc($backup['created_by'] ?? 'System') ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button"
                                                    class="btn btn-warning btn-restore-existing text-dark font-weight-bold"
                                                    data-filename="<?= esc($backup['filename']) ?>"
                                                    data-date="<?= date('d M Y, H:i', strtotime($backup['created_at'])) ?>"
                                                    title="Pulihkan Database dari Berkas Ini">
                                                <i class="fas fa-undo-alt mr-1"></i> Pulihkan
                                            </button>
                                            <a href="<?= base_url('admin/backup/download/' . $backup['filename']) ?>"
                                               class="btn btn-success"
                                               title="Unduh Berkas Cadangan">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="<?= base_url('admin/backup/delete/' . $backup['filename']) ?>"
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus berkas cadangan ini dari server?')"
                                               title="Hapus Berkas">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Konfirmasi Restore Berkas Server -->
    <div class="modal fade" id="modalConfirmRestoreExisting" tabindex="-1" role="dialog" aria-labelledby="modalRestoreTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form id="formRestoreExisting" action="" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold" id="modalRestoreTitle">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Pemulihan Database
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger mb-3">
                            <strong>PERINGATAN KRITIS:</strong> Seluruh data sistem saat ini akan <strong>ditimpa total</strong> dengan data dari berkas cadangan terpilih.
                        </div>
                        <p class="text-gray-800 mb-2">Anda akan memulihkan database dari berkas:</p>
                        <div class="bg-light p-3 rounded border mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-file-archive text-warning mr-2"></i>
                                <span class="font-weight-bold text-dark text-break" id="restoreModalFilename">-</span>
                            </div>
                            <small class="text-muted" id="restoreModalDate">Dibuat pada: -</small>
                        </div>
                        <p class="small text-muted mb-0">
                            Pastikan Anda telah mencadangkan data terkini jika ada perubahan penting sebelum melanjutkan proses pemulihan.
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-dark font-weight-bold">
                            <i class="fas fa-undo-alt mr-1"></i> Ya, Pulihkan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update label nama file saat file dipilih pada custom input
        var fileInput = document.getElementById('backup_file');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih berkas arsip...';
                var label = document.querySelector('label[for="backup_file"]');
                if (label) {
                    label.textContent = fileName;
                }
            });
        }

        // Modal konfirmasi restore berkas server
        var restoreButtons = document.querySelectorAll('.btn-restore-existing');
        var formElement = document.getElementById('formRestoreExisting');
        var filenameDisplay = document.getElementById('restoreModalFilename');
        var dateDisplay = document.getElementById('restoreModalDate');

        restoreButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var filename = this.getAttribute('data-filename');
                var date = this.getAttribute('data-date');

                filenameDisplay.textContent = filename;
                dateDisplay.textContent = 'Dibuat pada: ' + date + ' WIB';
                formElement.action = '<?= base_url('admin/backup/restore-file') ?>/' + encodeURIComponent(filename);

                if (window.jQuery) {
                    $('#modalConfirmRestoreExisting').modal('show');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>