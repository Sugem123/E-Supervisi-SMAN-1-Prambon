<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Laporan Hasil Supervisi</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/laporan/hasil-supervisi') ?>">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="tahun_ajar_id">Tahun Ajaran</label>
                            <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control">
                                <option value="">Semua Tahun Ajaran</option>
                                <?php foreach ($tahun_ajars as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= (isset($tahun_ajar_id) && $tahun_ajar_id == $tahun['id']) ? 'selected' : '' ?>>
                                        <?= $tahun['tahun_ajar'] ?> - <?= $tahun['semester'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="Terjadwal" <?= (isset($status) && $status == 'Terjadwal') ? 'selected' : '' ?>>Terjadwal</option>
                                <option value="Selesai" <?= (isset($status) && $status == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Export buttons -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/pdf') ?><?= (!empty($tahun_ajar_id) || !empty($status)) ? '?' . http_build_query(array_filter(['tahun_ajar_id' => $tahun_ajar_id, 'status' => $status])) : '' ?>" 
                       class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export rekap nilai PDF
                    </a>
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/excel') ?><?= (!empty($tahun_ajar_id) || !empty($status)) ? '?' . http_build_query(array_filter(['tahun_ajar_id' => $tahun_ajar_id, 'status' => $status])) : '' ?>" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export rekap penilaian Excel
                    </a>
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/batch-excel-all') ?><?= (!empty($tahun_ajar_id) || !empty($status)) ? '?' . http_build_query(array_filter(['tahun_ajar_id' => $tahun_ajar_id, 'status' => $status])) : '' ?>" 
                       class="btn btn-info">
                        <i class="fas fa-file-excel"></i> Export Detail Nilai Semua Guru (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Hasil Supervisi</h6>
        </div>
        <div class="card-body">
            <form id="batchExcelForm" method="post" action="<?= base_url('admin/laporan/hasil-supervisi/batch-excel') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun_ajar_id" value="<?= $tahun_ajar_id ?? '' ?>">
                <input type="hidden" name="status" value="<?= $status ?? '' ?>">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>No</th>
                                <th>Tahun Ajaran</th>
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Tanggal Supervisi</th>
                                <th>Status</th>
                                <th>Nilai Akhir</th>
                                <th>Ketercapaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jadwals)): ?>
                                <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                                    <tr>
                                        <td>
                                            <?php if ($jadwal['status'] == 'Selesai'): ?>
                                                <input type="checkbox" class="checkbox-item" name="selected_ids[]" value="<?= $jadwal['id'] ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $no++ ?></td>
                                        <td><?= $jadwal['tahun_ajar'] ?> - <?= $jadwal['semester'] ?></td>
                                        <td><?= $jadwal['nama_guru'] ?></td>
                                        <td><?= $jadwal['mata_pelajaran'] ?></td>
                                        <td><?= $jadwal['kelas'] ?></td>
                                        <td><?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                                        <td>
                                            <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                                                <span class="badge badge-info"><?= $jadwal['status'] ?></span>
                                            <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                                                <span class="badge badge-success"><?= $jadwal['status'] ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?= $jadwal['status'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($jadwal['hasil']['nilai_akhir']) && !is_null($jadwal['hasil']['nilai_akhir'])): ?>
                                                <span class="badge badge-primary">
                                                    <?= number_format($jadwal['hasil']['nilai_akhir'], 2) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum Dinilai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($jadwal['hasil']['ketercapaian']) && !is_null($jadwal['hasil']['ketercapaian'])): ?>
                                                <span class="badge badge-<?= 
                                                    $jadwal['hasil']['ketercapaian'] == 'Baik Sekali' ? 'success' : 
                                                    ($jadwal['hasil']['ketercapaian'] == 'Baik' ? 'info' : 
                                                    ($jadwal['hasil']['ketercapaian'] == 'Cukup' ? 'warning' : 'danger')) ?>">
                                                    <?= $jadwal['hasil']['ketercapaian'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum Dinilai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($jadwal['status'] == 'Selesai'): ?>
                                                <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $jadwal['id']) ?>" 
                                                   class="btn btn-sm btn-primary" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/penilaian/form/' . $jadwal['id']) ?>" 
                                                   class="btn btn-sm btn-warning font-weight-bold" title="Edit Hasil Penilaian">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">Tidak ada data hasil supervisi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Check all checkboxes
    $('#checkAll').click(function() {
        $('.checkbox-item').prop('checked', this.checked);
        updateBatchButtonState();
    });
    
    // Update batch button state when individual checkboxes are clicked
    $('.checkbox-item').click(function() {
        updateBatchButtonState();
    });
    
    // Update batch button state
    function updateBatchButtonState() {
        const checkedCount = $('.checkbox-item:checked').length;
        $('#batchExcelBtn').prop('disabled', checkedCount === 0);
    }
    
    // Handle batch Excel export
    $('#batchExcelBtn').click(function() {
        const checkedCount = $('.checkbox-item:checked').length;
        if (checkedCount === 0) {
            alert('Silakan pilih setidaknya satu jadwal untuk diexport.');
            return;
        }
        
        // Submit the form
        $('#batchExcelForm').submit();
    });
});
</script>
<?= $this->endSection(); ?>