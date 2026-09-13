<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Audit & Log Sistem</h1>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
    </div>
    <div class="card-body">
        <form method="get" action="<?= base_url('admin/laporan/audit-log') ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user_id">Pengguna</label>
                        <select name="user_id" id="user_id" class="form-control select2">
                            <option value="">Semua Pengguna</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id']; ?>" <?= (isset($filters['user_id']) && $filters['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                    <?= esc($user['username']); ?> (<?= esc($user['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="activity_type">Jenis Aktivitas</label>
                        <select name="activity_type" id="activity_type" class="form-control">
                            <option value="">Semua Jenis</option>
                            <option value="login" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'login') ? 'selected' : ''; ?>>Login</option>
                            <option value="logout" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'logout') ? 'selected' : ''; ?>>Logout</option>
                            <option value="create" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'create') ? 'selected' : ''; ?>>Create Data</option>
                            <option value="update" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'update') ? 'selected' : ''; ?>>Update Data</option>
                            <option value="delete" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'delete') ? 'selected' : ''; ?>>Delete Data</option>
                            <option value="setting" <?= (isset($filters['activity_type']) && $filters['activity_type'] == 'setting') ? 'selected' : ''; ?>>Ubah Pengaturan</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="<?= isset($filters['start_date']) ? esc($filters['start_date']) : ''; ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="<?= isset($filters['end_date']) ? esc($filters['end_date']) : ''; ?>">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        <a href="<?= base_url('admin/laporan/audit-log'); ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Aktivitas</h6>
        <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display:none;">
            <i class="fas fa-trash"></i> Hapus Terpilih
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>No</th>
                        <th>Pengguna</th>
                        <th>Jenis Aktivitas</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                        <th>Tanggal & Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="log-checkbox" value="<?= $log['id'] ?>">
                                </td>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= esc($log['username'] ?? 'Tidak Diketahui'); ?>
                                    <?php if (!empty($log['email'])): ?>
                                        <br><small class="text-muted"><?= esc($log['email']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $activityLabels = [
                                        'login' => 'Login',
                                        'logout' => 'Logout',
                                        'create' => 'Create Data',
                                        'update' => 'Update Data',
                                        'delete' => 'Delete Data',
                                        'setting' => 'Ubah Pengaturan'
                                    ];
                                    echo esc($activityLabels[$log['activity_type']] ?? $log['activity_type']);
                                    ?>
                                </td>
                                <td><?= esc($log['description']); ?></td>
                                <td><?= esc($log['ip_address']); ?></td>
                                <td><?= date('d M Y H:i:s', strtotime($log['created_at'])); ?></td>
                                <td>
                                    <a href="<?= base_url('admin/laporan/audit-log/delete/' . $log['id']); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data aktivitas ditemukan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($logs)): ?>
            <div class="mt-3">
                <?= $pager->links('default', 'default_full'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih pengguna",
            allowClear: true
        });

        // Select All checkbox
        $('#selectAll').on('change', function() {
            $('.log-checkbox').prop('checked', this.checked);
            toggleBulkDeleteBtn();
        });

        // Individual checkbox change
        $('.log-checkbox').on('change', function() {
            toggleBulkDeleteBtn();
            const totalCheckboxes = $('.log-checkbox').length;
            const checkedCheckboxes = $('.log-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        });

        // Toggle bulk delete button visibility
        function toggleBulkDeleteBtn() {
            const checkedCount = $('.log-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#bulkDeleteBtn').show();
                $('#bulkDeleteBtn').html('<i class="fas fa-trash"></i> Hapus ' + checkedCount + ' Terpilih');
            } else {
                $('#bulkDeleteBtn').hide();
            }
        }

        // Bulk delete handler
        $('#bulkDeleteBtn').on('click', function() {
            const selectedIds = [];
            $('.log-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' log yang dipilih?')) {
                $.ajax({
                    url: '<?= base_url("admin/laporan/audit-log/bulk-delete") ?>',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Berhasil menghapus ' + selectedIds.length + ' log');
                            location.reload();
                        } else {
                            alert('Gagal menghapus log: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghapus log');
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection(); ?>