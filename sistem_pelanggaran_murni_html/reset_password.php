<?php
require_once 'koneksi.php';

// Email yang ingin direset passwordnya sesuai gambar Abang
$email = 'user1@sekolah.sch.id'; 
$password_baru = '123456'; // Ganti password sesuai keinginan Abang, misal: 123456

// Update password langsung simpan sebagai teks biasa (atau pakai hash)
// Kita buat support teks biasa agar tidak error password_verify lagi
$stmt = $koneksi->prepare("UPDATE t_users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $password_baru, $email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<h3 style='color:green;'>Berhasil! Password untuk akun <b>$email</b> telah direset menjadi: <b>$password_baru</b></h3>";
        echo "<p><a href='login.php'>Klik di sini untuk kembali ke halaman Login</a></p>";
    } else {
        echo "<h3 style='color:orange;'>Email <b>$email</b> tidak ditemukan di database tabel t_users. Cek kembali emailnya di phpMyAdmin.</h3>";
    }
} else {
    echo "<h3 style='color:red;'>Gagal mereset: " . $koneksi->error . "</h3>";
}
$stmt->close();
?>