
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
$current_page = basename($_SERVER['PHP_SELF']);
$nama_user = ($role === 'admin') ? 'AZRIL' : ($_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna');
$nama_user = trim((string)$nama_user) !== '' ? trim((string)$nama_user) : 'Pengguna';
$inisial_user = '';
foreach (preg_split('/\s+/', $nama_user) as $bagian_nama) {
    if ($bagian_nama !== '') $inisial_user .= strtoupper(substr($bagian_nama, 0, 1));
    if (strlen($inisial_user) >= 2) break;
}
if ($inisial_user === '') $inisial_user = 'U';
$label_role = ucwords(str_replace(['_', '-'], ' ', $role ?: 'pengguna'));
$judul_halaman = [
    'dashboard.php' => 'Dashboard', 'siswa.php' => 'Data Siswa',
    'guru.php' => 'Data Guru', 'kelas.php' => 'Data Kelas',
    'wali_kelas.php' => 'Wali Kelas', 'tahun_ajaran.php' => 'Tahun Ajaran',
    'pelanggaran.php' => 'Jenis Pelanggaran', 'pelanggaran_kategori.php' => 'Kategori Pelanggaran',
    'laporan.php' => 'Laporan & Rekap', 'rekap_poin.php' => 'Rekap Poin',
    'tindak_lanjut.php' => 'Tindakan', 'profile.php' => 'Pengaturan Profil',
    'users.php' => 'Manajemen Pengguna', 'log_aktivitas.php' => 'Log Aktivitas',
    'about_me.php' => 'Tentang Saya', 'riwayat_pelanggaran.php' => 'Riwayat Pelanggaran',
    'kelas_siswa.php' => 'Penempatan Siswa', 'siswa_kelas.php' => 'Data Siswa per Kelas',
    'tambah_pelanggaran.php' => 'Catat Pelanggaran', 'import_siswa.php' => 'Import Data Siswa'
];
$judul_topbar = $judul_halaman[$current_page] ?? ucwords(str_replace(['_', '.php'], [' ', ''], $current_page));
$tahun_aktif_nav = '';
if (isset($koneksi) && $koneksi instanceof mysqli) {
    try {
        $q_tahun_nav = $koneksi->query("SELECT nama FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC, id DESC LIMIT 1");
        if ($q_tahun_nav && ($row_tahun_nav = $q_tahun_nav->fetch_assoc())) {
            $tahun_aktif_nav = (string)($row_tahun_nav['nama'] ?? '');
        }
    } catch (Throwable $e) {
        // Navbar tetap tampil meskipun informasi tahun ajaran belum tersedia.
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 4.1.3 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- DataTables Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- jQuery dan DataTables dimuat sebelum script inisialisasi tiap halaman -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #173b78;
            --ink: #17233b;
            --muted: #718096;
            --surface: #ffffff;
            --line: #e8edf5;
            --page: #f3f6fb;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0; color: var(--ink); background: var(--page);
            font-family: Inter, "Segoe UI", Arial, sans-serif; font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }
        .sidebar {
            width: 258px; min-height: 100vh; color: #eaf1ff;
            background: linear-gradient(180deg, #142b50 0%, #10213e 100%);
            position: fixed; inset: 0 auto 0 0; padding: 22px 15px;
            z-index: 1000; overflow-y: auto; box-shadow: 8px 0 28px rgba(15,35,70,.08);
        }
        .sidebar-title {
            display: flex; align-items: center; gap: 11px; color: #fff;
            font-weight: 750; padding: 8px 10px 25px; font-size: 16px;
            letter-spacing: -.3px; line-height: 1.35;
        }
        .sidebar-title i {
            display: grid; place-items: center; width: 39px; height: 39px;
            border-radius: 12px; color: #fff; background: linear-gradient(135deg,#3984ff,#2457c5);
            box-shadow: 0 7px 18px rgba(37,99,235,.35); font-size: 17px;
        }
        .sidebar .nav { gap: 4px; }
        .sidebar .nav-link {
            color: #c3d0e5; display: flex; align-items: center; gap: 10px;
            padding: 11px 13px; margin: 0; border-radius: 10px;
            text-decoration: none; font-size: 13px; font-weight: 550;
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }
        .sidebar .nav-link i { width: 20px; text-align: center; margin: 0; font-size: 14px; opacity: .9; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.09); color: #fff; transform: translateX(2px); }
        .sidebar .nav-link.active {
            color: #fff; background: linear-gradient(100deg,#2d73ec,#2359c6);
            box-shadow: 0 7px 18px rgba(22,83,190,.28);
        }
        .sidebar-divider { border-top: 1px solid rgba(220,232,255,.14); margin: 20px 5px 14px; }
        .sidebar .nav-link.text-danger { color: #ffb4b4 !important; }
        .sidebar .nav-link.text-danger:hover { background: rgba(239,68,68,.14); color: #fff !important; }
        .main-content { margin-left: 258px; padding: 30px; min-height: 100vh; }
        .container, .container-fluid { max-width: 100%; }
        h1, h2, h3, h4, h5 { color: #182943; letter-spacing: -.45px; font-weight: 700; }
        h1.h2, h1.h3, h2 { margin-bottom: 20px; }
        p.text-muted, .text-muted { color: var(--muted) !important; }
        .card {
            border: 1px solid var(--line); border-radius: 15px; background: var(--surface);
            box-shadow: 0 5px 20px rgba(26,49,88,.045); margin-bottom: 20px;
        }
        .card-header { background: #fff; border-bottom: 1px solid var(--line); padding: 16px 20px; border-radius: 15px 15px 0 0 !important; font-weight: 700; }
        .card-body { padding: 20px; }
        .table-responsive { border-radius: 12px; }
        .table { color: #34435b; margin-bottom: 0; }
        .table thead th { background: #f5f8fd; color: #52627b; border-top: 0; border-bottom: 1px solid #e5ebf4; font-size: 11px; text-transform: uppercase; letter-spacing: .55px; font-weight: 750; white-space: nowrap; }
        .table td, .table th { padding: 12px 14px; vertical-align: middle; border-color: #edf1f7; }
        .table tbody tr:hover { background: #f8faff; }
        .form-control, .custom-select { min-height: 40px; border: 1px solid #dce4ef; border-radius: 9px; color: #24344e; box-shadow: none; }
        .form-control:focus, .custom-select:focus { border-color: #75a5fa; box-shadow: 0 0 0 3px rgba(37,99,235,.11); }
        label { color: #43536d; font-weight: 650; font-size: 12px; }
        .btn { border-radius: 9px; font-weight: 650; padding: 8px 14px; transition: all .18s ease; }
        .btn-primary { background: var(--brand); border-color: var(--brand); box-shadow: 0 4px 10px rgba(37,99,235,.14); }
        .btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; transform: translateY(-1px); }
        .btn-secondary, .btn-light { border-color: #e1e7f0; }
        .badge { border-radius: 7px; padding: 6px 9px; font-weight: 650; }
        .alert { border: 0; border-radius: 11px; box-shadow: 0 3px 12px rgba(20,40,75,.04); }
        .pagination .page-link { color: var(--brand); border-color: #e5ebf4; }
        .pagination .page-item.active .page-link { background: var(--brand); border-color: var(--brand); }
        .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select { border: 1px solid #dce4ef; border-radius: 8px; padding: 5px 9px; outline: none; }
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #75a5fa; }
        .modal { z-index: 2000; }
        .modal-content { background: #fff !important; color: #212529; opacity: 1 !important; border: 1px solid rgba(0,0,0,.1); border-radius: 15px; box-shadow: 0 18px 55px rgba(10,25,50,.2); overflow: hidden; }
        .modal-body, .modal-footer { background: #fff; }
        .modal-header { border-bottom-color: #edf1f7; }
        .modal-footer { border-top-color: #edf1f7; }
        .modal-backdrop { z-index: 1990; }
        .topbar {
            position: sticky; top: 0; z-index: 950; min-height: 76px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            margin: -30px -30px 26px; padding: 13px 30px;
            background: rgba(255,255,255,.94); border-bottom: 1px solid #e8edf5;
            box-shadow: 0 5px 18px rgba(22,43,77,.035); backdrop-filter: blur(12px);
        }
        .topbar-heading { min-width: 0; }
        .topbar-eyebrow { color: #8a98ad; font-size: 10px; font-weight: 800; letter-spacing: 1.15px; text-transform: uppercase; margin-bottom: 3px; }
        .topbar-title { color: #1b2d49; font-size: 19px; line-height: 1.2; font-weight: 800; letter-spacing: -.45px; margin: 0; }
        .topbar-right { display: flex; align-items: center; gap: 13px; }
        .topbar-year { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid #dce9ff; border-radius: 999px; color: #2458a8; background: #f2f7ff; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .topbar-year i { color: #3978d8; }
        .topbar-user { display: flex; align-items: center; gap: 9px; padding: 5px 9px 5px 5px; border: 1px solid #e8edf5; border-radius: 13px; background: #fff; color: #263650; text-decoration: none !important; transition: .18s ease; }
        .topbar-user:hover, .topbar-user:focus { border-color: #c8d9f5; background: #f9fbff; color: #1c4d9b; box-shadow: 0 5px 15px rgba(35,79,145,.08); }
        .topbar-avatar { width: 37px; height: 37px; flex: 0 0 37px; display: grid; place-items: center; border-radius: 11px; color: #fff; background: linear-gradient(135deg,#4389f5,#2455bd); font-size: 12px; font-weight: 800; letter-spacing: .4px; }
        .topbar-user-meta { min-width: 0; display: flex; flex-direction: column; line-height: 1.25; }
        .topbar-user-name { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px; font-weight: 800; }
        .topbar-user-role { color: #8a98ad; font-size: 10px; margin-top: 3px; text-transform: capitalize; }
        .topbar-caret { color: #8a98ad; font-size: 11px; margin-left: 2px; }
        .topbar .dropdown-menu { min-width: 205px; padding: 7px; margin-top: 9px; border: 1px solid #e8edf5; border-radius: 13px; box-shadow: 0 14px 35px rgba(20,40,75,.14); }
        .topbar .dropdown-item { padding: 9px 11px; border-radius: 8px; color: #42516a; font-size: 12px; font-weight: 600; }
        .topbar .dropdown-item:hover { background: #f2f6fd; color: #2458a8; }
        .topbar .dropdown-item i { width: 19px; color: #7890b2; }
        .topbar .dropdown-divider { border-color: #edf1f7; }
        @media (max-width: 900px) {
            .sidebar { width: 220px; padding: 18px 11px; }
            .main-content { margin-left: 220px; padding: 22px 18px; }
            .topbar { margin: -22px -18px 22px; padding: 12px 18px; }
        }
        @media (max-width: 600px) {
            .sidebar { width: 68px; padding: 15px 8px; }
            .sidebar-title { justify-content: center; padding: 5px 0 22px; font-size: 0; }
            .sidebar-title i { flex: 0 0 40px; }
            .sidebar .nav-link { justify-content: center; padding: 12px 5px; font-size: 0; }
            .sidebar .nav-link i { font-size: 16px; width: auto; }
            .sidebar-divider { margin: 15px 2px; }
            .main-content { margin-left: 68px; padding: 16px 12px; }
            .topbar { min-height: 65px; margin: -16px -12px 18px; padding: 10px 12px; gap: 8px; }
            .topbar-title { font-size: 16px; }
            .topbar-eyebrow { font-size: 9px; }
            .topbar-right { gap: 6px; }
            .topbar-year { padding: 7px 8px; font-size: 10px; gap: 5px; }
            .topbar-user { gap: 6px; padding: 4px; }
            .topbar-avatar { width: 33px; height: 33px; flex-basis: 33px; }
            .topbar-user-meta { display: none; }
            .topbar-caret { display: none; }
            .card-body { padding: 15px; }
            h1.h2 { font-size: 1.35rem; }
        }
        /* Tema gelap: aktif hanya saat pengguna memilihnya. */
        html[data-theme="dark"] body { color: #e5eaf3; background: #101827; }
        html[data-theme="dark"] h1, html[data-theme="dark"] h2, html[data-theme="dark"] h3,
        html[data-theme="dark"] h4, html[data-theme="dark"] h5 { color: #edf3ff; }
        html[data-theme="dark"] .main-content { background: #101827; }
        html[data-theme="dark"] .card { background: #182438; border-color: #2b3a52; box-shadow: 0 5px 20px rgba(0,0,0,.16); }
        html[data-theme="dark"] .card-header { background: #1c2b42; color: #edf3ff; border-color: #2b3a52; }
        html[data-theme="dark"] .table { color: #d8e1ef; }
        html[data-theme="dark"] .table thead th { background: #22324a; color: #b9c8dc; border-color: #34445c; }
        html[data-theme="dark"] .table td, html[data-theme="dark"] .table th { border-color: #2b3a52; }
        html[data-theme="dark"] .table tbody tr:hover { background: #22324a; }
        html[data-theme="dark"] .form-control, html[data-theme="dark"] .custom-select,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_filter input,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_length select {
            background-color: #111d2e; color: #e5eaf3; border-color: #3a4b64;
        }
        html[data-theme="dark"] .form-control::placeholder { color: #8292aa; }
        html[data-theme="dark"] label, html[data-theme="dark"] .text-dark { color: #d8e1ef !important; }
        html[data-theme="dark"] .text-muted, html[data-theme="dark"] p.text-muted { color: #9aabc2 !important; }
        html[data-theme="dark"] .topbar { background: rgba(24,36,56,.95); border-color: #2b3a52; }
        html[data-theme="dark"] .topbar-title { color: #edf3ff; }
        html[data-theme="dark"] .topbar-eyebrow, html[data-theme="dark"] .topbar-user-role { color: #9aabc2; }
        html[data-theme="dark"] .topbar-user { background: #1c2b42; border-color: #34445c; color: #e5eaf3; }
        html[data-theme="dark"] .topbar-user:hover { background: #263954; }
        html[data-theme="dark"] .topbar-year { background: #1b3455; border-color: #34547d; color: #c7dcff; }
        html[data-theme="dark"] .topbar .dropdown-menu { background: #1c2b42; border-color: #34445c; }
        html[data-theme="dark"] .topbar .dropdown-item { color: #d8e1ef; }
        html[data-theme="dark"] .topbar .dropdown-item:hover { background: #263954; color: #fff; }
        html[data-theme="dark"] .topbar .dropdown-divider { border-color: #34445c; }
        html[data-theme="dark"] .topbar .dropdown-menu .font-weight-bold { color: #edf3ff !important; }
        html[data-theme="dark"] .modal-content, html[data-theme="dark"] .modal-body,
        html[data-theme="dark"] .modal-footer, html[data-theme="dark"] .modal-header {
            background: #182438 !important; color: #e5eaf3; border-color: #34445c;
        }
        html[data-theme="dark"] .close { color: #fff; text-shadow: none; }
        html[data-theme="dark"] .alert { filter: brightness(.88); }
        .theme-toggle { width: 39px; height: 39px; display: inline-grid; place-items: center;
            border: 1px solid #e1e8f2; border-radius: 11px; background: #fff; color: #425675;
            cursor: pointer; transition: .18s ease; }
        .theme-toggle:hover { border-color: #b9ccef; color: #2458a8; transform: translateY(-1px); }
        html[data-theme="dark"] .theme-toggle { background: #1c2b42; color: #ffda85; border-color: #34445c; }
        @media print {
            .sidebar, .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
            .card { box-shadow: none !important; }
        }
    
        /* Konsistensi seluruh formulir tambah/edit pada semua modul */
        form:not(.form-inline) { width: 100%; }
        form:not(.form-inline) .form-group,
        form:not(.form-inline) .form-row { margin-bottom: 16px; }
        form:not(.form-inline) label,
        form:not(.form-inline) td label {
            display: inline-block; margin-bottom: 7px; color: #43536d;
            font-size: 12px; font-weight: 700;
        }
        form:not(.form-inline) input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="hidden"]),
        form:not(.form-inline) select,
        form:not(.form-inline) textarea {
            display: block; width: 100%; max-width: 100%; min-height: 42px;
            padding: 9px 12px; color: #24344e; background: #fff;
            border: 1px solid #dce4ef; border-radius: 9px;
            font: inherit; line-height: 1.4; box-shadow: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        form:not(.form-inline) textarea { min-height: 100px; resize: vertical; }
        form:not(.form-inline) input:focus,
        form:not(.form-inline) select:focus,
        form:not(.form-inline) textarea:focus {
            outline: 0; border-color: #75a5fa;
            box-shadow: 0 0 0 3px rgba(37,99,235,.11);
        }
        form:not(.form-inline) input[type="checkbox"],
        form:not(.form-inline) input[type="radio"] { width: auto; min-height: auto; margin-right: 6px; }
        form:not(.form-inline) button,
        form:not(.form-inline) input[type="submit"] { min-height: 38px; }
        /* Form lama berbasis tabel tetap rapi dan responsif */
        form:not(.form-inline) table:not(.table) {
            width: 100%; max-width: 100%; border: 0; border-collapse: separate;
            border-spacing: 0 10px;
        }
        form:not(.form-inline) table:not(.table) td {
            padding: 3px 8px; border: 0; vertical-align: middle;
        }
        form:not(.form-inline) table:not(.table) td:first-child { width: 180px; }
        form:not(.form-inline) table:not(.table) td input,
        form:not(.form-inline) table:not(.table) td select,
        form:not(.form-inline) table:not(.table) td textarea { width: 100%; }
        .modal .modal-body form { padding: 2px 0; }
        .modal .modal-body form .form-group:last-child { margin-bottom: 0; }
        .card form .form-actions,
        form .form-actions { display: flex; flex-wrap: wrap; gap: 9px; align-items: center; margin-top: 20px; }
        @media (max-width: 767.98px) {
            .main-content { padding: 16px; margin-left: 0; }
            .topbar { margin: -16px -16px 20px; padding: 12px 16px; }
            form:not(.form-inline) table:not(.table),
            form:not(.form-inline) table:not(.table) tbody,
            form:not(.form-inline) table:not(.table) tr,
            form:not(.form-inline) table:not(.table) td { display: block; width: 100% !important; }
            form:not(.form-inline) table:not(.table) td { padding: 4px 0; }
            form:not(.form-inline) table:not(.table) td:first-child { padding-bottom: 0; }
        }
</style>
</head>

<body>

<aside class="sidebar">
    <div class="sidebar-title" title="Sistem Informasi Pelanggaran Siswa">
        <i class="fas fa-shield-alt"></i>
        <span>Sistem Pelanggaran</span>
    </div>

    <nav class="nav flex-column">

        <a href="dashboard.php"
           class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <?php if ($role === 'siswa'): ?>

            <a href="profile.php"
               class="nav-link <?= $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Pengaturan Profil
            </a>

        <?php elseif ($role === 'guru'): ?>

            <a href="tambah_pelanggaran.php"
               class="nav-link <?= $current_page === 'tambah_pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i> Catat Pelanggaran
            </a>

            <a href="tindak_lanjut.php"
               class="nav-link <?= $current_page === 'tindak_lanjut.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check"></i> Tindakan
            </a>

            <a href="riwayat_pelanggaran.php"
               class="nav-link <?= $current_page === 'riwayat_pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Riwayat
            </a>

            <a href="rekap_poin.php"
               class="nav-link <?= $current_page === 'rekap_poin.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Rekap Poin
            </a>

            <a href="laporan.php"
               class="nav-link <?= $current_page === 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan & Rekap
            </a>

            <a href="profile.php"
               class="nav-link <?= $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Pengaturan Profil
            </a>

        <?php elseif (in_array($role, ['kepala sekolah', 'kepsek'], true)): ?>

            <a href="riwayat_pelanggaran.php"
               class="nav-link <?= $current_page === 'riwayat_pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Riwayat Pelanggaran
            </a>

            <a href="laporan.php"
               class="nav-link <?= $current_page === 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan & Rekap
            </a>

            <a href="tindak_lanjut.php"
               class="nav-link <?= $current_page === 'tindak_lanjut.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check"></i> Tindak Lanjut BK
            </a>

            <a href="rekap_poin.php"
               class="nav-link <?= $current_page === 'rekap_poin.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Rekap Poin
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

            <a href="kelas_siswa.php"
               class="nav-link <?= $current_page === 'kelas_siswa.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Penempatan Siswa
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
                <i class="fas fa-exclamation-triangle"></i> Jenis Pelanggaran
            </a>
<a href="riwayat_pelanggaran.php"
               class="nav-link <?= $current_page === 'riwayat_pelanggaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Riwayat Pelanggaran
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

    <a href="logout.php"
       class="nav-link text-danger"
       onclick="return confirm('Yakin ingin logout?');">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</aside>

<main class="main-content">
    <header class="topbar">
        <div class="topbar-heading">
            <div class="topbar-eyebrow">Sistem Informasi Pelanggaran Siswa</div>
            <h1 class="topbar-title"><?= htmlspecialchars($judul_topbar, ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
        <div class="topbar-right">
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Aktifkan mode gelap" title="Ganti tema">
                <i class="fas fa-moon" aria-hidden="true"></i>
            </button>
            <?php if ($tahun_aktif_nav !== ''): ?>
                <div class="topbar-year" title="Tahun ajaran aktif">
                    <i class="far fa-calendar-check"></i>
                    <span><?= htmlspecialchars($tahun_aktif_nav, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>
            <div class="dropdown">
                <a href="#" class="topbar-user" id="topbarUserMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="topbar-avatar"><?= htmlspecialchars($inisial_user, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="topbar-user-meta">
                        <span class="topbar-user-name"><?= htmlspecialchars($nama_user, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="topbar-user-role"><?= htmlspecialchars($label_role, ENT_QUOTES, 'UTF-8'); ?></span>
                    </span>
                    <i class="fas fa-chevron-down topbar-caret"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="topbarUserMenu">
                    <div class="px-3 py-2">
                        <div class="font-weight-bold" style="font-size:12px;color:#24344e;"><?= htmlspecialchars($nama_user, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($label_role, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog"></i> Pengaturan Profil</a>
                    <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Yakin ingin logout?');"><i class="fas fa-sign-out-alt"></i> Keluar dari aplikasi</a>
                </div>
            </div>
        </div>
    </header>

<!--
    Konten setiap halaman PHP ditampilkan setelah header.php.
    JavaScript Bootstrap dimuat di footer.php atau di akhir halaman.
-->
