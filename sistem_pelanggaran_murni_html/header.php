<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pelanggaran Siswa</title>
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);

if ($current_page !== 'dashboard.php'): 
?>
    <p><a href="dashboard.php">&larr; Kembali ke Dashboard</a></p>
    <hr>
<?php endif; ?>