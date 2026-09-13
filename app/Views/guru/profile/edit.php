<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profil</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form untuk update foto profil saja -->
                    <form action="<?= base_url('guru/profile/update-photo') ?>" method="post" enctype="multipart/form-data" id="photoForm">
                        <?= csrf_field() ?>
                        <!-- Section 1: Foto Profil -->
                        <div class="form-group">
                            <label for="foto_profil">Foto Profil</label>
                            <div class="mb-2">
                                <?php if (!empty($user['foto_profil'])): ?>
                                    <img id="currentPhoto" src="<?= base_url('uploads/profile/' . $user['foto_profil']) ?>" 
                                         alt="Current Profile Photo" class="img-thumbnail" width="150">
                                <?php else: ?>
                                    <img id="currentPhoto" src="<?= base_url('assets/img/undraw_profile.svg') ?>" 
                                         alt="Default Profile Photo" class="img-thumbnail" width="150">
                                <?php endif; ?>
                            </div>
                            <div class="custom-file mb-2">
                                <input type="file" class="custom-file-input" id="foto_profil" name="foto_profil" accept="image/*">
                                <label class="custom-file-label" for="foto_profil">Pilih foto...</label>
                            </div>
                            <div id="photoPreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="#" alt="Preview" class="img-thumbnail" width="150">
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-info" id="savePhotoBtn" style="display: none;">Simpan Foto</button>
                    </form>
                    
                    <hr>
                    
                    <form action="<?= base_url('guru/profile/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <!-- Section 2: Informasi Akun -->
                        <h5>Informasi Akun</h5>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= $user['username'] ?>" readonly>
                            <small class="form-text text-muted">Username tidak dapat diubah.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= old('email', $user['email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
                        
                        <hr>
                        
                        <!-- Section 3: Data Pribadi -->
                        <h5>Data Pribadi</h5>
                        
                        <div class="form-group">
                            <label for="nama">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= old('nama', $guru['nama'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telepon">Nomor Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" 
                                   value="<?= old('telepon', $user['telepon'] ?? '') ?>" 
                                   placeholder="Contoh: 081234567890">
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" 
                                   value="<?= old('mata_pelajaran', $guru['mata_pelajaran'] ?? '') ?>">
                        </div>
                        
                        <hr>
                        
                        <!-- Section 4: Preferensi -->
                        <h5>Preferensi</h5>
                        
                        <div class="form-group">
                            <label for="bahasa">Bahasa Interface</label>
                            <select class="form-control" id="bahasa" name="bahasa">
                                <option value="id">Indonesia</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notifikasi">Notifikasi</label>
                            <select class="form-control" id="notifikasi" name="notifikasi">
                                <option value="email">Email</option>
                                <option value="in_app">In-App</option>
                                <option value="both">Keduanya</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('/guru') ?>" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Petunjuk</h6>
                </div>
                <div class="card-body">
                    <p>Hal-hal yang perlu diperhatikan:</p>
                    <ul>
                        <li>Email harus unik dan tidak boleh sama dengan pengguna lain</li>
                        <li>Password minimal 8 karakter</li>
                        <li>Untuk mengganti password, isi kedua kolom password</li>
                        <li>Format nomor telepon: 08XXXXXXXXXX</li>
                        <li>File foto profil maksimal 2MB</li>
                        <li>Anda dapat menyimpan foto profil secara terpisah dengan menggunakan tombol "Simpan Foto"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Update file label and show preview when file is selected
document.getElementById('foto_profil').addEventListener('change', function(e) {
    var fileName = e.target.files[0]?.name;
    var label = document.querySelector('label[for="foto_profil"]');
    var saveBtn = document.getElementById('savePhotoBtn');
    var preview = document.getElementById('photoPreview');
    var previewImg = document.getElementById('previewImg');
    
    if (fileName) {
        label.textContent = fileName;
        saveBtn.style.display = 'inline-block';
        
        // Show preview
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    } else {
        label.textContent = 'Pilih foto...';
        saveBtn.style.display = 'none';
        preview.style.display = 'none';
    }
});
</script>
<?= $this->endSection() ?>