<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pengguna</h1>
        <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pengguna</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('/admin/pengguna/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="kepala">Kepala</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>
                
                <div class="form-group" id="guru-fields" style="display: none;">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap">
                    
                    <label for="nip">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip">
                    
                    <label for="pangkat_golongan">Pangkat/Golongan</label>
                    <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan">
                    
                    <label for="mata_pelajaran">Mata Pelajaran</label>
                    <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran">
                    
                    <label for="status_kepegawaian">Status Kepegawaian</label>
                    <select class="form-control" id="status_kepegawaian" name="status_kepegawaian">
                        <option value="">Pilih Status</option>
                        <option value="PNS">PNS</option>
                        <option value="PPPK">PPPK</option>
                        <option value="Honorer">Honorer</option>
                    </select>
                    
                    <div class="form-check" style="margin-top: 10px;">
                        <input class="form-check-input" type="checkbox" id="is_supervisor" name="is_supervisor" value="1">
                        <label class="form-check-label" for="is_supervisor">
                            Jadikan sebagai Supervisor
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    var guruFields = document.getElementById('guru-fields');
    if (this.value === 'guru') {
        guruFields.style.display = 'block';
    } else {
        guruFields.style.display = 'none';
    }
});
</script>
<?= $this->endSection(); ?>