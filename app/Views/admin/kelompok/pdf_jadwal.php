<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Supervisi - <?= esc($kelompok['nama_kelompok']) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 15mm 12mm 15mm;
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
            font-size: 12.5pt;
            font-weight: bold;
            margin: 8px 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111;
        }

        .section-subtitle {
            text-align: center;
            font-size: 9.5pt;
            color: #444;
            margin-bottom: 12px;
        }

        /* Tabel Info Kelompok */
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
            border: 1px solid #ddd;
            background-color: #fcfcfc;
        }

        table.info-table td {
            padding: 4px 8px;
            vertical-align: top;
            border: none;
        }

        /* Tabel Data Jadwal */
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
            text-transform: uppercase;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
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

        .badge-nonkbm {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Signature Table */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            height: 50px;
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
    </style>
</head>
<body>
    <!-- Kop Surat Instansi Resmi SMAN 1 Prambon -->
    <div style="margin-bottom: 5px;">
        <?= render_kop_surat() ?>
    </div>
    
    <div class="section-title">Jadwal Supervisi <?= esc(strtoupper($kelompok['nama_kelompok'])) ?></div>
    <div class="section-subtitle">Tahun Ajaran <?= esc($kelompok['tahun_ajar'] ?? '-') ?> - Semester <?= esc($kelompok['semester'] ?? '-') ?></div>
    
    <!-- Informasi Singkat Kelompok -->
    <table class="info-table">
        <tr>
            <td style="width: 18%; font-weight: bold;">Nama Kelompok</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;"><?= esc($kelompok['nama_kelompok']); ?></td>
            <td style="width: 20%; font-weight: bold;">Supervisor Pembina</td>
            <td style="width: 2%;">:</td>
            <td style="width: 23%;">
                <strong><?= esc($kelompok['nama_supervisor'] ?? '-'); ?></strong>
                <?php if (!empty($kelompok['nip_supervisor'])): ?>
                    <br><small style="color:#555;">NIP. <?= esc($kelompok['nip_supervisor']); ?></small>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Instrumen Penilaian</td>
            <td>:</td>
            <td colspan="4">
                <?php if (!empty($assignedJenis)): ?>
                    <?= esc(implode(', ', array_column($assignedJenis, 'nama'))); ?>
                <?php else: ?>
                    Semua Komponen Penilaian Aktif
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Jadwal Anggota -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 26px;">No</th>
                <th>Nama Guru / Pegawai &amp; NIP</th>
                <th style="width: 140px;">Mata Pelajaran / Tugas</th>
                <th style="width: 70px;">Kelas</th>
                <th style="width: 130px;">Hari / Tanggal</th>
                <th style="width: 130px;">Waktu &amp; Jam</th>
                <th>Materi / Fokus Supervisi</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jadwals)): ?>
                <?php $no = 1; foreach ($jadwals as $j): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <strong><?= esc($j['nama_guru']) ?></strong>
                        <?php if (!empty($j['nip_guru'])): ?>
                            <br><small style="color: #555; font-size: 7.2pt;">NIP. <?= esc($j['nip_guru']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($j['mata_pelajaran'] ?? '-') ?></td>
                    <td class="text-center">
                        <?php if (empty($j['kelas']) || $j['kelas'] === '-' || ($j['jenis_ptk'] ?? '') === 'Tendik'): ?>
                            <span class="badge badge-nonkbm">Non-KBM</span>
                        <?php else: ?>
                            <strong><?= esc($j['nama_kelas'] ?? $j['kelas']) ?></strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= esc($j['hari'] ?: format_hari_indonesia($j['tanggal_supervisi'])) ?></strong>,
                        <br><?= format_tanggal_indonesia($j['tanggal_supervisi'], false) ?>
                    </td>
                    <td>
                        Jam Ke-<?= esc($j['jam_ke']) ?>
                        <?php if (!empty($j['waktu_dari']) && !empty($j['waktu_sampai'])): ?>
                            <br><small style="color: #555;"><?= substr($j['waktu_dari'], 0, 5) ?> - <?= substr($j['waktu_sampai'], 0, 5) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($j['materi_supervisi'] ?? 'Supervisi Proses Pembelajaran / Kinerja') ?></td>
                    <td class="text-center">
                        <?php if ($j['status'] === 'Selesai'): ?>
                            <span class="badge badge-selesai">Selesai</span>
                        <?php elseif ($j['status'] === 'Terjadwal'): ?>
                            <span class="badge badge-terjadwal">Terjadwal</span>
                        <?php else: ?>
                            <span class="badge"><?= esc($j['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #777;">
                        Belum ada jadwal supervisi yang diterbitkan untuk kelompok ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Kompatibel Dompdf (Mengetahui Kepala Sekolah di Kiri, Supervisor di Kanan) -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                Mengetahui,<br>
                Kepala <?= esc(get_nama_sekolah()) ?><br>
                <div class="signature-space"></div>
                <span class="signature-name"><?= esc(!empty($nama_kepala) ? $nama_kepala : '....................................') ?></span><br>
                NIP. <?= esc(!empty($nip_kepala) ? $nip_kepala : '....................................') ?>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                <?= esc(!empty($kota) ? $kota : 'Prambon') ?>, <?= format_tanggal_indonesia(date('Y-m-d'), false) ?><br>
                Supervisor Pembina,<br>
                <div class="signature-space"></div>
                <span class="signature-name"><?= esc($kelompok['nama_supervisor'] ?? '....................................') ?></span><br>
                NIP. <?= esc($kelompok['nip_supervisor'] ?: '....................................') ?>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dicetak melalui Sistem E-Supervisi <?= esc(get_nama_sekolah()) ?> &bull; Tanggal: <?= date('d/m/Y H:i') ?> WIB
    </div>
</body>
</html>
