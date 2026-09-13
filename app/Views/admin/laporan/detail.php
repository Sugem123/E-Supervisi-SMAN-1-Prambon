<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <style>
        @media print {
            /* Sembunyikan navigasi dan tombol saat mencetak */
            .sidebar, #accordionSidebar, .navbar, .btn, footer, .modal, .sticky-footer, .scroll-to-top {
                display: none !important;
            }
            #content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                background-color: #fff !important;
            }
            #content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .container-fluid {
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 11pt !important;
            }
            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                margin-bottom: 15px !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding: 8px 15px !important;
            }
            .card-body {
                padding: 12px 15px !important;
            }

            /* Paksa card ringkasan nilai dan hasil akhir dalam 1 lembar utuh tanpa terpotong */
            .card-ringkasan-nilai,
            .ringkasan-nilai-wrapper,
            .hasil-akhir-wrapper {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .card-ringkasan-nilai {
                page-break-before: auto;
            }
            .card-ringkasan-nilai .table-responsive {
                overflow: visible !important;
            }
            .card-ringkasan-nilai table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 10px !important;
            }
            .card-ringkasan-nilai h5 {
                margin-top: 5px !important;
                margin-bottom: 8px !important;
            }
            .card-ringkasan-nilai p,
            .card-ringkasan-nilai ul {
                margin-bottom: 8px !important;
            }
            .table-bordered th, 
            .table-bordered td {
                border: 1px solid #333 !important;
                padding: 6px 8px !important;
            }
        }
    </style>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Hasil Supervisi</h1>
        <div>
            <a href="<?= base_url('admin/penilaian/form/' . ($schedule['id'] ?? '')) ?>" class="btn btn-warning btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-edit"></i> Edit Penilaian
            </a>
            <a href="<?= base_url('admin/foto-bukti/upload/' . ($schedule['id'] ?? '')) ?>" class="btn btn-info btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-camera"></i> Foto Bukti
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi/excel-detail/' . ($schedule['id'] ?? '')) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi/cetak-detail/' . ($schedule['id'] ?? '')) ?>" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
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
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['mata_pelajaran']) ? esc($schedule['mata_pelajaran']) : '' ?></td>
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
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Results -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Penilaian</h6>
            <a href="<?= base_url('admin/penilaian/form/' . ($schedule['id'] ?? '')) ?>" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-edit mr-1"></i> Edit Semua Penilaian
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($hasilList)): ?>
                <?php foreach ($hasilList as $hasil): ?>
                    <?php 
                    $jenisId = $hasil['jenis_penilaian_id'];
                    $jenisNama = '';
                    switch($jenisId) {
                        case 1: $jenisNama = 'Administrasi Guru'; break;
                        case 2: $jenisNama = 'Proses Pembelajaran'; break;
                        case 3: $jenisNama = 'Evaluasi Pembelajaran'; break;
                        case 4: $jenisNama = 'Pengembangan Diri'; break;
                        default: $jenisNama = 'Komponen Lain';
                    }
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="font-weight-bold text-gray-800 m-0"><?= esc($jenisNama) ?></h5>
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
                            <div class="d-flex align-items-center">
                                <span class="badge badge-<?= $badgeClass ?> mr-1"><?= esc($kategori) ?></span>
                                <span class="badge badge-info mr-2"><?= number_format($nilai, 2) ?></span>
                                <a href="<?= base_url('admin/penilaian/form/' . ($schedule['id'] ?? '') . '?tab=' . $jenisId) ?>" class="btn btn-outline-warning btn-sm no-print font-weight-bold" title="Edit Komponen Ini di Form">
                                    <i class="fas fa-edit mr-1"></i> Edit Komponen
                                </a>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Aspek Penilaian</th>
                                        <th width="12%" class="text-center">Skor</th>
                                        <th width="33%">Catatan</th>
                                        <th width="10%" class="text-center no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($detailResults[$jenisId] as $detail): ?>
                                            <tr id="row-aspek-<?= $detail['aspek_penilaian_id'] ?>">
                                                <td class="text-center align-middle"><?= $no++ ?></td>
                                                <td class="align-middle"><?= isset($detail['nama_aspek']) ? esc($detail['nama_aspek']) : '' ?></td>
                                                <td class="text-center align-middle cell-skor">
                                                    <span class="badge badge-<?= ($detail['skor'] == 4 ? 'success' : ($detail['skor'] == 3 ? 'primary' : ($detail['skor'] == 2 ? 'warning' : 'danger'))) ?> px-2 py-1 font-weight-bold">
                                                        <?= isset($detail['skor']) ? esc($detail['skor']) : '0' ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle cell-catatan"><?= isset($detail['catatan']) ? esc($detail['catatan']) : '-' ?></td>
                                                <td class="text-center align-middle no-print">
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-quick-edit"
                                                        data-jadwal-id="<?= esc($schedule['id']) ?>"
                                                        data-jenis-id="<?= esc($jenisId) ?>"
                                                        data-aspek-id="<?= esc($detail['aspek_penilaian_id']) ?>"
                                                        data-aspek-nama="<?= htmlspecialchars($detail['nama_aspek'] ?? '', ENT_QUOTES) ?>"
                                                        data-skor="<?= esc($detail['skor'] ?? '') ?>"
                                                        data-catatan="<?= htmlspecialchars($detail['catatan'] ?? '', ENT_QUOTES) ?>"
                                                        title="Quick Edit Aspek Ini">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada detail penilaian</td>
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
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-camera mr-1"></i> Foto Bukti Supervisi
            </h6>
            <a href="<?= base_url('admin/foto-bukti/upload/' . ($schedule['id'] ?? '')) ?>" class="btn btn-primary btn-sm no-print font-weight-bold shadow-sm">
                <i class="fas fa-upload mr-1"></i> Kelola / Upload Foto Bukti
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($fotoBukti)): ?>
                <div class="row">
                    <?php foreach ($fotoBukti as $foto): ?>
                        <div class="col-md-4 mb-3" id="detail-foto-<?= $foto['id'] ?>">
                            <div class="card border shadow-sm h-100">
                                <div style="position: relative; overflow: hidden; background: #f8f9fa;">
                                    <img src="<?= base_url($foto['file_path']) ?>" 
                                         class="card-img-top foto-preview" alt="Foto Bukti" 
                                         style="height: 200px; object-fit: cover; cursor: pointer;"
                                         data-toggle="modal" data-target="#imageModal" 
                                         data-src="<?= base_url($foto['file_path']) ?>"
                                         title="Klik untuk memperbesar foto">
                                </div>
                                <div class="card-body p-3">
                                    <?php if (!empty($foto['keterangan'])): ?>
                                        <p class="card-text text-dark small mb-2"><?= esc($foto['keterangan']) ?></p>
                                    <?php else: ?>
                                        <p class="card-text text-muted font-italic small mb-2">Tanpa keterangan</p>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2 text-muted small">
                                        <span><i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y H:i', strtotime($foto['created_at'])) ?></span>
                                        <a href="<?= base_url('admin/foto-bukti/upload/' . ($schedule['id'] ?? '')) ?>" class="text-primary no-print" title="Kelola foto">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-camera fa-2x text-gray-300 mb-2"></i>
                    <p class="text-muted mb-2">Belum ada foto bukti supervisi yang diunggah untuk jadwal ini.</p>
                    <a href="<?= base_url('admin/foto-bukti/upload/' . ($schedule['id'] ?? '')) ?>" class="btn btn-outline-primary btn-sm font-weight-bold no-print">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Foto Bukti Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calculation Summary -->
    <div class="card shadow mb-4 card-ringkasan-nilai" style="page-break-inside: avoid; break-inside: avoid;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ringkasan & Perhitungan Nilai Supervisi</h6>
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
                    $nama = isset($nama_komponen[$jenis_id]) ? $nama_komponen[$jenis_id] : 'Komponen ' . $jenis_id;
                    
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
            
            <div class="ringkasan-nilai-wrapper" style="page-break-inside: avoid; break-inside: avoid;">
                <h5>🧮 Ringkasan Nilai Supervisi</h5>
                <div class="table-responsive">
                    <table class="table table-bordered mb-3" style="page-break-inside: avoid; break-inside: avoid;">
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
                
                <div class="row mt-4 hasil-akhir-wrapper" style="page-break-inside: avoid; break-inside: avoid;">
                    <div class="col-md-6">
                        <h5>📊 Hasil Akhir</h5>
                        <p>
                            Nilai Akhir: <span class="badge badge-primary" style="font-size: 1.2rem;"><?= number_format($nilai_akhir, 2) ?>%</span><br>
                            Kategori: <span class="badge badge-<?= esc($badgeClass) ?>"><?= esc($kategori) ?></span>
                        </p>
                        
                        <h5>📋 Rumus Perhitungan</h5>
                        <p>
                            Nilai Akhir = (Total Skor / Skor Maksimal) × 100<br>
                            = (<?= esc($total_skor) ?> / <?= esc($total_maksimal) ?>) × 100<br>
                            = <?= number_format($nilai_akhir, 2) ?>%
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>📋 Kategori Penilaian</h5>
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

