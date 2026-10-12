<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pelanggaran Siswa</title>
</head>
<body>

<?php 
// Otomatis memunculkan tombol kembali ke dashboard di semua menu kecuali di dashboard itu sendiri
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'dashboard.php'): 
?>
    <p><a href="dashboard.php"><b>&larr; Kembali ke Dashboard</b></a></p>
    <hr>
<?php endif; ?>