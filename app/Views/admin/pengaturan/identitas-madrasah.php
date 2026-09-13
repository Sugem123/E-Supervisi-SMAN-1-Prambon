<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Identitas Madrasah</h1>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4">

            <!-- Profile/Logo Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($identitas['logo'])): ?>
                            <img src="<?= base_url('uploads/' . $identitas['logo']) ?>" alt="Logo Madrasah" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/logo-placeholder.png') ?>" alt="Logo Placeholder" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-1"><?= $identitas['nama_madrasah'] ?></h5>
                    <p class="text-muted mb-4">Logo Madrasah</p>

                    <div class="d-flex justify-content-center">
                        <?php if (!empty($identitas['logo'])): ?>
                            <a href="<?= base_url('admin/pengaturan/identitas-madrasah/delete-logo/logo') ?>" class="btn btn-danger btn-sm mr-2" onclick="return confirm('Hapus logo?')">Hapus Logo</a>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/pengaturan/upload-asset/logo') ?>" method="post" enctype="multipart/form-data" id="formUploadLogo" class="mr-2">
                            <?= csrf_field() ?>
                            <input type="file" name="logo" id="logoInput" style="display:none;" accept="image/*" onchange="document.getElementById('formUploadLogo').submit()">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('logoInput').click()">Upload Logo</button>
                        </form>

                        <!-- <a href="<?= base_url('admin/pengaturan/sync-profile') ?>" class="btn btn-outline-primary btn-sm">Syncron Profile</a> -->
                    </div>

                    <hr>

                    <!-- Sidebar Logo Section -->
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-dark mb-2 small">Logo Sidebar</h6>
                        <?php if (!empty($identitas['sidebar_logo'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url('uploads/' . $identitas['sidebar_logo']) ?>" alt="Sidebar Logo" class="img-fluid" style="max-height: 50px;">
                            </div>
                            <a href="<?= base_url('admin/pengaturan/identitas-madrasah/delete-logo/sidebar_logo') ?>" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Hapus logo sidebar?')"><i class="fas fa-trash"></i></a>
                        <?php else: ?>
                            <p class="small text-muted mb-2">Belum ada logo sidebar</p>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/pengaturan/upload-asset/sidebar_logo') ?>" method="post" enctype="multipart/form-data" id="formUploadSidebarLogo">
                            <?= csrf_field() ?>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="sidebarLogoInput" name="sidebar_logo" accept="image/*" onchange="document.getElementById('formUploadSidebarLogo').submit()">
                                <label class="custom-file-label text-left" for="sidebarLogoInput" style="overflow:hidden;">Pilih file...</label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-key mr-2"></i>Ubah Password</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/pengaturan/update-password') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="password_baru" class="small font-weight-bold">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Masukkan Password baru" required>
                        </div>
                        <div class="form-group">
                            <label for="konfirmasi_password" class="small font-weight-bold">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" placeholder="Masukkan Password Baru" required>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="col-lg-8">

            <!-- Info Alert -->
            <div class="alert alert-warning shadow-sm mb-4" role="alert">
                <h6 class="alert-heading font-weight-bold">Info Perubahan data</h6>
                <p class="mb-0 small">Silahkan isi data sesui dengan data Madrasah.</p>
            </div>

            <!-- Identitas Madrasah List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-school mr-2"></i>Identitas Madrasah</h6>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editIdentitasModal">
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 30%;" class="font-weight-bold small pl-4">Nama</td>
                                <td class="small"><?= $identitas['nama_madrasah'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">NSM</td>
                                <td class="small"><?= $identitas['nsm'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">NPSN</td>
                                <td class="small"><?= $identitas['npsn'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">Alamat</td>
                                <td class="small"><?= $identitas['alamat'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">Kecamatan</td>
                                <td class="small"><?= $identitas['kecamatan'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">Kabupaten/Kota</td>
                                <td class="small"><?= $identitas['kabupaten'] ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold small pl-4">Provinsi</td>
                                <td class="small"><?= $identitas['provinsi'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Identitas Modal -->
            <div class="modal fade" id="editIdentitasModal" tabindex="-1" role="dialog" aria-labelledby="editIdentitasModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editIdentitasModalLabel">Edit Identitas Madrasah</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= base_url('admin/pengaturan/update-identitas') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nama_madrasah">Nama Madrasah</label>
                                    <input type="text" class="form-control" name="nama_madrasah" value="<?= $identitas['nama_madrasah'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="nsm">NSM</label>
                                    <input type="text" class="form-control" name="nsm" value="<?= $identitas['nsm'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="npsn">NPSN</label>
                                    <input type="text" class="form-control" name="npsn" value="<?= $identitas['npsn'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" name="alamat" rows="3" required><?= $identitas['alamat'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <input type="text" class="form-control" name="kecamatan" value="<?= $identitas['kecamatan'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="kabupaten">Kabupaten/Kota</label>
                                    <input type="text" class="form-control" name="kabupaten" value="<?= $identitas['kabupaten'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="provinsi">Provinsi</label>
                                    <input type="text" class="form-control" name="provinsi" value="<?= $identitas['provinsi'] ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pimpinan Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-user mr-2"></i>Pimpinan</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/pengaturan/update-pimpinan') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="nama_kepala" class="small font-weight-bold">Nama</label>
                            <input type="text" class="form-control" id="nama_kepala" name="nama_kepala" value="<?= $identitas['nama_kepala'] ?>" placeholder="Nama Kepala Madrasah">
                            <small class="form-text text-muted">Nama Kepala Madrasah</small>
                        </div>
                        <div class="form-group">
                            <label for="nip_kepala" class="small font-weight-bold">NIP</label>
                            <input type="text" class="form-control" id="nip_kepala" name="nip_kepala" value="<?= $identitas['nip_kepala'] ?>" placeholder="NIP Kepala Madrasah">
                            <small class="form-text text-muted">NIP Kepala Madrasah</small>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kop Instansi Card -->
            <div class="card shadow mb-4" id="cardKopSurat">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice mr-2"></i>Kop Instansi (Header Cetak PDF)</h6>
                    <span class="badge badge-info"><i class="fas fa-print mr-1"></i> Digunakan pada Cetak PDF</span>
                </div>
                <div class="card-body">
                    <!-- Live Preview Box -->
                    <label class="small font-weight-bold text-dark">Pratinjau Kop Surat:</label>
                    <div class="p-3 mb-4 bg-white border rounded shadow-sm" style="background-color: #fff; overflow-x: auto;">
                        <?= render_kop_surat() ?>
                    </div>

                    <form action="<?= base_url('admin/pengaturan/update-kop') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="kop_baris_1" class="small font-weight-bold">Baris 1 (Instansi Pusat/Kementerian)</label>
                            <input type="text" class="form-control" id="kop_baris_1" name="kop_baris_1" 
                                value="<?= esc($kop['baris_1'] ?? '') ?>" 
                                placeholder="Contoh: KEMENTERIAN AGAMA REPUBLIK INDONESIA" required>
                            <small class="form-text text-muted">Ditampilkan di baris paling atas (huruf kapital).</small>
                        </div>

                        <div class="form-group">
                            <label for="kop_baris_2" class="small font-weight-bold">Baris 2 (Instansi Daerah / Kantor Wilayah/Kabupaten)</label>
                            <input type="text" class="form-control" id="kop_baris_2" name="kop_baris_2" 
                                value="<?= esc($kop['baris_2'] ?? '') ?>" 
                                placeholder="Contoh: KANTOR KEMENTERIAN AGAMA KABUPATEN TANGGAMUS">
                            <small class="form-text text-muted">Ditampilkan di baris kedua.</small>
                        </div>

                        <div class="form-group">
                            <label for="kop_baris_3" class="small font-weight-bold">Baris 3 (Nama Madrasah / Satuan Pendidikan)</label>
                            <input type="text" class="form-control font-weight-bold" id="kop_baris_3" name="kop_baris_3" 
                                value="<?= esc($kop['baris_3'] ?? '') ?>" 
                                placeholder="Contoh: MADRASAH IBTIDAIYAH NEGERI 2 TANGGAMUS" required>
                            <small class="form-text text-muted">Ditampilkan tebal (bold) sebagai nama utama madrasah.</small>
                        </div>

                        <div class="form-group">
                            <label for="kop_baris_4" class="small font-weight-bold">Baris 4 (Alamat Lengkap)</label>
                            <input type="text" class="form-control" id="kop_baris_4" name="kop_baris_4" 
                                value="<?= esc($kop['baris_4'] ?? '') ?>" 
                                placeholder="Contoh: Jl. Lapangan Ampera Purwodadi No. 109 Kec. Gisting Kab. Tanggamus">
                            <small class="form-text text-muted">Alamat jalan, desa/kelurahan, kecamatan, kabupaten, provinsi.</small>
                        </div>

                        <div class="form-group">
                            <label for="kop_baris_5" class="small font-weight-bold">Baris 5 (Kontak / Telepon / Email / Website / Kode Pos)</label>
                            <input type="text" class="form-control" id="kop_baris_5" name="kop_baris_5" 
                                value="<?= esc($kop['baris_5'] ?? '') ?>" 
                                placeholder="Contoh: Website: https://min2tanggamus.sch.id | Email: min2tanggamus@kemenag.go.id">
                            <small class="form-text text-muted">Ditampilkan dengan format miring (italic) di baris bawah.</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="kop_tampilkan_logo" name="kop_tampilkan_logo" value="1" 
                                        <?= ($kop['tampilkan_logo'] == '1') ? 'checked' : '' ?>>
                                    <label class="custom-control-label small font-weight-bold" for="kop_tampilkan_logo">
                                        Tampilkan Logo Madrasah pada Kop
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="kop_tampilkan_garis" name="kop_tampilkan_garis" value="1" 
                                        <?= ($kop['tampilkan_garis'] == '1') ? 'checked' : '' ?>>
                                    <label class="custom-control-label small font-weight-bold" for="kop_tampilkan_garis">
                                        Tampilkan Garis Ganda Pemisah Kop
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Pengaturan Kop
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>