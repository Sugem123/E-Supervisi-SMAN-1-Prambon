<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Jadwal Supervisi</h1>
        <a href="<?= base_url('/admin/jadwal') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
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
                            <td><?= $jadwal['tahun_ajar'] ?> - <?= $jadwal['semester'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Guru</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['nama_guru'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['mata_pelajaran'] ?><?= !empty($jadwal['nama_mapel']) ? ' (' . esc($jadwal['nama_mapel']) . ')' : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelompok Supervisi</strong></td>
                            <td>:</td>
                            <td><?= !empty($jadwal['nama_kelompok']) ? esc($jadwal['nama_kelompok']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['kelas'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Materi yang Disupervisi</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['materi_supervisi'] ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Jam Ke-</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['jam_ke'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hari</strong></td>
                            <td>:</td>
                            <td><?= $jadwal['hari'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Supervisi</strong></td>
                            <td>:</td>
                            <td><?= format_tanggal_indonesia($jadwal['tanggal_supervisi']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu</strong></td>
                            <td>:</td>
                            <td>
                                <?php if (!empty($jadwal['waktu_dari']) && !empty($jadwal['waktu_sampai'])): ?>
                                    <?= $jadwal['waktu_dari'] ?> - <?= $jadwal['waktu_sampai'] ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
                            <td>
                                <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                                    <span class="badge badge-info"><?= $jadwal['status'] ?></span>
                                <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                                    <span class="badge badge-success"><?= $jadwal['status'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= $jadwal['status'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>