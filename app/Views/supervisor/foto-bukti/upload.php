<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Foto Bukti Supervisi</h1>
        <a href="<?= base_url('supervisor/jadwal/' . $schedule['id']) ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
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
                                    <td><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Mata Pelajaran</strong></td>
                                    <td>:</td>
                                    <td><?= isset($schedule['mata_pelajaran']) ? esc($schedule['mata_pelajaran']) : '' ?></td>
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
                                    <td><?= isset($schedule['kelas']) ? esc($schedule['kelas']) : '' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upload Foto Bukti</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('supervisor/foto-bukti/upload/' . $schedule['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
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
                                        <img src="<?= base_url($photo['file_path']) ?>" 
                                             class="card-img-top foto-preview" alt="Foto Bukti" 
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             data-toggle="modal" data-target="#imageModal" 
                                             data-src="<?= base_url($photo['file_path']) ?>">
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
        
        <div class="col-lg-4">
            <!-- Schedule Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Jam Ke-</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['jam_ke']) ? esc($schedule['jam_ke']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
                            <td>
                                <?php if (isset($schedule['status'])): ?>
                                    <?php if ($schedule['status'] == 'Terjadwal'): ?>
                                        <span class="badge badge-info"><?= $schedule['status'] ?></span>
                                    <?php elseif ($schedule['status'] == 'Selesai'): ?>
                                        <span class="badge badge-success"><?= $schedule['status'] ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= $schedule['status'] ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Preview Foto Bukti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Preview Foto Bukti" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image click for preview
    const images = document.querySelectorAll('.foto-preview');
    const modalImage = document.getElementById('modalImage');
    
    images.forEach(function(img) {
        img.addEventListener('click', function() {
            const src = this.getAttribute('data-src');
            modalImage.src = src;
        });
    });
    
    // Delete photo functionality
    const deleteButtons = document.querySelectorAll('.delete-photo');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const photoId = this.getAttribute('data-id');
            if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                // Send AJAX request to delete photo
                fetch(`<?= base_url('supervisor/foto-bukti/delete') ?>/${photoId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Gagal menghapus foto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus foto');
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>