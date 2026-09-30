<?php
include_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelanggaran Siswa</title>
</head>
<body>
    <div>
        <h2>SIPELANGGARAN - Sistem Informasi Pelanggaran Siswa</h2>
        <p>
            <a href="dashboard.php">Dashboard</a> | 
            <a href="pelanggaran.php">Data Pelanggaran</a> | 
            <a href="tambah_pelanggaran.php">Tambah Pelanggaran</a> | 
            <strong>User: <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?></strong> | 
            <a href="logout.php">Logout</a>
        </p>
    </div>
    <hr>