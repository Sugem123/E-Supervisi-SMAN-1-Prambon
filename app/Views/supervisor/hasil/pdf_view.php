<!DOCTYPE html>
<html>

<head>
    <title>Detail Hasil Supervisi</title>
    <meta charset="UTF-8">
    <style>
        /* Pengaturan Dasar Dokumen */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 10px 20px 35px 20px;
            color: #333;
        }

        /* Style untuk Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 20px;
            right: 20px;
            height: 30px;
            font-size: 8.5px;
            border-top: 1px solid #999;
            padding-top: 3px;
            color: #555;
            background-color: white;
            /* Menambahkan background agar tidak transparan */
        }

        /* Style untuk Nomor Halaman */
        /* Catatan: counter(page) dan counter(pages) adalah fitur Paged Media, 
           hanya bekerja saat dicetak atau konversi ke PDF. */
        .footer .page-number:before {
            content: "Halaman " counter(page);
        }


        /* Header Dokumen */
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }

        .header h1 {
            margin: 0;
            font-size: 15px;
        }

        .header h2 {
            margin: 3px 0 0 0;
            font-size: 13px;
            font-weight: normal;
            color: #555;
        }

        /* Konten Section */
        .section {
            margin-bottom: 8px;
        }

        .ringkasan-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .section-info {
            justify-content: center;
        }

        .section-title {
            background-color: #EAEAEA;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            border-left: 4px solid #007bff;
            margin-bottom: 6px;
        }

        /* Pengaturan Tabel Utama (untuk data) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: avoid;
            /* Mencegah tabel terpotong */
        }

        .data-table th,
        .data-table td {
            border: 1px solid #161616ff;
            padding: 3px 5px;
            font-size: 9.5px;
        }

        .data-table th {
            background-color: #F0F0F0;
            padding: 4px 5px;
            text-align: center;
            font-weight: bold;
        }

        /* Pengaturan Tabel Info (Nama Guru, dll) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .info-table td {
            border: none;
            /* Tanpa border */
            padding: 2px 4px;
            font-size: 10px;
        }

        /* Utilities */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .mb-20 {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        /* Class untuk memaksa pindah halaman */
        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        /* Badge Status */
        .badge {
            display: inline-block;
            padding: 0.25em 0.5em;
            font-size: 85%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        /* Alert Rekomendasi */
        .alert {
            padding: 0.75rem 1.25rem;
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            page-break-inside: avoid;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        /* Perbaikan untuk Grid Foto */
        .photo-container {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
            /* Menjaga foto-foto tidak terpisah aneh */
        }

        .photo-item {
            display: inline-block;
            width: 100%;
            vertical-align: top;
            text-align: center;
            align-items: center;
            margin-bottom: 10px;
            margin-right: 1%;
        }

        .photo-item:nth-child(3n) {
            margin-right: 0;
        }

        .photo-item img {
            max-width: 100%;
            height: 350px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .photo-caption {
            font-size: 10px;
            margin-top: 5px;
        }

        /* Kotak Rumus */
        .formula-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        /* Tabel Layout */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .layout-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        /* Signature table */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            line-height: 1.2;
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        /* Menambahkan style untuk TD signature-table agar rapi */
        .signature-table td {
            padding: 0;
            /* Menghapus padding agar width akurat */
            vertical-align: top;
            /* Semua rata atas */
        }

        /* Penanda Tangan (kiri, kanan, tengah) */
        .signature-block {
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 60px;
            /* Ruang untuk TTD basah */
        }
    </style>
</head>

<body>

    <!-- Footer -->
    <div class="footer">
        <table class="layout-table">
            <tr>
                <td style="width: 50%;">
                    <span><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '' ?></span>
                </td>
                <td style="width: 50%; text-align: right;">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Kop Surat Instansi -->
    <?= render_kop_surat() ?>

    <!-- Judul Dokumen -->
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase;">HASIL SUPERVISI GURU</h2>
        <div style="font-size: 10pt; color: #555; margin-top: 2px;">Program Pembinaan dan Pengembangan Guru</div>
    </div>
        <!-- Informasi Guru -->
        <div class="section">
            <div class="section-title">Informasi Guru</div>
            <table class="layout-table">
                <tr>
                    <td style="width: 50%;">
                        <table class="info-table">
                            <tr>
                                <td style="width: 35%;"><strong>Nama Guru</strong></td>
                                <td style="width: 5%;">:</td>
                                <td><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '' ?></td>
                            </tr>
                            <tr>
                                <td style="width: 35%;"><strong>NIP</strong></td>
                                <td style="width: 5%;">:</td>
                                <td><?= isset($schedule['nip_guru']) ? esc($schedule['nip_guru']) : '' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mata Pelajaran</strong></td>
                                <td>:</td>
                                <td><?= isset($schedule['mata_pelajaran']) && $schedule['mata_pelajaran'] !== '' ? esc($schedule['mata_pelajaran']) : (isset($schedule['guru_mata_pelajaran']) ? esc($schedule['guru_mata_pelajaran']) : '') ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%;">
                        <!-- Kolom kedua untuk info supervisi -->
                        <table class="info-table">
                            <tr>
                                <td style="width: 35%;"><strong>Materi</strong></td>
                                <td style="width: 5%;">:</td>
                                <td><?= isset($schedule['materi_supervisi']) ? esc($schedule['materi_supervisi']) : '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>:</td>
                                <td><?= isset($schedule['tanggal_supervisi']) ? format_tanggal_indonesia($schedule['tanggal_supervisi']) : '' // Asumsi fungsi format_tanggal_indonesia ada 
                                    ?></td>
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
                    </td>
                </tr>
            </table>
            <!-- Meletakkan info-table di luar layout-table jika tidak perlu 2 kolom -->
            <!-- Saya perbaiki layout di atas agar lebih seimbang -->
        </div>

        <!-- Halaman 1: Hasil Penilaian (Ringkasan Nilai Supervisi) -->
        <?php if (!empty($hasilList)): ?>
            <div class="section ringkasan-section" style="page-break-inside: avoid;">
                <div class="section-title">Hasil Penilaian: Ringkasan Nilai Supervisi</div>
                <?php
                // (LOGIKA PHP TIDAK DIUBAH)
                $komponen_nilai = [];
                $total_skor = 0;
                $total_maksimal = 0;

                // === Array Nama Komponen yang KONSISTEN ===
                $nama_komponen = [
                    1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                    2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                    3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                    4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
                ];

                if (!empty($hasilList)) {
                    foreach ($hasilList as $hasil) {
                        $jenis_id = $hasil['jenis_penilaian_id'];
                        $nama = isset($nama_komponen[$jenis_id]) ? $nama_komponen[$jenis_id] : ('Komponen ' . $jenis_id);
                        $skor_komponen = 0;
                        $jumlah_aspek = 0;

                        if (isset($detailResults[$jenis_id]) && !empty($detailResults[$jenis_id])) {
                            foreach ($detailResults[$jenis_id] as $detail) {
                                $skor_komponen += isset($detail['skor']) ? $detail['skor'] : 0;
                                $jumlah_aspek++;
                            }
                        }

                        $skor_maksimal = $jumlah_aspek * 4;
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

                $nilai_akhir = $total_maksimal > 0 ? ($total_skor / $total_maksimal) * 100 : 0;

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

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Komponen Penilaian</th>
                            <th>Skor Diperoleh</th>
                            <th>Presentase (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($komponen_nilai)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($komponen_nilai as $komponen): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= isset($komponen['nama']) ? esc($komponen['nama']) : '' ?></td>
                                    <td class="text-center"><?= isset($komponen['skor']) ? esc($komponen['skor']) : '0' ?></td>
                                    <td class="text-center"><?= isset($komponen['persentase']) ? number_format($komponen['persentase'], 2) : '0.00' ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada detail penilaian</td>
                            </tr>
                        <?php endif; ?>
                        <tr style="font-weight: bold; background-color: #F0F0F0;">
                            <td class="text-center"></td>
                            <td class="text-center">Total</td>
                            <td class="text-center"><?= esc($total_skor) ?></td>
                            <td class="text-center"><?= number_format($nilai_akhir, 2) ?>%</td>
                        </tr>
                    </tbody>
                </table>

                <table class="layout-table" style="margin-top: 4px; page-break-inside: avoid;">
                    <tr>
                        <td class="layout-cell" style="width: 55%; padding-right: 12px; vertical-align: top;">
                            <h4 style="margin: 0 0 3px 0; font-size: 11px;">Hasil Akhir</h4>
                            <p style="font-size: 10px; margin-bottom: 4px;">
                                Nilai Akhir: <span class="badge badge-primary" style="font-size: 0.95rem;"><?= number_format($nilai_akhir, 2) ?>%</span>
                                &nbsp; Kategori: <span class="badge badge-<?= esc($badgeClass) ?>" style="font-size: 0.95rem;"><?= esc($kategori) ?></span>
                            </p>

                            <div class="formula-box" style="padding: 3px 8px; font-size: 9px; line-height: 1.25;">
                                <strong>Rumus Perhitungan:</strong><br>
                                Nilai Akhir = (Total Skor / Skor Maksimal) × 100<br>
                                = (<?= esc($total_skor) ?> / <?= esc($total_maksimal) ?>) × 100 = <strong><?= number_format($nilai_akhir, 2) ?>%</strong>
                            </div>
                        </td>
                        <td class="layout-cell" style="width: 45%; vertical-align: top;">
                            <h4 style="margin: 0 0 3px 0; font-size: 11px;">Kategori Penilaian</h4>
                            <table class="data-table" style="margin-bottom: 0;">
                                <tr>
                                    <td>Baik Sekali</td>
                                    <td class="text-center"><span class="badge badge-success">86-100%</span></td>
                                </tr>
                                <tr>
                                    <td>Baik</td>
                                    <td class="text-center"><span class="badge badge-info">70-85%</span></td>
                                </tr>
                                <tr>
                                    <td>Cukup</td>
                                    <td class="text-center"><span class="badge badge-warning">55-69%</span></td>
                                </tr>
                                <tr>
                                    <td>Kurang</td>
                                    <td class="text-center"><span class="badge badge-danger">0-54%</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Halaman 2+: Detail Penilaian Per Komponen -->
            <div class="section">
                <!-- Bagian Looping Detail Penilaian -->
                <?php $isFirstAssessment = true; ?>

                <!-- === PERBAIKAN: Gunakan Array yang Konsisten === -->
                <?php
                $komponen_nama_list = [
                    1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                    2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                    3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                    4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
                ];
                ?>

                <?php foreach ($hasilList as $hasil): ?>
                    <?php
                    $jenisId = $hasil['jenis_penilaian_id'];

                    // === PERBAIKAN: Gunakan $jenisId sebagai kunci, bukan $counter ===
                    $jenisNama = $hasil['nama_jenis'] ?? ($komponen_nama_list[$jenisId] ?? 'Komponen Penilaian');

                    // (LOGIKA PHP TIDAK DIUBAH)
                    $total_skor = 0;
                    $jumlah_aspek = 0;
                    if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                        foreach ($detailResults[$jenisId] as $detail) {
                            $total_skor += isset($detail['skor']) ? $detail['skor'] : 0;
                            $jumlah_aspek++;
                        }
                    }
                    $skor_maksimal = $jumlah_aspek * 4;
                    $nilai = $skor_maksimal > 0 ? ($total_skor / $skor_maksimal) * 100 : 0;

                    if ($nilai >= 86) {
                        $badgeClass = 'success';
                        $kategori = 'Baik Sekali';
                    } elseif ($nilai >= 70) {
                        $badgeClass = 'info'; // Menggunakan 'info' agar konsisten dengan ringkasan
                        $kategori = 'Baik';
                    } elseif ($nilai >= 55) {
                        $badgeClass = 'warning';
                        $kategori = 'Cukup';
                    } else {
                        $badgeClass = 'danger';
                        $kategori = 'Kurang';
                    }
                    ?>

                    <div class="mb-20 page-break-before">

                        <!-- Judul Komponen Detail -->
                        <table class="layout-table" style="margin-bottom: 10px; margin-top: 20px;">
                            <tr>
                                <td class="layout-cell">
                                    <!-- Menggunakan section-title agar konsisten -->
                                    <div class="section-title" style="margin-bottom: 0;">
                                        <?= esc($jenisNama) ?>
                                    </div>
                                </td>
                                <td class="layout-cell text-right" style="width: 40%;">
                                    <span class="badge badge-<?= $badgeClass ?>"><?= esc($kategori) ?></span>
                                    <span class="badge badge-primary"><?= number_format($nilai, 2) ?>%</span>
                                </td>
                            </tr>
                        </table>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Aspek Penilaian</th>
                                    <th style="width: 15%;">Skor</th>
                                    <th style="width: 30%;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])): ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($detailResults[$jenisId] as $detail): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
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

                        <?php if (!empty($hasil['rekomendasi'])): ?>
                            <div class="alert alert-info">
                                <strong>Catatan dan Rekomendasi Perbaikan:</strong><br>
                                <?= nl2br(esc($hasil['rekomendasi'])) // Menggunakan nl2br untuk jaga baris baru 
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php $isFirstAssessment = false; ?>

                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada hasil penilaian.</p>
            <?php endif; ?>
        </div>


        <!-- Tanda Tangan -->
        <!-- Kode tanda tangan Anda sudah sangat baik untuk dompdf -->
        <table class="signature-table">
            <!-- Baris Atas: Guru dan Supervisor -->
            <tr>
                <!-- Kolom Kiri: Guru yang Disupervisi -->
                <td style="width: 33.3%;" class="signature-block">
                    <p>Guru yang Disupervisi,</p>
                    <div class="signature-space"></div>
                    <p style="margin: 2px 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($schedule['nama_guru'] ?? '') ?>
                    </p>
                    <p style="margin: 2px 0;">
                        NIP: <?= esc($schedule['nip_guru'] ?? '') ?>
                    </p>
                </td>

                <!-- Kolom Tengah: Kosong (Spacer) -->
                <td style="width: 33.3%;">
                    <!-- Sel ini sengaja dikosongkan untuk spasi -->
                </td>

                <!-- Kolom Kanan: Supervisor -->
                <td style="width: 33.3%;" class="signature-block">
                    <p>Gisting, <?= format_tanggal_indonesia(date('Y-m')) ?><br>
                        Supervisor,</p>
                    <div class="signature-space"></div>
                    <p style="margin: 2px 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($schedule['nama_supervisor'] ?? '...........................') ?>
                    </p>
                    <p style="margin: 2px 0;">
                        NIP: <?= esc($schedule['nip_supervisor'] ?? '...........................') ?>
                    </p>
                </td>
            </tr>

            <!-- Baris Bawah: Kepala Sekolah (Tengah) -->
            <tr>
                <!-- Kolom Kiri: Kosong -->
                <td></td>

                <!-- Kolom Tengah: Kepala Sekolah -->
                <td style="padding-top: 40px;" class="signature-block">
                    <p>Mengetahui,<br>Kepala Sekolah</p>
                    <div class="signature-space"></div>
                    <p style="margin: 2px 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($schedule['nama_kepala'] ?? '...........................') ?>
                    </p>
                    <p style="margin: 2px 0;">
                        NIP: <?= esc($schedule['nip_kepala'] ?? '...........................') ?>
                    </p>
                </td>

                <!-- Kolom Kanan: Kosong -->
                <td></td>
            </tr>
        </table>


        <!-- Halaman Foto Bukti -->
        <?php if (!empty($fotoBukti)): ?>
            <!-- Halaman baru untuk Foto -->
            <div class="section page-break-before">
                <div class="section-title">Lampiran Foto Bukti Supervisi</div>

                <div class="photo-container">
                    <?php foreach ($fotoBukti as $foto): ?>
                        <?php $filePath = FCPATH . $foto['file_path']; // FCPATH adalah konstanta CodeIgniter, ini benar 
                        ?>
                        <?php if (file_exists($filePath)): ?>
                            <?php
                            $mimeType = mime_content_type($filePath);
                            $imageData = file_get_contents($filePath);
                            $base64Data = base64_encode($imageData);
                            ?>
                            <div class="photo-item">
                                <img src="data:<?= $mimeType ?>;base64,<?= $base64Data ?>" alt="Foto Bukti">
                                <div class="photo-caption">
                                    <?= !empty($foto['keterangan']) ? esc($foto['keterangan']) : 'Foto Dokumentasi' ?><br>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

</body>

</html>