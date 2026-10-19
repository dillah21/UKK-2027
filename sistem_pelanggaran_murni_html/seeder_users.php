<?php
require_once 'koneksi.php';

// Daftar Akun yang Ingin Dibuat
$users = [
    [
        'nama'     => 'Guru Pembimbing, S.Pd.',
        'email'    => 'guru@gmail.com',
        'password' => 'guru123',
        'role'     => 'guru'
    ],
    [
        'nama'     => 'Ahmad Siswa Teladan',
        'email'    => 'siswa@gmail.com',
        'password' => 'siswa123',
        'role'     => 'siswa'
    ]
];

echo "<h3>Proses Pembuatan Akun Guru & Siswa</h3>";

foreach ($users as $u) {
    $nama     = $u['nama'];
    $email    = $u['email'];
    $pass_raw = $u['password'];
    $role     = $u['role'];
    
    // Encrypt password menggunakan PASSWORD_DEFAULT
    $hashed_password = password_hash($pass_raw, PASSWORD_DEFAULT);

    // Cek apakah email sudah ada
    $stmt_cek = $koneksi->prepare("SELECT id FROM t_users WHERE email = ?");
    $stmt_cek->bind_param("s", $email);
    $stmt_cek->execute();
    $res = $stmt_cek->get_result();

    if ($res->num_rows > 0) {
        // Update jika user sudah ada
        $stmt_up = $koneksi->prepare("UPDATE t_users SET name = ?, password = ? WHERE email = ?");
        $stmt_up->bind_param("sss", $nama, $hashed_password, $email);
        if ($stmt_up->execute()) {
            echo "<p style='color:green;'>✓ Akun <b>$email</b> ($role) berhasil diperbarui! Password: <b>$pass_raw</b></p>";
        } else {
            echo "<p style='color:red;'>✗ Gagal memperbarui akun $email: " . $koneksi->error . "</p>";
        }
        $stmt_up->close();
    } else {
        // Insert jika user belum ada
        // Catatan: Jika tabel t_users Abang tidak punya kolom 'role', hapus bagian role di query ini
        $cek_kolom_role = $koneksi->query("SHOW COLUMNS FROM t_users LIKE 'role'");
        if ($cek_kolom_role && $cek_kolom_role->num_rows > 0) {
            $stmt_in = $koneksi->prepare("INSERT INTO t_users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt_in->bind_param("ssss", $nama, $email, $hashed_password, $role);
        } else {
            $stmt_in = $koneksi->prepare("INSERT INTO t_users (name, email, password) VALUES (?, ?, ?)");
            $stmt_in->bind_param("sss", $nama, $email, $hashed_password);
        }

        if ($stmt_in->execute()) {
            echo "<p style='color:green;'>✓ Akun <b>$email</b> ($role) berhasil dibuat! Password: <b>$pass_raw</b></p>";
        } else {
            echo "<p style='color:red;'>✗ Gagal membuat akun $email: " . $koneksi->error . "</p>";
        }
        $stmt_in->close();
    }
    $stmt_cek->close();
}
?>