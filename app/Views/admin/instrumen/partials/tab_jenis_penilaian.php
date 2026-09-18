<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Jenis Penilaian</h6>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createJenisModal">
            <i class="fas fa-plus mr-1"></i> Tambah Jenis Penilaian
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTableJenis" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Jenis</th>
                        <th width="15%">Skor Maksimal</th>
                        <th width="30%">Kategori Skor</th>
                        <th width="10%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($jenis_penilaians as $jenis): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= esc($jenis['nama_jenis'] ?? '') ?></strong></td>
                        <td><?= esc($jenis['skor_maksimal'] ?? '') ?></td>
                        <td>
                            <?php 
                            if (isset($jenis['kategori_skor'])) {
                                $kategori = json_decode($jenis['kategori_skor'], true);
                                if (is_array($kategori)) {
                                    echo '<ul class="mb-0 pl-3 small">';
                                    foreach ($kategori as $range => $label) {
                                        echo '<li>' . esc($range) . ': ' . esc($label) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo esc($jenis['kategori_skor']);
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php $jStatus = $jenis['status'] ?? 'Aktif'; ?>
                            <?php if ($jStatus === 'Aktif'): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1"><i class="fas fa-pause-circle mr-1"></i>Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editJenisModal<?= $jenis['id'] ?? '' ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/toggle-status') ?>" method="post" class="d-inline" onsubmit="return confirm('Ubah status jenis ini? Data lama tetap tersimpan.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm <?= $jStatus === 'Aktif' ? 'btn-outline-secondary' : 'btn-outline-success' ?>" title="Aktif/Nonaktif — tanpa hapus data">
                                    <i class="fas fa-<?= $jStatus === 'Aktif' ? 'eye-slash' : 'eye' ?> mr-1"></i><?= $jStatus === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteJenisModal<?= $jenis['id'] ?? '' ?>">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editJenisModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/update') ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Jenis Penilaian</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Status Pakai</label>
                                                    <select class="form-control" name="status">
                                                        <option value="Aktif" <?= ($jenis['status'] ?? 'Aktif') === 'Aktif' ? 'selected' : '' ?>>Aktif — dipakai di form penilaian</option>
                                                        <option value="Nonaktif" <?= ($jenis['status'] ?? '') === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif — disembunyikan, histori aman</option>
                                                    </select>
                                                </div>
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
                                                    <textarea class="form-control font-monospace" name="kategori_skor" rows="4"><?= esc($jenis['kategori_skor'] ?? '') ?></textarea>
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
                            <div class="modal fade" id="deleteJenisModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus jenis penilaian "<strong><?= esc($jenis['nama_jenis'] ?? '') ?></strong>"?</p>
                                            <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
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
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createJenisModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/instrumen/jenis-penilaian/create') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Penilaian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status Pakai</label>
                        <select class="form-control" name="status">
                            <option value="Aktif">Aktif — dipakai di form penilaian</option>
                            <option value="Nonaktif">Nonaktif — disembunyikan, histori aman</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Jenis</label>
                        <input type="text" class="form-control" name="nama_jenis" required placeholder="Contoh: Supervisi Administrasi Guru">
                    </div>
                    <div class="form-group">
                        <label>Skor Maksimal</label>
                        <input type="number" class="form-control" name="skor_maksimal" value="48" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Skor (JSON)</label>
                        <textarea class="form-control font-monospace" name="kategori_skor" rows="4">{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</textarea>
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
