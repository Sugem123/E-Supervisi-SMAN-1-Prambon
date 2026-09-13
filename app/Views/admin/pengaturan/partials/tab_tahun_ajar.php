<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt mr-2"></i>Daftar Tahun Ajaran & Semester</h6>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTahunModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Tahun Ajaran
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTableTahunAjar" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($tahun_ajar as $item) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= esc($item['tahun_ajar']); ?></strong></td>
                                    <td><?= esc($item['semester']); ?></td>
                                    <td>
                                        <?php if ($item['status_aktif'] == 'Aktif') : ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                        <?php else : ?>
                                            <a href="<?= base_url('admin/pengaturan/tahun-ajar/activate/' . $item['id']) ?>" class="btn btn-sm btn-outline-secondary py-0" title="Klik untuk mengaktifkan">
                                                Non-Aktif
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm btn-edit-tahun"
                                            data-id="<?= $item['id'] ?>"
                                            data-tahun="<?= esc($item['tahun_ajar']) ?>"
                                            data-semester="<?= esc($item['semester']) ?>"
                                            data-status="<?= esc($item['status_aktif']) ?>"
                                            data-toggle="modal" data-target="#editTahunModal" title="Edit Data">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <?php if ($item['status_aktif'] != 'Aktif') : ?>
                                            <form action="<?= base_url('admin/pengaturan/tahun-ajar/delete/' . $item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
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

<!-- Add Modal -->
<div class="modal fade" id="addTahunModal" tabindex="-1" role="dialog" aria-labelledby="addTahunModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/pengaturan/tahun-ajar/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="addTahunModalLabel">Tambah Tahun Ajaran</h5>
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
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editTahunModal" tabindex="-1" role="dialog" aria-labelledby="editTahunModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editFormTahun" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editTahunModalLabel">Edit Tahun Ajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajar" id="edit_tahun_ajar_val" required>
                    </div>
                    <div class="form-group">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select class="form-control" name="semester" id="edit_semester_val" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status_aktif" id="edit_status_aktif_val">
                            <option value="Nonaktif">Non-Aktif</option>
                            <option value="Aktif">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '.btn-edit-tahun', function() {
        var id = $(this).data('id');
        var tahun = $(this).data('tahun');
        var semester = $(this).data('semester');
        var status = $(this).data('status');

        $('#editFormTahun').attr('action', '<?= base_url('admin/pengaturan/tahun-ajar/update') ?>/' + id);
        $('#edit_tahun_ajar_val').val(tahun);
        $('#edit_semester_val').val(semester);
        $('#edit_status_aktif_val').val(status);
    });
});
</script>
