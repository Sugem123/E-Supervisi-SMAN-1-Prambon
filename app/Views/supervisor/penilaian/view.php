<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hasil Penilaian Supervisi</h1>
        <div>
            <?php if (isset($schedule['id']) && !empty($schedule['id'])): ?>
                <a href="<?= base_url('supervisor/penilaian/print/' . $schedule['id']) ?>" class="btn btn-success btn-sm no-print">
                    <i class="fas fa-file-pdf"></i> Unduh PDF
                </a>
            <?php else: ?>
                <button class="btn btn-success btn-sm no-print" disabled>
                    <i class="fas fa-file-pdf"></i> Unduh PDF
                </button>
            <?php endif; ?>
            <button class="btn btn-primary btn-sm no-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
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
                            <td><?= isset($schedule['nip']) ? esc($schedule['nip']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= isset($schedule['mata_pelajaran']) && $schedule['mata_pelajaran'] !== '' ? esc($schedule['mata_pelajaran']) : (isset($schedule['guru_mata_pelajaran']) ? esc($schedule['guru_mata_pelajaran']) : '') ?></td>
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
            <?php if (isset($schedule['status']) && $schedule['status'] == 'Terjadwal'): ?>
                <button type="button" class="btn btn-success btn-sm" id="completeBtn">
                    <i class="fas fa-check"></i> Selesaikan Supervisi
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (!empty($hasilPenilaian)): ?>
                <?php foreach ($hasilPenilaian as $hasil): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5><?= isset($hasil['nama_jenis']) ? esc($hasil['nama_jenis']) : '' ?></h5>
                            <?php 
                            $nilai = isset($hasil['nilai_akhir']) ? $hasil['nilai_akhir'] : 0;
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
                                    <?php if (isset($detailResults[$hasil['jenis_penilaian_id']]) && !empty($detailResults[$hasil['jenis_penilaian_id']])): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($detailResults[$hasil['jenis_penilaian_id']] as $detail): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= isset($detail['nama_aspek']) ? esc($detail['nama_aspek']) : '' ?></td>
                                                <td class="text-center"><?= isset($detail['skor']) ? esc($detail['skor']) : '0' ?></td>
                                                <td><?= isset($detail['catatan']) ? esc($detail['catatan']) : '-' ?></td>
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
                        
                        <div class="form-group">
                            <label for="rekomendasi-<?= $hasil['id'] ?>"><strong>Rekomendasi Perbaikan:</strong></label>
                            <?php if (isset($schedule['status']) && $schedule['status'] == 'Terjadwal'): ?>
                                <textarea class="form-control rekomendasi-input" id="rekomendasi-<?= $hasil['id'] ?>" 
                                          name="rekomendasi[<?= $hasil['id'] ?>]" rows="3"
                                          placeholder="Masukkan rekomendasi perbaikan..."><?= isset($hasil['rekomendasi']) ? esc($hasil['rekomendasi']) : '' ?></textarea>
                            <?php else: ?>
                                <?php if (!empty($hasil['rekomendasi'])): ?>
                                    <div class="alert alert-info mt-2">
                                        <?= esc($hasil['rekomendasi']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary mt-2">
                                        Tidak ada rekomendasi
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Belum ada hasil penilaian.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringkasan Hasil -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ringkasan Hasil</h6>
        </div>
        <div class="card-body">
            <?php 
            $komponen_nilai = [];
            $total_skor = 0;
            $total_maksimal = 0;
            $nama_komponen = [
                1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
            ];

            if (!empty($hasilPenilaian)) {
                foreach ($hasilPenilaian as $hasil) {
                    $jenis_id = $hasil['jenis_penilaian_id'];
                    // === INI ADALAH PERBAIKANNYA (underscore dihapus) ===
                    $nama = isset($nama_komponen[$jenis_id]) ? $nama_komponen[$jenis_id] : ('Komponen ' . $jenis_id);
                    $skor_komponen = 0;
                    $jumlah_aspek = 0;
                    
                    if (isset($detailResults[$jenis_id]) && !empty($detailResults[$jenis_id])) {
                        foreach ($detailResults[$jenis_id] as $detail) {
                            $skor_komponen += isset($detail['skor']) ? $detail['skor'] : 0;
                            $jumlah_aspek++;
                        }
                    }
                    
                    // Tetapkan skor maksimal berdasarkan jumlah aspek * 4 (skor maksimal per aspek)
                    $skor_maksimal = $jumlah_aspek * 4;
                    
                    // Hitung persentase berdasarkan rumus proporsional
                    // Rumus: (skor_diperoleh / skor_maksimal) * 100
                    $persentase = $skor_maksimal > 0 ? ($skor_komponen / $skor_maksimal) * 100 : 0;
                    
                    $komponen_nilai[$jenis_id] = [
                        'nama' => $nama,
                        'skor' => $skor_komponen,
                        'maksimal' => $skor_maksimal,
                        'persentase' => $persentase
                    ];
                    
                    $total_skor += $skor_komponen;
                    $total_maksimal += $skor_maksimal;
                }
            }
            
            // Hitung nilai akhir berdasarkan rumus proporsional
            // Rumus: (total_skor / total_maksimal) * 100
            $nilai_akhir = $total_maksimal > 0 ? ($total_skor / $total_maksimal) * 100 : 0;
            
            if ($nilai_akhir >= 86) { $kategori = 'Baik Sekali'; $badgeClass = 'success'; }
            elseif ($nilai_akhir >= 70) { $kategori = 'Baik'; $badgeClass = 'info'; }
            elseif ($nilai_akhir >= 55) { $kategori = 'Cukup'; $badgeClass = 'warning'; }
            else { $kategori = 'Kurang'; $badgeClass = 'danger'; }
            ?>
            
            <h5>🧮 Perhitungan Nilai</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Komponen Penilaian</th>
                            <th>Skor Diperoleh</th>
                            <th>Presentase (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($komponen_nilai)): ?>
                            <?php foreach ($komponen_nilai as $komponen): ?>
                            <tr>
                                <td><?= isset($komponen['nama']) ? esc($komponen['nama']) : '' ?></td>
                                <td><?= isset($komponen['skor']) ? esc($komponen['skor']) : '0' ?></td>
                                <td><?= isset($komponen['persentase']) ? number_format($komponen['persentase'], 2) : '0.00' ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data penilaian</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="table-primary font-weight-bold">
                            <td>Total</td>
                            <td><?= esc($total_skor) ?></td>
                            <td><?= number_format($nilai_akhir, 2) ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
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

    <!-- Photo Evidence -->
    <?php if (isset($schedule['status']) && $schedule['status'] == 'Terjadwal'): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Foto Bukti Supervisi</h6>
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="jadwal_id" value="<?= isset($schedule['id']) ? $schedule['id'] : '' ?>">
                    <div class="form-group">
                        <label for="photos">Upload Foto Bukti:</label>
                        <input type="file" class="form-control-file" id="photos" name="photos[]" multiple accept="image/*">
                        <small class="form-text text-muted">Anda dapat memilih beberapa foto sekaligus</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Foto</button>
                </form>
                
                <div class="row mt-4" id="photoContainer">
                    <?php if (!empty($uploadedPhotos)): ?>
                        <?php foreach ($uploadedPhotos as $photo): ?>
                            <div class="col-md-4 mb-3 photo-item" data-id="<?= $photo['id'] ?>">
                                <div class="card">
                                    <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top" alt="Foto Bukti Supervisi" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <button class="btn btn-danger btn-sm delete-photo" data-id="<?= $photo['id'] ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($uploadedPhotos)): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Bukti Supervisi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($uploadedPhotos as $photo): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top" alt="Foto Bukti Supervisi" style="height: 200px; object-fit: cover;">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Hide non-print elements when printing */
    @media print {
        .no-print {
            display: none !important;
        }
        
        /* Make tables responsive when printing */
        .table-responsive {
            display: block !important;
        }
        
        /* Adjust table width for printing */
        .table {
            width: 100% !important;
        }
        
        /* Ensure content fits on printed page */
        .container-fluid, .card-body {
            max-width: 100%;
            padding: 10px;
        }
    }
</style>

<script>
$(document).ready(function() {
    // Save rekomendasi when input changes
    $('.rekomendasi-input').on('blur', function() {
        var hasilId = $(this).attr('id').split('-')[1];
        var rekomendasi = $(this).val();
        
        $.ajax({
            url: '<?= base_url('supervisor/penilaian/save') ?>',
            method: 'POST',
            data: {
                hasil_id: hasilId,
                rekomendasi: rekomendasi,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if(response.status === 'success') {
                    // Tidak perlu menampilkan pesan karena ini adalah auto-save
                } else {
                    alert('Gagal menyimpan rekomendasi: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menyimpan rekomendasi');
            }
        });
    });
    
    // Complete supervision
    $('#completeBtn').click(function() {
        if(confirm('Apakah Anda yakin ingin menyelesaikan supervisi ini? Setelah selesai, Anda tidak dapat mengedit lagi.')) {
            $.ajax({
                url: '<?= base_url('supervisor/penilaian/complete/' . (isset($schedule['id']) ? $schedule['id'] : '')) ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if(response.status === 'success') {
                        alert('Supervisi berhasil diselesaikan!');
                        window.location.href = response.redirect;
                    } else {
                        alert('Gagal menyelesaikan supervisi: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyelesaikan supervisi');
                }
            });
        }
    });
    
    // Upload photo
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('supervisor/foto-bukti/upload/' . (isset($schedule['id']) ? $schedule['id'] : '')) ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status === 'success') {
                    alert('Foto berhasil diupload!');
                    location.reload();
                } else {
                    alert('Gagal mengupload foto: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengupload foto');
            }
        });
    });
    
    // Delete photo
    $('.delete-photo').click(function() {
        var photoId = $(this).data('id');
        var photoItem = $(this).closest('.photo-item');
        
        if(confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
            $.ajax({
                url: '<?= base_url('supervisor/foto-bukti/delete/') ?>' + photoId,
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if(response.status === 'success') {
                        photoItem.remove();
                        alert('Foto berhasil dihapus!');
                    } else {
                        alert('Gagal menghapus foto: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus foto');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>