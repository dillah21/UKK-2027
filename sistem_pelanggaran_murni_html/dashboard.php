<?php
require_once 'auth.php';
require_once 'koneksi.php';

include 'header.php';
?>

<h2>Dashboard Utama</h2>
<p>Selamat datang di Sistem Pelanggaran Siswa.</p>

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