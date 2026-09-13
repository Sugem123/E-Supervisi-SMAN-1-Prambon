<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Tahun Ajaran & Semester</h1>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Tahun Ajaran</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                        <i class="fas fa-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($tahun_ajar as $item) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($item['tahun_ajar']); ?></td>
                                        <td><?= esc($item['semester']); ?></td>
                                        <td>
                                            <?php if ($item['status_aktif'] == 'Aktif') : ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else : ?>
                                                <a href="<?= base_url('admin/pengaturan/tahun-ajar/activate/' . $item['id']) ?>" class="btn btn-sm btn-light border" title="Klik untuk mengaktifkan">
                                                    Non-Aktif
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                data-id="<?= $item['id'] ?>"
                                                data-tahun="<?= $item['tahun_ajar'] ?>"
                                                data-semester="<?= $item['semester'] ?>"
                                                data-status="<?= $item['status_aktif'] ?>"
                                                data-toggle="modal" data-target="#editModal">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <?php if ($item['status_aktif'] != 'Aktif') : ?>
                                                <form action="<?= base_url('admin/pengaturan/tahun-ajar/delete/' . $item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/pengaturan/tahun-ajar/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Tahun Ajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajar" placeholder="Contoh: 2024/2025" required>
                    </div>
                    <div class="form-group">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select class="form-control" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status_aktif">
                            <option value="Nonaktif">Non-Aktif</option>
                            <option value="Aktif">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Tahun Ajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajar" id="edit_tahun_ajar" required>
                    </div>
                    <div class="form-group">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select class="form-control" name="semester" id="edit_semester" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status_aktif" id="edit_status_aktif">
                            <option value="Nonaktif">Non-Aktif</option>
                            <option value="Aktif">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.btn-edit').on('click', function() {
            const id = $(this).data('id');
            const tahun = $(this).data('tahun');
            const semester = $(this).data('semester');
            const status = $(this).data('status');

            $('#editForm').attr('action', '<?= base_url('admin/pengaturan/tahun-ajar/update') ?>/' + id);
            $('#edit_tahun_ajar').val(tahun);
            $('#edit_semester').val(semester);
            $('#edit_status_aktif').val(status);
        });
    });
</script>
<?= $this->endSection(); ?>