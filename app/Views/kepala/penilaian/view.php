<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hasil Penilaian Supervisi</h1>
        <div>
            <a href="<?= base_url('/kepala/penilaian/print/' . $schedule['id']) ?>" target="_blank" class="btn btn-info btn-sm">
                <i class="fas fa-print"></i> Cetak
            </a>
            <a href="<?= base_url('/kepala/jadwal/' . $schedule['id']) ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Data Guru -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Guru</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Nama Guru</th>
                                    <td><?= $schedule['nama_guru'] ?></td>
                                </tr>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <td><?= $schedule['mata_pelajaran'] ?></td>
                                </tr>
                                <tr>
                                    <th>NIP</th>
                                    <td><?= $schedule['nip'] ?? '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Supervisor</th>
                                    <td><?= $schedule['nama_supervisor'] ?? get_nama_kepala() ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Supervisi</th>
                                    <td><?= format_tanggal_indonesia($schedule['tanggal_supervisi']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hasil Penilaian -->
            <?php if (!empty($hasilPenilaian)): ?>
                <?php foreach ($hasilPenilaian as $hasil): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <?= esc($hasil['nama_jenis']) ?>
                                <span class="float-right badge badge-info">
                                    Nilai Akhir: <?= number_format($hasil['nilai_akhir'], 2) ?> 
                                    (<?= $hasil['ketercapaian'] ?>)
                                </span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Aspek Penilaian</th>
                                            <th>Skor</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($detailResults[$hasil['jenis_penilaian_id']])): ?>
                                            <?php $no = 1; ?>
                                            <?php foreach ($detailResults[$hasil['jenis_penilaian_id']] as $detail): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= esc($detail['nama_aspek']) ?></td>
                                                    <td><?= esc($detail['skor']) ?></td>
                                                    <td><?= esc($detail['catatan']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada detail penilaian</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="form-group">
                                <label><strong>Rekomendasi Perbaikan:</strong></label>
                                <p><?= nl2br(esc($hasil['rekomendasi'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <p class="text-muted">Belum ada hasil penilaian</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Foto Bukti -->
            <?php if (!empty($uploadedPhotos)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto Bukti</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($uploadedPhotos as $photo): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="<?= base_url('uploads/foto_bukti/' . $photo['file_name']) ?>" 
                                             class="card-img-top" alt="Foto Bukti" 
                                             style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <p class="card-text"><?= esc($photo['keterangan']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>