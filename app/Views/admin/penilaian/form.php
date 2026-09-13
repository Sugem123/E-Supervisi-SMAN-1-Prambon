<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Hasil Penilaian Supervisi</h1>
        <div>
            <a href="<?= base_url('admin/foto-bukti/upload/' . $schedule['id']) ?>" class="btn btn-info btn-sm mr-2 shadow-sm font-weight-bold">
                <i class="fas fa-camera mr-1"></i> Foto Bukti Supervisi
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail
            </a>
        </div>
    </div>

    <!-- Alert Info Admin -->
    <div class="alert alert-warning border-left-warning shadow-sm" role="alert">
        <i class="fas fa-user-shield mr-2"></i>
        <strong>Mode Administrator:</strong> Anda memiliki hak akses penuh untuk meninjau dan memperbarui skor, catatan, serta rekomendasi penilaian supervisi ini. Perubahan yang Anda simpan akan otomatis memperbarui nilai akhir, predikat ketercapaian, dan dicatat di Audit Log sistem.
    </div>

    <!-- Teacher & Schedule Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-1"></i> Informasi Guru & Pelaksanaan Supervisi
            </h6>
            <span class="badge badge-<?= $schedule['status'] === 'Selesai' ? 'success' : 'info' ?> px-3 py-2">
                Status: <?= esc($schedule['status']) ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td width="35%"><strong>Nama Guru</strong></td>
                            <td width="5%">:</td>
                            <td><strong><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '-' ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>NIP Guru</strong></td>
                            <td>:</td>
                            <td><?= !empty($schedule['nip_guru']) ? esc($schedule['nip_guru']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['mata_pelajaran']) && $schedule['mata_pelajaran'] !== '' ? esc($schedule['mata_pelajaran']) : (isset($schedule['guru_mata_pelajaran']) ? esc($schedule['guru_mata_pelajaran']) : '-') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Materi Supervisi</strong></td>
                            <td>:</td>
                            <td><?= !empty($schedule['materi_supervisi']) ? esc($schedule['materi_supervisi']) : '-' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td width="35%"><strong>Tahun Ajaran</strong></td>
                            <td width="5%">:</td>
                            <td><?= esc($schedule['tahun_ajar'] ?? '-') ?> (<?= esc($schedule['semester'] ?? '-') ?>)</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Supervisi</strong></td>
                            <td>:</td>
                            <td><?= !empty($schedule['tanggal_supervisi']) ? date('d M Y', strtotime($schedule['tanggal_supervisi'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['kelas']) ? esc($schedule['kelas']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Supervisor / Penilai</strong></td>
                            <td>:</td>
                            <td>
                                <span class="badge badge-light border text-dark">
                                    <i class="fas fa-user-tie mr-1"></i> <?= !empty($schedule['nama_supervisor']) ? esc($schedule['nama_supervisor']) : 'Supervisor Pembina' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-step Assessment Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks mr-1"></i> Form Penilaian Supervisi (4 Tahap / Komponen)
            </h6>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="assessmentTabs" role="tablist">
                <?php if (!empty($jenisPenilaian) && is_array($jenisPenilaian)): ?>
                    <?php foreach ($jenisPenilaian as $index => $jenis): ?>
                        <?php 
                            $jId = $jenis['id'];
                            $isTabActive = ($activeTab == $jId || ($activeTab == null && $index == 0));
                            $namaJenis = $jenis['nama'] ?? ($jenis['nama_jenis'] ?? ('Tahap ' . ($index + 1)));
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isTabActive ? 'active font-weight-bold' : '' ?>" 
                               id="step-<?= $jId ?>-tab" 
                               data-toggle="tab" 
                               href="#step-<?= $jId ?>" 
                               role="tab"
                               aria-controls="step-<?= $jId ?>"
                               aria-selected="<?= $isTabActive ? 'true' : 'false' ?>"
                               data-toggle-tooltip="tooltip"
                               data-placement="top"
                               title="<?= ($index + 1) . '. ' . esc($namaJenis) ?>">
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
                    <a class="nav-link <?= (isset($activeTab) && $activeTab == 'foto') ? 'active font-weight-bold' : '' ?>" 
                       id="step-foto-tab" 
                       data-toggle="tab" 
                       href="#step-foto" 
                       role="tab"
                       aria-controls="step-foto"
                       aria-selected="<?= (isset($activeTab) && $activeTab == 'foto') ? 'true' : 'false' ?>"
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
            <div class="tab-content pt-4" id="assessmentTabsContent">
                <?php if (!empty($jenisPenilaian) && is_array($jenisPenilaian)): ?>
                    <?php foreach ($jenisPenilaian as $index => $jenis): ?>
                        <?php 
                            $jId = $jenis['id'];
                            $isTabActive = ($activeTab == $jId || ($activeTab == null && $index == 0));
                            $namaJenis = $jenis['nama'] ?? ($jenis['nama_jenis'] ?? ('Komponen ' . $jId));
                        ?>
                        <div class="tab-pane fade <?= $isTabActive ? 'show active' : '' ?>" 
                             id="step-<?= $jId ?>" 
                             role="tabpanel"
                             aria-labelledby="step-<?= $jId ?>-tab">
                             
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="font-weight-bold text-gray-800 m-0">
                                    <?= ($index + 1) . '. ' . esc($namaJenis) ?>
                                </h5>
                                <span class="badge badge-primary px-3 py-2">
                                    Skor Maksimal per Aspek: 4
                                </span>
                            </div>

                            <!-- Bulk Edit Box -->
                            <div class="alert alert-light border shadow-sm mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold text-gray-700 mb-1">
                                            <i class="fas fa-magic mr-1 text-primary"></i> Atur Cepat Semua Skor:
                                        </label>
                                        <select class="form-control form-control-sm bulk-skor-select" id="bulk-skor-<?= $jId ?>" data-tab-id="<?= $jId ?>">
                                            <option value="">-- Pilih Skor --</option>
                                            <option value="4">4 - Sangat Baik</option>
                                            <option value="3">3 - Baik</option>
                                            <option value="2">2 - Cukup</option>
                                            <option value="1">1 - Kurang</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-md-4 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary bulk-edit-apply-btn" data-tab-id="<?= $jId ?>">
                                            <i class="fas fa-check mr-1"></i> Terapkan ke Semua Aspek
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-md-right text-muted small mt-md-4 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> Catatan akan terisi otomatis sesuai skala skor dan tetap dapat diedit secara manual.
                                    </div>
                                </div>
                            </div>

                            <!-- Assessment Form for this Step -->
                            <form id="form-<?= $jId ?>" class="form-step-penilaian">
                                <?= csrf_field() ?>
                                <input type="hidden" name="jadwal_id" value="<?= esc($schedule['id']) ?>">
                                <input type="hidden" name="jenis_penilaian_id" value="<?= esc($jId) ?>">

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover" width="100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="35%">Aspek Penilaian</th>
                                                <th width="20%">Skor (1 - 4)</th>
                                                <th width="40%">Catatan / Bukti Fisik</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($aspekByJenis[$jId])): ?>
                                                <?php $no = 1; ?>
                                                <?php foreach ($aspekByJenis[$jId] as $aspek): ?>
                                                    <?php 
                                                        $aspekId = $aspek['id'];
                                                        $existingDetail = $existingDetails[$jId][$aspekId] ?? null;
                                                        $currentSkor = $existingDetail['skor'] ?? '';
                                                        $currentCatatan = $existingDetail['catatan'] ?? '';
                                                    ?>
                                                    <tr>
                                                        <td class="text-center align-middle"><?= $no++ ?></td>
                                                        <td class="align-middle">
                                                            <strong><?= esc($aspek['nama_aspek']) ?></strong>
                                                        </td>
                                                        <td class="align-middle">
                                                            <select name="skor_<?= $aspekId ?>" 
                                                                    class="form-control form-control-sm skala-dropdown" 
                                                                    data-aspek="<?= $aspekId ?>"
                                                                    data-tab-id="<?= $jId ?>" 
                                                                    required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="4" <?= $currentSkor == 4 ? 'selected' : '' ?>>4 - Sangat Baik</option>
                                                                <option value="3" <?= $currentSkor == 3 ? 'selected' : '' ?>>3 - Baik</option>
                                                                <option value="2" <?= $currentSkor == 2 ? 'selected' : '' ?>>2 - Cukup</option>
                                                                <option value="1" <?= $currentSkor == 1 ? 'selected' : '' ?>>1 - Kurang</option>
                                                            </select>
                                                        </td>
                                                        <td class="align-middle">
                                                            <textarea name="catatan_<?= $aspekId ?>" 
                                                                      class="form-control form-control-sm catatan-field" 
                                                                      data-aspek="<?= $aspekId ?>" 
                                                                      rows="2" 
                                                                      placeholder="Tulis catatan atau bukti fisik..."><?= esc($currentCatatan) ?></textarea>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">
                                                        Tidak ada aspek penilaian pada komponen ini.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Rekomendasi Section -->
                                <div class="form-group mb-4 bg-light p-3 rounded border">
                                    <label for="rekomendasi-<?= $jId ?>" class="font-weight-bold text-gray-800">
                                        <i class="fas fa-comment-dots text-primary mr-1"></i> Rekomendasi Perbaikan untuk <?= esc($namaJenis) ?>:
                                    </label>
                                    <textarea name="rekomendasi" 
                                              id="rekomendasi-<?= $jId ?>" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Tuliskan rekomendasi atau saran perbaikan untuk komponen penilaian ini..."><?= esc($existingResults[$jId]['rekomendasi'] ?? '') ?></textarea>
                                </div>

                                <!-- Navigation & Save Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
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
                        <a href="<?= base_url('admin/foto-bukti/upload/' . $schedule['id']) ?>" class="btn btn-outline-primary btn-sm">
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
                                        <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top foto-preview-item" alt="Foto Bukti" style="height: 160px; object-fit: cover; cursor: pointer;" onclick="window.open('<?= base_url($photo['file_path']) ?>', '_blank')">
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <p class="small text-muted mb-2 text-truncate" title="<?= esc($photo['keterangan'] ?? '-') ?>">
                                                <?= esc($photo['keterangan'] ?: 'Tanpa keterangan') ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url($photo['file_path']) ?>" target="_blank" class="btn btn-outline-info btn-xs py-1 px-2">
                                                    <i class="fas fa-search-plus"></i> Lihat
                                                </a>
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

    <!-- Final Save & Complete Card -->
    <div class="card shadow mb-5 border-left-success">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-check-circle mr-1"></i> Selesaikan dan Perbarui Rekapitulasi Supervisi
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-1 text-gray-800">
                        Klik tombol di samping untuk menghitung ulang seluruh skor, menetapkan predikat kualifikasi baru, dan menyimpan pembaruan hasil supervisi.
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Anda akan langsung dialihkan kembali ke halaman <strong>Detail Hasil Supervisi</strong> dengan data terbaru.
                    </small>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <button type="button" id="btnCompleteSupervisi" class="btn btn-success btn-lg shadow-sm" data-jadwal-id="<?= esc($schedule['id']) ?>">
                        <i class="fas fa-check-double mr-1"></i> Simpan & Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // CSRF Management
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    function updateCsrf(newToken) {
        if (newToken) {
            csrfHash = newToken;
            $('input[name="' + csrfName + '"]').val(newToken);
        }
    }

    // Inisialisasi tooltip pada tab navigasi agar judul lengkap tampil saat hover
    $('#assessmentTabs [data-toggle="tab"]').tooltip({
        boundary: 'window',
        placement: 'top',
        trigger: 'hover'
    }).on('click', function() {
        $(this).tooltip('hide');
    });

    // Config standar teks catatan otomatis berdasarkan skala 1 - 4
    const skalaConfig = {
        1: { kategori: "Kurang", catatan: "Kurang, Tidak memiliki bukti dukung." },
        2: { kategori: "Cukup", catatan: "Cukup, Memiliki bukti dukung, tetapi belum lengkap." },
        3: { kategori: "Baik", catatan: "Baik, Memiliki bukti dukung yang lengkap, namun belum sepenuhnya sesuai." },
        4: { kategori: "Sangat Baik", catatan: "Sangat Baik, Memiliki bukti dukung yang lengkap dan sepenuhnya sesuai." }
    };

    // Auto-fill catatan saat dropdown skor berubah
    $(document).on('change', '.skala-dropdown', function() {
        var dropdown = $(this);
        var aspekId = dropdown.data('aspek');
        var score = dropdown.val();
        var catatanField = dropdown.closest('tr').find('.catatan-field');

        if (score && skalaConfig[score]) {
            // Jika catatan kosong atau cocok dengan default catatan lain, perbarui otomatis
            var currentVal = catatanField.val().trim();
            var isDefault = currentVal === "" || 
                            currentVal === skalaConfig[1].catatan || 
                            currentVal === skalaConfig[2].catatan || 
                            currentVal === skalaConfig[3].catatan || 
                            currentVal === skalaConfig[4].catatan;

            if (isDefault) {
                catatanField.val(skalaConfig[score].catatan);
            }
        }
    });

    // Bulk edit per tab
    $(document).on('click', '.bulk-edit-apply-btn', function() {
        var tabId = $(this).data('tab-id');
        var select = $('#bulk-skor-' + tabId);
        var score = select.val();

        if (!score) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih skor terlebih dahulu pada dropdown!'
            });
            return;
        }

        var activePane = $('#step-' + tabId);
        activePane.find('.skala-dropdown').each(function() {
            var dropdown = $(this);
            dropdown.val(score);
            var catatanField = dropdown.closest('tr').find('.catatan-field');
            if (skalaConfig[score]) {
                catatanField.val(skalaConfig[score].catatan);
            }
        });

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Semua aspek pada komponen ini telah diatur ke skor ' + score + '.',
            timer: 1500,
            showConfirmButton: false
        });
    });

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
        form.data('trigger-next', true);
        form.trigger('submit');
    });

    // Tombol trigger Selesaikan Supervisi dari Tahap 5
    $(document).on('click', '.btn-trigger-complete', function() {
        $('#btnCompleteSupervisi').trigger('click');
    });

    // Handle AJAX form submission per tab
    $('.form-step-penilaian').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = form.find('.btn-save-step');
        var nextBtn = form.find('.btn-next-tab-save');
        var originalBtnHtml = submitBtn.html();
        var originalNextHtml = nextBtn.html();
        var tabId = form.find('input[name="jenis_penilaian_id"]').val();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        if (nextBtn.length) {
            nextBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        }

        // Pastikan CSRF token terbaru terkirim
        form.find('input[name="' + csrfName + '"]').val(csrfHash);

        $.ajax({
            url: '<?= base_url('admin/penilaian/save') ?>',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.token) {
                    updateCsrf(res.token);
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: res.message || 'Penilaian tahap ini berhasil disimpan.',
                        timer: 1400,
                        showConfirmButton: false
                    });

                    // Update badge checkmark pada tab header
                    $('.badge-status-' + tabId).html('<span class="badge badge-success ml-1">✓</span>');

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
                        title: 'Gagal Menyimpan',
                        text: res.message || 'Terjadi kesalahan saat menyimpan data.'
                    });
                }
            },
            error: function(xhr, status, error) {
                let msg = 'Terjadi kesalahan pada server: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error Server',
                    text: msg
                });
                if (xhr.responseJSON && xhr.responseJSON.token) {
                    updateCsrf(xhr.responseJSON.token);
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

    // Handle tombol Complete / Selesaikan Supervisi
    $('#btnCompleteSupervisi').on('click', function() {
        var jadwalId = $(this).data('jadwal-id');

        Swal.fire({
            title: 'Simpan & Selesaikan?',
            text: 'Sistem akan menghitung ulang seluruh skor penilaian dan memperbarui rekapitulasi supervisi ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Simpan Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var btn = $('#btnCompleteSupervisi');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

                $.ajax({
                    url: '<?= base_url('admin/penilaian/complete') ?>/' + jadwalId,
                    method: 'POST',
                    data: {
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.token) {
                            updateCsrf(res.token);
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Diperbarui!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = res.redirect || '<?= base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal memperbarui hasil supervisi.'
                            });
                            btn.prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> Simpan & Selesaikan');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan: ' + error
                        });
                        btn.prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> Simpan & Selesaikan');
                    }
                });
            }
        });
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
            url: '<?= base_url('admin/foto-bukti/upload/' . $schedule['id']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.token) {
                    updateCsrf(res.token);
                } else if (res.csrf_token) {
                    updateCsrf(res.csrf_token);
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
                if (xhr.responseJSON && xhr.responseJSON.token) {
                    updateCsrf(xhr.responseJSON.token);
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
                    url: '<?= base_url('admin/foto-bukti/delete/') ?>/' + photoId,
                    type: 'POST',
                    data: {
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.token) {
                            updateCsrf(res.token);
                        } else if (res.csrf_token) {
                            updateCsrf(res.csrf_token);
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
                        if (xhr.responseJSON && xhr.responseJSON.token) {
                            updateCsrf(xhr.responseJSON.token);
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
