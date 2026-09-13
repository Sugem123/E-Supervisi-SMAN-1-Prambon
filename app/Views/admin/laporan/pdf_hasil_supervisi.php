<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil Supervisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        
        .filter-info {
            margin-bottom: 15px;
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
        
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .status-terjadwal {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .ketercapaian-baik-sekali {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .ketercapaian-baik {
            background-color: #cce7ff;
            color: #004085;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .ketercapaian-cukup {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .ketercapaian-kurang {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .nilai-baik-sekali {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .nilai-baik {
            background-color: #cce7ff;
            color: #004085;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .nilai-cukup {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .nilai-kurang {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Instansi -->
    <?= render_kop_surat() ?>

    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase;">LAPORAN HASIL SUPERVISI GURU</h2>
    </div>
    
    <?php if (!empty($filter_tahun_ajar)): ?>
    <div class="filter-info">
        <p><strong>Tahun Ajaran:</strong> <?= $filter_tahun_ajar ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($tahun_ajar_id) && empty($filter_tahun_ajar)): ?>
        <?php foreach ($tahun_ajars as $ta): ?>
            <?php if ($ta['id'] == $tahun_ajar_id): ?>
            <div class="filter-info">
                <p><strong>Tahun Ajaran:</strong> <?= $ta['tahun_ajar'] ?> - <?= $ta['semester'] ?></p>
            </div>
            <?php break; endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (!empty($status)): ?>
    <div class="filter-info">
        <p><strong>Status:</strong> <?= $status ?></p>
    </div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun Ajaran</th>
                <th>Nama Guru</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Tanggal Supervisi</th>
                <th>Status</th>
                <th>Nilai Akhir</th>
                <th>Ketercapaian</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jadwals)): ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data supervisi</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $jadwal['tahun_ajar'] ?> - <?= $jadwal['semester'] ?></td>
                    <td><?= $jadwal['nama_guru'] ?></td>
                    <td><?= $jadwal['mata_pelajaran'] ?></td>
                    <td><?= $jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-' ?></td>
                    <td><?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                    <td class="text-center">
                        <?php if ($jadwal['status'] == 'Selesai'): ?>
                            <span class="status-selesai">Selesai</span>
                        <?php elseif ($jadwal['status'] == 'Terjadwal'): ?>
                            <span class="status-terjadwal">Terjadwal</span>
                        <?php else: ?>
                            <?= $jadwal['status'] ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if (isset($jadwal['hasil']['nilai_akhir']) && !is_null($jadwal['hasil']['nilai_akhir'])): ?>
                            <?php 
                                $nilai = (float)$jadwal['hasil']['nilai_akhir'];
                                $class = '';
                                if ($nilai >= 86) {
                                    $class = 'nilai-baik-sekali';
                                } elseif ($nilai >= 70) {
                                    $class = 'nilai-baik';
                                } elseif ($nilai >= 55) {
                                    $class = 'nilai-cukup';
                                } else {
                                    $class = 'nilai-kurang';
                                }
                            ?>
                            <span class="<?= $class ?>"><?= number_format($nilai, 2, '.', '') ?></span>
                        <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                            <span class="nilai-kurang">Belum Dinilai</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if (isset($jadwal['hasil']['ketercapaian']) && !is_null($jadwal['hasil']['ketercapaian'])): ?>
                            <span class="ketercapaian-<?= strtolower(str_replace(' ', '-', $jadwal['hasil']['ketercapaian'])) ?>">
                                <?= $jadwal['hasil']['ketercapaian'] ?>
                            </span>
                        <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                            <span class="ketercapaian-kurang">Belum Dinilai</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: <?= date('d F Y H:i:s') ?></p>
    </div>
</body>
</html>