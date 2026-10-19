
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek status login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil role dan normalisasi huruf
$user_role = strtolower(trim($_SESSION['role'] ?? ''));
$current_page = basename($_SERVER['PHP_SELF']);

// Halaman yang boleh dibuka guru
$akses_guru = [
    'dashboard.php',
    'pelanggaran_kategori.php',
    'pelanggaran.php',
    'prestasi.php',
    'laporan.php',
    'about_me.php',
    'profile.php',
    'logout.php'
];

// Halaman yang boleh dibuka siswa
$akses_siswa = [
    'dashboard.php',
    'siswa.php',
    'prestasi.php',
    'kelas.php',
    'wali_kelas.php',
    'logout.php'
];

// Validasi akses guru
if (
    $user_role === 'guru' &&
    !in_array($current_page, $akses_guru, true)
) {
    header("Location: dashboard.php?error=akses_ditolak");
    exit();
}

// Validasi akses siswa
if (
    $user_role === 'siswa' &&
    !in_array($current_page, $akses_siswa, true)
) {
    header("Location: dashboard.php?error=akses_ditolak");
    exit();
}
?>

