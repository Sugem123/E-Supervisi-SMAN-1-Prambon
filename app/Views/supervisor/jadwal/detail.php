<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Detail Jadwal Supervisi</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Guru</strong></td>
                            <td>:</td>
                            <td><?= $schedule['nama_guru'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>NIP</strong></td>
                            <td>:</td>
                            <td><?= $schedule['nip'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Pangkat/Golongan</strong></td>
                            <td>:</td>
                            <td><?= $schedule['pangkat_golongan'] ?? '-' ?></td>
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
                        <tr>
                            <td><strong>Hari/Tanggal</strong></td>
                            <td>:</td>
                            <td><?= $schedule['hari'] ?>, <?= format_tanggal_indonesia($schedule['tanggal_supervisi']) ?></td>
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
                            <td><strong>Jam Ke-</strong></td>
                            <td>:</td>
                            <td><?= $schedule['jam_ke'] ?></td>
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

    <a href="<?= base_url('supervisor/jadwal') ?>" class="btn btn-secondary">Kembali</a>
</div>
<?= $this->endSection() ?>