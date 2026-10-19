
<?php
$role = strtolower($_SESSION['role'] ?? '');
?>

<?php if ($role === 'siswa'): ?>

    <!-- MENU KHUSUS SISWA -->
    <a href="dashboard.php" class="nav-link">
        <i class="fas fa-home"></i>
        Dashboard
    </a>

    <a href="data_siswa.php" class="nav-link">
        <i class="fas fa-user"></i>
        Data Siswa
    </a>

    <a href="prestasi.php" class="nav-link">
        <i class="fas fa-trophy"></i>
        Data Prestasi
    </a>

    <a href="kelas.php" class="nav-link">
        <i class="fas fa-school"></i>
        Kelas
    </a>

    <a href="wali_kelas.php" class="nav-link">
        <i class="fas fa-chalkboard-teacher"></i>
        Wali Kelas
    </a>

<?php else: ?>

    <!-- MENU UNTUK ROLE LAIN -->
    <!-- Pertahankan menu lama admin dan guru di bagian ini -->

<?php endif; ?>
