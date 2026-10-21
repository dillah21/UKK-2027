
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
$current_page = basename($_SERVER['PHP_SELF']);
$nama_user = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Pelanggaran Siswa</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            margin: 0;
            background: #f8f9fc;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #212529;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding: 15px 10px;
            z-index: 1000;
        }

        .sidebar-title {
            color: #fff;
            font-weight: bold;
            padding: 12px 10px 20px;
            font-size: 18px;
        }

        .sidebar .nav-link {
            color: #d5d9dd;
            display: block;
            padding: 13px 15px;
            margin-bottom: 4px;
            border-radius: 5px;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            width: 22px;
            margin-right: 3px;
        }

        .sidebar .nav-link:hover {
            background: #343a40;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: #087bff;
            color: #fff;
        }

        .sidebar-divider {
            border-top: 1px solid #454a4f;
            margin: 20px 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-title">
        <i class="fas fa-shield-alt mr-2"></i>
        Sistem Pelanggaran
    </div>

    <nav class="nav flex-column">
        <a href="dashboard.php"
           class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <?php if ($role === 'siswa'): ?>

            <a href="siswa.php"
               class="nav-link <?= $current_page === 'siswa.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate"></i> Data Siswa
            </a>

            <a href="prestasi.php"
               class="nav-link <?= $current_page === 'prestasi.php' ? 'active' : ''; ?>">
                <i class="fas fa-trophy text-warning"></i> Data Prestasi
            </a>

            <a href="kelas.php"
               class="nav-link <?= $current_page === 'kelas.php' ? 'active' : ''; ?>">
                <i class="fas fa-school"></i> Kelas
            </a>

            <a href="wali_kelas.php"
               class="nav-link <?= $current_page === 'wali_kelas.php' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i> Wali Kelas
            </a>

        <?php elseif ($role === 'guru'): ?>

            <a href="pelanggaran_kategori.php"
               class="nav-link <?= $current_page === 'pelanggaran_kategori.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori Pelanggaran
            </a>

            <a href="pelanggaran.php"
               class="nav-link <?= $current_page === 'pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-exclamation-triangle"></i> Data Pelanggaran
            </a>

            <a href="prestasi.php"
               class="nav-link <?= $current_page === 'prestasi.php' ? 'active' : ''; ?>">
                <i class="fas fa-trophy text-warning"></i> Data Prestasi
            </a>

            <a href="laporan.php"
               class="nav-link <?= $current_page === 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan & Rekap
            </a>

            <a href="about_me.php"
               class="nav-link <?= $current_page === 'about_me.php' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i> Tentang Saya
            </a>

            <a href="profile.php"
               class="nav-link <?= $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Pengaturan Profil
            </a>

        <?php elseif ($role === 'admin'): ?>

            <a href="siswa.php"
               class="nav-link <?= $current_page === 'siswa.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate"></i> Data Siswa
            </a>

            <a href="guru.php"
               class="nav-link <?= $current_page === 'guru.php' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i> Data Guru
            </a>

            <a href="kelas.php"
               class="nav-link <?= $current_page === 'kelas.php' ? 'active' : ''; ?>">
                <i class="fas fa-school"></i> Kelas
            </a>

            <a href="wali_kelas.php"
               class="nav-link <?= $current_page === 'wali_kelas.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie"></i> Wali Kelas
            </a>

            <a href="tahun_ajaran.php"
               class="nav-link <?= $current_page === 'tahun_ajaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Tahun Ajaran
            </a>

            <a href="pelanggaran_kategori.php"
               class="nav-link <?= $current_page === 'pelanggaran_kategori.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori Pelanggaran
            </a>

            <a href="pelanggaran.php"
               class="nav-link <?= $current_page === 'pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-exclamation-triangle"></i> Data Pelanggaran
            </a>

            <a href="prestasi.php"
               class="nav-link <?= $current_page === 'prestasi.php' ? 'active' : ''; ?>">
                <i class="fas fa-trophy text-warning"></i> Data Prestasi
            </a>

            <a href="laporan.php"
               class="nav-link <?= $current_page === 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan & Rekap
            </a>

            <a href="about_me.php"
               class="nav-link <?= $current_page === 'about_me.php' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i> Tentang Saya
            </a>

            <a href="profile.php"
               class="nav-link <?= $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Pengaturan Profil
            </a>

        <?php endif; ?>
    </nav>

    <div class="sidebar-divider"></div>

    <a href="logout.php" class="nav-link text-danger"
       onclick="return confirm('Yakin ingin logout?');">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</aside>

<main class="main-content">