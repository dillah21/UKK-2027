<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

// Ambil statistik ringkasan untuk Admin
$jumlah_siswa = 0;
$jumlah_guru = 0;
$jumlah_kelas = 0;
$jumlah_pelanggaran = 0;

if ($res = $koneksi->query("SELECT COUNT(*) as total FROM t_siswa")) {
    $jumlah_siswa = $res->fetch_assoc()['total'];
}
if ($res = $koneksi->query("SELECT COUNT(*) as total FROM t_guru")) {
    $jumlah_guru = $res->fetch_assoc()['total'];
}
if ($res = $koneksi->query("SELECT COUNT(*) as total FROM t_kelas")) {
    $jumlah_kelas = $res->fetch_assoc()['total'];
}
if ($res = $koneksi->query("SELECT COUNT(*) as total FROM t_riwayat_pelanggaran")) {
    $jumlah_pelanggaran = $res->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Pelanggaran Siswa</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; }
        .sidebar a { color: #cfd2d6; text-decoration: none; }
        .sidebar a:hover, .sidebar .active { color: #fff; background-color: #007bff; border-radius: 4px; }
        .stat-card { border: none; border-radius: 10px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link active p-2"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="siswa.php" class="nav-link p-2"><i class="fas fa-user-graduate mr-2"></i> Data Siswa</a></li>
                <li class="nav-item mb-2"><a href="guru.php" class="nav-link p-2"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</a></li>
                <li class="nav-item mb-2"><a href="kelas.php" class="nav-link p-2"><i class="fas fa-school mr-2"></i> Kelas</a></li>
                <li class="nav-item mb-2"><a href="wali_kelas.php" class="nav-link p-2"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</a></li>
                <li class="nav-item mb-2"><a href="tahun_ajaran.php" class="nav-link p-2"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran_kategori.php" class="nav-link p-2"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran.php" class="nav-link p-2"><i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="laporan.php" class="nav-link p-2"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap</a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link p-2"><i class="fas fa-info-circle mr-2"></i> Tentang Saya</a></li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger p-2"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Administrator</h1>
                <span class="text-muted">Selamat datang, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong></span>
            </div>
            <p class="text-muted">Panel kontrol utama untuk mengelola seluruh data master, akademik, pemantauan pelanggaran, dan laporan sistem.</p>

            <!-- STATISTIK KARTU -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase font-weight-bold mb-1">Total Siswa</h6>
                                    <h2 class="mb-0 font-weight-bold"><?= $jumlah_siswa; ?></h2>
                                </div>
                                <i class="fas fa-user-graduate fa-2x text-white-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="siswa.php" class="text-white small">Kelola Siswa <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase font-weight-bold mb-1">Total Guru</h6>
                                    <h2 class="mb-0 font-weight-bold"><?= $jumlah_guru; ?></h2>
                                </div>
                                <i class="fas fa-chalkboard-teacher fa-2x text-white-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="guru.php" class="text-white small">Kelola Guru <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase font-weight-bold mb-1">Total Kelas</h6>
                                    <h2 class="mb-0 font-weight-bold"><?= $jumlah_kelas; ?></h2>
                                </div>
                                <i class="fas fa-school fa-2x text-white-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="kelas.php" class="text-white small">Kelola Kelas <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card bg-danger text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase font-weight-bold mb-1">Pelanggaran</h6>
                                    <h2 class="mb-0 font-weight-bold"><?= $jumlah_pelanggaran; ?></h2>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="pelanggaran.php" class="text-white small">Pantau Pelanggaran <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL AKSES CEPAT MENU UTAMA -->
            <div class="row">
                <!-- Manajemen Akademik & Master -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="fas fa-database text-primary mr-2"></i> Pengelolaan Data Master & Akademik
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Atur struktur dasar sekolah, penempatan kelas, tahun ajaran aktif, dan wali kelas.</p>
                            <div class="list-group">
                                <a href="tahun_ajaran.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-calendar-alt text-secondary mr-2"></i> Kelola Tahun Ajaran & Semester</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="kelas.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-school text-secondary mr-2"></i> Pengaturan Data Kelas</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="wali_kelas.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-user-tie text-secondary mr-2"></i> Penempatan Wali Kelas</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="siswa.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-user-graduate text-secondary mr-2"></i> Penempatan & Data Siswa</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemantauan Pelanggaran & Laporan -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="fas fa-chart-line text-danger mr-2"></i> Pemantauan Pelanggaran & Laporan
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Awasi kedisiplinan siswa, catat poin sanksi, dan akses seluruh laporan rekapitulasi.</p>
                            <div class="list-group">
                                <a href="pelanggaran_kategori.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-tags text-secondary mr-2"></i> Kategori & Poin Pelanggaran</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="pelanggaran.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-exclamation-triangle text-secondary mr-2"></i> Data & Riwayat Pelanggaran Siswa</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="laporan.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-file-alt text-secondary mr-2"></i> Cetak & Akses Seluruh Laporan</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                                <a href="guru.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chalkboard-teacher text-secondary mr-2"></i> Data Guru & Hak Akses Pelapor</span>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa
            </footer>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>