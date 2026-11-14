<?php
require_once 'auth.php';
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: kelas.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$tingkat = trim($_POST['tingkat'] ?? '');
$jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));
$status_aktif = isset($_POST['status_aktif']) ? (int)$_POST['status_aktif'] : 1;

if ($nama === '' || strlen($nama) > 100 || !in_array($tingkat, ['10', '11', '12'], true)
    || !in_array($jurusan, ['RPL', 'TKJ', 'TKR', 'TSM', 'BDPM'], true)
    || !in_array($status_aktif, [0, 1], true)) {
    header('Location: kelas.php?status=invalid');
    exit;
}

$stmt = $koneksi->prepare('INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
if (!$stmt) {
    header('Location: kelas.php?status=gagal');
    exit;
}
$stmt->bind_param('sisi', $nama, $tingkat, $jurusan, $status_aktif);
$berhasil = $stmt->execute();
$stmt->close();

header('Location: kelas.php?status=' . ($berhasil ? 'berhasil' : 'gagal'));
exit;
?>
