
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
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $pesan_error = 'Email dan password wajib diisi.';
    } elseif (strlen($email) > 190 || strlen($password) > 1024) {
        $pesan_error = 'Input tidak valid.';
    } else {
        // Pencarian email dibuat toleran terhadap huruf besar/kecil.
        // bind_result dipakai agar tidak bergantung pada mysqlnd/get_result.
        $stmt = $koneksi->prepare(
            "SELECT id, name, email, password, role
             FROM t_users
             WHERE LOWER(TRIM(email)) = LOWER(?)
             LIMIT 1"
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result(
            $user_id,
            $user_name,
            $user_email,
            $user_password,
            $user_role
        );

        $user = null;
        if ($stmt->fetch()) {
            $user = [
                'id' => $user_id,
                'name' => $user_name,
                'email' => $user_email,
                'password' => $user_password,
                'role' => $user_role
            ];
        }
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
            $role = str_replace('_', ' ', $role);

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
    <meta name="theme-color" content="#10264a">
    <title>Masuk | Sistem Informasi Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root { --navy:#10264a; --blue:#2868d7; --ink:#192b47; --muted:#8290a5; }
        * { box-sizing:border-box; }
        body {
            min-height:100vh; margin:0; padding:28px;
            display:flex; align-items:center; justify-content:center;
            background:radial-gradient(circle at 12% 12%,#e4edff 0,transparent 27%),
                       radial-gradient(circle at 90% 88%,#dce9ff 0,transparent 25%),#f3f6fc;
            color:var(--ink); font-family:Inter,"Segoe UI",Arial,sans-serif;
        }
        .login-shell {
            width:100%; max-width:1060px; min-height:630px; display:grid;
            grid-template-columns:1.02fr .98fr; overflow:hidden; background:#fff;
            border:1px solid rgba(220,229,243,.9); border-radius:27px;
            box-shadow:0 28px 85px rgba(25,51,91,.16);
        }
        .login-visual {
            position:relative; isolation:isolate; overflow:hidden; padding:52px 48px;
            display:flex; flex-direction:column; justify-content:space-between; color:#fff;
            background:linear-gradient(145deg,#102344 0%,#173d75 58%,#2869c8 100%);
        }
        .login-visual:before,.login-visual:after { content:""; position:absolute; z-index:-1; border:1px solid rgba(255,255,255,.12); border-radius:50%; }
        .login-visual:before { width:440px;height:440px;right:-190px;top:-155px;box-shadow:0 0 0 34px rgba(255,255,255,.025),0 0 0 72px rgba(255,255,255,.02); }
        .login-visual:after { width:330px;height:330px;left:-180px;bottom:-160px;box-shadow:0 0 0 28px rgba(255,255,255,.025); }
        .brand { display:flex; align-items:center; gap:13px; position:relative; z-index:1; }
        .brand-mark { width:48px;height:48px;display:grid;place-items:center;border-radius:15px;background:linear-gradient(145deg,#69a4ff,#3675e0);box-shadow:0 10px 25px rgba(0,0,0,.18);font-size:21px; }
        .brand-name { font-size:14px;font-weight:800;letter-spacing:.35px;line-height:1.35; }
        .brand-caption { display:block;color:#b8cbea;font-size:10px;font-weight:600;letter-spacing:1.15px;text-transform:uppercase;margin-top:3px; }
        .visual-copy { position:relative; z-index:1; max-width:440px; margin:48px 0; }
        .visual-kicker { display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(255,255,255,.2);border-radius:99px;background:rgba(255,255,255,.08);color:#dce9ff;font-size:11px;font-weight:700;letter-spacing:.6px; }
        .visual-copy h1 { color:#fff;font-size:36px;line-height:1.18;font-weight:800;letter-spacing:-1.1px;margin:22px 0 15px; }
        .visual-copy p { max-width:390px;color:#c5d5ee;font-size:14px;line-height:1.8;margin:0; }
        .visual-points { display:grid;gap:13px;margin-top:32px; }
        .visual-point { display:flex;align-items:center;gap:12px;color:#e3edff;font-size:12px;font-weight:600; }
        .visual-point i { width:28px;height:28px;display:grid;place-items:center;border-radius:9px;background:rgba(255,255,255,.12);color:#a9ccff; }
        .visual-footer { color:#a9bfdf;font-size:11px;position:relative;z-index:1; }
        .login-panel { padding:58px 58px 42px;display:flex;flex-direction:column;justify-content:center; }
        .mobile-brand { display:none; }
        .form-heading { margin-bottom:31px; }
        .form-heading .eyebrow { color:#3974ce;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px; }
        .form-heading h2 { color:#172b49;font-size:28px;font-weight:800;letter-spacing:-.7px;margin:0 0 8px; }
        .form-heading p { color:var(--muted);font-size:13px;margin:0;line-height:1.6; }
        .form-group { margin-bottom:20px; }
        .form-group label { color:#344762;font-size:12px;font-weight:750;margin-bottom:9px; }
        .field-wrap { position:relative; }
        .field-icon { position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#8a9bb4;font-size:14px;pointer-events:none; }
        .login-input { height:51px;border:1px solid #dfe6f0;border-radius:12px;padding:10px 45px 10px 43px;color:#243650;font-size:13px;box-shadow:none!important;transition:border-color .18s,box-shadow .18s;background:#fbfcfe; }
        .login-input:focus { border-color:#6c9de9;background:#fff;box-shadow:0 0 0 4px rgba(43,107,215,.1)!important; }
        .login-input::placeholder { color:#a5b0c0; }
        .toggle-password { position:absolute;right:8px;top:50%;transform:translateY(-50%);width:35px;height:35px;border:0;border-radius:9px;background:transparent;color:#8190a6;cursor:pointer; }
        .toggle-password:hover { background:#edf3fc;color:#2868d7; }
        .btn-login { height:50px;border:0;border-radius:12px;color:#fff;font-size:13px;font-weight:800;letter-spacing:.2px;background:linear-gradient(100deg,#347ce8,#2258bd);box-shadow:0 10px 22px rgba(39,100,205,.22);transition:transform .18s,box-shadow .18s; }
        .btn-login:hover { color:#fff;transform:translateY(-1px);box-shadow:0 13px 26px rgba(39,100,205,.28); }
        .btn-login:focus { color:#fff;outline:3px solid rgba(52,124,232,.22);outline-offset:2px; }
        .secure-note { display:flex;justify-content:center;align-items:center;gap:7px;color:#8a98aa;font-size:10px;margin-top:21px; }
        .secure-note i { color:#48a17b; }
        .login-error { border:0;border-radius:11px;background:#fff0f0;color:#a82d38;font-size:12px;padding:12px 14px;margin-bottom:20px; }
        .login-copyright { color:#a0aaba;font-size:10px;text-align:center;margin-top:34px; }
        @media(max-width:850px) {
            body { padding:18px; }
            .login-shell { max-width:540px;min-height:0;grid-template-columns:1fr; }
            .login-visual { padding:26px 30px 24px;min-height:205px; }
            .visual-copy { margin:25px 0 0; }
            .visual-copy h1 { font-size:25px;margin:13px 0 8px; }
            .visual-copy p,.visual-points,.visual-footer { display:none; }
            .visual-kicker { font-size:9px;padding:6px 10px; }
            .login-panel { padding:38px 38px 28px; }
            .login-copyright { margin-top:25px; }
        }
        @media(max-width:480px) {
            body { padding:12px; }
            .login-shell { border-radius:20px; }
            .login-visual { min-height:180px;padding:22px 22px; }
            .brand-mark { width:42px;height:42px;border-radius:13px; }
            .login-panel { padding:31px 23px 24px; }
            .form-heading h2 { font-size:25px; }
        }
        @media(prefers-reduced-motion:reduce) { *,*:before,*:after { scroll-behavior:auto!important;transition:none!important; } }
    </style>
</head>
<body>
<main class="login-shell" aria-label="Halaman masuk sistem">
    <section class="login-visual" aria-label="Informasi aplikasi">
        <div class="brand">
            <div class="brand-mark"><i class="fas fa-graduation-cap" aria-hidden="true"></i></div>
            <div class="brand-name">SISTEM INFORMASI<span class="brand-caption">PELANGGARAN SISWA</span></div>
        </div>
        <div class="visual-copy">
            <span class="visual-kicker"><i class="fas fa-shield-alt" aria-hidden="true"></i> PORTAL ADMINISTRASI SEKOLAH</span>
            <h1>Kelola kedisiplinan<br>lebih mudah.</h1>
            <p>Platform administrasi untuk membantu pencatatan, pemantauan, dan tindak lanjut kedisiplinan siswa secara terstruktur.</p>
            <div class="visual-points">
                <div class="visual-point"><i class="fas fa-chart-line" aria-hidden="true"></i> Pemantauan data dan rekap pelanggaran</div>
                <div class="visual-point"><i class="fas fa-clipboard-check" aria-hidden="true"></i> Pencatatan dan tindak lanjut terpusat</div>
                <div class="visual-point"><i class="fas fa-users" aria-hidden="true"></i> Akses sesuai peran pengguna</div>
            </div>
        </div>
        <div class="visual-footer">Lingkungan sekolah yang tertib dimulai dari data yang terkelola.</div>
    </section>

    <section class="login-panel">
        <div class="form-heading">
            <div class="eyebrow">Selamat datang kembali</div>
            <h2>Masuk ke akun Anda</h2>
            <p>Gunakan email dan password yang telah terdaftar untuk melanjutkan.</p>
        </div>

        <?php if ($pesan_error !== ''): ?>
            <div class="login-error" role="alert">
                <i class="fas fa-exclamation-circle mr-2" aria-hidden="true"></i><?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="form-group">
                <label for="email">Alamat email</label>
                <div class="field-wrap">
                    <i class="far fa-envelope field-icon" aria-hidden="true"></i>
                    <input type="email" id="email" name="email" class="form-control login-input"
                           value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="nama@sekolah.sch.id" maxlength="190"
                           autocomplete="username" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="field-wrap">
                    <i class="fas fa-lock field-icon" aria-hidden="true"></i>
                    <input type="password" id="password" name="password" class="form-control login-input"
                           placeholder="Masukkan password" autocomplete="current-password" required>
                    <button class="toggle-password" type="button" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false">
                        <i class="far fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-login btn-block mt-4">
                Masuk ke Dashboard <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i>
            </button>
        </form>
        <div class="secure-note"><i class="fas fa-lock" aria-hidden="true"></i> Akses aman sesuai akun dan hak pengguna</div>
        <div class="login-copyright">&copy; <?= date('Y') ?> SISWA TERTIB · Sistem Informasi Pelanggaran Siswa</div>
    </section>
</main>
<script>
(function () {
    var button = document.getElementById('togglePassword');
    var input = document.getElementById('password');
    if (!button || !input) return;
    button.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.setAttribute('aria-pressed', show ? 'true' : 'false');
        button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        button.innerHTML = '<i class="' + (show ? 'far fa-eye-slash' : 'far fa-eye') + '" aria-hidden="true"></i>';
    });
})();
</script>
</body>
</html>
