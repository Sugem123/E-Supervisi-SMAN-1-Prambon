<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Aspek Penilaian</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Aspek Penilaian</h6>
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
                <i class="fas fa-plus"></i> Tambah Aspek Penilaian
            </button>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Penilaian</th>
                            <th>Nama Aspek</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($aspek_penilaians as $aspek): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $aspek['nama_jenis'] ?? '' ?></td>
                            <td><?= $aspek['nama_aspek'] ?? '' ?></td>
                            <td><?= $aspek['urutan'] ?? '' ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $aspek['id'] ?? '' ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $aspek['id'] ?? '' ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/update') ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Aspek Penilaian</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Jenis Penilaian</label>
                                                        <select class="form-control" name="jenis_penilaian_id" required>
                                                            <option value="">Pilih Jenis Penilaian</option>
                                                            <?php foreach ($jenis_penilaians as $jenis): ?>
                                                                <option value="<?= $jenis['id'] ?>" <?= (isset($aspek['jenis_penilaian_id']) && $aspek['jenis_penilaian_id'] == $jenis['id']) ? 'selected' : '' ?>>
                                                                    <?= esc($jenis['nama_jenis']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama Aspek</label>
                                                        <input type="text" class="form-control" name="nama_aspek" value="<?= esc($aspek['nama_aspek'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Urutan</label>
                                                        <input type="number" class="form-control" name="urutan" value="<?= esc($aspek['urutan'] ?? '') ?>" required>
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
                                <div class="modal fade" id="deleteModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus aspek penilaian "<?= esc($aspek['nama_aspek'] ?? '') ?>"?</p>
                                                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <a href="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/delete') ?>" class="btn btn-danger">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
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
            <form action="<?= base_url('/admin/instrumen/aspek-penilaian/create') ?>" method="post" id="createAspekForm">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aspek Penilaian</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis Penilaian</label>
                        <select class="form-control" name="jenis_penilaian_id" id="jenisPenilaianCreate" required>
                            <option value="">Pilih Jenis Penilaian</option>
                            <?php foreach ($jenis_penilaians as $jenis): ?>
                                <option value="<?= $jenis['id'] ?>"><?= esc($jenis['nama_jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Aspek</label>
                        <input type="text" class="form-control" name="nama_aspek" required>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="urutan" id="urutanCreate" required>
                        <small class="form-text text-muted">Nomor urut akan otomatis melanjutkan dari urutan terakhir</small>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle urutan auto-increment for create form
    const jenisPenilaianCreate = document.getElementById('jenisPenilaianCreate');
    const urutanCreate = document.getElementById('urutanCreate');
    
    // Max urutan data from PHP
    const maxUrutanData = <?= json_encode($max_urutan ?? []) ?>;
    
    if (jenisPenilaianCreate && urutanCreate) {
        jenisPenilaianCreate.addEventListener('change', function() {
            const selectedJenisId = this.value;
            if (selectedJenisId) {
                const maxUrutan = maxUrutanData[selectedJenisId] || 0;
                urutanCreate.value = parseInt(maxUrutan) + 1;
            } else {
                urutanCreate.value = '';
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>