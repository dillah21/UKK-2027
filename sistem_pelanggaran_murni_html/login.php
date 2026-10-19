<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $stmt = $koneksi->prepare("SELECT * FROM t_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $db_password = trim($user['password']);

            // Fleksibel: Cocokkan plain text ATAU password_verify
            $is_match = ($password === $db_password) || password_verify($password, $db_password);

            if ($is_match) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['name'] ?? ($user['nama'] ?? 'User');
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = strtolower(trim($user['role'] ?? 'admin'));

                header("Location: dashboard.php");
                exit();
            } else {
                $pesan_error = "Password yang Anda masukkan salah!";
            }
        } else {
            $pesan_error = "Email/Username tidak terdaftar!";
        }
        $stmt->close();
    } else {
        $pesan_error = "Harap isi Email dan Password secara lengkap!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { border: none; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 420px; }
        .card-login .card-header { background-color: transparent; border-bottom: none; padding-top: 30px; padding-bottom: 0; }
    </style>
</head>
<body>
<div class="container p-3">
    <div class="row justify-content-center">
        <div class="card card-login bg-white">
            <div class="card-header text-center">
                <div class="mb-3 text-primary"><i class="fas fa-user-shield fa-3x"></i></div>
                <h4 class="font-weight-bold text-dark">Sistem Pelanggaran</h4>
                <p class="text-muted small">Silakan login menggunakan akun Anda</p>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($pesan_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i> <?= htmlspecialchars($pesan_error); ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Email / Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                            <input type="email" name="email" class="form-control" placeholder="contoh@gmail.com" required autofocus>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk Akun
                    </button>
                </form>
            </div>
            <div class="card-footer bg-light text-center py-3 border-0 rounded-bottom">
                <small class="text-muted">&copy; 2026 UKK RPL - All Rights Reserved</small>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>