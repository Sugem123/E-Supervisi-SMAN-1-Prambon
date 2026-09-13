<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Supervisi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .section-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin: 10px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
            page-break-inside: auto;
        }

        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.data-table th, 
        table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: middle;
            text-align: left;
        }

        table.data-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        .badge-selesai {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-terjadwal {
            background-color: #cce7ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .badge-lainnya {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* Tabel Tanda Tangan Kompatibel Dompdf */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
            border: none !important;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            border: none !important;
            font-size: 8.5pt;
            padding: 0;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

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
            border: none !important;
        }

        .footer-table td {
            padding: 0;
            border: none !important;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Instansi -->
    <div style="margin-bottom: 5px;">
        <?= render_kop_surat() ?>
    </div>
    
    <div class="section-title">Jadwal Pelaksanaan Supervisi Akademik Guru</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 120px;">Tahun Ajaran</th>
                <th>Nama Guru & NIP</th>
                <th>Mata Pelajaran</th>
                <th style="width: 60px;">Kelas</th>
                <th style="width: 130px;">Hari / Tanggal</th>
                <th>Supervisor</th>
                <th style="width: 75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jadwals)): ?>
                <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center">
                        <?= esc($jadwal['tahun_ajar'] ?? '-') ?> - Semester <?= esc($jadwal['semester'] ?? '-') ?>
                    </td>
                    <td>
                        <strong><?= esc($jadwal['nama_guru']) ?></strong>
                        <?php if (!empty($jadwal['nip_guru'])): ?>
                            <br><small style="color: #555; font-size: 7pt;">NIP. <?= esc($jadwal['nip_guru']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($jadwal['mata_pelajaran'] ?? '-') ?></td>
                    <td class="text-center"><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($jadwal['tanggal_supervisi'])): ?>
                            <?= format_hari_indonesia($jadwal['tanggal_supervisi']) ?>, <?= format_tanggal_indonesia($jadwal['tanggal_supervisi'], false) ?>
                        <?php else: ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($jadwal['nama_supervisor'] ?? '-') ?></td>
                    <td class="text-center">
                        <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                            <span class="badge badge-terjadwal">Terjadwal</span>
                        <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                            <span class="badge badge-selesai">Selesai</span>
                        <?php else: ?>
                            <span class="badge badge-lainnya"><?= esc($jadwal['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #777;">
                        <em>Tidak ada data jadwal supervisi yang tersedia.</em>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Blok Tanda Tangan Resmi -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Pengawas / Pembina Supervisi</strong>
                <div class="signature-space"></div>
                <div class="signature-name">( ..................................................... )</div>
                <div style="font-size: 7.5pt; color: #555;">NIP. ....................................................</div>
            </td>
            <td>
                <?= esc($kota_madrasah ?? 'Gisting') ?>, <?= function_exists('format_tanggal_indonesia') ? format_tanggal_indonesia(date('Y-m-d'), false) : date('d F Y') ?><br>
                <strong>Kepala Madrasah</strong>
                <div class="signature-space"></div>
                <div class="signature-name"><?= esc($nama_kepala ?? get_nama_kepala()) ?></div>
                <div style="font-size: 7.5pt; color: #555;">NIP. <?= esc($nip_kepala ?? get_pengaturan('nip_kepala', '')) ?></div>
            </td>
        </tr>
    </table>

    <!-- Footer Catatan Otomatis -->
    <div class="footer-note">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    Dokumen Jadwal Supervisi &mdash; Dicetak otomatis oleh Sistem Supervisi pada <?= date('d/m/Y H:i') ?> WIB
                </td>
                <td style="text-align: right;">
                    MIN 2 Tanggamus
                </td>
            </tr>
        </table>
    </div>
</body>
</html>