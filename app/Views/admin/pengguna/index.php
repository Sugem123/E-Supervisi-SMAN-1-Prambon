<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengguna</h1>
        <div>
            <a href="<?= base_url('/admin/pengguna/export-excel') ?>" class="btn btn-success btn-sm shadow-sm mr-1">
                <i class="fas fa-file-excel mr-1"></i> Rekap Akun (Excel)
            </a>
            <a href="<?= base_url('/admin/pengguna/sync-guru-data') ?>" class="btn btn-info btn-sm shadow-sm mr-1"
                onclick="return confirm('Apakah Anda yakin ingin menyinkronkan data guru (username dan NIP)?')">
                <i class="fas fa-sync mr-1"></i> Sinkron Data Guru
            </a>
            <a href="<?= base_url('/admin/pengguna/create') ?>" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Pengguna
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
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th width="10%" class="text-center">Role</th>
                            <th width="8%" class="text-center">Status</th>
                            <th>Terakhir Login</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($users as $user): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <strong class="text-gray-900"><?= esc($user['nama_lengkap'] ?? $user['username']) ?></strong>
                                    <?php if (!empty($user['mata_pelajaran'])): ?>
                                        <div class="small text-muted"><i class="fas fa-book-open mr-1"></i><?= esc($user['mata_pelajaran']) ?></div>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <div class="small text-muted"><i class="fas fa-user-shield mr-1"></i>Administrator Sistem</div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><code><?= esc($user['username']) ?></code></td>
                                <td class="align-middle"><?= esc($user['email']) ?></td>
                                <td class="align-middle"><?= esc(!empty($user['nip_gabungan']) ? $user['nip_gabungan'] : ($user['nip'] ?? '-')) ?></td>
                                <td class="align-middle text-center">
                                    <?php $badge = [
                                        'admin' => 'dark',
                                        'kepala' => 'primary',
                                        'supervisor' => 'warning',
                                        'guru' => 'info',
                                    ];
                                    $role = strtolower($user['role']); ?>
                                    <span class="badge badge-<?= $badge[$role] ?? 'secondary' ?> px-2 py-1"><?= ucfirst($role) ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if (strtolower($user['status']) == 'aktif'): ?>
                                        <span class="badge badge-success px-2 py-1">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-2 py-1">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?= $user['last_login'] ?? '-' ?></td>
                                <td class="text-nowrap text-center align-middle">
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