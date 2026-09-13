<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Penilaian Supervisi</h1>
    </div>

    <!-- Teacher Info -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Guru</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Guru</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['nama_guru']) ? $schedule['nama_guru'] : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['mata_pelajaran']) ? $schedule['mata_pelajaran'] : '' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tanggal Supervisi</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['tanggal_supervisi']) ? date('d M Y', strtotime($schedule['tanggal_supervisi'])) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['kelas']) ? $schedule['kelas'] : '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-step Assessment Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Penilaian Supervisi</h6>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="assessmentTabs" role="tablist">
                <?php if (isset($jenisPenilaian) && is_array($jenisPenilaian)): ?>
                    <?php foreach ($jenisPenilaian as $index => $jenis): ?>
                        <?php 
                            $jId = isset($jenis['id']) ? $jenis['id'] : '';
                            $labels = [
                                1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                                2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                                3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                                4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
                            ];
                            $namaJenis = $jenis['nama_jenis'] ?? ($jenis['nama'] ?? ($labels[$index + 1] ?? ('Tahap ' . ($index + 1))));
                            $fullTitle = ($index + 1) . '. ' . $namaJenis;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $index == 0 ? 'active' : '' ?>" 
                               id="step-<?= $jId ?>-tab" 
                               data-toggle="tab" 
                               href="#step-<?= $jId ?>" 
                               role="tab"
                               aria-controls="step-<?= $jId ?>"
                               aria-selected="<?= $index == 0 ? 'true' : 'false' ?>"
                               data-toggle-tooltip="tooltip"
                               data-placement="top"
                               title="<?= esc($fullTitle) ?>">
                                <i class="fas fa-clipboard-check mr-1 text-primary"></i>
                                <span>Tahap <?= ($index + 1) ?></span>
                                <span class="badge-status-<?= $jId ?>">
                                    <?php if (isset($existingResults[$jId])): ?>
                                        <span class="badge badge-success ml-1">✓</span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- Tahap 5: Foto Bukti Supervisi -->
                <li class="nav-item">
                    <a class="nav-link" 
                       id="step-foto-tab" 
                       data-toggle="tab" 
                       href="#step-foto" 
                       role="tab"
                       aria-controls="step-foto"
                       aria-selected="false"
                       data-toggle-tooltip="tooltip"
                       data-placement="top"
                       title="5. FOTO BUKTI SUPERVISI">
                        <i class="fas fa-camera mr-1 text-primary"></i>
                        <span>Tahap 5</span>
                        <span class="badge-status-foto">
                            <?php if (!empty($existingPhotos)): ?>
                                <span class="badge badge-success ml-1">✓</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" id="assessmentTabsContent">
                <?php if (isset($jenisPenilaian) && is_array($jenisPenilaian)): ?>
                    <?php foreach ($jenisPenilaian as $index => $jenis): ?>
                        <div class="tab-pane fade <?= $index == 0 ? 'show active' : '' ?>" 
                             id="step-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>" 
                             role="tabpanel"
                             aria-labelledby="step-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>-tab">
                                     
                                    <h5 class="mb-4"><?= isset($jenis['nama']) ? $jenis['nama'] : '' ?></h5>
                                    
                                    <div class="alert alert-info">
                                        <strong>Fitur Edit Massal (Bulk Edit)</strong>
                                        <div class="form-row align-items-center mt-2">
                                            <div class="col-md-4">
                                                <label for="bulk-skor-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>">Set Semua Skor ke:</label>
                                                <select class="form-control bulk-skor-select" id="bulk-skor-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>" data-tab-id="<?= isset($jenis['id']) ? $jenis['id'] : '' ?>">
                                                    <option value="">-- Pilih Skor --</option>
                                                    <option value="1">1 - Kurang</option>
                                                    <option value="2">2 - Cukup</option>
                                                    <option value="3">3 - Baik</option>
                                                    <option value="4">4 - Sangat Baik</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>&nbsp;</label><br>
                                                <button type="button" class="btn btn-primary bulk-edit-apply-btn" data-tab-id="<?= isset($jenis['id']) ? $jenis['id'] : '' ?>">
                                                    Terapkan ke Semua
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <form id="form-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="jadwal_id" value="<?= isset($schedule['id']) ? $schedule['id'] : '' ?>">
                                        <input type="hidden" name="jenis_penilaian_id" value="<?= isset($jenis['id']) ? $jenis['id'] : '' ?>">
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered" style="table-layout: auto; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Aspek Penilaian</th>
                                                        <th>Skor (1-4)</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1; ?>
                                                    <?php if (isset($aspekByJenis[$jenis['id']]) && is_array($aspekByJenis[$jenis['id']])): ?>
                                                        <?php foreach ($aspekByJenis[$jenis['id']] as $aspek): ?>
                                                            <?php 
                                                                $existingDetail = isset($existingDetails[$jenis['id']][$aspek['id']]) ? $existingDetails[$jenis['id']][$aspek['id']] : null;
                                                            ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td style="white-space: normal; word-wrap: break-word;"><?= isset($aspek['nama_aspek']) ? $aspek['nama_aspek'] : '' ?></td>
                                                                <td>
                                                                    <select name="skor_<?= isset($aspek['id']) ? $aspek['id'] : '' ?>" class="form-control skala-dropdown" data-aspek="<?= isset($aspek['id']) ? $aspek['id'] : '' ?>" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="1" <?= $existingDetail && isset($existingDetail['skor']) && $existingDetail['skor'] == 1 ? 'selected' : '' ?>>1 - Kurang</option>
                                                                        <option value="2" <?= $existingDetail && isset($existingDetail['skor']) && $existingDetail['skor'] == 2 ? 'selected' : '' ?>>2 - Cukup</option>
                                                                        <option value="3" <?= $existingDetail && isset($existingDetail['skor']) && $existingDetail['skor'] == 3 ? 'selected' : '' ?>>3 - Baik</option>
                                                                        <option value="4" <?= $existingDetail && isset($existingDetail['skor']) && $existingDetail['skor'] == 4 ? 'selected' : '' ?>>4 - Sangat Baik</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <textarea name="catatan_<?= isset($aspek['id']) ? $aspek['id'] : '' ?>" class="form-control catatan-field" data-aspek="<?= isset($aspek['id']) ? $aspek['id'] : '' ?>" rows="1" placeholder="Catatan akan terisi otomatis"><?= $existingDetail && isset($existingDetail['catatan']) ? $existingDetail['catatan'] : '' ?></textarea>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="rekomendasi-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>"><strong>Rekomendasi Perbaikan</strong></label>
                                            <textarea name="rekomendasi" id="rekomendasi-<?= isset($jenis['id']) ? $jenis['id'] : '' ?>" class="form-control" rows="3"><?= isset($existingResults[$jenis['id']]['rekomendasi']) ? $existingResults[$jenis['id']]['rekomendasi'] : '' ?></textarea>
                                        </div>
                                        
                                        <!-- Navigation & Save Buttons -->
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3 mb-2">
                                            <div>
                                                <?php if ($index > 0): ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-prev-tab" data-prev-id="<?= $jenisPenilaian[$index - 1]['id'] ?>">
                                                        <i class="fas fa-arrow-left mr-1"></i> Tahap Sebelumnya
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button type="submit" class="btn btn-outline-primary btn-sm btn-save-step shadow-sm mr-2" data-next-id="<?= ($index < count($jenisPenilaian) - 1) ? $jenisPenilaian[$index + 1]['id'] : 'foto' ?>">
                                                    <i class="fas fa-save mr-1"></i> Simpan Tahap Ini
                                                </button>
                                                <?php if ($index < count($jenisPenilaian) - 1): ?>
                                                    <button type="button" class="btn btn-primary btn-sm btn-next-tab-save" data-next-id="<?= $jenisPenilaian[$index + 1]['id'] ?>">
                                                        Tahap Selanjutnya <i class="fas fa-arrow-right ml-1"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-primary btn-sm btn-next-tab-save" data-next-id="foto">
                                                        Tahap 5: Foto Bukti <i class="fas fa-arrow-right ml-1"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Tab Pane Tahap 5: Foto Bukti Supervisi -->
                        <div class="tab-pane fade <?= (isset($activeTab) && $activeTab == 'foto') ? 'show active' : '' ?>" 
                             id="step-foto" 
                             role="tabpanel" 
                             aria-labelledby="step-foto-tab">
                             
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="font-weight-bold text-gray-800 m-0">
                                    5. FOTO BUKTI SUPERVISI
                                    <span class="badge badge-info ml-2" id="photoCountBadge"><?= !empty($existingPhotos) ? count($existingPhotos) : 0 ?> / 5 Foto</span>
                                </h5>
                                <a href="<?= base_url('supervisor/foto-bukti/upload/' . (isset($schedule['id']) ? $schedule['id'] : '')) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Kelola di Halaman Khusus
                                </a>
                            </div>

                            <!-- Form Upload Cepat -->
                            <form id="quickPhotoUploadForm" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border shadow-sm">
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <label for="quickPhotos" class="font-weight-bold small text-gray-700">Pilih Foto Dokumentasi:</label>
                                        <input type="file" class="form-control-file" id="quickPhotos" name="photos[]" multiple accept="image/*" required>
                                        <small class="text-muted">Maks. 3MB per file (JPG, JPEG, PNG, WEBP)</small>
                                    </div>
                                    <div class="col-md-5 mt-2 mt-md-0">
                                        <label for="quickKeterangan" class="font-weight-bold small text-gray-700">Keterangan Foto (Opsional):</label>
                                        <input type="text" class="form-control form-control-sm" id="quickKeterangan" name="keterangan" placeholder="Contoh: Dokumentasi proses pembelajaran">
                                    </div>
                                    <div class="col-md-2 mt-3 mt-md-0 text-md-right">
                                        <button type="submit" id="btnQuickUpload" class="btn btn-primary btn-sm btn-block shadow-sm">
                                            <i class="fas fa-upload mr-1"></i> Upload Foto
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Galeri Foto Terunggah -->
                            <div id="quickPhotoGallery" class="row">
                                <?php if (!empty($existingPhotos)): ?>
                                    <?php foreach ($existingPhotos as $photo): ?>
                                        <div class="col-md-3 col-sm-6 mb-3 photo-card-<?= $photo['id'] ?>">
                                            <div class="card border h-100 shadow-sm">
                                                <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top foto-preview-item" alt="Foto Bukti" style="height: 160px; object-fit: cover; cursor: pointer;" data-src="<?= base_url($photo['file_path']) ?>" data-keterangan="<?= esc($photo['keterangan'] ?? '') ?>">
                                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                    <p class="small text-muted mb-2 text-truncate" title="<?= esc($photo['keterangan'] ?? '-') ?>">
                                                        <?= esc($photo['keterangan'] ?: 'Tanpa keterangan') ?>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <button type="button" class="btn btn-outline-info btn-xs py-1 px-2 btn-view-image" data-src="<?= base_url($photo['file_path']) ?>" data-keterangan="<?= esc($photo['keterangan'] ?? '') ?>">
                                                            <i class="fas fa-search-plus"></i> Lihat
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2 btn-delete-quick-photo" data-id="<?= $photo['id'] ?>">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-4 text-muted empty-photo-msg">
                                        <i class="fas fa-images fa-3x mb-2 text-gray-300"></i>
                                        <p class="mb-0">Belum ada foto dokumentasi yang diupload untuk supervisi ini.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Navigation CTA Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-prev-tab" data-prev-id="<?= $jenisPenilaian[count($jenisPenilaian) - 1]['id'] ?>">
                                    <i class="fas fa-arrow-left mr-1"></i> Tahap Sebelumnya
                                </button>
                                <button type="button" class="btn btn-success btn-sm btn-trigger-complete shadow-sm">
                                    <i class="fas fa-check-double mr-1"></i> Selesaikan Supervisi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="imageModalTitle">Preview Foto Bukti</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="modalPreviewImg" src="" class="img-fluid rounded" style="max-height: 75vh;">
                    <p id="modalPreviewDesc" class="mt-2 text-muted"></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Final Review and Submit -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Review dan Selesaikan</h6>
        </div>
        <div class="card-body">
            <p>Setelah menyelesaikan semua tahap penilaian, silakan klik tombol di bawah ini untuk menyelesaikan proses supervisi.</p>
            <button id="completeBtn" class="btn btn-success">Selesaikan Supervisi</button>
        </div>
    </div>

