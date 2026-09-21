<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelompok Supervisi</h1>
        <a href="<?= base_url('admin/kelompok/create'); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Kelompok
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <?php $tahunAktifLabel = isset($tahunAktif) && $tahunAktif ? esc($tahunAktif['tahun_ajar'] . ' - ' . $tahunAktif['semester']) : 'Belum ada tahun Aktif'; ?>
    <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center" role="status">
        <div><strong>Lembaran aktif:</strong> <?= $tahunAktifLabel; ?> <span class="badge badge-success ml-1">1 guru otomatis lanjut · kelompok perlu carry-over</span></div>
        <small class="text-muted">Arsip tahun lama aman. Aktifkan tahun lama untuk melihatnya.</small>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Carry-over ke Tahun Aktif</h6>
        </div>
        <div class="card-body">
            <?php if (empty($tahunAktif)): ?>
                <div class="alert alert-warning mb-0">Aktifkan dulu satu tahun ajaran sebelum carry-over.</div>
            <?php elseif (empty($tahunAjars)): ?>
                <div class="alert alert-warning mb-0">Data tahun ajaran belum tersedia.</div>
            <?php else: ?>
                <form action="<?= base_url('admin/kelompok/carry-over'); ?>" method="post" class="row align-items-end">
                    <?= csrf_field(); ?>
                    <div class="col-md-4">
                        <label for="from_tahun_ajar_id"><strong>Tahun sumber (arsip)</strong></label>
                        <select class="form-control" id="from_tahun_ajar_id" name="from_tahun_ajar_id" required>
                            <option value="">Pilih tahun sumber</option>
                            <?php foreach ($tahunAjars as $t): ?>
                                <?php if (isset($tahunAktif) && $tahunAktif && (int) $t['id'] === (int) $tahunAktif['id']) continue; ?>
                                <?php $c = $arsipCounts[(int) $t['id']] ?? 0; ?>
                                <option value="<?= $t['id']; ?>"><?= esc($t['tahun_ajar'] . ' - ' . $t['semester']); ?> (<?= (int) $c; ?> kelompok)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="to_tahun_ajar_id"><strong>Tahun tujuan</strong></label>
                        <select class="form-control" id="to_tahun_ajar_id" name="to_tahun_ajar_id" required>
                            <option value="<?= $tahunAktif['id']; ?>" selected><?= esc($tahunAktif['tahun_ajar'] . ' - ' . $tahunAktif['semester']); ?> (Aktif)</option>
                        </select>
                        <small class="text-muted">Idempotent: nama kelompok yang sama tidak diduplikasi.</small>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="copy_jadwal" name="copy_jadwal">
                            <label class="form-check-label" for="copy_jadwal">Salin jadwal</label>
                        </div>
                        <small class="text-muted">Jadwal yang sudah ada di kelompok tujuan dilewati.</small>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Salin kelompok dari tahun sumber ke tahun aktif?')"><i class="fas fa-copy"></i> Carry-over</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kelompok (Supervisor - Anggota)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th>Nama Kelompok</th>
                            <th>Supervisor</th>
                            <th>Tahun Ajaran</th>
                            <th width="18%">Jenis Penilaian</th>
                            <th width="10%" class="text-center">Anggota</th>
                            <th width="14%" class="text-center">Status Jadwal</th>
                            <th width="16%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kelompoks as $k): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td>
                                <a href="<?= base_url('admin/kelompok/' . $k['id']); ?>" class="font-weight-bold text-primary">
                                    <?= esc($k['nama_kelompok']); ?>
                                </a>
                            </td>
                            <td>
                                <i class="fas fa-user-tie mr-1 text-info"></i>
                                <strong><?= esc($k['nama_supervisor'] ?? '-'); ?></strong>
                                <?php if (!empty($k['nip_supervisor'])): ?>
                                    <div class="small text-muted"><i class="fas fa-id-card mr-1"></i>NIP: <?= esc($k['nip_supervisor']); ?></div>
                                <?php elseif (!empty($k['username_supervisor']) && $k['username_supervisor'] !== ($k['nama_supervisor'] ?? '')): ?>
                                    <div class="small text-muted"><?= esc($k['username_supervisor']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($k['role_supervisor'])): ?>
                                    <span class="badge badge-light border mt-1 font-weight-normal"><?= ucfirst(esc($k['role_supervisor'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc(($k['tahun_ajar'] ?? '-') . (isset($k['semester']) ? ' - ' . $k['semester'] : '')); ?></td>
                            <td>
                                <?php if (!empty($k['assigned_jenis'])): ?>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($k['assigned_jenis'] as $aj): ?>
                                            <span class="badge badge-primary mr-1 mb-1 text-truncate" style="max-width: 180px;" title="<?= esc($aj['nama']); ?>">
                                                <i class="fas fa-check-circle mr-1"></i><?= esc($aj['nama']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge badge-light border text-muted">Semua Aktif (Default)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><span class="badge badge-info px-2 py-1"><?= (int) ($k['total_anggota'] ?? 0); ?> guru</span></td>
                            <td class="text-center">
                                <?php 
                                    $tot = (int) ($k['total_anggota'] ?? 0);
                                    $jTot = (int) ($k['total_jadwal'] ?? 0);
                                    $jTer = (int) ($k['jadwal_terjadwal'] ?? 0);
                                    $jSel = (int) ($k['jadwal_selesai'] ?? 0);
                                ?>
                                <span class="badge badge-<?= ($jTot >= $tot && $tot > 0) ? 'success' : ($jTot > 0 ? 'warning' : 'secondary') ?> px-2 py-1">
                                    <i class="fas fa-calendar-alt mr-1"></i><?= $jTot; ?> / <?= $tot; ?> Terjadwal
                                </span>
                                <?php if ($jSel > 0): ?>
                                    <div class="small text-success font-weight-bold mt-1"><i class="fas fa-check-double mr-1"></i><?= $jSel; ?> Selesai</div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url('admin/kelompok/' . $k['id']); ?>" class="btn btn-info" title="Detail & Jadwal"><i class="fas fa-eye"></i></a>
                                    <a href="<?= base_url('admin/kelompok/' . $k['id'] . '/edit'); ?>" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?= base_url('admin/kelompok/' . $k['id'] . '/delete'); ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Hapus kelompok ini? Jadwal yang memakai kelompok akan dilepas (tidak ikut terhapus).')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kelompoks)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada kelompok supervisi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