<!-- Quick Edit Aspek Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" aria-labelledby="quickEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="quickEditModalLabel">
                    <i class="fas fa-edit mr-1"></i> Quick Edit Aspek Penilaian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickEditForm">
                <?= csrf_field() ?>
                <input type="hidden" id="qeJadwalId" name="jadwal_id">
                <input type="hidden" id="qeJenisId" name="jenis_penilaian_id">
                <input type="hidden" id="qeAspekId" name="aspek_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Nama Aspek Penilaian:</label>
                        <p id="qeAspekNama" class="text-primary font-weight-bold mb-0 border p-2 bg-light rounded"></p>
                    </div>
                    <div class="form-group">
                        <label for="qeSkor" class="small font-weight-bold text-gray-700">Skor (1 - 4):</label>
                        <select class="form-control font-weight-bold" id="qeSkor" name="skor" required>
                            <option value="4">4 - Sangat Baik</option>
                            <option value="3">3 - Baik</option>
                            <option value="2">2 - Cukup</option>
                            <option value="1">1 - Kurang</option>
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="qeCatatan" class="small font-weight-bold text-gray-700 m-0">Catatan / Bukti Fisik:</label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-muted" id="btnAutoNote">
                                <i class="fas fa-magic mr-1"></i> Set Catatan Standar
                            </button>
                        </div>
                        <textarea class="form-control" id="qeCatatan" name="catatan" rows="3" placeholder="Tulis catatan atau bukti fisik..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnSaveQuickEdit">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    const skalaConfig = {
        1: "Kurang, Tidak memiliki bukti dukung.",
        2: "Cukup, Memiliki bukti dukung, tetapi belum lengkap.",
        3: "Baik, Memiliki bukti dukung yang lengkap, namun belum sepenuhnya sesuai.",
        4: "Sangat Baik, Memiliki bukti dukung yang lengkap dan sepenuhnya sesuai."
    };

    // Trigger quick edit modal
    $(document).on('click', '.btn-quick-edit', function() {
        var btn = $(this);
        var jadwalId = btn.data('jadwal-id');
        var jenisId = btn.data('jenis-id');
        var aspekId = btn.data('aspek-id');
        var aspekNama = btn.data('aspek-nama');
        var skor = btn.data('skor');
        var catatan = btn.data('catatan');

        $('#qeJadwalId').val(jadwalId);
        $('#qeJenisId').val(jenisId);
        $('#qeAspekId').val(aspekId);
        $('#qeAspekNama').text(aspekNama);
        $('#qeSkor').val(skor || '4');
        $('#qeCatatan').val(catatan || '');

        $('#quickEditModal').modal('show');
    });

    // Auto Note button
    $('#btnAutoNote').on('click', function() {
        var score = $('#qeSkor').val();
        if (skalaConfig[score]) {
            $('#qeCatatan').val(skalaConfig[score]);
        }
    });

    // Auto-update note on score change if currently empty or default
    $('#qeSkor').on('change', function() {
        var score = $(this).val();
        var currentNote = $('#qeCatatan').val().trim();
        var isDefault = currentNote === "" || 
                        currentNote === skalaConfig[1] || 
                        currentNote === skalaConfig[2] || 
                        currentNote === skalaConfig[3] || 
                        currentNote === skalaConfig[4];
        if (isDefault && skalaConfig[score]) {
            $('#qeCatatan').val(skalaConfig[score]);
        }
    });

    // Submit Quick Edit
    $('#quickEditForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btnSaveQuickEdit');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('admin/penilaian/quick-update') ?>',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#quickEditModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal memperbarui nilai aspek.'
                    });
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error
                });
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>