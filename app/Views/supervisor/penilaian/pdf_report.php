<!DOCTYPE html>
<html>
<head>
    <title>Hasil Penilaian Supervisi</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            font-weight: bold;
            border-left: 4px solid #007bff;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table, th, td {
            border: 1px solid #333;
        }
        
        th {
            background-color: #e9ecef;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        
        td {
            padding: 6px 8px;
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
            padding-top: 5px;
            border-top: 1px solid #333;
        }
        
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .photo-item {
            text-align: center;
        }
        
        .photo-item img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
        }
        
        .photo-caption {
            font-size: 10px;
            margin-top: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Instansi -->
    <?= render_kop_surat() ?>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase;">HASIL PENILAIAN SUPERVISI GURU</h2>
        <div style="font-size: 10.5pt; color: #555; margin-top: 3px;">Program Pembinaan dan Pengembangan Guru</div>
    </div>
    
    <!-- Teacher Info -->
    <div class="section">
        <div class="section-title">Informasi Guru</div>
        <table>
            <tr>
                <td width="30%"><strong>Nama Guru</strong></td>
                <td width="5%">:</td>
                <td><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '' ?></td>
            </tr>
            <tr>
                <td><strong>Mata Pelajaran</strong></td>
                <td>:</td>
                <td><?= isset($schedule['mata_pelajaran']) ? esc($schedule['mata_pelajaran']) : '' ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Supervisi</strong></td>
                <td>:</td>
                <td><?= isset($schedule['tanggal_supervisi']) ? date('d F Y', strtotime($schedule['tanggal_supervisi'])) : '' ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Assessment Results -->
    <?php if (!empty($hasilPenilaian)): ?>
        <?php foreach ($hasilPenilaian as $hasil): ?>
            <div class="section">
                <div class="section-title">Aspek Penilaian: <?= esc($hasil['nama_jenis']) ?></div>
                
                <?php if (isset($detailResults[$hasil['jenis_penilaian_id']]) && !empty($detailResults[$hasil['jenis_penilaian_id']])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="60%">Aspek Penilaian</th>
                                <th width="10%">Skor</th>
                                <th width="25%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($detailResults[$hasil['jenis_penilaian_id']] as $detail): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($detail['nama_aspek']) ?></td>
                                    <td class="text-center"><?= esc($detail['skor']) ?></td>
                                    <td><?= esc($detail['catatan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total Skor:</strong></td>
                                <td class="text-center"><strong><?= esc($hasil['total_skor']) ?></strong></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <table>
                        <tr>
                            <td width="70%" class="text-right"><strong>Nilai Akhir:</strong></td>
                            <td width="30%" class="text-center"><strong><?= number_format($hasil['nilai_akhir'], 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>Ketercapaian:</strong></td>
                            <td class="text-center"><strong><?= esc($hasil['ketercapaian']) ?></strong></td>
                        </tr>
                    </table>
                <?php else: ?>
                    <p>Tidak ada detail penilaian untuk aspek ini.</p>
                <?php endif; ?>
                
                <div style="margin-top: 15px;">
                    <strong>Rekomendasi:</strong>
                    <p><?= nl2br(esc($hasil['rekomendasi'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="section">
            <p>Tidak ada hasil penilaian yang tersedia.</p>
        </div>
    <?php endif; ?>
    
    <!-- Photo Documentation -->
    <?php if (!empty($uploadedPhotos)): ?>
        <div class="section">
            <div class="section-title">Dokumentasi Foto</div>
            <div class="photo-grid">
                <?php foreach ($uploadedPhotos as $photo): ?>
                    <?php $filePath = FCPATH . $photo['file_path']; ?>
                    <?php if (file_exists($filePath)): ?>
                        <div class="photo-item">
                            <?php
                                $type = pathinfo($filePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($filePath);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            ?>
                            <img src="<?= $base64 ?>" alt="Foto Bukti">
                            <div class="photo-caption">
                                <?= esc($photo['keterangan'] ?? 'Foto Dokumentasi') ?><br>
                                <?= date('d/m/Y H:i', strtotime($photo['created_at'] ?? date('Y-m-d H:i:s'))) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Guru yang Dinilai,</p>
            <div class="signature-line">
                <!-- Signature line -->
            </div>
            <p>___________________________</p>
        </div>
        
        <div class="signature-box">
            <p>Supervisor,</p>
            <div class="signature-line">
                <!-- Signature line -->
            </div>
            <p>___________________________</p>
        </div>
    </div>
</body>
</html>