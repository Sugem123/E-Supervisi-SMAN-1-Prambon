<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Pengaturan Profil</h1>
            <p class="text-muted small mb-0">Kelola identitas akun, foto profil avatar, dan keamanan kata sandi Administrator.</p>
        </div>
        <div>
            <a href="<?= base_url('/admin') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Periksa kembali kesalahan berikut:</strong>
            <ul class="mb-0 mt-1 pl-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulir Terpadu -->
    <form action="<?= base_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data" id="profileForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Kolom Kiri: Avatar & Kartu Identitas -->
            <div class="col-lg-4 col-xl-4 mb-4">
                <div class="card shadow border-0 mb-4">
                    <div class="card-body text-center p-4">
                        <!-- Avatar Wrapper -->
                        <div class="position-relative d-inline-block mb-3">
                            <?php 
                                $avatarSrc = !empty($user['foto_profil']) 
                                    ? base_url('uploads/profile/' . $user['foto_profil']) 
                                    : base_url('assets/img/undraw_profile.svg');
                            ?>
                            <img id="avatarPreview" src="<?= $avatarSrc ?>" 
                                 alt="Foto Profil" 
                                 class="rounded-circle shadow-sm border" 
                                 style="width: 140px; height: 140px; object-fit: cover; border-width: 4px !important; border-color: #f8f9fc !important;">
                            
                            <!-- Tombol Ganti Foto (Icon Badge) -->
                            <label for="foto_profil" class="btn btn-primary btn-sm rounded-circle position-absolute shadow" 
                                   style="bottom: 5px; right: 5px; width: 38px; height: 38px; padding: 0; line-height: 38px; cursor: pointer;"
                                   title="Pilih Foto Baru">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" class="d-none" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/jpg">
                        </div>

                        <h5 class="font-weight-bold text-gray-800 mb-1"><?= esc($user['username']) ?></h5>
                        <p class="text-muted small mb-2"><?= esc($user['email']) ?></p>
                        
                        <div class="mb-3">
                            <span class="badge badge-primary px-3 py-2 font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="fas fa-user-shield mr-1"></i> Administrator
                            </span>
                        </div>

                        <div class="text-left bg-light rounded p-3 border small text-muted">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                <span>Klik ikon kamera untuk memilih foto profil baru.</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check text-success mr-2"></i>
                                <span>Mendukung JPG/PNG, ukuran maksimal 2 MB.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Bantuan / Keamanan -->
                <div class="card shadow border-0">
                    <div class="card-header py-3 bg-white border-bottom">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-shield-alt mr-1"></i> Keamanan Akun
                        </h6>
                    </div>
                    <div class="card-body small text-gray-700">
                        <p class="mb-2"><strong>Tips menjaga akun Anda:</strong></p>
                        <ul class="pl-3 mb-0">
                            <li class="mb-1">Pastikan email selalu aktif untuk pemulihan akun.</li>
                            <li class="mb-1">Gunakan kombinasi huruf, angka, dan simbol untuk password yang kuat.</li>
                            <li>Jangan membagikan kata sandi Administrator kepada pihak lain.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Formulir Akun & Password -->
            <div class="col-lg-8 col-xl-8 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-edit mr-1"></i> Edit Data Akun
                        </h6>
                        <span class="badge badge-light border text-muted small">ID Akun: #<?= esc($user['id']) ?></span>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Seksi 1: Informasi Dasar & Kontak -->
                        <h6 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card text-primary mr-1"></i> 1. Informasi Akun & Kontak
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="font-weight-bold small text-gray-700">Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted border-right-0">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control bg-light border-left-0" id="username" name="username" 
                                           value="<?= esc($user['username']) ?>" readonly>
                                </div>
                                <small class="text-muted">Username bersifat unik dan tidak dapat diubah.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="font-weight-bold small text-gray-700">Alamat Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-primary border-right-0">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control border-left-0" id="email" name="email" 
                                           value="<?= old('email', $user['email']) ?>" required placeholder="nama@email.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="font-weight-bold small text-gray-700">Nomor Telepon / WhatsApp</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-success border-right-0">
                                            <i class="fas fa-phone-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" id="telepon" name="telepon" 
                                           value="<?= old('telepon', $user['telepon'] ?? '') ?>" 
                                           placeholder="Contoh: 081234567890">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="font-weight-bold small text-gray-700">Alamat Lengkap</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-danger border-right-0">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    </div>
                                    <textarea class="form-control border-left-0" id="alamat" name="alamat" rows="1" 
                                              placeholder="Alamat tempat tinggal..."><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Seksi 2: Keamanan & Kata Sandi -->
                        <h6 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-key text-warning mr-1"></i> 2. Keamanan & Ganti Kata Sandi
                        </h6>

                        <div class="alert alert-light border small text-muted mb-3">
                            <i class="fas fa-info-circle text-info mr-1"></i>
                            Kosongkan kedua kolom di bawah ini jika Anda <strong>tidak ingin</strong> mengganti kata sandi lama Anda.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="font-weight-bold small text-gray-700">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-warning border-right-0">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control border-left-0 border-right-0" id="password" name="password" 
                                           placeholder="Minimal 8 karakter" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary border-left-0 btn-toggle-password" type="button" data-target="password" title="Lihat/Sembunyikan Kata Sandi">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirm" class="font-weight-bold small text-gray-700">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-info border-right-0">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control border-left-0 border-right-0" id="password_confirm" name="password_confirm" 
                                           placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary border-left-0 btn-toggle-password" type="button" data-target="password_confirm" title="Lihat/Sembunyikan Kata Sandi">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Footer Tombol Aksi -->
                    <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/admin') ?>" class="btn btn-secondary btn-sm shadow-sm">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Avatar Preview saat file dipilih
    var photoInput = document.getElementById('foto_profil');
    var avatarPreview = document.getElementById('avatarPreview');

    if (photoInput && avatarPreview) {
        photoInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (maks 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran berkas foto melebihi batas maksimal 2 MB.');
                    this.value = '';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(event) {
                    avatarPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Toggle Show/Hide Password
    document.querySelectorAll('.btn-toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');

            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>