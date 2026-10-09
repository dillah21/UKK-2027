<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$user_id = $_SESSION['user_id']; // Mengambil ID dari akun yang sedang login

// Fetch Data User yang Sedang Login
$stmt = $koneksi->prepare("SELECT * FROM t_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handling Update Profil & Password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass_lama = $_POST['pass_lama'] ?? '';
    $pass_baru = $_POST['pass_baru'] ?? '';
    $konfirm_pass = $_POST['konfirm_pass'] ?? '';

    if (!empty($nama) && !empty($email)) {
        if (!empty($pass_lama) || !empty($pass_baru)) {
            // Cek password lama (dukung hash bcrypt maupun plain text)
            $is_valid = ($pass_lama === $current_user['password']) || password_verify($pass_lama, $current_user['password']);
            
            if ($is_valid) {
                if ($pass_baru === $konfirm_pass && strlen($pass_baru) >= 4) {
                    $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);
                    $update_stmt = $koneksi->prepare("UPDATE t_users SET name=?, email=?, password=? WHERE id=?");
                    $update_stmt->bind_param("sssi", $nama, $email, $hash_baru, $user_id);
                    if ($update_stmt->execute()) {
                        $message = "Profil dan kata sandi berhasil diperbarui!";
                        $_SESSION['nama'] = $nama;
                    } else {
                        $message = "Gagal memperbarui profil: " . $update_stmt->error;
                    }
                    $update_stmt->close();
                } else {
                    $message = "Konfirmasi kata sandi baru tidak cocok atau kurang dari 4 karakter!";
                }
            } else {
                $message = "Kata sandi lama salah!";
            }
        } else {
            // Hanya update nama dan email
            $update_stmt = $koneksi->prepare("UPDATE t_users SET name=?, email=? WHERE id=?");
            $update_stmt->bind_param("ssi", $nama, $email, $user_id);
            if ($update_stmt->execute()) {
                $message = "Data profil berhasil diperbarui!";
                $_SESSION['nama'] = $nama;
            } else {
                $message = "Gagal memperbarui profil: " . $update_stmt->error;
            }
            $update_stmt->close();
        }
    } else {
        $message = "Nama dan email wajib diisi!";
    }

    // Refresh Data User Setelah Update
    $stmt = $koneksi->prepare("SELECT * FROM t_users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $current_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include 'header.php';
?>

<h2>Profil Saya</h2>
<p>Informasi akun yang sedang aktif digunakan saat ini.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<form method="POST" action="profil.php">
    <table border="0" cellpadding="5">
        <tr>
            <td>ID Pengguna</td>
            <td>: <b>#<?= $current_user['id']; ?></b></td>
        </tr>
        <tr>
            <td><label for="name">Nama Lengkap</label></td>
            <td>: <input type="text" id="name" name="name" value="<?= htmlspecialchars($current_user['name'] ?? ''); ?>" required size="30"></td>
        </tr>
        <tr>
            <td><label for="email">Email</label></td>
            <td>: <input type="email" id="email" name="email" value="<?= htmlspecialchars($current_user['email'] ?? ''); ?>" required size="30"></td>
        </tr>
        <tr>
            <td>Role / Hak Akses</td>
            <td>: <b><?= htmlspecialchars(strtoupper($current_user['role'] ?? 'USER')); ?></b></td>
        </tr>
        <tr>
            <td colspan="2"><br><b>Ganti Kata Sandi (Kosongkan jika tidak ingin mengubah)</b></td>
        </tr>
        <tr>
            <td><label for="pass_lama">Kata Sandi Lama</label></td>
            <td>: <input type="password" id="pass_lama" name="pass_lama" placeholder="Masukkan kata sandi lama"></td>
        </tr>
        <tr>
            <td><label for="pass_baru">Kata Sandi Baru</label></td>
            <td>: <input type="password" id="pass_baru" name="pass_baru" placeholder="Minimal 4 karakter"></td>
        </tr>
        <tr>
            <td><label for="konfirm_pass">Konfirmasi Kata Sandi Baru</label></td>
            <td>: <input type="password" id="konfirm_pass" name="konfirm_pass" placeholder="Ulangi kata sandi baru"></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit">Simpan Perubahan</button></td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>