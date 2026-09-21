<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Jadwal Supervisi Pembinaan</h1>
            <p class="text-muted small mb-0">
                Kelola jadwal observasi, tinjau permohonan reschedule dari guru/tendik, dan terbitkan jadwal baru.
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#createJadwalModal">
                <i class="fas fa-plus-circle mr-1"></i> Buat Jadwal Baru
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Alert Notifikasi Pengajuan Pembatalan / Reschedule dari Guru -->
    <?php if (!empty($pendingAjuanCount) && $pendingAjuanCount > 0): ?>
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap py-2 px-3 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-bell fa-2x mr-3 text-warning"></i>
                <div>
                    <strong class="text-gray-900">Perhatian: Ada <?= (int) $pendingAjuanCount; ?> pengajuan jadwal pengganti yang menunggu respon Anda.</strong>
                    <div class="small text-muted">Guru/pegawai yang berhalangan telah menyertakan alasan dan usulan tanggal pengganti. Silakan tinjau dan respon jadwal di bawah.</div>
                </div>
            </div>
            <span class="badge badge-danger px-2 py-1 mt-2 mt-md-0 font-weight-bold">
                <?= (int) $pendingAjuanCount; ?> Ajuan Menunggu
            </span>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt mr-1"></i> Daftar Jadwal Supervisi</h6>
            <?php if (!empty($tahunAktif)): ?>
                <span class="badge badge-success px-2 py-1">
                    <i class="fas fa-calendar-check mr-1"></i> <?= esc($tahunAktif['tahun_ajar'] . ' - ' . $tahunAktif['semester']); ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle small" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center font-weight-bold">
                            <th width="4%">No</th>
                            <th width="14%">Hari / Tanggal</th>
                            <th width="14%">Waktu &amp; Jam</th>
                            <th>Pegawai / Guru Binaan</th>
                            <th>Mata Pelajaran / Tugas</th>
                            <th width="9%">Kelas</th>
                            <th width="14%">Status &amp; Ajuan</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <?php 
                                $isTendik = (($schedule['jenis_ptk'] ?? '') === 'Tendik' || stripos($schedule['mata_pelajaran'] ?? '', 'tata usaha') !== false);
                                $statusAjuan = $schedule['status_ajuan'] ?? 'Tidak Ada';
                            ?>
                            <tr class="<?= ($statusAjuan === 'Diajukan') ? 'table-warning' : ''; ?>">
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <strong><?= esc($schedule['hari'] ?: format_hari_indonesia($schedule['tanggal_supervisi'])); ?></strong>,
                                    <div class="text-gray-800"><?= format_tanggal_indonesia($schedule['tanggal_supervisi'], false); ?></div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border">Jam Ke-<?= esc($schedule['jam_ke'] ?? '1'); ?></span>
                                    <?php if (!empty($schedule['waktu_dari']) && !empty($schedule['waktu_sampai'])): ?>
                                        <div class="text-muted mt-1" style="font-size: 0.78rem;">
                                            <i class="far fa-clock mr-1"></i><?= substr($schedule['waktu_dari'], 0, 5); ?> - <?= substr($schedule['waktu_sampai'], 0, 5); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <strong><?= esc($schedule['nama_guru']); ?></strong>
                                    <?php if (!empty($schedule['nip_guru'])): ?>
                                        <div class="text-muted" style="font-size: 0.75rem;">NIP: <?= esc($schedule['nip_guru']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($schedule['nama_kelompok'])): ?>
                                        <span class="badge badge-info mt-1" style="font-size: 0.72rem;"><?= esc($schedule['nama_kelompok']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <strong><?= esc($schedule['mata_pelajaran'] ?? '-'); ?></strong>
                                    <?php if (!empty($schedule['materi_supervisi'])): ?>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= esc($schedule['materi_supervisi']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($isTendik || empty($schedule['kelas']) || $schedule['kelas'] === '-'): ?>
                                        <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-briefcase mr-1"></i>Non-KBM</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary font-weight-bold px-2 py-1"><?= esc($schedule['nama_kelas'] ?? $schedule['kelas']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center">
                                    <!-- Status Utama Supervisi -->
                                    <?php if ($schedule['status'] === 'Terjadwal'): ?>
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-clock mr-1"></i>Terjadwal</span>
                                    <?php elseif ($schedule['status'] === 'Selesai'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i>Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1"><?= esc($schedule['status']); ?></span>
                                    <?php endif; ?>

                                    <!-- Status Reschedule / Pengajuan Pengganti -->
                                    <?php if ($statusAjuan === 'Diajukan'): ?>
                                        <div class="mt-2">
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Minta Reschedule
                                            </span>
                                            <div class="small text-danger mt-1 font-weight-bold" style="font-size: 0.75rem;">
                                                Usulan: <?= date('d/m/y', strtotime($schedule['usulan_tanggal'])); ?> (Jam <?= esc($schedule['usulan_jam_ke']); ?>)
                                            </div>
                                        </div>
                                    <?php elseif ($statusAjuan === 'Disetujui'): ?>
                                        <div class="mt-1">
                                            <span class="badge badge-success px-2 py-1" style="font-size: 0.72rem;">Pengganti Disetujui</span>
                                        </div>
                                    <?php elseif ($statusAjuan === 'Ditolak'): ?>
                                        <div class="mt-1">
                                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.72rem;">Ajuan Ditolak</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center" style="white-space: nowrap;">
                                    <?php if ($statusAjuan === 'Diajukan'): ?>
                                        <button type="button" class="btn btn-danger btn-sm font-weight-bold py-1 px-2 shadow-sm mb-1" data-toggle="modal" data-target="#responModal<?= $schedule['id']; ?>">
                                            <i class="fas fa-clipboard-check mr-1"></i> Respon Ajuan
                                        </button>
                                        <br>
                                    <?php endif; ?>

                                    <?php if ($schedule['status'] === 'Terjadwal'): ?>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if ($schedule['tanggal_supervisi'] <= date('Y-m-d')): ?>
                                                <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>" class="btn btn-primary" title="Mulai Melakukan Penilaian Supervisi">
                                                    <i class="fas fa-clipboard-check mr-1"></i>Nilai
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('supervisor/dokumen-ajar/view/' . $schedule['id']) ?>" class="btn btn-warning" title="Lihat Perangkat / Dokumen Ajar">
                                                <i class="fas fa-folder-open"></i> Dokumen
                                            </a>
                                            <a href="<?= base_url('supervisor/jadwal/' . $schedule['id']) ?>" class="btn btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    <?php elseif ($schedule['status'] === 'Selesai'): ?>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= base_url('supervisor/penilaian/view/' . $schedule['id']) ?>" class="btn btn-info" title="Lihat Hasil Supervisi">
                                                <i class="fas fa-file-alt"></i> Hasil
                                            </a>
                                            <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>" class="btn btn-warning" title="Edit Penilaian">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('supervisor/foto-bukti/upload/' . $schedule['id']) ?>" class="btn btn-success" title="Upload Bukti / Berita Acara">
                                                <i class="fas fa-camera"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= base_url('supervisor/jadwal/' . $schedule['id']) ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-info-circle mr-1"></i>Detail
                                        </a>
                                    <?php endif; ?>

                                    <!-- Modal Respon Ajuan Pembatalan & Jadwal Pengganti -->
                                    <?php if ($statusAjuan === 'Diajukan'): ?>
                                        <div class="modal fade text-left" id="responModal<?= $schedule['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <form action="<?= base_url('supervisor/jadwal/' . $schedule['id'] . '/respon-ajuan'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title font-weight-bold" style="font-size: 1.05rem;">
                                                                <i class="fas fa-calendar-alt mr-1"></i> Respon Pengajuan Jadwal Pengganti
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-secondary border small mb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <strong>Pegawai Binaan: <?= esc($schedule['nama_guru']); ?></strong>
                                                                    <span class="text-muted">NIP: <?= esc($schedule['nip_guru'] ?: '-'); ?></span>
                                                                </div>
                                                                <div class="mt-1">Mata Pelajaran / Tugas: <strong><?= esc($schedule['mata_pelajaran'] ?? '-'); ?></strong></div>
                                                                <div class="mt-1">Jadwal Lama: <strong><?= esc($schedule['hari']); ?>, <?= format_tanggal_indonesia($schedule['tanggal_supervisi'], false); ?> (Jam Ke-<?= esc($schedule['jam_ke']); ?>)</strong> &bull; Kelas: <?= esc($schedule['nama_kelas'] ?? $schedule['kelas'] ?? '-'); ?></div>
                                                            </div>

                                                            <div class="card border-danger mb-3">
                                                                <div class="card-header bg-danger text-white py-1 px-3 small font-weight-bold">
                                                                    <i class="fas fa-comment-dots mr-1"></i> Alasan Pembatalan dari Guru:
                                                                </div>
                                                                <div class="card-body py-2 px-3 bg-light">
                                                                    <p class="mb-0 text-dark font-italic">"<?= esc($schedule['alasan_batal']); ?>"</p>
                                                                </div>
                                                            </div>

                                                            <div class="card border-success mb-3">
                                                                <div class="card-header bg-success text-white py-1 px-3 small font-weight-bold">
                                                                    <i class="fas fa-calendar-check mr-1"></i> Usulan Jadwal Pengganti yang Diajukan:
                                                                </div>
                                                                <div class="card-body py-2 px-3">
                                                                    <div class="row small">
                                                                        <div class="col-md-4">
                                                                            <span class="text-muted d-block">Tanggal Pengganti:</span>
                                                                            <strong class="text-primary" style="font-size: 0.95rem;">
                                                                                <?= esc($schedule['usulan_hari']); ?>, <?= format_tanggal_indonesia($schedule['usulan_tanggal'], false); ?>
                                                                            </strong>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <span class="text-muted d-block">Sesi Jam Pelajaran:</span>
                                                                            <strong>Jam Ke-<?= esc($schedule['usulan_jam_ke']); ?></strong>
                                                                            <?php if (!empty($schedule['usulan_waktu_dari']) && !empty($schedule['usulan_waktu_sampai'])): ?>
                                                                                <small class="text-muted">(<?= substr($schedule['usulan_waktu_dari'], 0, 5); ?> - <?= substr($schedule['usulan_waktu_sampai'], 0, 5); ?>)</small>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <span class="text-muted d-block">Usulan Kelas:</span>
                                                                            <strong><?= esc(!empty($schedule['usulan_kelas']) ? $schedule['usulan_kelas'] : ($schedule['nama_kelas'] ?? $schedule['kelas'] ?? '-')); ?></strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold small">Catatan / Feedback Supervisor (Opsional):</label>
                                                                <textarea class="form-control" name="catatan_supervisor" rows="2" placeholder="Tuliskan catatan konfirmasi atau alasan jika menolak..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer d-flex justify-content-between">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                                                            <div>
                                                                <button type="submit" name="aksi" value="tolak" class="btn btn-outline-danger btn-sm font-weight-bold mr-1" onclick="return confirm('Apakah Anda yakin ingin MENOLAK ajuan jadwal pengganti ini? Jadwal akan tetap pada tanggal semula.');">
                                                                    <i class="fas fa-times mr-1"></i> Tolak Ajuan
                                                                </button>
                                                                <button type="submit" name="aksi" value="setujui" class="btn btn-success btn-sm font-weight-bold" onclick="return confirm('Setujui jadwal pengganti ini? Tanggal & jam supervisi akan otomatis diperbarui ke usulan guru.');">
                                                                    <i class="fas fa-check mr-1"></i> Setujui Jadwal Pengganti
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Buat Jadwal Baru oleh Supervisor -->
<div class="modal fade" id="createJadwalModal" tabindex="-1" role="dialog" aria-labelledby="createJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('supervisor/jadwal/create-jadwal'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="createJadwalModalLabel" style="font-size: 1.05rem;">
                        <i class="fas fa-plus-circle mr-1"></i> Terbitkan Jadwal Supervisi Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold small">Pilih Pegawai / Guru yang Disupervisi: <span class="text-danger">*</span></label>
                            <select class="form-control" name="guru_id" id="selectBinaanGuru" required>
                                <option value="">-- Pilih Guru / Tendik --</option>
                                <?php foreach ($binaanGurus as $bg): ?>
                                    <option value="<?= $bg['id']; ?>" data-ptk="<?= esc($bg['jenis_ptk'] ?? 'Guru'); ?>" data-mapel="<?= esc($bg['mata_pelajaran'] ?? ''); ?>">
                                        <?= esc($bg['nama']); ?><?= !empty($bg['nip']) ? ' (' . esc($bg['nip']) . ')' : ''; ?> - <?= esc($bg['mata_pelajaran'] ?: 'Umum'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold small">Kelompok Pembina (Opsional):</label>
                            <select class="form-control" name="kelompok_id">
                                <?php if (empty($myKelompoks)): ?>
                                    <option value="">-- Tanpa Kelompok --</option>
                                <?php else: ?>
                                    <?php foreach ($myKelompoks as $mk): ?>
                                        <option value="<?= $mk['id']; ?>"><?= esc($mk['nama_kelompok']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold small">Tanggal Pelaksanaan: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_supervisi" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold small">Jam Pelajaran: <span class="text-danger">*</span></label>
                            <select class="form-control" name="jam_ke" required>
                                <?php foreach (($jamPelajaranKbm ?? []) as $jk => $slot): ?>
                                    <option value="<?= esc($jk); ?>">
                                        <?= esc($slot['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold small">Kelas Observasi:</label>
                            <select class="form-control" name="kelas_id" id="selectKelasCreate">
                                <option value="">-- Non-KBM / Tendik (Tanpa Kelas) --</option>
                                <?php foreach ($kelases as $kls): ?>
                                    <option value="<?= $kls['id']; ?>">
                                        Kelas <?= esc($kls['nama_kelas']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Kosongkan jika pegawai adalah Tenaga Teknis / TU.</small>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Materi / Fokus Supervisi:</label>
                        <input type="text" class="form-control" name="materi_supervisi" placeholder="Contoh: Supervisi Proses Pembelajaran / Pengelolaan Dokumen" value="Supervisi Akademik Proses Pembelajaran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Terbitkan Jadwal Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectGuru = document.getElementById('selectBinaanGuru');
    var selectKelas = document.getElementById('selectKelasCreate');
    if (selectGuru && selectKelas) {
        selectGuru.addEventListener('change', function() {
            var selectedOpt = this.options[this.selectedIndex];
            var ptk = selectedOpt ? selectedOpt.getAttribute('data-ptk') : '';
            var mapel = selectedOpt ? selectedOpt.getAttribute('data-mapel') : '';

            if (ptk === 'Tendik' || (mapel && mapel.toLowerCase().indexOf('tata usaha') !== -1)) {
                selectKelas.value = '';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
