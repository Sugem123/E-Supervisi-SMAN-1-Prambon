<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Detail Hasil Supervisi</h1>
            <p class="text-muted small mb-0">Rincian lembar penilaian, catatan aspek, dan rekomendasi hasil supervisi akademik.</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="<?= base_url('guru/hasil/cetak/' . $schedule['id']) ?>" target="_blank" class="btn btn-danger btn-sm shadow-sm mr-1">
                <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
            </a>
            <a href="<?= base_url('guru/hasil') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <?php
        // Hitung rata-rata nilai akhir dan predikat keseluruhan
        $totalNilaiSemua = 0;
        $countNilaiSemua = 0;
        foreach ($hasilList as $h) {
            if (!is_null($h['nilai_akhir'])) {
                $totalNilaiSemua += $h['nilai_akhir'];
                $countNilaiSemua++;
            }
        }
        $overallScore = $countNilaiSemua > 0 ? ($totalNilaiSemua / $countNilaiSemua) : 0;
        if ($overallScore >= 86) {
            $overallPred = 'Baik Sekali';
            $overallBadge = 'success';
        } elseif ($overallScore >= 70) {
            $overallPred = 'Baik';
            $overallBadge = 'info';
        } elseif ($overallScore >= 55) {
            $overallPred = 'Cukup';
            $overallBadge = 'warning';
        } else {
            $overallPred = 'Kurang';
            $overallBadge = 'danger';
        }
    ?>

    <!-- Card 1: Informasi Identitas Supervisi -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-id-card mr-1"></i> Identitas Pelaksanaan Supervisi
            </h6>
            <div>
                <span class="badge badge-<?= $overallBadge ?> px-3 py-2" style="font-size: 0.95rem;">
                    Nilai Akhir: <?= number_format($overallScore, 2) ?> (<?= $overallPred ?>)
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-right">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="38%"><i class="fas fa-user mr-2 text-primary"></i>Nama Guru</td>
                            <td width="2%">:</td>
                            <td class="font-weight-bold text-gray-800"><?= esc($guru['nama_lengkap'] ?? (session()->get('username') ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-id-badge mr-2 text-primary"></i>NIP / NUPTK</td>
                            <td>:</td>
                            <td class="text-gray-800"><?= esc($guru['nip'] ?? ($guru['nuptk'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-book mr-2 text-primary"></i>Mata Pelajaran</td>
                            <td>:</td>
                            <td class="font-weight-bold text-gray-800"><?= esc($schedule['mata_pelajaran'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-chalkboard mr-2 text-primary"></i>Kelas</td>
                            <td>:</td>
                            <td class="text-gray-800">
                                <span class="badge badge-light border text-gray-800">
                                    <?= esc($schedule['nama_kelas'] ?? ($schedule['kelas'] ?? '-')) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="38%"><i class="fas fa-user-tie mr-2 text-info"></i>Supervisor</td>
                            <td width="2%">:</td>
                            <td class="font-weight-bold text-gray-800"><?= esc($schedule['supervisor_name'] ?? ($hasilList[0]['supervisor_name'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-calendar-alt mr-2 text-info"></i>Tanggal & Waktu</td>
                            <td>:</td>
                            <td class="text-gray-800">
                                <?= format_hari_indonesia($schedule['tanggal_supervisi']) ?>, <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?>
                                <span class="badge badge-light border ml-1">Jam Ke-<?= esc($schedule['jam_ke'] ?? '-') ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-graduation-cap mr-2 text-info"></i>Tahun Ajaran</td>
                            <td>:</td>
                            <td class="text-gray-800">
                                <?= esc($schedule['tahun_ajar'] ?? '-') ?>
                                <small class="text-muted">(<?= esc($schedule['semester'] ?? '-') ?>)</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-check-circle mr-2 text-success"></i>Status</td>
                            <td>:</td>
                            <td><span class="badge badge-success">Supervisi Selesai</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Rincian Penilaian Per Komponen (Tabs) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks mr-1"></i> Rincian Lembar Penilaian Aspek (4 Komponen)
            </h6>
        </div>
        <div class="card-body">
            <?php
                $komponenNames = [
                    1 => ['nama' => '1. Administrasi Perencanaan', 'icon' => 'fas fa-file-alt'],
                    2 => ['nama' => '2. Proses Pembelajaran', 'icon' => 'fas fa-chalkboard-teacher'],
                    3 => ['nama' => '3. Evaluasi Pembelajaran', 'icon' => 'fas fa-chart-bar'],
                    4 => ['nama' => '4. Pengembangan Diri', 'icon' => 'fas fa-user-graduate']
                ];
            ?>

            <!-- Nav Pills -->
            <ul class="nav nav-pills mb-3 flex-column flex-sm-row" id="komponenTabs" role="tablist">
                <?php foreach ($hasilList as $idx => $hasil): ?>
                    <?php 
                        $jId = $hasil['jenis_penilaian_id'];
                        $meta = $komponenNames[$jId] ?? ['nama' => 'Komponen ' . $jId, 'icon' => 'fas fa-check'];
                    ?>
                    <li class="nav-item mr-1 mb-1">
                        <a class="nav-link <?= $idx === 0 ? 'active' : '' ?>" 
                           id="tab-komponen-<?= $jId ?>-tab" 
                           data-toggle="pill" 
                           href="#tab-komponen-<?= $jId ?>" 
                           role="tab" 
                           aria-controls="tab-komponen-<?= $jId ?>" 
                           aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <i class="<?= $meta['icon'] ?> mr-1"></i>
                            <?= $meta['nama'] ?>
                            <?php if (!is_null($hasil['nilai_akhir'])): ?>
                                <span class="badge badge-light ml-1"><?= number_format($hasil['nilai_akhir'], 1) ?>%</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content" id="komponenTabsContent">
                <?php foreach ($hasilList as $idx => $hasil): ?>
                    <?php 
                        $jId = $hasil['jenis_penilaian_id'];
                        $meta = $komponenNames[$jId] ?? ['nama' => 'Komponen ' . $jId, 'icon' => 'fas fa-check'];
                        $aspekList = $detailResults[$jId] ?? [];
                    ?>
                    <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>" 
                         id="tab-komponen-<?= $jId ?>" 
                         role="tabpanel" 
                         aria-labelledby="tab-komponen-<?= $jId ?>-tab">

                        <!-- Komponen Header Summary -->
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
                            <div>
                                <h5 class="font-weight-bold text-gray-800 m-0">
                                    <i class="<?= $meta['icon'] ?> text-primary mr-1"></i> <?= $meta['nama'] ?>
                                </h5>
                                <small class="text-muted">Total <?= count($aspekList) ?> butir aspek dinilai</small>
                            </div>
                            <div class="text-right">
                                <?php if (!is_null($hasil['nilai_akhir'])): ?>
                                    <span class="badge badge-primary px-3 py-2" style="font-size: 0.95rem;">
                                        Nilai: <?= number_format($hasil['nilai_akhir'], 2) ?>% (<?= esc($hasil['ketercapaian'] ?? '-') ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary px-2 py-1">Belum Dinilai</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Table Aspek Penilaian -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="45%">Aspek Penilaian</th>
                                        <th class="text-center" width="18%">Skor & Predikat</th>
                                        <th width="32%">Catatan / Bukti Fisik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($aspekList)): ?>
                                        <?php $no = 1; foreach ($aspekList as $aspek): ?>
                                            <?php
                                                $skor = $aspek['skor'] ?? 0;
                                                $skorBadge = 'secondary';
                                                $skorText = 'Belum';
                                                if ($skor == 4) { $skorBadge = 'success'; $skorText = '4 - Sangat Baik'; }
                                                elseif ($skor == 3) { $skorBadge = 'primary'; $skorText = '3 - Baik'; }
                                                elseif ($skor == 2) { $skorBadge = 'warning'; $skorText = '2 - Cukup'; }
                                                elseif ($skor == 1) { $skorBadge = 'danger'; $skorText = '1 - Kurang'; }
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                                <td class="align-middle">
                                                    <span class="text-gray-800"><?= esc($aspek['nama_aspek']) ?></span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-<?= $skorBadge ?> px-2 py-1">
                                                        <?= $skorText ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-gray-700">
                                                        <?= !empty($aspek['catatan']) ? nl2br(esc($aspek['catatan'])) : '<em class="text-muted">Tidak ada catatan</em>' ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada data aspek untuk komponen ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Rekomendasi Box -->
                        <div class="card mt-3 bg-light border">
                            <div class="card-body p-3">
                                <h6 class="font-weight-bold text-gray-800 mb-2">
                                    <i class="fas fa-comment-dots text-primary mr-1"></i> Rekomendasi & Catatan Perbaikan Supervisor:
                                </h6>
                                <p class="mb-0 text-gray-700">
                                    <?= !empty($hasil['rekomendasi']) ? nl2br(esc($hasil['rekomendasi'])) : '<em class="text-muted">Belum ada catatan rekomendasi spesifik untuk komponen ini.</em>' ?>
                                </p>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Card 3: Perhitungan Nilai Supervisi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calculator mr-1"></i> Rekapitulasi & Rumus Perhitungan Nilai Akhir
            </h6>
        </div>
        <div class="card-body">
            <?php
                $komponen_rekap = [];
                $total_skor_all = 0;
                $total_maks_all = 0;

                foreach ($hasilList as $hasil) {
                    $jId = $hasil['jenis_penilaian_id'];
                    $nama = $komponenNames[$jId]['nama'] ?? 'Komponen ' . $jId;
                    
                    $skor_komp = 0;
                    $jml_aspek = 0;
                    if (isset($detailResults[$jId])) {
                        foreach ($detailResults[$jId] as $d) {
                            $skor_komp += (int)($d['skor'] ?? 0);
                            $jml_aspek++;
                        }
                    }
                    $maks_komp = $jml_aspek * 4;
                    $persen_komp = $maks_komp > 0 ? ($skor_komp / $maks_komp) * 100 : 0;

                    $komponen_rekap[] = [
                        'nama' => $nama,
                        'skor' => $skor_komp,
                        'maksimal' => $maks_komp,
                        'persentase' => $persen_komp
                    ];

                    $total_skor_all += $skor_komp;
                    $total_maks_all += $maks_komp;
                }

                $nilai_akhir_hitung = $total_maks_all > 0 ? ($total_skor_all / $total_maks_all) * 100 : 0;
            ?>

            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-left">Komponen Penilaian</th>
                            <th width="20%">Skor Diperoleh</th>
                            <th width="20%">Skor Maksimal</th>
                            <th width="20%">Persentase Capaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($komponen_rekap as $k): ?>
                            <tr>
                                <td class="text-left font-weight-bold text-gray-800"><?= $k['nama'] ?></td>
                                <td><?= $k['skor'] ?></td>
                                <td><?= $k['maksimal'] ?></td>
                                <td class="font-weight-bold"><?= number_format($k['persentase'], 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-primary font-weight-bold">
                            <td class="text-left">TOTAL AKUMULASI</td>
                            <td><?= $total_skor_all ?></td>
                            <td><?= $total_maks_all ?></td>
                            <td style="font-size: 1.1rem;"><?= number_format($nilai_akhir_hitung, 2) ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 border-right">
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-square-root-alt text-primary mr-1"></i> Rumus Perhitungan:
                    </h6>
                    <div class="p-3 bg-light rounded border text-muted small">
                        <strong>Nilai Akhir</strong> = (Total Skor Diperoleh / Total Skor Maksimal) × 100<br>
                        = (<?= $total_skor_all ?> / <?= $total_maks_all ?>) × 100<br>
                        = <strong class="text-primary" style="font-size: 1.1rem;"><?= number_format($nilai_akhir_hitung, 2) ?>%</strong>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-bookmark text-primary mr-1"></i> Standar Kualifikasi Nilai:
                    </h6>
                    <div class="row small">
                        <div class="col-6 mb-2">
                            <span class="badge badge-success px-2 py-1 mr-1">86 - 100%</span>
                            <strong>Baik Sekali</strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="badge badge-info px-2 py-1 mr-1">70 - 85%</span>
                            <strong>Baik</strong>
                        </div>
                        <div class="col-6">
                            <span class="badge badge-warning px-2 py-1 mr-1">55 - 69%</span>
                            <strong>Cukup</strong>
                        </div>
                        <div class="col-6">
                            <span class="badge badge-danger px-2 py-1 mr-1">0 - 54%</span>
                            <strong>Kurang</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Foto Bukti Supervisi (Jika Ada) -->
    <?php if (!empty($fotoBukti)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-camera mr-1"></i> Dokumentasi Foto Bukti Supervisi
                </h6>
                <span class="badge badge-info"><?= count($fotoBukti) ?> Foto Terunggah</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($fotoBukti as $foto): ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 border shadow-sm">
                                <img src="<?= base_url($foto['file_path']) ?>" 
                                     class="card-img-top img-thumbnail-preview" 
                                     alt="Foto Dokumentasi" 
                                     style="height: 160px; object-fit: cover; cursor: pointer;"
                                     data-src="<?= base_url($foto['file_path']) ?>"
                                     data-keterangan="<?= esc($foto['keterangan'] ?? 'Tanpa keterangan') ?>">
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <p class="small text-muted mb-2 text-truncate" title="<?= esc($foto['keterangan'] ?? '-') ?>">
                                        <?= esc($foto['keterangan'] ?: 'Dokumentasi supervisi') ?>
                                    </p>
                                    <button type="button" class="btn btn-outline-info btn-xs btn-block py-1 btn-modal-preview"
                                            data-src="<?= base_url($foto['file_path']) ?>"
                                            data-keterangan="<?= esc($foto['keterangan'] ?? '') ?>">
                                        <i class="fas fa-search-plus mr-1"></i> Perbesar Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Preview Foto Bukti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="modalImgPreview" src="" class="img-fluid rounded" style="max-height: 75vh;">
                <p id="modalImgDesc" class="mt-2 text-muted mb-0 font-italic"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox modal preview untuk gambar bukti
    $(document).on('click', '.btn-modal-preview, .img-thumbnail-preview', function() {
        var src = $(this).data('src');
        var ket = $(this).data('keterangan') || 'Tanpa keterangan';
        $('#modalImgPreview').attr('src', src);
        $('#modalImgDesc').text(ket);
        $('#imagePreviewModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>