<!DOCTYPE html>
<html>
<head>
    <title>Test PDF Generation</title>
</head>
<body>
    <h1>Test PDF Generation</h1>
    <p>If you see this page, PDF generation is working correctly.</p>
    <p>Schedule ID: <?= isset($schedule['id']) ? $schedule['id'] : 'N/A' ?></p>
    <p>Teacher Name: <?= isset($schedule['nama_guru']) ? $schedule['nama_guru'] : 'N/A' ?></p>
</body>
</html>