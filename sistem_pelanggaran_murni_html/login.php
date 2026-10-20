
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pesan_error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $pesan_error = 'Email dan password wajib diisi.';
    } elseif (strlen($email) > 190 || strlen($password) > 1024) {
        $pesan_error = 'Input tidak valid.';
    } else {
        $stmt = $koneksi->prepare(
            "SELECT id, name, email, password, role
             FROM t_users
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $valid = false;

        if ($user) {
            $hash = (string) $user['password'];

            // Password hash baru
            if (password_verify($password, $hash)) {
                $valid = true;

                // Perbarui hash jika algoritma perlu ditingkatkan
                if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                    $hashBaru = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $update = $koneksi->prepare(
                        "UPDATE t_users SET password = ? WHERE id = ?"
                    );
                    $update->bind_param(
                        'si',
                        $hashBaru,
                        $user['id']
                    );
                    $update->execute();
                    $update->close();
                }
            } elseif (hash_equals($hash, $password)) {
                // Kompatibilitas sementara untuk password lama
                $valid = true;

                // Ubah password teks lama menjadi hash
                $hashBaru = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $update = $koneksi->prepare(
                    "UPDATE t_users SET password = ? WHERE id = ?"
                );
                $update->bind_param(
                    'si',
                    $hashBaru,
                    $user['id']
                );
                $update->execute();
                $update->close();
            }
        }

        if ($valid) {
            $role = strtolower(trim($user['role'] ?? ''));

            $roleValid = [
                'admin',
                'guru',
                'siswa',
                'kepsek',
                'kepala sekolah'
            ];

            if (!in_array($role, $roleValid, true)) {
                $pesan_error =
                    'Role akun tidak valid. Hubungi administrator.';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['nama'] = $user['name'] ?? 'Pengguna';
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $role;

                header('Location: dashboard.php');
                exit;
            }
        } else {
            $pesan_error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Pelanggaran Siswa</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, .2);
        }

        .login-title {
            color: #1e3c72;
            font-weight: 700;
        }

        .btn-login {
            background: #1e3c72;
            color: #fff;
        }

        .btn-login:hover {
            background: #16315f;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card login-card mx-auto">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="login-title">SISWA TERTIB</h2>
                <p class="text-muted">
                    Sistem Informasi Pelanggaran Siswa
                </p>
                <p class="small text-muted">
                    Silakan masuk menggunakan akun Anda.
                </p>
            </div>

            <?php if ($pesan_error !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars(
                        $pesan_error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Masukkan email"
                        maxlength="190"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-login btn-block mt-4"
                >
                    Masuk
                </button>
            </form>

            <hr>
            <p class="small text-muted text-center mb-0">
                &copy; 2026 SISWA TERTIB
            </p>
        </div>
    </div>
</div>

</body>
</html>
