<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Detail Hasil Supervisi Akademik</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #1a1a1a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat & Layout */
        .kop-container {
            margin-bottom: 6px;
        }

        .judul-dokumen {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 12px;
        }

        .judul-dokumen h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            color: #111;
            letter-spacing: 0.5px;
        }

        .judul-dokumen .sub-judul {
            font-size: 9.5pt;
            color: #444;
            margin: 0;
        }

        /* Info Filter Box */
        .info-bar {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        .info-bar table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-bar td {
            font-size: 8.5pt;
            padding: 1px 0;
            vertical-align: middle;
        }

        /* Rekapitulasi Tabel Ringkasan */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 12px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .metric-box {
            border: 1px solid #bbb;
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: center;
        }

        .metric-box .metric-title {
            font-size: 7.5pt;
            color: #555;
            text-transform: uppercase;
            font-weight: bold;
        }

        .metric-box .metric-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0b5ed7;
            margin-top: 2px;
        }

        /* Tabel Distribusi Predikat */
        .distribusi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .distribusi-table th,
        .distribusi-table td {
            border: 1px solid #aaa;
            padding: 3px 5px;
            text-align: center;
        }

        .distribusi-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        /* Tabel Utama Rekapitulasi */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .data-table thead th {
            background-color: #e2e6ea;
            font-weight: bold;
            text-align: center;
            font-size: 7.8pt;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        /* Predikat Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-baik-sekali {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-baik {
            background-color: #cce7ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .badge-cukup {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .badge-kurang {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Kolom Tanda Tangan */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 8.5pt;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Footer Halaman */
        .footer-note {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            font-size: 7pt;
            color: #777;
            border-top: 0.5px solid #ccc;
            padding-top: 3px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 0;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi Instansi -->
    <div class="kop-container">
        <?= render_kop_surat() ?>
    </div>

    <!-- Judul Dokumen -->
    <div class="judul-dokumen">
        <h1>Rekapitulasi Detail Hasil Supervisi Akademik Guru</h1>
        <div class="sub-judul">
            Tahun Ajaran: <strong><?= esc($selectedTahunAjar['tahun_ajar'] ?? '-') ?></strong> &nbsp;|&nbsp;
            Semester: <strong><?= esc($selectedTahunAjar['semester'] ?? '-') ?></strong>
        </div>
    </div>

    <!-- Baris Ringkasan Metrik & Distribusi Predikat -->
    <table class="summary-wrapper" style="border: none;">
        <tr>
            <!-- Kartu Metrik Kiri -->
            <td style="width: 32%; padding-right: 10px; vertical-align: top; border: none;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 4px;">
                    <tr>
                        <td>
                            <div class="metric-box">
                                <div class="metric-title">Total Guru Disupervisi</div>
                                <div class="metric-value"><?= $totalGuru ?> <span style="font-size: 9pt; font-weight: normal; color: #555;">Orang</span></div>
                            </div>
                        </td>
                        <td>
                            <div class="metric-box" style="margin-left: 6px;">
                                <div class="metric-title">Rata-Rata Nilai Akhir</div>
                                <div class="metric-value" style="color: #198754;"><?= number_format($rataRata, 2, ',', '.') ?></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Tabel Distribusi Kualifikasi Kanan -->
            <td style="width: 68%; vertical-align: top; border: none;">
                <table class="distribusi-table">
                    <thead>
                        <tr>
                            <th colspan="5" style="background-color: #dbeafe; color: #1e40af; text-transform: uppercase;">Distribusi Kualifikasi Hasil Supervisi</th>
                        </tr>
                        <tr>
                            <th style="width: 25%;">Predikat</th>
                            <th style="width: 25%;">Rentang Skor</th>
                            <th style="width: 20%;">Frekuensi</th>
                            <th style="width: 15%;">Persentase</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($predikatStats as $predName => $pData): ?>
                        <?php 
                            $persen = $totalGuru > 0 ? round(($pData['count'] / $totalGuru) * 100, 1) : 0; 
                            $badgeCls = 'badge-' . strtolower(str_replace(' ', '-', $predName));
                        ?>
                        <tr>
                            <td class="text-left font-weight-bold">
                                <span class="badge <?= $badgeCls ?>"><?= esc($predName) ?></span>
                            </td>
                            <td><?= esc($pData['range']) ?></td>
                            <td class="font-weight-bold"><?= $pData['count'] ?> Guru</td>
                            <td><?= $persen ?>%</td>
                            <td>
                                <?php if (in_array($predName, ['Baik Sekali', 'Baik'])): ?>
                                    <span style="color: #198754; font-weight: bold;">Tuntas</span>
                                <?php else: ?>
                                    <span style="color: #dc3545; font-weight: bold;">Perlu Binaan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Rekapitulasi Guru Lengkap -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">No</th>
                <th rowspan="2" style="width: 130px;">Nama Guru & NIP</th>
                <th rowspan="2" style="width: 90px;">Mata Pelajaran</th>
                <th rowspan="2" style="width: 55px;">Kelas</th>
                <th rowspan="2" style="width: 70px;">Tanggal</th>
                <?php if (!empty($jenisPenilaians)): ?>
                    <th colspan="<?= count($jenisPenilaians) ?>">Skor per Jenis Penilaian (%)</th>
                <?php endif; ?>
                <th rowspan="2" style="width: 55px;">Nilai Akhir</th>
                <th rowspan="2" style="width: 75px;">Kualifikasi</th>
            </tr>
            <tr>
                <?php if (!empty($jenisPenilaians)): ?>
                    <?php foreach ($jenisPenilaians as $jp): ?>
                        <th style="font-size: 7pt; max-width: 90px;"><?= esc($jp['nama']) ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rekapData)): ?>
            <tr>
                <td colspan="<?= 7 + count($jenisPenilaians) ?>" class="text-center" style="padding: 15px; color: #777;">
                    <em>Tidak ada data hasil supervisi yang berstatus selesai pada tahun ajaran ini.</em>
                </td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($rekapData as $row): ?>
                <?php
                    $nilai = (float)($row['nilai_akhir'] ?? 0);
                    $pred = $row['predikat'] ?? '-';
                    $badgeCls = 'badge-' . strtolower(str_replace(' ', '-', $pred));
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <strong><?= esc($row['nama_guru']) ?></strong>
                        <?php if (!empty($row['nip_guru'])): ?>
                            <br><small style="color: #555; font-size: 7pt;">NIP. <?= esc($row['nip_guru']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($row['nama_supervisor'])): ?>
                            <br><small style="color: #1e40af; font-size: 6.8pt;">Spv: <?= esc($row['nama_supervisor']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row['mata_pelajaran'] ?? '-') ?></td>
                    <td class="text-center">
                        <?php if (empty($row['kelas']) || $row['kelas'] === '-' || ($row['jenis_ptk'] ?? '') === 'Tendik'): ?>
                            <span style="font-size: 7pt; color: #64748b;">Non-KBM</span>
                        <?php else: ?>
                            <?= esc($row['nama_kelas'] ?? $row['kelas'] ?? '-') ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($row['tanggal_supervisi']) ? date('d/m/Y', strtotime($row['tanggal_supervisi'])) : '-' ?>
                    </td>

                    <!-- Nilai per Jenis Penilaian -->
                    <?php if (!empty($jenisPenilaians)): ?>
                        <?php foreach ($jenisPenilaians as $jp): ?>
                            <?php 
                                $skorJenis = isset($row['nilai_per_jenis'][$jp['id']]) ? $row['nilai_per_jenis'][$jp['id']] : null;
                            ?>
                            <td class="text-center">
                                <?php if ($skorJenis !== null): ?>
                                    <strong><?= number_format($skorJenis, 1, ',', '.') ?>%</strong>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Nilai Akhir -->
                    <td class="text-center font-weight-bold" style="font-size: 8.5pt;">
                        <?= number_format($nilai, 2, ',', '.') ?>
                    </td>

                    <!-- Kualifikasi / Predikat -->
                    <td class="text-center">
                        <span class="badge <?= $badgeCls ?>"><?= esc($pred) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f3f5; font-weight: bold;">
                <td colspan="5" class="text-center">RATA-RATA KESELURUHAN</td>
                <?php if (!empty($jenisPenilaians)): ?>
                    <?php foreach ($jenisPenilaians as $jp): ?>
                        <?php
                            $sumCol = 0;
                            $countCol = 0;
                            foreach ($rekapData as $r) {
                                if (isset($r['nilai_per_jenis'][$jp['id']]) && $r['nilai_per_jenis'][$jp['id']] !== null) {
                                    $sumCol += (float)$r['nilai_per_jenis'][$jp['id']];
                                    $countCol++;
                                }
                            }
                            $avgCol = $countCol > 0 ? round($sumCol / $countCol, 1) : 0;
                        ?>
                        <td class="text-center"><?= $avgCol > 0 ? number_format($avgCol, 1, ',', '.') . '%' : '-' ?></td>
                    <?php endforeach; ?>
                <?php endif; ?>
                <td class="text-center" style="color: #0d6efd;"><?= number_format($rataRata, 2, ',', '.') ?></td>
                <td class="text-center">
                    <?php
                        if ($rataRata >= 86) echo 'Baik Sekali';
                        elseif ($rataRata >= 70) echo 'Baik';
                        elseif ($rataRata >= 55) echo 'Cukup';
                        elseif ($rataRata > 0) echo 'Kurang';
                        else echo '-';
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Blok Tanda Tangan Resmi (2 Kolom: Supervisor Pembina & Kepala Sekolah) -->
    <table class="signature-table">
        <tr>
            <!-- Kolom Kiri: Supervisor Pembina -->
            <td>
                Mengetahui,<br>
                <strong>Supervisor Pembina</strong>
                <div class="signature-space"></div>
                <div class="signature-name"><?= esc($namaSupervisor) ?></div>
                <div style="font-size: 7.5pt; color: #444;">NIP. <?= esc($nipSupervisor) ?></div>
            </td>

            <!-- Kolom Kanan: Kepala Sekolah -->
            <td>
                <?= esc(!empty($kotaMadrasah) ? $kotaMadrasah : '....................') ?>, <?= function_exists('format_tanggal_indonesia') ? format_tanggal_indonesia($tanggalCetak, false) : date('d F Y') ?><br>
                <strong>Kepala Sekolah</strong>
                <div class="signature-space"></div>
                <div class="signature-name"><?= esc($namaKepala) ?></div>
                <div style="font-size: 7.5pt; color: #444;">NIP. <?= esc($nipKepala) ?></div>
            </td>
        </tr>
    </table>

    <!-- Footer Catatan Otomatis -->
    <div class="footer-note">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    Dokumen Rekapitulasi Supervisi Akademik &mdash; Dicetak secara otomatis oleh Sistem Supervisi pada <?= date('d/m/Y H:i') ?> WIB
                </td>
                <td style="text-align: right;">
                    Halaman 1 dari 1
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
