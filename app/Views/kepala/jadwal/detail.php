<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Jadwal Supervisi</h1>
        <div>
            <?php if ($schedule['status'] == 'Terjadwal' || $schedule['status'] == 'Selesai'): ?>
                <a href="<?= base_url('/kepala/penilaian/form/' . $schedule['id']) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-clipboard-check"></i> 
                    <?php if ($schedule['status'] == 'Selesai'): ?>
                        Lihat Penilaian
                    <?php else: ?>
                        Lakukan Penilaian
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <a href="<?= base_url('/kepala/jadwal') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tahun Ajaran</strong></td>
                            <td>:</td>
                            <td><?= $schedule['tahun_ajar'] ?> - <?= $schedule['semester'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Guru</strong></td>
                            <td>:</td>
                            <td><?= $schedule['nama_guru'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= $schedule['mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td><?= $schedule['kelas'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Materi yang Disupervisi</strong></td>
                            <td>:</td>
                            <td><?= $schedule['materi_supervisi'] ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Jam Ke-</strong></td>
                            <td>:</td>
                            <td><?= $schedule['jam_ke'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hari</strong></td>
                            <td>:</td>
                            <td><?= $schedule['hari'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Supervisi</strong></td>
                            <td>:</td>
                            <td><?= format_tanggal_indonesia($schedule['tanggal_supervisi']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu</strong></td>
                            <td>:</td>
                            <td>
                                <?php if (!empty($schedule['waktu_dari']) && !empty($schedule['waktu_sampai'])): ?>
                                    <?= $schedule['waktu_dari'] ?> - <?= $schedule['waktu_sampai'] ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
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
</div>
<?= $this->endSection() ?>