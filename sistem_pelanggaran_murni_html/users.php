<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah Pengguna Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'petugas';

    if (!empty($nama) && !empty($email) && !empty($password)) {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("INSERT INTO t_users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $hash_password, $role);
        if ($stmt->execute()) {
            $message = "Pengguna baru berhasil ditambahkan.";
        } else {
            $message = "Gagal menambah pengguna (Email mungkin sudah terdaftar): " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Semua kolom form wajib diisi!";
    }
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
                        <?php if ($row['id'] !== ($_SESSION['user_id'] ?? 0)): ?>
                            <a href="users.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
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