</div>

<style>
.manual-catatan {
    background-color: #fff3cd !important;
    border: 2px solid #ffeaa7 !important;
}

.skala-dropdown:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Adjust Teacher Info */
    .card-body .row .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .table-borderless td {
        display: block;
        width: 100%;
        padding: 0.25rem 0;
    }
    
    .table-borderless td:first-child {
        font-weight: bold;
        color: #858796;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    
    .table-borderless td:nth-child(2) {
        display: none; /* Hide colon */
    }
    
    .table-borderless td:last-child {
        padding-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .table-borderless tr:last-child td:last-child {
        border-bottom: none;
    }

    /* Transform Assessment Table to Cards */
    .table-responsive table, 
    .table-responsive thead, 
    .table-responsive tbody, 
    .table-responsive th, 
    .table-responsive td, 
    .table-responsive tr { 
        display: block; 
    }
    
    /* Hide table headers */
    .table-responsive thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    .table-responsive tr { 
        border: 1px solid #e3e6f0; 
        margin-bottom: 1.5rem;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        overflow: hidden;
    }
    
    .table-responsive td { 
        border: none;
        position: relative;
        padding: 1rem;
        text-align: left;
    }
    
    /* No column - hide it */
    .table-responsive td:nth-of-type(1) { 
        display: none; 
    }
    
    /* Aspek Penilaian - Card Header */
    .table-responsive td:nth-of-type(2) { 
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        color: #4e73df;
        font-weight: 700;
        font-size: 1rem;
    }
    
    /* Skor */
    .table-responsive td:nth-of-type(3) {
        padding-bottom: 0.5rem;
    }
    
    .table-responsive td:nth-of-type(3):before {
        content: "Skor:";
        display: block;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #5a5c69;
    }
    
    /* Catatan */
    .table-responsive td:nth-of-type(4) {
        padding-top: 0.5rem;
    }
    
    .table-responsive td:nth-of-type(4):before {
        content: "Catatan:";
        display: block;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #5a5c69;
    }

    /* Adjust Tabs */
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        border-bottom: 1px solid #dddfeb;
    }
    
    .nav-tabs .nav-link {
        white-space: nowrap;
        font-size: 0.9rem;
    }

    /* Bulk Edit Section */
    .alert-info .form-row {
        flex-direction: column;
    }
    
    .alert-info .col-md-4 {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .bulk-edit-apply-btn {
        width: 100%;
    }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/penilaian.js') ?>"></script>
<script>
$(document).ready(function() {
    // CSRF Token Management
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    function updateCsrfToken(newToken) {
        csrfHash = newToken;
        $('input[name="' + csrfName + '"]').val(newToken);
    }

    // Inisialisasi tooltip pada tab navigasi agar judul lengkap tampil saat hover
    $('#assessmentTabs [data-toggle="tab"]').tooltip({
        boundary: 'window',
        placement: 'top',
        trigger: 'hover'
    }).on('click', function() {
        $(this).tooltip('hide');
    });

    // Initialize penilaian elements for auto-fill functionality
    initializePenilaianElements();
    
    // Navigasi Prev Tab
    $(document).on('click', '.btn-prev-tab', function() {
        var prevId = $(this).data('prev-id');
        var targetTab = $('#step-' + prevId + '-tab');
        targetTab.tooltip('hide');
        targetTab.tab('show');
        if ($('#assessmentTabs').length) {
            $('html, body').animate({ scrollTop: $('#assessmentTabs').offset().top - 20 }, 300);
        }
    });

    // Navigasi Next Tab dengan Simpan Otomatis (Save & Next)
    $(document).on('click', '.btn-next-tab-save', function() {
        var nextId = $(this).data('next-id');
        var form = $(this).closest('form');
        form.data('next-tab-id', nextId);
        form.trigger('submit');
    });

    // Trigger Selesaikan Supervisi dari Tahap 5
    $(document).on('click', '.btn-trigger-complete', function() {
        $('#completeBtn').trigger('click');
    });

    // Handle form submission for each step
    $('form[id^="form-"]').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();
        var stepId = form.attr('id').split('-')[1];
        var submitBtn = form.find('.btn-save-step, button[type="submit"]');
        var nextBtn = form.find('.btn-next-tab-save');
        var originalBtnHtml = submitBtn.html();
        var originalNextHtml = nextBtn.html();
        
        // Disable submit button and show loading text with spinner
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        if (nextBtn.length) {
            nextBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        }
        
        $.ajax({
            url: '<?= base_url('supervisor/penilaian/save') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 30000, // 30 seconds timeout
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data penilaian berhasil disimpan',
                        timer: 1400,
                        showConfirmButton: false
                    });
                    // Mark step as completed
                    if ($('.badge-status-' + stepId).length) {
                        $('.badge-status-' + stepId).html('<span class="badge badge-success ml-1">✓</span>');
                    } else if (!$('#step-' + stepId + '-tab .badge-success').length) {
                        $('#step-' + stepId + '-tab').append(' <span class="badge badge-success ml-1">✓</span>');
                    }

                    // Auto-pindah ke tab berikutnya
                    var nextTabId = form.data('next-tab-id') || form.find('.btn-save-step').data('next-id');
                    if (nextTabId) {
                        setTimeout(function() {
                            var targetTab = $('#step-' + nextTabId + '-tab');
                            targetTab.tooltip('hide');
                            targetTab.tab('show');
                            if ($('#assessmentTabs').length) {
                                $('html, body').animate({ scrollTop: $('#assessmentTabs').offset().top - 20 }, 300);
                            }
                        }, 600);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyimpan data: ' + (response.message || 'Unknown error')
                    });
                }
                
                // Update CSRF Token
                if (response.token) {
                    updateCsrfToken(response.token);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                
                if (status === 'timeout') {
                    errorMessage = 'Waktu permintaan habis. Silakan coba lagi.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    errorMessage = 'Sesi Anda mungkin telah berakhir atau token keamanan tidak valid. Silakan muat ulang halaman.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan internal server.';
                } else {
                    errorMessage += ' ' + error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
                
                // Try to get token from JSON response if available
                if (xhr.responseJSON && xhr.responseJSON.token) {
                    updateCsrfToken(xhr.responseJSON.token);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (nextBtn.length) {
                    nextBtn.prop('disabled', false).html(originalNextHtml);
                }
            }
        });
    });
    
    // Handle bulk edit functionality
    $(document).on('click', '.bulk-edit-apply-btn', function() {
        // Ambil ID tab dari tombol yang diklik
        var tabId = $(this).data('tab-id');
        
        // Cari dropdown skor di dalam area alert yang sama
        var selectedScore = $(this).closest('.alert').find('.bulk-skor-select').val();
        
        if (!selectedScore) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih skor terlebih dahulu'
            });
            return;
        }
        
        // Tentukan area tab yang aktif
        var activeTabPane = $('#step-' + tabId);

        // Get all skala-dropdown and catatan-field elements HANYA DI DALAM TAB YANG AKTIF
        activeTabPane.find('.skala-dropdown').each(function() {
            const dropdown = $(this);
            const aspekId = dropdown.data('aspek');
            const catatanField = activeTabPane.find('.catatan-field[data-aspek="' + aspekId + '"]');
            
            // Set skor yang dipilih
            dropdown.val(selectedScore);
            
            // Isi otomatis catatan berdasarkan 'skalaConfig' dari penilaian.js
            if (typeof skalaConfig !== 'undefined' && skalaConfig[selectedScore]) {
                catatanField.val(skalaConfig[selectedScore].catatan);
                catatanField.removeClass('manual-catatan');
                if (typeof aspekStates !== 'undefined') {
                    aspekStates.set(aspekId, 'auto'); // Update state di penilaian.js
                }
            }
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Edit massal untuk tahap ini berhasil!',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Modal preview image
    $(document).on('click', '.btn-view-image, .foto-preview-item', function() {
        var src = $(this).data('src');
        var ket = $(this).data('keterangan') || 'Tanpa keterangan';
        $('#modalPreviewImg').attr('src', src);
        $('#modalPreviewDesc').text(ket);
        $('#imagePreviewModal').modal('show');
    });

    // Handle Quick Photo Upload
    $('#quickPhotoUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var fileInput = $('#quickPhotos')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih file foto terlebih dahulu.'
            });
            return;
        }

        var btn = $('#btnQuickUpload');
        var originalBtnHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengupload...');

        var formData = new FormData(this);
        formData.append(csrfName, csrfHash);

        $.ajax({
            url: '<?= base_url('supervisor/foto-bukti/upload/' . (isset($schedule['id']) ? $schedule['id'] : '')) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_token) {
                    updateCsrfToken(res.csrf_token);
                } else if (res.token) {
                    updateCsrfToken(res.token);
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Foto bukti berhasil diupload.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan saat mengupload foto.'
                    });
                    btn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan atau server: ' + error
                });
                if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
                    updateCsrfToken(xhr.responseJSON.csrf_token);
                } else if (xhr.responseJSON && xhr.responseJSON.token) {
                    updateCsrfToken(xhr.responseJSON.token);
                }
                btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // Handle Quick Photo Delete
    $(document).on('click', '.btn-delete-quick-photo', function() {
        var photoId = $(this).data('id');

        Swal.fire({
            title: 'Hapus Foto?',
            text: 'Foto dokumentasi ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('supervisor/foto-bukti/delete/') ?>/' + photoId,
                    type: 'POST',
                    data: {
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.csrf_token) {
                            updateCsrfToken(res.csrf_token);
                        } else if (res.token) {
                            updateCsrfToken(res.token);
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message || 'Foto berhasil dihapus.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            $('.photo-card-' + photoId).fadeOut(400, function() {
                                $(this).remove();
                                var totalRemaining = $('#quickPhotoGallery [class*="photo-card-"]').length;
                                $('#photoCountBadge').text(totalRemaining + ' / 5 Foto');
                                if (totalRemaining === 0) {
                                    $('#quickPhotoGallery').html(`
                                        <div class="col-12 text-center py-4 text-muted empty-photo-msg">
                                            <i class="fas fa-images fa-3x mb-2 text-gray-300"></i>
                                            <p class="mb-0">Belum ada foto dokumentasi yang diupload untuk supervisi ini.</p>
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menghapus foto.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus foto: ' + error
                        });
                        if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
                            updateCsrfToken(xhr.responseJSON.csrf_token);
                        } else if (xhr.responseJSON && xhr.responseJSON.token) {
                            updateCsrfToken(xhr.responseJSON.token);
                        }
                    }
                });
            }
        });
    });
    
    // Handle final submission
    $('#completeBtn').on('click', function() {
        Swal.fire({
            title: 'Selesaikan Supervisi?',
            text: "Apakah Anda yakin ingin menyelesaikan supervisi ini? Setelah diselesaikan, data tidak dapat diubah.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var completeBtn = $('#completeBtn');
                completeBtn.prop('disabled', true).text('Memproses...');
                
                $.ajax({
                    url: '<?= base_url('supervisor/penilaian/complete/' . (isset($schedule['id']) ? $schedule['id'] : '')) ?>',
                    method: 'POST',
                    data: {
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Supervisi berhasil diselesaikan!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menyelesaikan supervisi: ' + response.message
                            });
                            if (response.token) {
                                updateCsrfToken(response.token);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyelesaikan supervisi: ' + error
                        });
                        if (xhr.responseJSON && xhr.responseJSON.token) {
                            updateCsrfToken(xhr.responseJSON.token);
                        }
                    },
                    complete: function() {
                        completeBtn.prop('disabled', false).text('Selesaikan Supervisi');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>