<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dokumen Ajar Guru</h1>
        <a href="<?= base_url('kepala/jadwal') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Jadwal
        </a>
    </div>

    <div class="row">
        <!-- Info Card -->
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Supervisi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="font-weight-bold" style="width: 200px">Guru</td>
                            <td>: <?= $jadwal['nama_guru'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tanggal</td>
                            <td>: <?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Mapel</td>
                            <td>: <?= $jadwal['mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Kelas</td>
                            <td>: <?= $jadwal['nama_kelas'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Document List -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Dokumen</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($dokumen)): ?>
                        <div class="alert alert-info text-center">
                            Guru belum mengunggah dokumen ajar untuk jadwal ini.
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
                                                <a href="<?= $doc['link_drive'] ?>" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-external-link-alt"></i> Buka
                                                </a>

                                                <button type="button" class="btn btn-success btn-sm btn-action"
                                                    data-id="<?= $doc['id'] ?>" data-action="Disetujui">
                                                    <i class="fas fa-check"></i> Setuju
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-action"
                                                    data-id="<?= $doc['id'] ?>" data-action="Ditolak">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
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

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">Verifikasi Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="actionForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="status" id="statusInput">
                    <p id="actionMessage"></p>
                    <div class="form-group" id="feedbackGroup" style="display: none;">
                        <label for="feedback">Catatan / Feedback</label>
                        <textarea class="form-control" name="feedback" id="feedback" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitActionBtn">Simpan</button>
                </div>
            </form>
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

            var previewUrl = url;
            if (url.includes('drive.google.com') && url.includes('/view')) {
                previewUrl = url.replace('/view', '/preview');
            }

            $('#previewModalLabel').text(name);
            $('#previewFrame').attr('src', previewUrl);
            $('#openExternalBtn').attr('href', url);
        });

        $('#previewModal').on('hidden.bs.modal', function() {
            $('#previewFrame').attr('src', '');
        });

        $('.btn-action').on('click', function() {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var formAction = '<?= base_url("kepala/dokumen-ajar/verify/") ?>' + id;

            $('#actionForm').attr('action', formAction);
            $('#statusInput').val(action);
            $('#actionModal').modal('show');

            if (action === 'Ditolak') {
                $('#actionMessage').text('Apakah Anda yakin ingin MENOLAK dokumen ini? Silakan berikan catatan.');
                $('#feedbackGroup').show();
                $('#feedback').attr('required', true);
                $('#submitActionBtn').removeClass('btn-success').addClass('btn-danger').text('Tolak Dokumen');
            } else {
                $('#actionMessage').text('Apakah Anda yakin ingin MENYETUJUI dokumen ini?');
                $('#feedbackGroup').hide();
                $('#feedback').removeAttr('required');
                $('#submitActionBtn').removeClass('btn-danger').addClass('btn-success').text('Setujui Dokumen');
            }
        });
    });
</script>
<?= $this->endSection() ?>