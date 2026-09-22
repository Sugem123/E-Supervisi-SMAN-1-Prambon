<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Hasil Supervisi</h1>
        <div>
            <a href="<?= base_url('supervisor/hasil') ?>" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <!-- === PERBAIKAN DI BAWAH INI === -->
            <!-- 
                Saya menambahkan atribut 'onclick' untuk memaksa link terbuka di tab baru.
                'window.open(this.href, '_blank')' secara manual membuka tab baru.
                'return false;' mencegah skrip lain di halaman ini (yang mungkin rusak)
                untuk menghentikan perilaku default.
            -->
            <a href="<?= base_url('supervisor/hasil/cetak/' . ($schedule['id'] ?? '')) ?>"
                class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1"
                target="_blank"
                rel="noopener noreferrer"
                title="Cetak hasil supervisi dalam format PDF"
                onclick="window.open(this.href, '_blank'); return false;">
                <i class="fas fa-print"></i>
                <span>Cetak PDF</span>
            </a>
            <!-- === AKHIR PERBAIKAN === -->

        </div>
    </div>

    <!-- Teacher Info -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Guru</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Guru</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>NIP</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['nip_guru']) ? esc($schedule['nip_guru']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Pangkat/Golongan</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['pangkat_golongan']) ? esc($schedule['pangkat_golongan']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['mata_pelajaran']) && $schedule['mata_pelajaran'] !== '' ? esc($schedule['mata_pelajaran']) : (isset($schedule['guru_mata_pelajaran']) ? esc($schedule['guru_mata_pelajaran']) : '') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Materi yang Disupervisi</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['materi_supervisi']) ? esc($schedule['materi_supervisi']) : '-' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tanggal Supervisi</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['tanggal_supervisi']) ? date('d M Y', strtotime($schedule['tanggal_supervisi'])) : '' ?></td>
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
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['kelas']) ? esc($schedule['kelas']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jam Ke-</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['jam_ke']) ? esc($schedule['jam_ke']) : '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Results -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Penilaian</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($hasilList)): ?>
                <?php foreach ($hasilList as $hasil): ?>
                    <?php
                    $jenisId = $hasil['jenis_penilaian_id'];
                    $jenisNama = !empty($hasil['nama_jenis']) ? $hasil['nama_jenis'] : '';
                    if (empty($jenisNama)) {
                        switch ($jenisId) {
                            case 1: $jenisNama = 'Administrasi Guru'; break;
                            case 2: $jenisNama = 'Proses Pembelajaran'; break;
                            case 3: $jenisNama = 'Evaluasi Pembelajaran'; break;
                            case 4: $jenisNama = 'Pengembangan Diri'; break;
                            default: $jenisNama = 'Komponen ' . $jenisId;
                        }
                    }
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5><?= esc($jenisNama) ?></h5>
                            <?php
                            // Hitung nilai secara dinamis berdasarkan detail penilaian
                            $total_skor = 0;
                            $jumlah_aspek = 0;

                            if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                                foreach ($detailResults[$jenisId] as $detail) {
                                    $total_skor += isset($detail['skor']) ? $detail['skor'] : 0;
                                    $jumlah_aspek++;
                                }
                            }

                            // Skor maksimal per komponen (4 x jumlah aspek)
                            $skor_maksimal = $jumlah_aspek * 4;

                            // Hitung persentase
                            $nilai = $skor_maksimal > 0 ? ($total_skor / $skor_maksimal) * 100 : 0;

                            if ($nilai >= 86) {
                                $badgeClass = 'success';
                                $kategori = 'Baik Sekali';
                            } elseif ($nilai >= 70) {
                                $badgeClass = 'primary';
                                $kategori = 'Baik';
                            } elseif ($nilai >= 55) {
                                $badgeClass = 'warning';
                                $kategori = 'Cukup';
                            } else {
                                $badgeClass = 'danger';
                                $kategori = 'Kurang';
                            }
                            ?>
                            <div>
                                <span class="badge badge-<?= $badgeClass ?>"><?= esc($kategori) ?></span>
                                <span class="badge badge-info"><?= number_format($nilai, 2) ?></span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Aspek Penilaian</th>
                                        <th width="15%">Skor</th>
                                        <th width="30%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($detailResults[$jenisId] as $detail): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= isset($detail['nama_aspek']) ? esc($detail['nama_aspek']) : '' ?></td>
                                                <td class="text-center"><?= isset($detail['skor']) ? esc($detail['skor']) : '0' ?></td>
                                                <td><?= isset($detail['catatan']) && $detail['catatan'] !== null ? nl2br(esc($detail['catatan'])) : '' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada detail penilaian</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($hasil['rekomendasi'])): ?>
                            <div class="alert alert-info">
                                <strong>Rekomendasi Perbaikan:</strong><br>
                                <?= esc($hasil['rekomendasi']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Belum ada hasil penilaian.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Photo Evidence -->
    <?php if (!empty($fotoBukti)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Foto Bukti Supervisi</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($fotoBukti as $foto): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img src="<?= base_url($foto['file_path']) ?>"
                                    class="card-img-top foto-preview" alt="Foto Bukti"
                                    style="height: 200px; object-fit: cover; cursor: pointer;"
                                    data-toggle="modal" data-target="#imageModal"
                                    data-src="<?= base_url($foto['file_path']) ?>">
                                <div class="card-body">
                                    <?php if (!empty($foto['keterangan'])): ?>
                                        <p class="card-text"><?= esc($foto['keterangan']) ?></p>
                                    <?php endif; ?>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <?= date('d M Y H:i', strtotime($foto['created_at'])) ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Calculation Summary -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Perhitungan Nilai Supervisi</h6>
        </div>
        <div class="card-body">
            <?php
            // Inisialisasi variabel perhitungan
            $komponen_nilai = [];
            $total_skor = 0;
            $total_maksimal = 0;

            // Nama komponen
            $nama_komponen = [
                1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
            ];

            // Hitung nilai untuk setiap komponen
            if (!empty($hasilList)) {
                foreach ($hasilList as $hasil) {
                    $jenis_id = $hasil['jenis_penilaian_id'];
                    $nama = !empty($hasil['nama_jenis']) ? $hasil['nama_jenis'] : (isset($nama_komponen[$jenis_id]) ? $nama_komponen[$jenis_id] : ('Komponen ' . $jenis_id));

                    // Hitung total skor dan jumlah aspek untuk komponen ini
                    $skor_komponen = 0;
                    $jumlah_aspek = 0;

                    if (isset($detailResults[$jenis_id]) && !empty($detailResults[$jenis_id])) {
                        foreach ($detailResults[$jenis_id] as $detail) {
                            $skor_komponen += isset($detail['skor']) ? $detail['skor'] : 0;
                            $jumlah_aspek++;
                        }
                    }

                    // Skor maksimal per komponen (4 x jumlah aspek)
                    $skor_maksimal = $jumlah_aspek * 4;

                    // Hitung persentase
                    $persentase = $skor_maksimal > 0 ? ($skor_komponen / $skor_maksimal) * 100 : 0;

                    // Simpan data komponen
                    $komponen_nilai[$jenis_id] = [
                        'nama' => $nama,
                        'skor' => $skor_komponen,
                        'maksimal' => $skor_maksimal,
                        'persentase' => $persentase
                    ];

                    // Tambahkan ke total keseluruhan
                    $total_skor += $skor_komponen;
                    $total_maksimal += $skor_maksimal;
                }
            }

            // Hitung nilai akhir
            $nilai_akhir = $total_maksimal > 0 ? ($total_skor / $total_maksimal) * 100 : 0;

            // Tentukan kategori
            if ($nilai_akhir >= 86) {
                $kategori = 'Baik Sekali';
                $badgeClass = 'success';
            } elseif ($nilai_akhir >= 70) {
                $kategori = 'Baik';
                $badgeClass = 'info';
            } elseif ($nilai_akhir >= 55) {
                $kategori = 'Cukup';
                $badgeClass = 'warning';
            } else {
                $kategori = 'Kurang';
                $badgeClass = 'danger';
            }
            ?>

            <h5>Perhitungan Nilai</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Komponen Penilaian</th>
                            <th>Skor Diperoleh</th>
                            <th>Skor Maksimal</th>
                            <th>Presentase (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($komponen_nilai)): ?>
                            <?php foreach ($komponen_nilai as $komponen): ?>
                                <tr>
                                    <td><?= isset($komponen['nama']) ? esc($komponen['nama']) : '' ?></td>
                                    <td><?= isset($komponen['skor']) ? esc($komponen['skor']) : '0' ?></td>
                                    <td><?= isset($komponen['maksimal']) ? esc($komponen['maksimal']) : '0' ?></td>
                                    <td><?= isset($komponen['persentase']) ? number_format($komponen['persentase'], 2) : '0.00' ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data penilaian</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="table-primary font-weight-bold">
                            <td>Total</td>
                            <td><?= esc($total_skor) ?></td>
                            <td><?= esc($total_maksimal) ?></td>
                            <td><?= number_format($nilai_akhir, 2) ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Hasil Akhir</h5>
                    <p>
                        Nilai Akhir: <span class="badge badge-primary" style="font-size: 1.2rem;"><?= number_format($nilai_akhir, 2) ?>%</span><br>
                        Kategori: <span class="badge badge-<?= esc($badgeClass) ?>"><?= esc($kategori) ?></span>
                    </p>

                    <h5>Rumus Perhitungan</h5>
                    <p>
                        Nilai Akhir = (Total Skor / Skor Maksimal) x 100<br>
                        = (<?= esc($total_skor) ?> / <?= esc($total_maksimal) ?>) x 100<br>
                        = <?= number_format($nilai_akhir, 2) ?>%
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Kategori Penilaian</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Baik Sekali
                            <span class="badge badge-success badge-pill">86-100%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Baik
                            <span class="badge badge-info badge-pill">70-85%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cukup
                            <span class="badge badge-warning badge-pill">55-69%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kurang
                            <span class="badge badge-danger badge-pill">0-54%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Preview Foto Bukti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Preview Foto Bukti" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle image click for preview
        const images = document.querySelectorAll('.foto-preview');
        const modalImage = document.getElementById('modalImage');

        images.forEach(function(img) {
            img.addEventListener('click', function() {
                const src = this.getAttribute('data-src');
                modalImage.src = src;
            });
        });
    });
</script>
<?= $this->endSection() ?>
