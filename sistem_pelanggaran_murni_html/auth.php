
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_role = strtolower(trim($_SESSION['role'] ?? ''));
$current_page = basename($_SERVER['PHP_SELF']);

// Admin memiliki akses penuh.
if ($user_role === 'admin') {
    return;
}

// Hak akses guru.
$akses_guru = [
    'dashboard.php',
    'pelanggaran_kategori.php',
    'pelanggaran.php',
    'tambah_pelanggaran.php',
    'laporan.php',
    'riwayat_pelanggaran.php',
    'tindak_lanjut.php',
    'profile.php',
    'about_me.php',
    'logout.php',
    'cetak_sp.php',
    'export_exel.php'
];

// Hak akses siswa.
$akses_siswa = [
    'dashboard.php',
    'profile.php',
    'about_me.php',
    'logout.php'
];

// Hak akses kepala sekolah.
$akses_kepsek = [
    'dashboard.php',
    'laporan.php',
    'riwayat_pelanggaran.php',
    'rekap_poin.php',
    'cetak_sp.php',
    'export_exel.php',
    'profile.php',
    'about_me.php',
    'logout.php'
];

$role_access = [
    'guru' => $akses_guru,
    'siswa' => $akses_siswa,
    'kepala sekolah' => $akses_kepsek,
    'kepsek' => $akses_kepsek
];

if (!isset($role_access[$user_role])) {
    http_response_code(403);
    exit('Role akun tidak dikenali. Hubungi administrator.');
}

if (!in_array($current_page, $role_access[$user_role], true)) {
    http_response_code(403);
    exit('Akses ditolak. Anda tidak memiliki izin membuka halaman ini.');
}
