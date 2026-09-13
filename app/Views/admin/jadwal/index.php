<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Supervisi</h1>
        <div>
            <a href="<?= base_url('/admin/jadwal/generate') ?>" class="btn btn-success btn-sm shadow-sm mr-1">
                <i class="fas fa-magic mr-1"></i> Generate Jadwal
            </a>
            <a href="<?= base_url('/admin/jadwal/create') ?>" class="btn btn-primary btn-sm shadow-sm mr-1">
                <i class="fas fa-plus mr-1"></i> Tambah Jadwal
            </a>
            <a href="<?= base_url('/admin/jadwal/cetak-pdf') ?>" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Supervisi</h6>
            <div>
                <!-- Tombol Bulk Delete (Muncul saat ada checkbox dipilih) -->
                <button type="button" class="btn btn-danger btn-sm shadow-sm" id="btnBulkDelete" style="display: none;">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih (<span id="bulkSelectedCount">0</span>)
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('warning') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="3%" class="text-center align-middle">
                                <input type="checkbox" id="checkAllJadwal" title="Pilih Semua Jadwal">
                            </th>
                            <th width="4%" class="text-center align-middle">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Nama Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Hari/Tanggal</th>
                            <th>Supervisor</th>
                            <th width="8%" class="text-center align-middle">Status</th>
                            <th width="15%" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                        <tr>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="row-checkbox" value="<?= $jadwal['id'] ?>" data-guru="<?= esc($jadwal['nama_guru']) ?>" data-mapel="<?= esc($jadwal['mata_pelajaran']) ?>">
                            </td>
                            <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                            <td><?= esc($jadwal['tahun_ajar']) ?> - <?= esc($jadwal['semester']) ?></td>
                            <td>
                                <strong><?= esc($jadwal['nama_guru']) ?></strong>
                            </td>
                            <td><?= esc($jadwal['mata_pelajaran']) ?></td>
                            <td><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?></td>
                            <td>
                                <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                <?= format_hari_indonesia($jadwal['tanggal_supervisi']) ?>, <?= format_tanggal_indonesia($jadwal['tanggal_supervisi'], false) ?>
                            </td>
                            <td>
                                <i class="fas fa-user-tie mr-1 text-info"></i>
                                <?= esc($jadwal['nama_supervisor'] ?? '-') ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                                    <span class="badge badge-info">Terjadwal</span>
                                <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= esc($jadwal['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url('/admin/jadwal/' . $jadwal['id']) ?>" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('/admin/jadwal/' . $jadwal['id'] . '/edit') ?>" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Tombol hapus satuan -->
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" 
                                            data-id="<?= $jadwal['id'] ?>" data-info="<?= esc($jadwal['nama_guru']) ?> - <?= esc($jadwal['mata_pelajaran']) ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Satuan -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Hapus Jadwal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jadwal supervisi ini?</p>
                <div class="p-2 bg-light rounded border mb-3">
                    <div><strong>Guru:</strong> <span id="guruName"></span></div>
                    <div><strong>Mata Pelajaran:</strong> <span id="mataPelajaran"></span></div>
                </div>
                <p class="text-danger small mb-2"><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.</p>
                <hr>
                <p class="small mb-1">Untuk konfirmasi, ketik "<strong>HAPUS</strong>" di bawah ini:</p>
                <input type="text" class="form-control" id="confirmDelete" placeholder="Ketik HAPUS" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Massal (Bulk Delete) -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/jadwal/bulk-delete') ?>" method="post" id="formBulkDelete">
                <?= csrf_field() ?>
                <div id="bulkDeleteHiddenInputs"></div>
                
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="bulkDeleteModalLabel">
                        <i class="fas fa-trash-alt mr-1"></i> Konfirmasi Hapus Massal Jadwal
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus <strong id="modalBulkCount" class="text-danger font-weight-bold">0</strong> jadwal supervisi yang dipilih?
                    </p>
                    <div class="alert alert-warning small mb-3">
                        <i class="fas fa-shield-alt mr-1"></i>
                        <strong>Proteksi Data:</strong> Jadwal yang sudah memiliki data hasil supervisi tidak akan dihapus untuk menjaga keutuhan riwayat penilaian.
                    </div>
                    <p class="text-danger small mb-2"><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.</p>
                    <hr>
                    <p class="small mb-1">Untuk konfirmasi penghapusan massal, ketik "<strong>HAPUS</strong>" di bawah ini:</p>
                    <input type="text" class="form-control" id="confirmBulkDeleteText" placeholder="Ketik HAPUS" autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btnSubmitBulkDelete" disabled>
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    var deleteId;
    
    // Saat tombol hapus satuan diklik
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        deleteId = button.data('id');
        var info = (button.data('info') || '').split(' - ');
        
        $('#guruName').text(info[0] || '-');
        $('#mataPelajaran').text(info[1] || '-');
        $('#confirmDelete').val('');
    });
    
    // Konfirmasi hapus satuan
    $('#confirmDeleteBtn').click(function() {
        var confirmation = $('#confirmDelete').val().trim();
        
        if (confirmation !== 'HAPUS') {
            alert('Mohon ketik "HAPUS" untuk konfirmasi penghapusan');
            return;
        }
        
        window.location.href = '<?= base_url('/admin/jadwal/') ?>' + deleteId + '/delete';
    });

    // --- BULK DELETE LOGIC ---
    function updateBulkDeleteUI() {
        var checked = $('#dataTable tbody .row-checkbox:checked');
        var count = checked.length;
        $('#bulkSelectedCount').text(count);

        if (count > 0) {
            $('#btnBulkDelete').fadeIn(150);
        } else {
            $('#btnBulkDelete').fadeOut(150);
            $('#checkAllJadwal').prop('checked', false);
        }

        var totalOnView = $('#dataTable tbody .row-checkbox').length;
        if (count === totalOnView && totalOnView > 0) {
            $('#checkAllJadwal').prop('checked', true);
        } else {
            $('#checkAllJadwal').prop('checked', false);
        }
    }

    // Select All Checkbox
    $('#checkAllJadwal').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#dataTable tbody .row-checkbox').prop('checked', isChecked);
        updateBulkDeleteUI();
    });

    // Row Checkbox change (event delegation untuk DataTables)
    $('#dataTable').on('change', '.row-checkbox', function() {
        updateBulkDeleteUI();
    });

    // Buka Modal Bulk Delete
    $('#btnBulkDelete').on('click', function() {
        var checked = $('#dataTable tbody .row-checkbox:checked');
        var count = checked.length;
        if (count === 0) {
            alert('Silakan pilih minimal satu jadwal untuk dihapus.');
            return;
        }

        $('#modalBulkCount').text(count);
        $('#confirmBulkDeleteText').val('');
        $('#btnSubmitBulkDelete').prop('disabled', true);

        // Pasang hidden inputs ID jadwal terpilih
        var container = $('#bulkDeleteHiddenInputs');
        container.empty();
        checked.each(function() {
            container.append('<input type="hidden" name="selected_ids[]" value="' + $(this).val() + '">');
        });

        $('#bulkDeleteModal').modal('show');
    });

    // Validasi ketik "HAPUS" untuk mengaktifkan tombol submit bulk delete
    $('#confirmBulkDeleteText').on('input', function() {
        var val = $(this).val().trim();
        if (val === 'HAPUS') {
            $('#btnSubmitBulkDelete').prop('disabled', false);
        } else {
            $('#btnSubmitBulkDelete').prop('disabled', true);
        }
    });
});
</script>
<?= $this->endSection(); ?>