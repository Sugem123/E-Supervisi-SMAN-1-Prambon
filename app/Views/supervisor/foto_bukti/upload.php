<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Foto Bukti Penilaian</h1>
        <a href="<?= base_url('supervisor/jadwal') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
        </a>
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
                            <td><?= $schedule['nama_guru'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= $schedule['mata_pelajaran'] ?? '' ?></td>
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
                            <td><?= $schedule['kelas'] ?? '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Upload Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload Foto Bukti</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Petunjuk:</strong>
                <ul>
                    <li>Upload maksimal 3 foto sebagai bukti pelaksanaan supervisi</li>
                    <li>Format yang diizinkan: JPG, JPEG, PNG</li>
                    <li>Ukuran maksimal per file: 2MB</li>
                </ul>
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                
                <div class="form-group">
                    <label for="photos">Pilih Foto (maksimal 3 file)</label>
                    <input type="file" class="form-control-file" id="photos" name="photos[]" multiple accept="image/*">
                    <small class="form-text text-muted">Anda dapat memilih lebih dari satu foto sekaligus (maksimal 3 foto)</small>
                </div>
                
                <div class="form-group">
                    <label for="keterangan">Keterangan Foto</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Tambahkan keterangan untuk foto yang diupload"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" id="uploadBtn">
                    <i class="fas fa-upload"></i> Upload Foto
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Photos -->
    <?php if (!empty($existingPhotos)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Foto Bukti yang Telah Diupload</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($existingPhotos as $photo): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top" alt="Foto Bukti" style="height: 200px; object-fit: cover;">
                                <div class="card-body text-center">
                                    <?php if (!empty($photo['keterangan'])): ?>
                                        <p class="card-text"><?= esc($photo['keterangan']) ?></p>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm delete-photo" data-id="<?= $photo['id'] ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle photo upload
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var uploadBtn = $('#uploadBtn');
        var originalText = uploadBtn.html();
        
        // Disable button and show loading text
        uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengupload...');
        
        $.ajax({
            url: '<?= base_url('supervisor/foto-bukti/upload/' . $schedule['id']) ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengupload foto: ' + error
                });
            },
            complete: function() {
                // Re-enable button
                uploadBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Handle photo deletion
    $('.delete-photo').on('click', function() {
        var photoId = $(this).data('id');
        var button = $(this);
        var originalText = button.html();

        Swal.fire({
            title: 'Hapus Foto?',
            text: "Apakah Anda yakin ingin menghapus foto ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button and show loading text
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '<?= base_url('supervisor/foto-bukti/delete/') ?>' + photoId,
                    method: 'POST',
                    data: {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                            // Re-enable button on error
                            button.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghapus foto: ' + error
                        });
                        // Re-enable button on error
                        button.prop('disabled', false).html(originalText);
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>