<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Generate Jadwal Supervisi Otomatis</h1>
            <p class="text-muted small mb-0">Sistem otomatis mendistribusikan jadwal supervisi guru pada hari Senin s.d. Sabtu secara merata.</p>
        </div>
        <div>
            <a href="<?= base_url('/admin/jadwal') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Jadwal
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/admin/jadwal/generate-preview') ?>" method="post" id="formGenerateJadwal">
        <?= csrf_field() ?>
        <input type="hidden" name="tahun_ajar_id" value="<?= esc($tahun_ajar['id'] ?? '') ?>">

        <div class="row">
            <!-- Kolom Kiri: Konfigurasi Periode & Supervisor -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-sliders-h mr-1"></i> 1. Parameter Waktu & Beban Kerja
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Info Tahun Ajar -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-gray-800 small">Tahun Ajaran & Semester:</label>
                            <div class="p-2 bg-light rounded border text-gray-800 font-weight-bold">
                                <i class="fas fa-calendar-check text-primary mr-1"></i>
                                <?= esc($tahun_ajar['tahun_ajar'] ?? '-') ?> - <?= esc($tahun_ajar['semester'] ?? '-') ?>
                                <span class="badge badge-success ml-2">Aktif</span>
                            </div>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-gray-800 small">Rentang Tanggal Pelaksanaan:</label>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Mulai Tanggal:</small>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control form-control-sm" value="<?= esc($default_start_date) ?>" required>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Sampai Tanggal:</small>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control form-control-sm" value="<?= esc($default_end_date) ?>" required>
                                </div>
                            </div>
                            <small class="form-text text-info mt-1">
                                <i class="fas fa-info-circle mr-1"></i> Hari <strong>Minggu</strong> otomatis dilewati oleh sistem.
                            </small>
                        </div>

                        <!-- Pengaturan Kuota & Jam -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="max_per_day" class="font-weight-bold text-gray-800 small">Maks. Guru / Hari:</label>
                                <select name="max_per_day" id="max_per_day" class="form-control form-control-sm">
                                    <option value="1" selected>1 Guru / Supervisor</option>
                                    <option value="2">2 Guru / Supervisor</option>
                                    <option value="3">3 Guru / Supervisor</option>
                                </select>
                                <small class="text-muted">Beban harian per supervisor</small>
                            </div>
                            <div class="col-6">
                                <label for="sesi_mulai" class="font-weight-bold text-gray-800 small d-flex justify-content-between align-items-center">
                                    <span>Mulai Sesi Jam Ke-:</span>
                                    <a href="<?= base_url('admin/pengaturan?tab=jam-pelajaran'); ?>" target="_blank" class="small text-primary font-weight-normal">
                                        <i class="fas fa-cog mr-1"></i>Atur Jam
                                    </a>
                                </label>
                                <select name="sesi_mulai" id="sesi_mulai" class="form-control form-control-sm">
                                    <?php 
                                    $kbmSlots = get_jam_pelajaran_kbm();
                                    foreach ($kbmSlots as $jk => $slot): 
                                    ?>
                                        <option value="<?= esc($jk); ?>">
                                            <?= esc($slot['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Mengikuti konfigurasi Jam Pelajaran di Pengaturan Sistem</small>
                            </div>
                        </div>

                        <hr>

                        <!-- Pilihan Supervisor -->
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="font-weight-bold text-gray-800 small mb-0">Pilih Supervisor yang Bertugas:</label>
                                <div>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" id="btnCheckAllSupervisor">Pilih Semua</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" id="btnUncheckAllSupervisor">Batal</button>
                                </div>
                            </div>

                            <?php if (empty($supervisors)): ?>
                                <div class="alert alert-warning py-2 small mb-0">
                                    Tidak ada data supervisor aktif. Silakan tambahkan pengguna dengan role Supervisor atau Kepala Sekolah terlebih dahulu.
                                </div>
                            <?php else: ?>
                                <div class="border rounded p-2 bg-light" style="max-height: 220px; overflow-y: auto;">
                                    <?php foreach ($supervisors as $spv): ?>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" name="supervisor_ids[]" value="<?= $spv['id'] ?>" class="custom-control-input cb-supervisor" id="spv-<?= $spv['id'] ?>" checked>
                                            <label class="custom-control-label small d-flex justify-content-between align-items-center" for="spv-<?= $spv['id'] ?>">
                                                <span><i class="fas fa-user-tie text-muted mr-1"></i> <strong><?= esc($spv['username']) ?></strong></span>
                                                <span class="badge badge-<?= $spv['role'] == 'kepala' ? 'danger' : 'info' ?> ml-2"><?= ucfirst(esc($spv['role'])) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pilihan Guru -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users mr-1"></i> 2. Pilih Guru yang Akan Dijadwalkan
                        </h6>
                        <div>
                            <button type="button" class="btn btn-xs btn-primary py-0 px-2" id="btnSelectOnlyUnscheduled">
                                <i class="fas fa-filter mr-1"></i> Hanya Guru Belum Ada Jadwal
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" id="btnCheckAllGuru">Pilih Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" id="btnUncheckAllGuru">Kosongkan</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($gurus)): ?>
                            <div class="p-4 text-center text-muted">
                                Tidak ada data guru yang tersedia.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                <table class="table table-hover table-sm table-striped mb-0">
                                    <thead class="thead-light sticky-top" style="z-index: 1;">
                                        <tr>
                                            <th width="5%" class="text-center">Pilih</th>
                                            <th>Nama Guru & NIP</th>
                                            <th>Mata Pelajaran</th>
                                            <th class="text-center" width="22%">Status Jadwal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($gurus as $guru): ?>
                                            <tr class="guru-row <?= $guru['has_schedule'] ? 'row-has-schedule' : 'row-no-schedule' ?>">
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="guru_ids[]" value="<?= $guru['id'] ?>" class="custom-control-input cb-guru" id="guru-<?= $guru['id'] ?>" <?= !$guru['has_schedule'] ? 'checked' : '' ?> data-has-schedule="<?= $guru['has_schedule'] ? '1' : '0' ?>">
                                                        <label class="custom-control-label" for="guru-<?= $guru['id'] ?>"></label>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <strong><?= esc($guru['nama']) ?></strong>
                                                    <div class="small text-muted">NIP: <?= esc($guru['nip'] ?: '-') ?></div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border text-gray-800">
                                                        <?= esc($guru['mata_pelajaran'] ?: 'Umum') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php if ($guru['has_schedule']): ?>
                                                        <span class="badge badge-warning">Sudah Terjadwal</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Belum Ada Jadwal</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                Terpilih: <strong id="selectedGuruCount">0</strong> dari <?= count($gurus) ?> guru
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm shadow-sm" id="btnSubmitGenerate">
                                    <i class="fas fa-magic mr-1"></i> Generate & Pratinjau Jadwal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateSelectedCount() {
        var count = document.querySelectorAll('.cb-guru:checked').length;
        var countElem = document.getElementById('selectedGuruCount');
        if (countElem) {
            countElem.innerText = count;
        }
    }

    // Toggle Supervisor Checkboxes
    document.getElementById('btnCheckAllSupervisor').addEventListener('click', function() {
        document.querySelectorAll('.cb-supervisor').forEach(function(cb) { cb.checked = true; });
    });
    document.getElementById('btnUncheckAllSupervisor').addEventListener('click', function() {
        document.querySelectorAll('.cb-supervisor').forEach(function(cb) { cb.checked = false; });
    });

    // Toggle Guru Checkboxes
    document.getElementById('btnCheckAllGuru').addEventListener('click', function() {
        document.querySelectorAll('.cb-guru').forEach(function(cb) { cb.checked = true; });
        updateSelectedCount();
    });
    document.getElementById('btnUncheckAllGuru').addEventListener('click', function() {
        document.querySelectorAll('.cb-guru').forEach(function(cb) { cb.checked = false; });
        updateSelectedCount();
    });
    document.getElementById('btnSelectOnlyUnscheduled').addEventListener('click', function() {
        document.querySelectorAll('.cb-guru').forEach(function(cb) {
            var hasSchedule = cb.getAttribute('data-has-schedule') === '1';
            cb.checked = !hasSchedule;
        });
        updateSelectedCount();
    });

    document.querySelectorAll('.cb-guru').forEach(function(cb) {
        cb.addEventListener('change', updateSelectedCount);
    });

    updateSelectedCount();

    // Form validation check
    document.getElementById('formGenerateJadwal').addEventListener('submit', function(e) {
        var checkedSupervisors = document.querySelectorAll('.cb-supervisor:checked').length;
        var checkedGurus = document.querySelectorAll('.cb-guru:checked').length;

        if (checkedSupervisors === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 supervisor yang ditugaskan.');
            return;
        }

        if (checkedGurus === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 guru yang akan dijadwalkan.');
            return;
        }
    });
});
</script>
<?= $this->endSection(); ?>
