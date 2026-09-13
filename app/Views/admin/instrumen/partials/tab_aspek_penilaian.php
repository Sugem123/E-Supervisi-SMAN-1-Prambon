<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Aspek Penilaian</h6>
        <div class="d-flex align-items-center">
            <div class="dropdown mr-2">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="menuCepatJenis" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-layer-group mr-1"></i> Jenis Penilaian
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="menuCepatJenis">
                    <h6 class="dropdown-header">Aksi Cepat:</h6>
                    <a class="dropdown-item" href="<?= base_url('/admin/instrumen?tab=jenis') ?>" onclick="$('#jenis-tab').tab('show'); return false;">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-primary"></i> Kelola Jenis Penilaian
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="$('#jenis-tab').tab('show'); setTimeout(function(){ $('#createJenisModal').modal('show'); }, 350);">
                        <i class="fas fa-plus fa-sm fa-fw mr-2 text-success"></i> Tambah Jenis Penilaian Baru
                    </a>
                    <?php if (!empty($jenis_penilaians)): ?>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Filter / Lihat Jenis:</h6>
                        <?php foreach ($jenis_penilaians as $jenis): ?>
                            <a class="dropdown-item small btn-filter-jenis" href="javascript:void(0)" data-jenis="<?= esc($jenis['nama_jenis'], 'attr') ?>" onclick="window.filterAspekByJenis ? filterAspekByJenis('<?= esc(addslashes($jenis['nama_jenis'])) ?>') : null">
                                <i class="fas fa-filter fa-sm fa-fw mr-2 text-gray-400"></i> <?= esc($jenis['nama_jenis']) ?>
                            </a>
                        <?php endforeach; ?>
                        <a class="dropdown-item small text-muted btn-filter-jenis" href="javascript:void(0)" data-jenis="" onclick="window.filterAspekByJenis ? filterAspekByJenis('') : null">
                            <i class="fas fa-undo fa-sm fa-fw mr-2"></i> Reset Filter Jenis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createAspekModal">
                <i class="fas fa-plus mr-1"></i> Tambah Aspek Penilaian
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTableAspek" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Jenis Penilaian</th>
                        <th>Nama Aspek</th>
                        <th width="10%">Urutan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($aspek_penilaians as $aspek): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge badge-info p-2"><?= esc($aspek['nama_jenis'] ?? '') ?></span></td>
                        <td><?= esc($aspek['nama_aspek'] ?? '') ?></td>
                        <td class="text-center font-weight-bold"><?= esc($aspek['urutan'] ?? '') ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editAspekModal<?= $aspek['id'] ?? '' ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteAspekModal<?= $aspek['id'] ?? '' ?>">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/update') ?>" method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Aspek Penilaian</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Jenis Penilaian</label>
                                                    <select class="form-control" name="jenis_penilaian_id" required>
                                                        <option value="">Pilih Jenis Penilaian</option>
                                                        <?php foreach ($jenis_penilaians as $jenis): ?>
                                                            <option value="<?= $jenis['id'] ?>" <?= (isset($aspek['jenis_penilaian_id']) && $aspek['jenis_penilaian_id'] == $jenis['id']) ? 'selected' : '' ?>>
                                                                <?= esc($jenis['nama_jenis']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nama Aspek</label>
                                                    <input type="text" class="form-control" name="nama_aspek" value="<?= esc($aspek['nama_aspek'] ?? '') ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Urutan</label>
                                                    <input type="number" class="form-control" name="urutan" value="<?= esc($aspek['urutan'] ?? '') ?>" required>
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
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus aspek penilaian "<strong><?= esc($aspek['nama_aspek'] ?? '') ?></strong>"?</p>
                                            <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <a href="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/delete') ?>" class="btn btn-danger">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createAspekModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/instrumen/aspek-penilaian/create') ?>" method="post" id="createAspekForm">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aspek Penilaian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0 font-weight-bold">Jenis Penilaian</label>
                            <a href="javascript:void(0)" onclick="$('#createAspekModal').modal('hide'); $('#jenis-tab').tab('show');" class="small text-primary">
                                <i class="fas fa-external-link-alt mr-1"></i> Kelola Jenis Penilaian
                            </a>
                        </div>
                        <select class="form-control" name="jenis_penilaian_id" id="jenisPenilaianCreate" required>
                            <option value="">Pilih Jenis Penilaian</option>
                            <?php foreach ($jenis_penilaians as $jenis): ?>
                                <option value="<?= $jenis['id'] ?>"><?= esc($jenis['nama_jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Aspek</label>
                        <input type="text" class="form-control" name="nama_aspek" required placeholder="Contoh: Kesesuaian RPP dengan Silabus">
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="urutan" id="urutanCreate" required>
                        <small class="form-text text-muted">Nomor urut akan otomatis melanjutkan dari urutan terakhir</small>
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

<script>
window.filterAspekByJenis = function(jenisName) {
    if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable().column(1).search(jenisName ? '^' + $.fn.dataTable.util.escapeRegex(jenisName) + '$' : '', true, false).draw();
    }
};

if (window.jQuery) {
    $(document).off('click', '.btn-filter-jenis').on('click', '.btn-filter-jenis', function(e) {
        e.preventDefault();
        var jenis = $(this).attr('data-jenis');
        if (typeof window.filterAspekByJenis === 'function') {
            window.filterAspekByJenis(jenis);
        }
    });
}
</script>
