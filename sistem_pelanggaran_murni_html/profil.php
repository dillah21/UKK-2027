<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';
$error = '';

// Ambil data user
$stmt = $koneksi->prepare("SELECT name, email, password FROM t_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_lama = trim($_POST['password_lama'] ?? '');
    $password_baru = trim($_POST['password_baru'] ?? '');

    if (!empty($nama) && !empty($email)) {
        // Cek email ganda
        $stmt_cek = $koneksi->prepare("SELECT id FROM t_users WHERE email = ? AND id != ?");
        $stmt_cek->bind_param("si", $email, $user_id);
        $stmt_cek->execute();
        $res_cek = $stmt_cek->get_result();

        if ($res_cek->num_rows > 0) {
            $error = "Email sudah digunakan oleh pengguna lain!";
        } else {
            // Update nama dan email
            $stmt_up = $koneksi->prepare("UPDATE t_users SET name = ?, email = ? WHERE id = ?");
            $stmt_up->bind_param("ssi", $nama, $email, $user_id);
            $stmt_up->execute();
            $stmt_up->close();

            $_SESSION['nama'] = $nama;
            $user['name'] = $nama;
            $user['email'] = $email;
            $pesan = "Profil berhasil diperbarui!";

            // Jika mau ganti password sekalian
            if (!empty($password_baru)) {
                $db_pass = trim($user['password']);
                $is_valid = ($password_lama === $db_pass) || password_verify($password_lama, $db_pass);

                if ($is_valid) {
                    $stmt_pass = $koneksi->prepare("UPDATE t_users SET password = ? WHERE id = ?");
                    $stmt_pass->bind_param("si", $password_baru, $user_id);
                    $stmt_pass->execute();
                    $stmt_pass->close();
                    $pesan = "Profil dan Password berhasil diperbarui!";
                } else {
                    $error = "Profil diperbarui, tetapi Password Lama salah!";
                }
            }
        }
        $stmt_cek->close();
    } else {
        $error = "Nama dan Email wajib diisi!";
    }
}

include 'header.php';
?>

<h2>Profil Saya & Ganti Password</h2>
<p>Kelola informasi akun dan kata sandi Anda.</p>

<hr>

<?php if ($pesan): ?>
    <p style="color: green;"><b><?= htmlspecialchars($pesan); ?></b></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color: red;"><b><?= htmlspecialchars($error); ?></b></p>
<?php endif; ?>

<form method="POST" action="profil.php">
    <table border="0" cellpadding="8" cellspacing="0">
        <tr>
            <td align="right"><label for="nama"><b>Nama Lengkap</b></label></td>
            <td>:</td>
            <td><input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['name'] ?? ''); ?>" required size="30"></td>
        </tr>
        <tr>
            <td align="right"><label for="email"><b>Email Account</b></label></td>
            <td>:</td>
            <td><input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required size="30"></td>
        </tr>
        <tr>
            <td colspan="3"><hr><b>Ganti Password (Opsional)</b></td>
        </tr>
        <tr>
            <td align="right"><label for="password_lama"><b>Password Lama</b></label></td>
            <td>:</td>
            <td><input type="password" id="password_lama" name="password_lama" size="30"></td>
        </tr>
        <tr>
            <td align="right"><label for="password_baru"><b>Password Baru</b></label></td>
            <td>:</td>
            <td><input type="password" id="password_baru" name="password_baru" size="30"></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <br>
                <button type="submit" style="padding: 5px 15px; cursor: pointer;"><b>Simpan Perubahan</b></button>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>