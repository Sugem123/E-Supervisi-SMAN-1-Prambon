<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hasil Supervisi</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Hasil Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (empty($jadwalSelesai)): ?>
                <div class="alert alert-info">
                    Belum ada hasil supervisi yang tersedia.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Guru</th>
                                <th>NIP</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Tanggal Supervisi</th>
                                <th>Nilai Akhir</th>
                                <th>Ketercapaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($jadwalSelesai as $jadwal): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $jadwal['nama_guru'] ?></td>
                                    <td><?= isset($jadwal['nip_guru']) ? esc($jadwal['nip_guru']) : '-' ?></td>
                                    <td><?= $jadwal['mata_pelajaran'] ?></td>
                                    <td><?= $jadwal['kelas'] ?></td>
                                    <td><?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                                    <td>
                                        <?php if (isset($jadwal['nilai_akhir']) && !is_null($jadwal['nilai_akhir'])): ?>
                                            <span class="badge badge-primary">
                                                <?= number_format($jadwal['nilai_akhir'], 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($jadwal['ketercapaian']) && !is_null($jadwal['ketercapaian'])): ?>
                                            <span class="badge badge-<?= 
                                                $jadwal['ketercapaian'] == 'Baik Sekali' ? 'success' : 
                                                ($jadwal['ketercapaian'] == 'Baik' ? 'info' : 
                                                ($jadwal['ketercapaian'] == 'Cukup' ? 'warning' : 'danger')) ?>">
                                                <?= $jadwal['ketercapaian'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('supervisor/hasil/detail/' . $jadwal['id']) ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
<?= $this->endSection() ?>