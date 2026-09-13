<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengguna</h1>
        <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pengguna</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/admin/pengguna/' . $user['id'] . '/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password (Kosongkan jika tidak ingin mengganti)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="kepala" <?= $user['role'] == 'kepala' ? 'selected' : '' ?>>Kepala</option>
                        <option value="supervisor" <?= $user['role'] == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                        <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Aktif" <?= $user['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Nonaktif" <?= $user['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>