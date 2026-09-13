<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 5px;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Kop Surat Instansi -->
    <?= render_kop_surat() ?>

    <h2 style="text-align: center; margin: 10px 0 20px 0; font-size: 14pt;"><?= $title ?></h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Guru</th>
                <th>Supervisor</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Jam Ke</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                    <td><?= $schedule['nama_guru'] ?></td>
                    <td><?= $schedule['nama_supervisor'] ?? 'Tidak ditentukan' ?></td>
                    <td><?= $schedule['mata_pelajaran'] ?></td>
                    <td class="text-center"><?= $schedule['kelas'] ?></td>
                    <td class="text-center"><?= $schedule['jam_ke'] ?></td>
                    <td class="text-center"><?= $schedule['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; float: right; width: 250px; text-align: left; font-size: 12px;line-height: 0.5;">
        <p>Tanggamus, <?= format_tanggal_indonesia(date('Y-m-d')) ?></p>
        <p>Kepala Madrasah,</p>
        <br><br><br><br>
        <p><strong><u><?= get_pengaturan('nama_kepala', '......................') ?></u></strong></p>
        <p>NIP. <?= get_pengaturan('nip_kepala', '......................') ?></p>
    </div>
</body>

</html>