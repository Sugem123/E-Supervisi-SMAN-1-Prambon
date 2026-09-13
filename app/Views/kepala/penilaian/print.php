<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Penilaian Supervisi - <?= $schedule['nama_guru'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Instansi -->
    <?= render_kop_surat() ?>

    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase;">HASIL PENILAIAN SUPERVISI GURU</h2>
    </div>

    <div class="section">
        <h3 class="section-title">DATA GURU</h3>
        <table>
            <tr>
                <td width="30%"><strong>Nama Guru</strong></td>
                <td width="5%">:</td>
                <td><?= $schedule['nama_guru'] ?></td>
                
                <td width="25%"><strong>Tanggal Supervisi</strong></td>
                <td width="5%">:</td>
                <td><?= format_tanggal_indonesia($schedule['tanggal_supervisi']) ?></td>
            </tr>
            <tr>
                <td><strong>Mata Pelajaran</strong></td>
                <td>:</td>
                <td><?= $schedule['mata_pelajaran'] ?></td>
                
                <td><strong>Supervisor</strong></td>
                <td>:</td>
                <td><?= $schedule['nama_supervisor'] ?? get_nama_kepala() ?></td>
            </tr>
            <?php if (!empty($schedule['nip'])): ?>
            <tr>
                <td><strong>NIP</strong></td>
                <td>:</td>
                <td><?= $schedule['nip'] ?></td>
                
                <td><strong>Pangkat/Golongan</strong></td>
                <td>:</td>
                <td><?= $schedule['pangkat_golongan'] ?? '-' ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <?php if (!empty($hasilPenilaian)): ?>
        <?php foreach ($hasilPenilaian as $index => $hasil): ?>
            <?php if ($index > 0): ?>
                <div class="page-break"></div>
                <div class="header">
                    <h1>HASIL PENILAIAN SUPERVISI GURU</h1>
                    <h2><?= strtoupper(get_nama_madrasah()) ?></h2>
                </div>
                
                <div class="section">
                    <h3 class="section-title">DATA GURU</h3>
                    <table>
                        <tr>
                            <td width="30%"><strong>Nama Guru</strong></td>
                            <td width="5%">:</td>
                            <td><?= $schedule['nama_guru'] ?></td>
                            
                            <td width="25%"><strong>Tanggal Supervisi</strong></td>
                            <td width="5%">:</td>
                            <td><?= format_tanggal_indonesia($schedule['tanggal_supervisi']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>:</td>
                            <td><?= $schedule['mata_pelajaran'] ?></td>
                            
                            <td><strong>Supervisor</strong></td>
                            <td>:</td>
                            <td><?= $schedule['nama_supervisor'] ?? get_nama_kepala() ?></td>
                        </tr>
                        <?php if (!empty($schedule['nip'])): ?>
                        <tr>
                            <td><strong>NIP</strong></td>
                            <td>:</td>
                            <td><?= $schedule['nip'] ?></td>
                            
                            <td><strong>Pangkat/Golongan</strong></td>
                            <td>:</td>
                            <td><?= $schedule['pangkat_golongan'] ?? '-' ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="section">
                <h3 class="section-title"><?= strtoupper($hasil['nama_jenis']) ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="50%">Aspek Penilaian</th>
                            <th width="10%" class="text-center">Skor</th>
                            <th width="35%">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($detailResults[$hasil['jenis_penilaian_id']])): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($detailResults[$hasil['jenis_penilaian_id']] as $detail): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $detail['nama_aspek'] ?></td>
                                    <td class="text-center"><?= $detail['skor'] ?></td>
                                    <td><?= $detail['catatan'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total Skor:</strong></td>
                                <td class="text-center"><strong><?= $hasil['total_skor'] ?></strong></td>
                                <td></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada detail penilaian</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div>
                    <p><strong>Nilai Akhir: <?= number_format($hasil['nilai_akhir'], 2) ?> (<?= $hasil['ketercapaian'] ?>)</strong></p>
                    <p><strong>Rekomendasi Perbaikan:</strong></p>
                    <p><?= nl2br($hasil['rekomendasi']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="signature-section">
            <div class="signature-box">
                <p>Guru yang Dinilai,</p>
                <div class="signature-line">
                    (<?= $schedule['nama_guru'] ?>)
                </div>
            </div>
            
            <div class="signature-box">
                <p><?= get_nama_madrasah() ?>, <?= format_tanggal_indonesia(date('Y-m-d')) ?></p>
                <p>Supervisor,</p>
                <div class="signature-line">
                    (<?= $schedule['nama_supervisor'] ?? get_nama_kepala() ?>)
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="section">
            <p class="text-center">Belum ada hasil penilaian</p>
        </div>
    <?php endif; ?>
</body>
</html>