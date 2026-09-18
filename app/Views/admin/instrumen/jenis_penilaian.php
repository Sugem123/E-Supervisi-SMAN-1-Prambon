<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Jenis Penilaian</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jenis Penilaian</h6>
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
            
            <!-- Add New Button -->
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> Tambah Jenis Penilaian
            </button>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jenis</th>
                            <th>Skor Maksimal</th>
                            <th>Kategori Skor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($jenis_penilaians as $jenis): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $jenis['nama_jenis'] ?? '' ?></td>
                            <td><?= $jenis['skor_maksimal'] ?? '' ?></td>
                            <td>
                                <?php 
                                if (isset($jenis['kategori_skor'])) {
                                    $kategori = json_decode($jenis['kategori_skor'], true);
                                    if (is_array($kategori)) {
                                        echo '<ul class="mb-0">';
                                        foreach ($kategori as $range => $label) {
                                            echo '<li>' . $range . ': ' . $label . '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo $jenis['kategori_skor'];
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $jenis['id'] ?? '' ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $jenis['id'] ?? '' ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/update') ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Jenis Penilaian</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nama Jenis</label>
                                                        <input type="text" class="form-control" name="nama_jenis" value="<?= esc($jenis['nama_jenis'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Skor Maksimal</label>
                                                        <input type="number" class="form-control" name="skor_maksimal" value="<?= esc($jenis['skor_maksimal'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Kategori Skor (JSON)</label>
                                                        <textarea class="form-control" name="kategori_skor" rows="4"><?= esc($jenis['kategori_skor'] ?? '') ?></textarea>
                                                        <small class="form-text text-muted">Format: {"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus jenis penilaian "<?= esc($jenis['nama_jenis'] ?? '') ?>"?</p>
                                                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <a href="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/delete') ?>" class="btn btn-danger">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Display related aspek penilaian -->
                        <?php 
                        // We need to get the aspek penilaian for this jenis penilaian
                        // This would normally be done in the controller, but we'll do it here for demonstration
                        $aspekModel = new \App\Models\AspekPenilaianModel();
                        $aspek_penilaians = $aspekModel->where('jenis_penilaian_id', $jenis['id'])->findAll();
                        ?>
                        <?php if (!empty($aspek_penilaians)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="ml-4">
                                    <h6>Aspek Penilaian untuk <?= esc($jenis['nama_jenis'] ?? '') ?>:</h6>
                                    <ul>
                                        <?php foreach ($aspek_penilaians as $aspek): ?>
                                            <li><?= esc($aspek['urutan'] ?? '') ?>. <?= esc($aspek['nama_aspek'] ?? '') ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/instrumen/jenis-penilaian/create') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Penilaian</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Jenis</label>
                        <input type="text" class="form-control" name="nama_jenis" required>
                    </div>
                    <div class="form-group">
                        <label>Skor Maksimal</label>
                        <input type="number" class="form-control" name="skor_maksimal" value="48" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Skor (JSON)</label>
                        <textarea class="form-control" name="kategori_skor" rows="4">{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</textarea>
                        <small class="form-text text-muted">Format: {"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>