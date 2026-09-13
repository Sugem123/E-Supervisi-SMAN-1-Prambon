<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Laporan Supervisi</h1>
        <a href="<?= base_url('/kepala/laporan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
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
                                    <th>NIP</th>
                                    <td><?= $schedule['nip'] ?></td>
                                </tr>
                                <tr>
                                    <th>Pangkat/Golongan</th>
                                    <td><?= $schedule['pangkat_golongan'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <td><?= $schedule['mata_pelajaran'] ?></td>
                                </tr>
                                <tr>
                                    <th>Materi yang Disupervisi</th>
                                    <td><?= $schedule['materi_supervisi'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Status Kepegawaian</th>
                                    <td><?= $schedule['status_kepegawaian'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Supervisor -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Supervisor</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nama Supervisor</th>
                            <td><?= $schedule['nama_supervisor'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Data Hasil Supervisi -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Supervisi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Tanggal Supervisi</th>
                                    <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td><?= $schedule['kelas'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Jam Ke-</th>
                                    <td><?= $schedule['jam_ke'] ?></td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td>
                                        <?php if (!empty($schedule['waktu_dari']) && !empty($schedule['waktu_sampai'])): ?>
                                            <?= $schedule['waktu_dari'] ?> - <?= $schedule['waktu_sampai'] ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($schedule['status'] == 'Terjadwal'): ?>
                                            <span class="badge badge-info"><?= $schedule['status'] ?></span>
                                        <?php elseif ($schedule['status'] == 'Selesai'): ?>
                                            <span class="badge badge-success"><?= $schedule['status'] ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= $schedule['status'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Penilaian -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Penilaian</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Jenis Penilaian</th>
                                    <th>Aspek Penilaian</th>
                                    <th>Skor</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detailResults)): ?>
                                    <?php foreach ($detailResults as $jenisId => $details): ?>
                                        <?php 
                                        $jenisNama = '';
                                        switch($jenisId) {
                                            case 1: $jenisNama = 'Administrasi Guru'; break;
                                            case 2: $jenisNama = 'Proses Pembelajaran'; break;
                                            case 3: $jenisNama = 'Evaluasi Pembelajaran'; break;
                                            case 4: $jenisNama = 'Pengembangan Diri'; break;
                                            default: $jenisNama = 'Komponen Lain';
                                        }
                                        ?>
                                        <?php foreach ($details as $detail): ?>
                                            <tr>
                                                <td><?= $jenisNama ?></td>
                                                <td><?= $detail['nama_aspek'] ?? '' ?></td>
                                                <td><?= $detail['skor'] ?? '' ?></td>
                                                <td><?= $detail['catatan'] ?? '' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data penilaian</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Foto Bukti -->
            <?php if (!empty($fotoBukti)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto Bukti</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($fotoBukti as $foto): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="<?= base_url('uploads/foto_bukti/' . $foto['file_name']) ?>" 
                                             class="card-img-top" alt="Foto Bukti" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <small class="text-muted"><?= $foto['keterangan'] ?? '' ?></small>
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