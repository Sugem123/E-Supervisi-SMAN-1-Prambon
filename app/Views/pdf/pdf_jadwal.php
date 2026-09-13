<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal Supervisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
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
            text-transform: uppercase;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        .section-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
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
        
        .signature {
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
    
    <div class="section-title" style="margin-top: 5px;">Laporan Jadwal Supervisi</div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun Ajaran</th>
                <th>Nama Guru</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Hari/Tanggal</th>
                <th>Supervisor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jadwals)): ?>
                <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $jadwal['tahun_ajar'] ?> - <?= $jadwal['semester'] ?></td>
                    <td><?= $jadwal['nama_guru'] ?></td>
                    <td><?= $jadwal['mata_pelajaran'] ?></td>
                    <td><?= $jadwal['nama_kelas'] ?? $jadwal['kelas'] ?></td>
                    <td><?= format_hari_indonesia($jadwal['tanggal_supervisi']) ?>, <?= format_tanggal_indonesia($jadwal['tanggal_supervisi'], false) ?></td>
                    <td><?= $jadwal['nama_supervisor'] ?? '-' ?></td>
                    <td class="text-center">
                        <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                            <span>Terjadwal</span>
                        <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                            <span>Selesai</span>
                        <?php else: ?>
                            <span><?= $jadwal['status'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data jadwal supervisi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>