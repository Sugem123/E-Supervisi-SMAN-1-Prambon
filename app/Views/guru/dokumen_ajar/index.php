<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Dokumen Ajar</h1>
        <a href="<?= base_url('guru/jadwal') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Jadwal
        </a>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- Form Input -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upload / Tautkan Dokumen</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('guru/dokumen-ajar/save') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">

                        <div class="form-group">
                            <label for="nama_dokumen">Nama Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" placeholder="Contoh: RPP Matematika Kelas X" required>
                        </div>

                        <div class="form-group">
                            <label for="link_drive">Link Google Drive</label>
                            <input type="url" class="form-control" id="link_drive" name="link_drive" placeholder="https://drive.google.com/..." required>
                            <small class="form-text text-muted">Pastikan link memiliki akses "Anyone with the link" atau "Viewer".</small>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Simpan Dokumen</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Info Jadwal</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td style="width: 40%">Tanggal</td>
                            <td>: <?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                        </tr>
                        <tr>
                            <td>Mapel</td>
                            <td>: <?= $jadwal['mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>: <?= $jadwal['kelas'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- List Dokumen -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Dokumen Ajar</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($dokumen)): ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500 mb-0">Belum ada dokumen yang ditautkan.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nama Dokumen</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dokumen as $doc): ?>
                                        <tr>
                                            <td>
                                                <strong><?= $doc['nama_dokumen'] ?></strong>
                                                <?php if ($doc['status'] == 'Ditolak' && !empty($doc['feedback'])): ?>
                                                    <div class="alert alert-danger mt-2 p-2" style="font-size: 0.85rem;">
                                                        <strong>Catatan Perbaikan:</strong><br>
                                                        <?= $doc['feedback'] ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $doc['keterangan'] ?? '-' ?></td>
                                            <td>
                                                <?php if ($doc['status'] == 'Pending'): ?>
                                                    <span class="badge badge-warning">Pending</span>
                                                <?php elseif ($doc['status'] == 'Disetujui'): ?>
                                                    <span class="badge badge-success">Disetujui</span>
                                                <?php elseif ($doc['status'] == 'Ditolak'): ?>
                                                    <span class="badge badge-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm btn-preview"
                                                    data-toggle="modal"
                                                    data-target="#previewModal"
                                                    data-url="<?= $doc['link_drive'] ?>"
                                                    data-name="<?= $doc['nama_dokumen'] ?>">
                                                    <i class="fas fa-eye"></i> Preview
                                                </button>
                                                <?php if ($doc['status'] != 'Disetujui'): ?>
                                                    <form action="<?= base_url('guru/dokumen-ajar/delete/' . $doc['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" id="previewFrame" src="" allow="autoplay"></iframe>
                </div>
                <div class="mt-3 text-center">
                    <a href="#" id="openExternalBtn" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.btn-preview').on('click', function() {
            var url = $(this).data('url');
            var name = $(this).data('name');

            // Transform URL for preview if needed (handling simple drive links)
            // Default assumes user provides direct link, but commonly drive links need /preview
            var previewUrl = url;
            if (url.includes('drive.google.com') && url.includes('/view')) {
                previewUrl = url.replace('/view', '/preview');
            } else if (url.includes('drive.google.com') && !url.includes('/preview')) {
                // Try to append preview if it looks like a file link
                // This is a naive heuristic
            }

            $('#previewModalLabel').text(name);
            $('#previewFrame').attr('src', previewUrl);
            $('#openExternalBtn').attr('href', url);
        });

        // Clear iframe when modal is closed to stop audio/video
        $('#previewModal').on('hidden.bs.modal', function() {
            $('#previewFrame').attr('src', '');
        });
    });
</script>
<?= $this->endSection() ?>