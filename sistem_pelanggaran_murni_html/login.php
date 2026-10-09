<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        // Query ambil data user berdasarkan email
        $stmt = $koneksi->prepare("SELECT * FROM t_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $db_password = trim($user['password']);

            // Pengecekan password: teks biasa ATAU hash bcrypt
            $is_valid = ($password === $db_password) || password_verify($password, $db_password);

            if ($is_valid) {
                // Simpan data login ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['name'] ?? $user['email'];
                $_SESSION['role'] = $user['role'];

                // Langsung lempar ke dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email '" . htmlspecialchars($email) . "' tidak ditemukan!";
        }
        $stmt->close();
    } else {
        $error = "Silakan isi Email dan Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Pelanggaran Siswa</title>
</head>
<body>

<center>
    <br><br><br>
    <h2>Login Sistem Pelanggaran Siswa</h2>
    <p>Silakan masuk untuk mengakses panel sistem</p>
    <br>

    <?php if ($error): ?>
        <p style="color: red;"><b><?= htmlspecialchars($error); ?></b></p>
        <br>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <table border="0" cellpadding="6" cellspacing="0" align="center">
            <tr>
                <td align="right"><label for="email"><b>Email Account</b></label></td>
                <td>:</td>
                <td><input type="email" id="email" name="email" placeholder="contoh: user1@sekolah.sch.id" required autofocus size="28"></td>
            </tr>
            <tr>
                <td align="right"><label for="password"><b>Kata Sandi</b></label></td>
                <td>:</td>
                <td><input type="password" id="password" name="password" placeholder="Masukkan password" required size="28"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td align="left">
                    <br>
                    <button type="submit" style="padding: 6px 18px; cursor: pointer;"><b>Masuk</b></button>
                </td>
            </tr>
        </table>
    </form>

    <br><br><br>
    <hr width="50%">
    <p>&copy; 2026 UKK - Sistem Informasi Pelanggaran Siswa | Dikembangkan oleh: <b>AZRIL AKBAR FAWZI ABDILLAH</b></p>
</center>

</body>
</html>