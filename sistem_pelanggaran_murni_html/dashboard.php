<?php
// Pastikan sesi aktif dan matikan warning agar tampilan bersih
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', '0');
error_reporting(0);

require_once 'koneksi.php';
require_once 'auth.php';

// Hitung total data untuk card dashboard secara dinamis
$total_siswa = 0;
$total_guru = 0;
$total_kelas = 0;

if ($koneksi) {
    $q_siswa = $koneksi->query("SELECT COUNT(*) as total FROM t_siswa");
    if ($q_siswa) {
        $total_siswa = $q_siswa->fetch_assoc()['total'] ?? 0;
    }
    
    // Sesuaikan nama tabel jika berbeda di database kamu (misal t_guru / guru)
    $q_guru = $koneksi->query("SELECT COUNT(*) as total FROM t_guru");
    if ($q_guru) {
        $total_guru = $q_guru->fetch_assoc()['total'] ?? 0;
    }

    $q_kelas = $koneksi->query("SELECT COUNT(*) as total FROM t_kelas");
    if ($q_kelas) {
        $total_kelas = $q_kelas->fetch_assoc()['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pelanggaran</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        /* Sidebar styling sesuai konsep gambar */
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }
        .sidebar a {
            color: #cfd2d6;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar .active {
            color: #fff;
            background-color: #007bff;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active p-2">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="siswa.php" class="nav-link p-2">Data Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link p-2">Data Guru</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelas.php" class="nav-link p-2">Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="laporan.php" class="nav-link p-2">Laporan</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about.php" class="nav-link p-2">About Me</a>
                </li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger p-2">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <p class="text-muted">
                Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama_user'] ?? 'Administrator'); ?></strong> di halaman dashboard.
            </p>

            <!-- CARD STATISTIK -->
            <div class="row mt-4">
                <!-- Card Data Siswa -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-secondary">Data Siswa</h5>
                            <h2 class="font-weight-bold"><?= $total_siswa; ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Card Data Guru -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-secondary">Data Guru</h5>
                            <h2 class="font-weight-bold"><?= $total_guru; ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Card Kelas -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-secondary">Kelas</h5>
                            <h2 class="font-weight-bold"><?= $total_kelas; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>