<div class="row">
    <!-- Kolom Kiri: Logo & Aset Visual + Ubah Password -->
    <div class="col-lg-4 mb-4">

        <!-- Card Logo & Branding Madrasah -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-palette mr-1"></i> Aset Visual & Logo
                </h6>
            </div>
            <div class="card-body text-center p-4">
                
                <!-- 1. Logo Utama Madrasah -->
                <div class="mb-4">
                    <div class="small font-weight-bold text-gray-700 text-uppercase mb-2" style="letter-spacing: 0.5px;">Logo Utama Madrasah</div>
                    <div class="d-inline-block position-relative mb-2">
                        <?php if (!empty($identitas['logo'])): ?>
                            <img src="<?= base_url('uploads/' . $identitas['logo']) ?>" 
                                 alt="Logo Madrasah" 
                                 class="img-thumbnail rounded-circle shadow-sm" 
                                 style="width: 120px; height: 120px; object-fit: contain; background: #fff; border: 3px solid #eaecf4;">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/logo-placeholder.png') ?>" 
                                 alt="Logo Placeholder" 
                                 class="img-thumbnail rounded-circle shadow-sm" 
                                 style="width: 120px; height: 120px; object-fit: contain; background: #f8f9fc; border: 3px solid #eaecf4;">
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <!-- Form Upload Logo Utama -->
                        <form action="<?= base_url('admin/pengaturan/upload-asset/logo') ?>" method="post" enctype="multipart/form-data" id="formUploadLogo" class="d-inline mr-1">
                            <?= csrf_field() ?>
                            <input type="file" name="logo" id="logoInput" class="d-none" accept="image/jpeg,image/png,image/jpg" onchange="document.getElementById('formUploadLogo').submit()">
                            <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="document.getElementById('logoInput').click()">
                                <i class="fas fa-camera mr-1"></i> <?= !empty($identitas['logo']) ? 'Ganti' : 'Unggah' ?> Logo
                            </button>
                        </form>

                        <?php if (!empty($identitas['logo'])): ?>
                            <a href="<?= base_url('admin/pengaturan/identitas-madrasah/delete-logo/logo') ?>" 
                               class="btn btn-outline-danger btn-sm shadow-sm" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus logo madrasah?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted d-block mt-1 font-italic">Format PNG/JPG, maks. 2MB</small>
                </div>

                <hr class="my-3">

                <!-- 2. Logo Sidebar Madrasah -->
                <div>
                    <div class="small font-weight-bold text-gray-700 text-uppercase mb-2" style="letter-spacing: 0.5px;">Logo Sidebar (Menu Kiri)</div>
                    <div class="p-3 bg-light rounded border mb-2 d-flex align-items-center justify-content-center" style="min-height: 70px;">
                        <?php if (!empty($identitas['sidebar_logo'])): ?>
                            <img src="<?= base_url('uploads/' . $identitas['sidebar_logo']) ?>" 
                                 alt="Sidebar Logo" 
                                 class="img-fluid" 
                                 style="max-height: 48px; max-width: 100%;">
                        <?php else: ?>
                            <span class="small text-muted font-italic">
                                <i class="fas fa-image mr-1"></i> Belum ada logo sidebar
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <!-- Form Upload Sidebar Logo -->
                        <form action="<?= base_url('admin/pengaturan/upload-asset/sidebar_logo') ?>" method="post" enctype="multipart/form-data" id="formUploadSidebarLogo" class="d-inline mr-1">
                            <?= csrf_field() ?>
                            <input type="file" name="sidebar_logo" id="sidebarLogoInput" class="d-none" accept="image/jpeg,image/png,image/jpg" onchange="document.getElementById('formUploadSidebarLogo').submit()">
                            <button type="button" class="btn btn-info btn-sm shadow-sm" onclick="document.getElementById('sidebarLogoInput').click()">
                                <i class="fas fa-upload mr-1"></i> <?= !empty($identitas['sidebar_logo']) ? 'Ganti' : 'Unggah' ?> Sidebar
                            </button>
                        </form>

                        <?php if (!empty($identitas['sidebar_logo'])): ?>
                            <a href="<?= base_url('admin/pengaturan/identitas-madrasah/delete-logo/sidebar_logo') ?>" 
                               class="btn btn-outline-danger btn-sm shadow-sm" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus logo sidebar?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted d-block mt-1 font-italic">Rasio horizontal / transparan disarankan</small>
                </div>

            </div>
        </div>

        <!-- Card Ubah Password Cepat -->
        <div class="card shadow border-0">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 font-weight-bold text-gray-800">
                    <i class="fas fa-key text-warning mr-1"></i> Ganti Password Akun Admin
                </h6>
            </div>
            <div class="card-body p-3">
                <form action="<?= base_url('admin/pengaturan/update-password') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group mb-2">
                        <label for="password_baru" class="small font-weight-bold text-gray-700">Password Baru</label>
                        <div class="input-group input-group-sm">
                            <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="password_baru">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="konfirmasi_password" class="small font-weight-bold text-gray-700">Ulangi Password Baru</label>
                        <div class="input-group input-group-sm">
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password baru" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="konfirmasi_password">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm btn-block shadow-sm font-weight-bold text-dark">
                        <i class="fas fa-lock mr-1"></i> Perbarui Password
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Formulir Identitas Lembaga & Pimpinan -->
    <div class="col-lg-8 mb-4">

        <!-- Card 1: Form Identitas Madrasah (Inline Form Langsung) -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-school mr-1"></i> Data Identitas Madrasah
                </h6>
                <span class="badge badge-light border text-primary small">
                    <i class="fas fa-info-circle mr-1"></i> Data Profil Resmi
                </span>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/pengaturan/update-identitas') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label for="nama_madrasah" class="font-weight-bold small text-gray-700">Nama Madrasah / Sekolah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light text-primary border-right-0">
                                    <i class="fas fa-building"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0" id="nama_madrasah" name="nama_madrasah" 
                                   value="<?= esc($identitas['nama_madrasah'] ?? '') ?>" required placeholder="Contoh: MAN 1 Bandar Lampung">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="nsm" class="font-weight-bold small text-gray-700">Nomor Statistik Madrasah (NSM) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light text-muted border-right-0">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="nsm" name="nsm" 
                                       value="<?= esc($identitas['nsm'] ?? '') ?>" required placeholder="12 digit NSM">
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="npsn" class="font-weight-bold small text-gray-700">Nomor Pokok Sekolah Nasional (NPSN) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light text-muted border-right-0">
                                        <i class="fas fa-fingerprint"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="npsn" name="npsn" 
                                       value="<?= esc($identitas['npsn'] ?? '') ?>" required placeholder="8 digit NPSN">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="alamat" class="font-weight-bold small text-gray-700">Alamat Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light text-danger border-right-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                            </div>
                            <textarea class="form-control border-left-0" id="alamat" name="alamat" rows="2" required 
                                      placeholder="Nama jalan, nomor, desa/kelurahan..."><?= esc($identitas['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="kecamatan" class="font-weight-bold small text-gray-700">Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kecamatan" name="kecamatan" 
                                   value="<?= esc($identitas['kecamatan'] ?? '') ?>" required placeholder="Kecamatan">
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="kabupaten" class="font-weight-bold small text-gray-700">Kabupaten / Kota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kabupaten" name="kabupaten" 
                                   value="<?= esc($identitas['kabupaten'] ?? '') ?>" required placeholder="Kabupaten/Kota">
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="provinsi" class="font-weight-bold small text-gray-700">Provinsi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="provinsi" name="provinsi" 
                                   value="<?= esc($identitas['provinsi'] ?? '') ?>" required placeholder="Provinsi">
                        </div>
                    </div>

                    <div class="text-right pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Identitas Madrasah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Form Kepala Madrasah (Pimpinan Lembaga) -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-gray-800">
                    <i class="fas fa-user-tie text-info mr-1"></i> Kepala Madrasah (Pejabat Penandatangan)
                </h6>
                <span class="badge badge-light border text-info small">
                    <i class="fas fa-stamp mr-1"></i> Lembar Pengesahan
                </span>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border small text-muted mb-3">
                    <i class="fas fa-info-circle text-info mr-1"></i>
                    Nama dan NIP Kepala Madrasah di bawah ini akan otomatis tercetak pada lembar pengesahan jadwal, instrumen penilaian, dan laporan supervisi PDF.
                </div>

                <form action="<?= base_url('admin/pengaturan/update-pimpinan') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-7 form-group mb-3">
                            <label for="nama_kepala" class="font-weight-bold small text-gray-700">Nama Kepala Madrasah & Gelar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light text-info border-right-0">
                                        <i class="fas fa-user-tie"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="nama_kepala" name="nama_kepala" 
                                       value="<?= esc($identitas['nama_kepala'] ?? '') ?>" required placeholder="Contoh: Drs. H. Ahmad Fauzi, M.Pd.">
                            </div>
                        </div>

                        <div class="col-md-5 form-group mb-3">
                            <label for="nip_kepala" class="font-weight-bold small text-gray-700">NIP Kepala Madrasah</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light text-muted border-right-0">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="nip_kepala" name="nip_kepala" 
                                       value="<?= esc($identitas['nip_kepala'] ?? '') ?>" placeholder="18 digit angka (atau - jika non-PNS)">
                            </div>
                        </div>
                    </div>

                    <div class="text-right pt-2 border-top">
                        <button type="submit" class="btn btn-info btn-sm px-4 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Data Pimpinan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle show/hide password
    document.querySelectorAll('.btn-toggle-pw').forEach(function(btn) {
        btn.addEventListener('click', function() {
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
