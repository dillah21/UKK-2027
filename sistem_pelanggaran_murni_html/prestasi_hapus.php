<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'auth.php';
require_once 'koneksi.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: prestasi.php?status=id_tidak_valid'); exit; }
try {
    $kolom = $koneksi->query("SHOW COLUMNS FROM prestasi");
    $pk = 'id'; $ada_pk = false;
    while ($c = $kolom->fetch_assoc()) {
        if ($c['Field'] === 'id_prestasi') { $pk = 'id_prestasi'; $ada_pk = true; break; }
        if ($c['Field'] === 'id') $ada_pk = true;
    }
    if (!$ada_pk) throw new RuntimeException('Kolom ID tabel prestasi tidak ditemukan.');
    $stmt = $koneksi->prepare("DELETE FROM prestasi WHERE `$pk` = ?");
    $stmt->bind_param('i', $id); $stmt->execute(); $terhapus = $stmt->affected_rows; $stmt->close();
    header('Location: prestasi.php?status=' . ($terhapus ? 'hapus_sukses' : 'data_tidak_ditemukan')); exit;
} catch (Throwable $e) {
    error_log('Hapus prestasi gagal: ' . $e->getMessage());
    header('Location: prestasi.php?status=hapus_gagal'); exit;
}
