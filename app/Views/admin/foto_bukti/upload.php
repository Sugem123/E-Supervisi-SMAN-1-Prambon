<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Foto Bukti Supervisi</h1>
        <div>
            <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail
            </a>
        </div>
    </div>

    <!-- Info Admin Alert -->
    <div class="alert alert-info border-left-info shadow-sm" role="alert">
        <i class="fas fa-camera mr-2"></i>
        <strong>Kelola Foto Dokumentasi Supervisi:</strong> Unggah foto dokumentasi kegiatan supervisi kelas/pembelajaran. Foto ini akan otomatis ditampilkan pada laporan detail dan lampiran cetak PDF hasil supervisi.
    </div>

    <div class="row">
        <!-- Kolom Kiri: Form Upload & Foto yang sudah ada -->
        <div class="col-lg-8">

            <!-- Upload Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-upload mr-1"></i> Form Upload Foto Bukti
                    </h6>
                    <span class="badge badge-light border text-muted">
                        Maks. 5 Foto Total (Format: JPG, PNG, WEBP, Maks. 3MB)
                    </span>
                </div>
                <div class="card-body">
                    <form id="uploadFotoForm" action="<?= base_url('admin/foto-bukti/upload/' . $schedule['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="form-group mb-3">
                            <label for="photos" class="font-weight-bold text-gray-800">
                                Pilih Berkas Foto:
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photos" name="photos[]" multiple accept="image/*" required>
                                <label class="custom-file-label" for="photos" id="customFileLabel">Pilih satu atau beberapa foto...</label>
                            </div>
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle mr-1"></i> Anda dapat memilih lebih dari satu foto sekaligus dengan menahan tombol Ctrl/Shift saat memilih file.
                            </small>
                        </div>

                        <!-- Preview thumbnail container -->
                        <div id="previewContainer" class="row mb-3 d-none">
                            <div class="col-12">
                                <label class="small font-weight-bold text-gray-700">Preview Berkas yang Dipilih:</label>
                                <div id="previewList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="keterangan" class="font-weight-bold text-gray-800">
                                Keterangan / Deskripsi Foto (Opsional):
                            </label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Kegiatan pembukaan pembelajaran, observasi diskusi kelompok siswa di kelas..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary font-weight-bold shadow-sm" id="btnSubmitUpload">
                                <i class="fas fa-cloud-upload-alt mr-1"></i> Unggah Foto Bukti
                            </button>
                            <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) ?>" class="text-muted small">
                                Selesai & Kembali ke Detail <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Photos Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-images mr-1"></i> Foto Bukti yang Telah Diunggah
                    </h6>
                    <span class="badge badge-primary">
                        <?= count($existingPhotos) ?> Foto
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($existingPhotos)): ?>
                        <div class="row">
                            <?php foreach ($existingPhotos as $photo): ?>
                                <div class="col-md-6 mb-4" id="photo-card-<?= $photo['id'] ?>">
                                    <div class="card h-100 border shadow-sm">
                                        <div style="position: relative; overflow: hidden; background: #f8f9fa;">
                                            <img src="<?= base_url($photo['file_path']) ?>" 
                                                 class="card-img-top foto-preview" 
                                                 alt="Foto Bukti Supervisi" 
                                                 style="height: 200px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                                 data-toggle="modal" 
                                                 data-target="#imageModal" 
                                                 data-src="<?= base_url($photo['file_path']) ?>"
                                                 title="Klik untuk memperbesar foto">
                                            <div style="position: absolute; top: 10px; right: 10px;">
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm shadow btn-delete-photo rounded-circle" 
                                                        style="width: 32px; height: 32px; padding: 0;"
                                                        data-id="<?= $photo['id'] ?>"
                                                        title="Hapus foto ini">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <?php if (!empty($photo['keterangan'])): ?>
                                                <p class="card-text text-dark small mb-2"><?= esc($photo['keterangan']) ?></p>
                                            <?php else: ?>
                                                <p class="card-text text-muted font-italic small mb-2">Tanpa keterangan</p>
                                            <?php endif; ?>
                                            <div class="text-muted small mt-auto border-top pt-2 d-flex justify-content-between">
                                                <span><i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y H:i', strtotime($photo['created_at'])) ?></span>
                                                <a href="<?= base_url($photo['file_path']) ?>" target="_blank" class="text-primary" title="Buka gambar tab baru">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">Belum ada foto bukti supervisi yang diunggah untuk jadwal ini.</p>
                            <small class="text-muted">Gunakan form di atas untuk mengunggah foto dokumentasi kegiatan.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Informasi Jadwal & Guru -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Informasi Guru & Jadwal
                    </h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Nama Guru</td>
                            <td width="5%">:</td>
                            <td><strong><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '-' ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIP Guru</td>
                            <td>:</td>
                            <td><?= !empty($schedule['nip_guru']) ? esc($schedule['nip_guru']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Mata Pelajaran</td>
                            <td>:</td>
                            <td><?= esc($schedule['mata_pelajaran'] ?? ($schedule['guru_mata_pelajaran'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kelas</td>
                            <td>:</td>
                            <td><?= esc($schedule['kelas'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Ajaran</td>
                            <td>:</td>
                            <td><?= esc($schedule['tahun_ajar'] ?? '-') ?> (<?= esc($schedule['semester'] ?? '-') ?>)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>:</td>
                            <td><?= !empty($schedule['tanggal_supervisi']) ? date('d M Y', strtotime($schedule['tanggal_supervisi'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>:</td>
                            <td><?= !empty($schedule['waktu_dari']) ? ($schedule['waktu_dari'] . ' - ' . ($schedule['waktu_sampai'] ?? '')) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Supervisor</td>
                            <td>:</td>
                            <td>
                                <span class="badge badge-light border text-dark">
                                    <i class="fas fa-user-tie mr-1"></i> <?= esc($schedule['nama_supervisor'] ?? 'Supervisor Pembina') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>:</td>
                            <td>
                                <span class="badge badge-<?= ($schedule['status'] ?? '') === 'Selesai' ? 'success' : 'info' ?> px-2 py-1">
                                    <?= esc($schedule['status'] ?? '-') ?>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <div class="text-center">
                        <a href="<?= base_url('admin/penilaian/form/' . $schedule['id']) ?>" class="btn btn-outline-warning btn-sm btn-block font-weight-bold mb-2">
                            <i class="fas fa-edit mr-1"></i> Buka Form Nilai Supervisi
                        </a>
                        <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) ?>" class="btn btn-outline-secondary btn-sm btn-block">
                            <i class="fas fa-eye mr-1"></i> Lihat Laporan Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="imageModalLabel">Preview Foto Bukti</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2 bg-light">
                <img id="modalImage" src="" alt="Preview Foto Bukti" class="img-fluid rounded" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    function updateCsrf(token) {
        if (token) {
            csrfHash = token;
            $('input[name="' + csrfName + '"]').val(token);
        }
    }

    // Modal Image Preview
    $(document).on('click', '.foto-preview', function() {
        var src = $(this).attr('data-src');
        $('#modalImage').attr('src', src);
    });

    // Custom file label & preview thumbnails
    $('#photos').on('change', function() {
        var files = this.files;
        if (files.length > 0) {
            if (files.length === 1) {
                $('#customFileLabel').text(files[0].name);
            } else {
                $('#customFileLabel').text(files.length + ' foto dipilih');
            }

            $('#previewList').empty();
            $('#previewContainer').removeClass('d-none');

            Array.from(files).forEach(function(file) {
                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgHtml = '<div class="mr-2 mb-2 border rounded p-1 bg-white" style="width: 80px; height: 80px; overflow: hidden; display: inline-block;">' +
                                      '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">' +
                                      '</div>';
                        $('#previewList').append(imgHtml);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            $('#customFileLabel').text('Pilih satu atau beberapa foto...');
            $('#previewContainer').addClass('d-none');
            $('#previewList').empty();
        }
    });

    // AJAX Form Upload
    $('#uploadFotoForm').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var submitBtn = $('#btnSubmitUpload');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunggah...');

        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.token) {
                    updateCsrf(res.token);
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Upload',
                        text: res.message || 'Terjadi kesalahan saat mengunggah foto.'
                    });
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr, status, error) {
                var msg = 'Terjadi kesalahan server: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // AJAX Delete Photo
    $(document).on('click', '.btn-delete-photo', function() {
        var photoId = $(this).data('id');

        Swal.fire({
            title: 'Hapus Foto Bukti?',
            text: 'Foto ini akan dihapus secara permanen dari server dan laporan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/foto-bukti/delete') ?>/' + photoId,
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
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1400,
                                showConfirmButton: false
                            });
                            $('#photo-card-' + photoId).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menghapus foto bukti.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus foto: ' + error
                        });
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
