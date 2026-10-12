<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link active">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelas.php" class="nav-link text-white">
                        Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="tentang_saya.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Dashboard</h2>
            <p>
                Selamat datang di halaman dashboard.
            </p>
            <div class="row">
                <!-- CARD DATA SISWA -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Siswa
                            </h5>
                            <h2>
                                120
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- CARD DATA GURU -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Guru
                            </h5>
                            <h2>
                                25
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- CARD KELAS -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Kelas
                            </h5>
                            <h2>
                             12
                          </h2>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
    <h2>Dashboard</h2>
    <p>
        Selamat datang,
        <?php echo $_SESSION['nama_user']; ?>
    </p>
    <p>
        Anda berhasil login.
    </p>
    <a href="logout.php">Logout</a>

<?php
require_once 'auth.php';
require_once 'koneksi.php';

include 'header.php';
?>

<h2>Dashboard Utama</h2>
<p style="font-size: 35px;"><b>Selamat datang di Sistem Pelanggaran Siswa.</b></p>

<h3>Master Data & Navigasi</h3>
<ul>
    <li><a href="siswa.php">Kelola Siswa</a></li>
    <li><a href="import_siswa.php">Impor Data Siswa (CSV)</a></li>
    <li><a href="guru.php">Kelola Guru</a></li>
    <li><a href="kelas.php">Kelola Kelas</a></li>
    <li><a href="kelas_siswa.php">Penempatan Siswa</a></li>
    <li><a href="wali_kelas.php">Wali Kelas</a></li>
    <li><a href="pelanggaran_kategori.php">Pelanggaran Kategori</a></li>
    <li><a href="pelanggaran.php">Master Pelanggaran</a></li>
    <li><a href="riwayat_pelanggaran.php">Input Pelanggaran Siswa</a></li>
    <li><a href="prestasi.php">Input Prestasi / Pengurangan Poin</a></li>
    <li><a href="rekap_poin.php">Rekap Poin & Cetak SP</a></li>
    <li><a href="tindak_lanjut.php">Catatan Tindak Lanjut / Pembinaan BK</a></li>
    <li><a href="laporan.php">Laporan</a></li>
    <li><a href="log_aktivitas.php">Audit Log Aktivitas</a></li>
    <li><a href="profil.php">Profil Saya & Ganti Password</a></li>
    <li><a href="users.php">Kelola Akun Pengguna / Petugas</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>

<?php include 'footer.php'; ?>