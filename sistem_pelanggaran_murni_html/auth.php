<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengguna wajib login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_role = strtolower(trim($_SESSION['role'] ?? ''));
$current_page = basename($_SERVER['PHP_SELF']);

// Halaman yang boleh diakses guru
$akses_guru = [
    'dashboard.php',
    'pelanggaran_kategori.php',
    'pelanggaran.php',
    'tambah_pelanggaran.php',
    'prestasi.php',
    'prestasi_tambah.php',
    'laporan.php',
    'riwayat_pelanggaran.php',
    'tindak_lanjut.php',
    'profile.php',
    'about_me.php',
    'logout.php'
];

// Halaman yang boleh diakses siswa
$akses_siswa = [
    'dashboard.php',
    'siswa.php',
    'prestasi.php',
    'kelas.php',
    'wali_kelas.php',
    'profile.php',
    'about_me.php',
    'logout.php'
];

// Admin memiliki akses ke seluruh halaman privat.
// Role lain hanya dapat mengakses halaman yang diizinkan.
if ($user_role === 'guru' &&
    !in_array($current_page, $akses_guru, true)) {
    http_response_code(403);
    exit('Akses ditolak. Anda tidak memiliki izin membuka halaman ini.');
}

if ($user_role === 'siswa' &&
    !in_array($current_page, $akses_siswa, true)) {
    http_response_code(403);
    exit('Akses ditolak. Anda tidak memiliki izin membuka halaman ini.');
}

// Tolak role yang tidak dikenal
$role_valid = ['admin', 'guru', 'siswa', 'kepala sekolah', 'kepsek'];

if (!in_array($user_role, $role_valid, true)) {
    http_response_code(403);
    exit('Role akun tidak dikenali. Hubungi administrator.');
}