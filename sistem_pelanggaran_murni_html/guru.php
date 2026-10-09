<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah / Edit Guru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'tambah') {
        $stmt = $koneksi->prepare("INSERT INTO t_guru (nip, nama, email, no_hp, status_aktif) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nip, $nama, $email, $no_hp, $status_aktif);
        if ($stmt->execute()) {
            $message = "Data guru berhasil ditambahkan.";
        } else {
            $message = "Gagal menambahkan data guru: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'edit' && !empty($id)) {
        $stmt = $koneksi->prepare("UPDATE t_guru SET nip=?, nama=?, email=?, no_hp=?, status_aktif=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssii", $nip, $nama, $email, $no_hp, $status_aktif, $id);
        if ($stmt->execute()) {
            $message = "Data guru berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data guru: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handling Hapus Guru
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_guru WHERE id=?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        $message = "Data guru berhasil dihapus.";
    } else {
        $message = "Gagal menghapus data guru: " . $stmt->error;
    }
    $stmt->close();
}

// Handling Mode Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $stmt = $koneksi->prepare("SELECT * FROM t_guru WHERE id=?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handling Pencarian & Fetch Data
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $param = "%" . $search . "%";
    $stmt = $koneksi->prepare("SELECT * FROM t_guru WHERE nama LIKE ? OR nip LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("SELECT * FROM t_guru ORDER BY id DESC");
}

include 'header.php';
?>

<h2>Kelola Data Guru</h2>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<!-- Form Tambah / Edit Guru -->
<h3><?= $edit_data ? 'Edit Data Guru' : 'Tambah Guru Baru'; ?></h3>
<form method="POST" action="guru.php">
    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah'; ?>">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
    <?php endif; ?>

    <table border="0" cellpadding="5">
        <tr>
            <td><label for="nip">NIP</label></td>
            <td>: <input type="text" id="nip" name="nip" value="<?= htmlspecialchars($edit_data['nip'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="nama">Nama Guru</label></td>
            <td>: <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="email">Email</label></td>
            <td>: <input type="email" id="email" name="email" value="<?= htmlspecialchars($edit_data['email'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <td><label for="no_hp">No. HP</label></td>
            <td>: <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($edit_data['no_hp'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <td><label for="status_aktif">Status Aktif</label></td>
            <td>: <input type="checkbox" id="status_aktif" name="status_aktif" <?= (isset($edit_data) ? ($edit_data['status_aktif'] ? 'checked' : '') : 'checked'); ?>> Aktif</td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Guru'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="guru.php">Batal Edit</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<hr>

<!-- Form Pencarian -->
<h3>Daftar Data Guru</h3>
<form method="GET" action="guru.php">
    <label for="search">Cari Guru:</label>
    <input type="text" id="search" name="search" placeholder="Nama, NIP, atau Email..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
    <?php if (!empty($search)): ?>
        <a href="guru.php">Reset</a>
    <?php endif; ?>
</form>
<br>

<!-- Tabel Data Guru -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama Guru</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nip']); ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['no_hp'] ?? '-'); ?></td>
                    <td><?= $row['status_aktif'] ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                        <a href="guru.php?edit=<?= $row['id']; ?>">Edit</a> | 
                        <a href="guru.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data guru ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" align="center">Data guru tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>