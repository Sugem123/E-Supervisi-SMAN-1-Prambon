<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengguna</h1>
        <div>
            <a href="<?= base_url('/admin/pengguna/sync-guru-data') ?>" class="btn btn-info btn-sm"
                onclick="return confirm('Apakah Anda yakin ingin menyinkronkan data guru (username dan NIP)?')">
                <i class="fas fa-sync"></i> Sinkron Data Guru
            </a>
            <a href="<?= base_url('/admin/pengguna/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terakhir Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['nip'] ?? '-' ?></td>
                                <td>
                                    <?php $badge = [
                                        'admin' => 'dark',
                                        'kepala' => 'primary',
                                        'supervisor' => 'warning',
                                        'guru' => 'info',
                                    ];
                                    $role = strtolower($user['role']); ?>
                                    <span class="badge badge-<?= $badge[$role] ?? 'secondary' ?>"><?= ucfirst($role) ?></span>
                                </td>
                                <td>
                                    <?php if (strtolower($user['status']) == 'aktif'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['last_login'] ?? '-' ?></td>
                                <td class="text-nowrap">
                                    <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/edit') ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-info btn-sm btn-reset-password"
                                        data-id="<?= $user['id'] ?>"
                                        data-username="<?= esc($user['username']) ?>"
                                        title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/toggle-status') ?>"
                                        class="btn btn-<?= (strtolower($user['status']) == 'aktif') ? 'secondary' : 'success' ?> btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin mengubah status pengguna ini?')"
                                        title="<?= (strtolower($user['status']) == 'aktif') ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/delete') ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="resetPasswordForm" action="" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="resetPasswordModalLabel"><i class="fas fa-key mr-2"></i>Reset Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Reset password untuk pengguna: <strong id="resetUsername"></strong></p>
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="text" class="form-control" id="new_password" name="password" value="12345678" required minlength="8">
                            <small class="form-text text-muted">Default: <code>12345678</code> (minimal 8 karakter).</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-reset-password', function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        $('#resetUsername').text(username);
        $('#new_password').val('12345678');
        $('#resetPasswordForm').attr('action', '<?= base_url('/admin/pengguna') ?>/' + id + '/reset-password');
        $('#resetPasswordModal').modal('show');
    });
});
</script>
<?= $this->endSection(); ?>