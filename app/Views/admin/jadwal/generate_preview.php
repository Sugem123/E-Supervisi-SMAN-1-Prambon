<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Pratinjau Hasil Generate Jadwal</h1>
            <p class="text-muted small mb-0">Tinjau simulasi pembagian jadwal supervisi otomatis sebelum disimpan secara permanen ke database.</p>
        </div>
        <div>
            <a href="<?= base_url('/admin/jadwal/generate') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Atur Ulang Parameter
            </a>
        </div>
    </div>

    <!-- Alert Ringkasan -->
    <?php if ($sisaBelumTerjadwalkan > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            <strong>Perhatian:</strong> Terdapat <strong><?= $sisaBelumTerjadwalkan ?> guru</strong> yang belum dapat dijadwalkan karena rentang tanggal kerja (Senin–Sabtu) dan kuota harian supervisor yang Anda pilih sudah terisi penuh. Anda dapat menambah rentang tanggal selesai atau menaikkan kuota harian.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php else: ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i>
            <strong>Sempurna!</strong> Seluruh <strong><?= $totalTerjadwalkan ?> guru</strong> berhasil dijadwalkan secara merata pada hari kerja (Senin s.d. Sabtu).
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Summary Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Guru Terjadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalTerjadwalkan ?> Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Supervisor Bertugas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($supervisors) ?> Orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hari Kerja Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalHariKerja ?> Hari</div>
                            <small class="text-muted">Senin s.d. Sabtu (Tanpa Minggu)</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-business-time fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rentang Tanggal</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?= date('d M Y', strtotime($tanggalMulai)) ?></div>
                            <small class="text-muted">s.d. <?= date('d M Y', strtotime($tanggalSelesai)) ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Konfirmasi Simpan -->
    <form action="<?= base_url('/admin/jadwal/generate-save') ?>" method="post" id="formSaveGeneratedJadwal">
        <?= csrf_field() ?>
        <input type="hidden" name="jadwals_json" value="<?= htmlspecialchars(json_encode($generatedJadwals), ENT_QUOTES, 'UTF-8') ?>">

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table mr-1"></i> Rincian Jadwal Hasil Generate
                </h6>
                <div>
                    <span class="small text-muted mr-2">Anda dapat menyesuaikan kelas atau membatalkan baris jadwal sebelum menyimpan.</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tablePreview">
                        <thead class="thead-light">
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th width="18%">Hari & Tanggal</th>
                                <th width="10%">Jam Ke-</th>
                                <th width="20%">Guru & NIP</th>
                                <th width="15%">Mata Pelajaran</th>
                                <th width="15%">Kelas Supervisi</th>
                                <th width="14%">Supervisor Ditugaskan</th>
                                <th width="4%" class="text-center">Batal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($generatedJadwals as $idx => $item): ?>
                                <tr id="row-<?= $idx ?>">
                                    <td class="text-center align-middle font-weight-bold row-no"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <input type="date" name="override_tanggal[<?= $idx ?>]" value="<?= esc($item['tanggal_supervisi']) ?>" class="form-control form-control-sm input-override-tanggal" data-index="<?= $idx ?>" style="min-width: 140px;">
                                            <div class="mt-1">
                                                <span class="badge badge-light border text-primary font-weight-bold label-hari" id="label-hari-<?= $idx ?>">
                                                    <i class="far fa-calendar-alt mr-1"></i> <?= esc($item['hari']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                     <td class="align-middle">
                                         <select name="override_jam[<?= $idx ?>]" class="form-control form-control-sm" style="min-width: 145px;">
                                             <?php 
                                             $previewSlots = get_jam_pelajaran_kbm();
                                             foreach ($previewSlots as $jk => $slot): 
                                             ?>
                                                 <option value="<?= esc($jk) ?>" <?= (string)$item['jam_ke'] === (string)$jk ? 'selected' : '' ?>>
                                                     Jam <?= esc($jk) ?> (<?= esc($slot['waktu_dari']) ?> - <?= esc($slot['waktu_sampai']) ?>)
                                                 </option>
                                             <?php endforeach; ?>
                                         </select>
                                     </td>
                                    <td class="align-middle">
                                        <strong><?= esc($item['nama_guru']) ?></strong>
                                        <div class="small text-muted">NIP: <?= esc($item['nip']) ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border text-gray-800">
                                            <?= esc($item['mata_pelajaran']) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!empty($kelases)): ?>
                                            <select name="override_kelas[<?= $idx ?>]" class="form-control form-control-sm">
                                                <?php foreach ($kelases as $kls): ?>
                                                    <option value="<?= $kls['id'] ?>" <?= $item['kelas_id'] == $kls['id'] ? 'selected' : '' ?>>
                                                        <?= esc($kls['nama_kelas']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" class="form-control form-control-sm" value="<?= esc($item['nama_kelas']) ?>" readonly>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-gray-800">
                                            <i class="fas fa-user-tie text-info mr-1"></i> <?= esc($item['nama_supervisor']) ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row" data-index="<?= $idx ?>" title="Batalkan baris ini">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="checkbox" name="exclude_index[]" value="<?= $idx ?>" id="exclude-<?= $idx ?>" class="d-none">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Action -->
                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <div>
                        <a href="<?= base_url('/admin/jadwal/generate') ?>" class="btn btn-secondary btn-sm shadow-sm">
                            <i class="fas fa-undo mr-1"></i> Atur Ulang Form
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted mr-3 small">
                            Total Siap Disimpan: <strong id="activeRowCount"><?= $totalTerjadwalkan ?></strong> Jadwal
                        </span>
                        <button type="submit" class="btn btn-success btn-lg shadow-sm" id="btnConfirmSave">
                            <i class="fas fa-check-double mr-1"></i> Konfirmasi & Simpan Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function renumberRows() {
        var visibleRows = document.querySelectorAll('#tablePreview tbody tr:not(.d-none)');
        visibleRows.forEach(function(row, idx) {
            var noCell = row.querySelector('.row-no');
            if (noCell) {
                noCell.innerText = idx + 1;
            }
        });
        document.getElementById('activeRowCount').innerText = visibleRows.length;
    }

    // Update badge hari realtime saat tanggal diubah
    var dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    document.querySelectorAll('.input-override-tanggal').forEach(function(input) {
        input.addEventListener('change', function() {
            var idx = this.getAttribute('data-index');
            var label = document.getElementById('label-hari-' + idx);
            if (!this.value) return;

            var parts = this.value.split('-');
            if (parts.length === 3) {
                var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                var dayIdx = d.getDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu
                var dayName = dayNames[dayIdx] || '';

                if (label) {
                    if (dayIdx === 0) {
                        label.className = 'badge badge-danger font-weight-bold label-hari';
                        label.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> ' + dayName + ' (Libur)';
                    } else {
                        label.className = 'badge badge-light border text-primary font-weight-bold label-hari';
                        label.innerHTML = '<i class="far fa-calendar-alt mr-1"></i> ' + dayName;
                    }
                }
            }
        });
    });

    // Handle remove row
    document.querySelectorAll('.btn-remove-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idx = this.getAttribute('data-index');
            var row = document.getElementById('row-' + idx);
            var excludeCb = document.getElementById('exclude-' + idx);

            if (row && excludeCb) {
                excludeCb.checked = true;
                row.classList.add('d-none');
                renumberRows();
            }
        });
    });

    // Form submit confirmation
    document.getElementById('formSaveGeneratedJadwal').addEventListener('submit', function(e) {
        var activeCount = document.querySelectorAll('#tablePreview tbody tr:not(.d-none)').length;
        if (activeCount === 0) {
            e.preventDefault();
            alert('Tidak ada jadwal aktif yang dapat disimpan.');
            return;
        }

        if (!confirm('Simpan ' + activeCount + ' jadwal supervisi ini ke database?')) {
            e.preventDefault();
        }
    });
});
</script>
<?= $this->endSection(); ?>
