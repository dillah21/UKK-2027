<?php
include 'koneksi.php';

// Otomatis buat tabel t_user jika belum ada agar tidak error fatal
$create_table = "CREATE TABLE IF NOT EXISTS `t_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($koneksi, $create_table);

// Tambah user default jika kosong
$check_user = mysqli_query($koneksi, "SELECT * FROM t_user WHERE username='admin'");
if ($check_user && mysqli_num_rows($check_user) == 0) {
    mysqli_query($koneksi, "INSERT INTO t_user (username, password, nama, role_id, status_aktif) VALUES ('admin', 'adminpassword', 'Administrator', 1, 1)");
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM t_user WHERE username = '$username' AND status_aktif = 1 LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (md5($password) === $user['password'] || password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan atau akun tidak aktif!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pelanggaran Siswa</title>
</head>
<body>
    <h2>Login Sistem Pelanggaran Siswa</h2>
    <hr>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Username:</label><br>
            <input type="text" name="username" required autofocus>
        </div>
        <br>
        <div>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <button type="submit" name="login">Login / Masuk</button>
    </form>
</body>
</html>