<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Jadwal Supervisi Saya</h1>
            <p class="text-muted small mb-0">
                Nama: <strong><?= esc($guru['nama'] ?? '-'); ?></strong>
                <?php if (!empty($guru['nip'])): ?>
                    &bull; NIP: <?= esc($guru['nip']); ?>
                <?php endif; ?>
                &bull; Status: <span class="badge badge-light border"><?= esc($guru['jenis_ptk'] ?? 'Guru'); ?></span>
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <span class="badge badge-success px-3 py-2">
                <i class="fas fa-calendar-check mr-1"></i> Tahun Aktif: <?= esc($tahunAktif ? ($tahunAktif['tahun_ajar'] . ' - ' . $tahunAktif['semester']) : '2026/2027 Ganjil'); ?>
            </span>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <!-- Info Card Petunjuk -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center py-2 px-3 mb-4">
        <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
        <div class="small">
            <strong>Petunjuk Supervisi:</strong>
            Untuk guru pengajar, Anda dapat <strong>memilih / mengganti kelas</strong> yang akan diobservasi melalui tombol <em>Ganti Kelas</em>. Jika Anda berhalangan pada tanggal yang ditentukan, gunakan tombol <em>Ajukan Batal &amp; Pengganti</em> untuk mengirimkan alasan dan usulan jadwal baru ke Supervisor Pembina.
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt mr-1"></i> Daftar Jadwal Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                    <p class="mb-0">Belum ada jadwal supervisi yang diterbitkan untuk Anda.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle small" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr class="text-center font-weight-bold">
                                <th width="4%">No</th>
                                <th width="15%">Hari / Tanggal</th>
                                <th width="14%">Waktu &amp; Jam</th>
                                <th width="15%">Kelas Supervisi</th>
                                <th>Mata Pelajaran &amp; Materi</th>
                                <th width="16%">Supervisor</th>
                                <th width="12%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($jadwal as $item): ?>
                                <?php 
                                    $isTendik = (($guru['jenis_ptk'] ?? '') === 'Tendik' || stripos($item['mata_pelajaran'] ?? '', 'tata usaha') !== false);
                                    $statusAjuan = $item['status_ajuan'] ?? 'Tidak Ada';
                                ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <strong><?= esc($item['hari'] ?: format_hari_indonesia($item['tanggal_supervisi'])); ?></strong>,
                                        <div class="text-gray-700"><?= format_tanggal_indonesia($item['tanggal_supervisi'], false); ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border">Jam Ke-<?= esc($item['jam_ke'] ?? '1'); ?></span>
                                        <?php if (!empty($item['waktu_dari']) && !empty($item['waktu_sampai'])): ?>
                                            <div class="text-muted mt-1" style="font-size: 0.8rem;">
                                                <i class="far fa-clock mr-1"></i><?= substr($item['waktu_dari'], 0, 5); ?> - <?= substr($item['waktu_sampai'], 0, 5); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($isTendik || empty($item['kelas']) || $item['kelas'] === '-'): ?>
                                            <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-briefcase mr-1"></i>Non-KBM / TU</span>
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 0.85rem;">
                                                    <i class="fas fa-chalkboard-teacher mr-1"></i><?= esc($item['nama_kelas'] ?? $item['kelas']); ?>
                                                </span>
                                                <?php if ($item['status'] !== 'Selesai'): ?>
                                                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-1 ml-1" data-toggle="modal" data-target="#editKelasModal<?= $item['id'] ?>" title="Ganti kelas untuk supervisi ini">
                                                        <i class="fas fa-pen fa-xs"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <strong><?= esc($item['mata_pelajaran'] ?? '-'); ?></strong>
                                        <div class="text-muted" style="font-size: 0.78rem;">
                                            <?= esc($item['materi_supervisi'] ?? 'Supervisi Akademik Pembelajaran'); ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <i class="fas fa-user-tie text-info mr-1"></i>
                                        <strong><?= esc($item['supervisor_name'] ?? '-'); ?></strong>
                                        <?php if (!empty($item['supervisor_nip'])): ?>
                                            <div class="text-muted" style="font-size: 0.75rem;">NIP: <?= esc($item['supervisor_nip']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($item['status'] === 'Selesai'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i>Selesai</span>
                                        <?php elseif ($item['status'] === 'Terjadwal'): ?>
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-clock mr-1"></i>Terjadwal</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1"><?= esc($item['status']); ?></span>
                                        <?php endif; ?>

                                        <!-- Indikator Status Pengajuan Reschedule -->
                                        <?php if ($statusAjuan === 'Diajukan'): ?>
                                            <div class="mt-2">
                                                <span class="badge badge-warning px-2 py-1 font-weight-bold" title="Usulan: <?= format_tanggal_indonesia($item['usulan_tanggal'], false); ?> Jam Ke-<?= esc($item['usulan_jam_ke']); ?>">
                                                    <i class="fas fa-hourglass-half mr-1"></i>Menunggu Respon
                                                </span>
                                                <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                                    Usulan: <?= date('d/m/y', strtotime($item['usulan_tanggal'])); ?> (Jam <?= esc($item['usulan_jam_ke']); ?>)
                                                </div>
                                                <form action="<?= base_url('guru/jadwal/' . $item['id'] . '/batalkan-ajuan'); ?>" method="post" class="mt-1" onsubmit="return confirm('Tarik kembali pengajuan pembatalan ini?');">
                                                    <?= csrf_field(); ?>
                                                    <button type="submit" class="btn btn-link btn-sm p-0 text-danger" style="font-size: 0.72rem;">
                                                        <i class="fas fa-undo mr-1"></i>Tarik Ajuan
                                                    </button>
                                                </form>
                                            </div>
                                        <?php elseif ($statusAjuan === 'Disetujui'): ?>
                                            <div class="mt-1">
                                                <span class="badge badge-success px-2 py-1" title="<?= esc($item['catatan_supervisor']); ?>">
                                                    <i class="fas fa-check mr-1"></i>Pengganti Disetujui
                                                </span>
                                            </div>
                                        <?php elseif ($statusAjuan === 'Ditolak'): ?>
                                            <div class="mt-1">
                                                <span class="badge badge-danger px-2 py-1" title="<?= esc($item['catatan_supervisor']); ?>">
                                                    <i class="fas fa-times mr-1"></i>Ajuan Ditolak
                                                </span>
                                                <?php if (!empty($item['catatan_supervisor'])): ?>
                                                    <div class="small text-danger mt-1" style="font-size: 0.72rem;">
                                                        <em>"<?= esc($item['catatan_supervisor']); ?>"</em>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center" style="white-space: nowrap;">
                                        <?php if ($item['status'] === 'Terjadwal'): ?>
                                            <div class="btn-group btn-group-sm mb-1" role="group">
                                                <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']); ?>" class="btn btn-warning" title="Upload Perangkat Pembelajaran / Dokumen Ajar">
                                                    <i class="fas fa-folder-open mr-1"></i>Dokumen
                                                </a>
                                                <?php if (!$isTendik): ?>
                                                    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#editKelasModal<?= $item['id'] ?>" title="Pilih / Ganti Kelas Observasi">
                                                        <i class="fas fa-chalkboard"></i> Kelas
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <?php if ($statusAjuan !== 'Diajukan'): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" data-toggle="modal" data-target="#ajukanBatalModal<?= $item['id'] ?>" title="Ajukan permohonan pembatalan & jadwal pengganti ke Supervisor">
                                                        <i class="fas fa-calendar-times mr-1"></i>Ajukan Batal / Pengganti
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-light btn-sm text-muted py-1 px-2" disabled>
                                                        <i class="fas fa-clock mr-1"></i>Sedang Diajukan
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= base_url('guru/hasil/' . $item['id']); ?>" class="btn btn-success btn-sm font-weight-bold">
                                                <i class="fas fa-eye mr-1"></i>Lihat Hasil
                                            </a>
                                        <?php endif; ?>

                                        <!-- Modal Ganti Kelas -->
                                        <?php if (!$isTendik && $item['status'] !== 'Selesai'): ?>
                                            <div class="modal fade text-left" id="editKelasModal<?= $item['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?= base_url('guru/jadwal/' . $item['id'] . '/update-kelas'); ?>" method="post">
                                                            <?= csrf_field(); ?>
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title font-weight-bold" style="font-size: 1rem;">
                                                                    <i class="fas fa-chalkboard mr-1"></i> Pilih Kelas untuk Supervisi
                                                                </h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group mb-3">
                                                                    <label class="font-weight-bold small">Mata Pelajaran:</label>
                                                                    <input type="text" class="form-control bg-light" value="<?= esc($item['mata_pelajaran'] ?? '-'); ?>" readonly>
                                                                </div>
                                                                <div class="form-group mb-0">
                                                                    <label class="font-weight-bold small">Pilih Rombel Kelas yang Diobservasi: <span class="text-danger">*</span></label>
                                                                    <select class="form-control" name="kelas_id" required>
                                                                        <option value="">-- Pilih Kelas --</option>
                                                                        <?php foreach ($kelases as $kls): ?>
                                                                            <option value="<?= $kls['id']; ?>" <?= ((string)($item['kelas_id'] ?? '') === (string)$kls['id']) ? 'selected' : ''; ?>>
                                                                                Kelas <?= esc($kls['nama_kelas']); ?> (Tingkat <?= esc($kls['tingkat'] ?? '-'); ?>)
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <small class="form-text text-muted">Supervisor akan melakukan observasi proses pembelajaran pada kelas yang Anda pilih.</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                                                                    <i class="fas fa-save mr-1"></i> Simpan Pilihan Kelas
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Modal Ajukan Pembatalan & Jadwal Pengganti -->
                                        <?php if ($item['status'] !== 'Selesai'): ?>
                                            <div class="modal fade text-left" id="ajukanBatalModal<?= $item['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?= base_url('guru/jadwal/' . $item['id'] . '/ajukan-batal'); ?>" method="post">
                                                            <?= csrf_field(); ?>
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title font-weight-bold" style="font-size: 1.05rem;">
                                                                    <i class="fas fa-calendar-times mr-1"></i> Ajukan Pembatalan &amp; Jadwal Pengganti
                                                                </h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-warning border-0 small mb-3">
                                                                    <i class="fas fa-exclamation-circle mr-1"></i> Jadwal Saat Ini: <strong><?= esc($item['hari']); ?>, <?= date('d M Y', strtotime($item['tanggal_supervisi'])); ?> (Jam Ke-<?= esc($item['jam_ke']); ?>)</strong> &bull; Supervisor: <strong><?= esc($item['supervisor_name']); ?></strong>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label class="font-weight-bold small">Alasan Pembatalan / Berhalangan: <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" name="alasan_batal" rows="3" placeholder="Jelaskan alasan berhalangan (contoh: Ada tugas kedinasan luar kota, pelatihan kurikulum MGMP, atau izin kesehatan)..." required><?= esc($item['alasan_batal'] ?? ''); ?></textarea>
                                                                    <small class="form-text text-muted">Alasan ini akan ditinjau langsung oleh Supervisor Pembina Anda.</small>
                                                                </div>

                                                                <div class="card bg-light border p-3 mb-2">
                                                                    <h6 class="font-weight-bold text-primary mb-3">
                                                                        <i class="fas fa-calendar-plus mr-1"></i> Usulan Jadwal Pengganti:
                                                                    </h6>
                                                                    <div class="form-row">
                                                                        <div class="form-group col-md-6 mb-3">
                                                                            <label class="font-weight-bold small">Usulan Tanggal Pengganti: <span class="text-danger">*</span></label>
                                                                            <input type="date" class="form-control" name="usulan_tanggal" value="<?= esc($item['usulan_tanggal'] ?? date('Y-m-d', strtotime('+3 days'))); ?>" min="<?= date('Y-m-d'); ?>" required>
                                                                        </div>
                                                                        <div class="form-group col-md-6 mb-3">
                                                                            <label class="font-weight-bold small">Usulan Jam Pelajaran: <span class="text-danger">*</span></label>
                                                                            <select class="form-control" name="usulan_jam_ke" required>
                                                                                <?php foreach (($jamPelajaranKbm ?? []) as $jk => $slot): ?>
                                                                                    <option value="<?= esc($jk); ?>" <?= ((string)($item['usulan_jam_ke'] ?? $item['jam_ke'] ?? '') === (string)$jk) ? 'selected' : ''; ?>>
                                                                                        <?= esc($slot['label']); ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <?php if (!$isTendik): ?>
                                                                        <div class="form-group mb-0">
                                                                            <label class="font-weight-bold small">Usulan Kelas (Opsional):</label>
                                                                            <select class="form-control" name="usulan_kelas_id">
                                                                                <option value="">-- Tetap Kelas Saat Ini (<?= esc($item['nama_kelas'] ?? $item['kelas']); ?>) --</option>
                                                                                <?php foreach ($kelases as $kls): ?>
                                                                                    <option value="<?= $kls['id']; ?>" <?= ((string)($item['usulan_kelas_id'] ?? '') === (string)$kls['id']) ? 'selected' : ''; ?>>
                                                                                        Kelas <?= esc($kls['nama_kelas']); ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                                                    <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan ke Supervisor
                                                                </button>
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
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
