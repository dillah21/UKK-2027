<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah Pengguna Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'guru';

    $roles_valid = ['admin', 'petugas', 'guru', 'wali_kelas', 'siswa'];
    if ($nama !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $password !== ''
        && strlen($password) >= 8 && in_array($role, $roles_valid, true)) {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $remember_token = bin2hex(random_bytes(20));
        $stmt = $koneksi->prepare("INSERT INTO t_users (name, email, password, remember_token, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sssss", $nama, $email, $hash_password, $remember_token, $role);
        if ($stmt->execute()) {
            $message = "Pengguna baru berhasil ditambahkan.";
        } else {
            $message = "Gagal menambah pengguna (Email mungkin sudah terdaftar): " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Isi nama, email valid, password minimal 8 karakter, dan role yang tersedia.";
    }
}

// Handling Edit Pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id_edit = (int)($_POST['id'] ?? 0);
    $nama_edit = trim($_POST['name'] ?? '');
    $email_edit = trim($_POST['email'] ?? '');
    $role_edit = $_POST['role'] ?? '';
    $password_edit = $_POST['password'] ?? '';
    $roles_valid_edit = ['admin', 'petugas', 'guru', 'wali_kelas', 'siswa'];
    if ($id_edit > 0 && $nama_edit !== '' && filter_var($email_edit, FILTER_VALIDATE_EMAIL) && in_array($role_edit, $roles_valid_edit, true)) {
        if ($password_edit !== '' && strlen($password_edit) < 8) {
            $message = 'Password baru minimal 8 karakter.';
        } else {
            if ($password_edit !== '') {
                $hash = password_hash($password_edit, PASSWORD_DEFAULT);
                $up = $koneksi->prepare("UPDATE t_users SET name=?, email=?, role=?, password=?, updated_at=NOW() WHERE id=?");
                $up->bind_param("ssssi", $nama_edit, $email_edit, $role_edit, $hash, $id_edit);
            } else {
                $up = $koneksi->prepare("UPDATE t_users SET name=?, email=?, role=?, updated_at=NOW() WHERE id=?");
                $up->bind_param("sssi", $nama_edit, $email_edit, $role_edit, $id_edit);
            }
            if ($up->execute()) { $message = 'Data pengguna berhasil diperbarui.'; unset($_GET['edit']); }
            else $message = 'Gagal memperbarui pengguna: ' . $up->error;
            $up->close();
        }
    } else $message = 'Data pengguna tidak valid.';
}

$edit_user_data = null;
if (isset($_GET['edit'])) {
    $id_form = (int)$_GET['edit'];
    $q = $koneksi->prepare("SELECT id, name, email, role FROM t_users WHERE id=?");
    $q->bind_param("i", $id_form); $q->execute();
    $edit_user_data = $q->get_result()->fetch_assoc(); $q->close();
}

// Handling Hapus User
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    if ($id_hapus !== ($_SESSION['user_id'] ?? 0)) {
        $stmt = $koneksi->prepare("DELETE FROM t_users WHERE id=?");
        $stmt->bind_param("i", $id_hapus);
        if ($stmt->execute()) {
            $message = "Pengguna berhasil dihapus.";
        } else {
            $message = "Gagal menghapus pengguna.";
        }
        $stmt->close();
    } else {
        $message = "Kamu tidak dapat menghapus akun yang sedang aktif digunakan!";
    }
}

// Fetch Daftar User
$result_users = $koneksi->query("SELECT id, name, email, role, created_at FROM t_users ORDER BY id ASC");

include 'header.php';
?>

<h2>Kelola Akun Pengguna / Petugas</h2>
<p>Daftar dan manajemen akun pengguna sistem.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<?php if ($edit_user_data): ?>
<div class="card shadow-sm mb-4"><div class="card-header"><strong>Edit Pengguna</strong></div><div class="card-body">
<form method="POST" action="users.php?edit=<?= (int)$edit_user_data['id'] ?>">
<input type="hidden" name="id" value="<?= (int)$edit_user_data['id'] ?>">
<div class="form-group"><label>Nama Lengkap</label><input class="form-control" name="name" required value="<?= htmlspecialchars($edit_user_data['name'], ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($edit_user_data['email'], ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="form-group"><label>Role / Akses</label><select class="form-control" name="role" required><?php foreach (['admin'=>'Admin','petugas'=>'Petugas BK','guru'=>'Guru','wali_kelas'=>'Wali Kelas','siswa'=>'Siswa'] as $rv=>$rl): ?><option value="<?= $rv ?>" <?= $edit_user_data['role']===$rv?'selected':'' ?>><?= $rl ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Password Baru (opsional)</label><input type="password" class="form-control" name="password" minlength="8" placeholder="Kosongkan jika tidak ingin mengganti password"></div>
<button type="submit" name="edit_user" class="btn btn-primary">Simpan Perubahan</button> <a href="users.php" class="btn btn-secondary">Batal</a>
</form></div></div>
<?php endif; ?>

<!-- Form Tambah User -->
<h3>Tambah Pengguna Baru</h3>
<form method="POST" action="users.php">
    <table border="0" cellpadding="5">
        <tr>
            <td><label for="name">Nama Lengkap</label></td>
            <td>: <input type="text" id="name" name="name" placeholder="Contoh: Pengguna 26" required size="30"></td>
        </tr>
        <tr>
            <td><label for="email">Email</label></td>
            <td>: <input type="email" id="email" name="email" placeholder="user@sekolah.sch.id" required size="30"></td>
        </tr>
        <tr>
            <td><label for="password">Password</label></td>
            <td>: <input type="password" id="password" name="password" placeholder="Password" required></td>
        </tr>
        <tr>
            <td><label for="role">Role / Akses</label></td>
            <td>: 
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas BK</option>
                    <option value="guru">Guru</option>
                    <option value="wali_kelas">Wali Kelas</option>
                    <option value="siswa">Siswa</option>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit">Tambah Pengguna</button></td>
        </tr>
    </table>
</form>

<hr>

<!-- Tabel Daftar User -->
<h3>Daftar Pengguna System</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_users && $result_users->num_rows > 0): ?>
            <?php while ($row = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><b><?= htmlspecialchars($row['role']); ?></b></td>
                    <td>
                        <a href="users.php?edit=<?= (int)$row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <?php if ((int)$row['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                            <a href="users.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        <?php else: ?>
                            <i>(Akun Aktif)</i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Belum ada data pengguna.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>