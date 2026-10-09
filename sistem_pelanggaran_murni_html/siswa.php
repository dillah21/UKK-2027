<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah / Edit Siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? 'L';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'tambah') {
        $stmt = $koneksi->prepare("INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $nis, $nisn, $nama, $jenis_kelamin, $tanggal_lahir, $alamat, $status_aktif);
        if ($stmt->execute()) {
            $message = "Data siswa berhasil ditambahkan.";
        } else {
            $message = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'edit' && !empty($id)) {
        $stmt = $koneksi->prepare("UPDATE t_siswa SET nis=?, nisn=?, nama=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, status_aktif=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssssii", $nis, $nisn, $nama, $jenis_kelamin, $tanggal_lahir, $alamat, $status_aktif, $id);
        if ($stmt->execute()) {
            $message = "Data siswa berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handling Hapus Siswa
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_siswa WHERE id=?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        $message = "Data siswa berhasil dihapus.";
    } else {
        $message = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
}

// Handling Mode Edit (Ambil Data Siswa yang Akan Diedit)
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $stmt = $koneksi->prepare("SELECT * FROM t_siswa WHERE id=?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handling Pencarian & Fetch Data
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $param = "%" . $search . "%";
    $stmt = $koneksi->prepare("SELECT * FROM t_siswa WHERE nama LIKE ? OR nis LIKE ? OR nisn LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("SELECT * FROM t_siswa ORDER BY id DESC");
}

include 'header.php';
?>

<h2>Kelola Data Siswa</h2>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<!-- Form Tambah / Edit Siswa -->
<h3><?= $edit_data ? 'Edit Data Siswa' : 'Tambah Siswa Baru'; ?></h3>
<form method="POST" action="siswa.php">
    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah'; ?>">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
    <?php endif; ?>

    <table border="0" cellpadding="5">
        <tr>
            <td><label for="nis">NIS</label></td>
            <td>: <input type="text" id="nis" name="nis" value="<?= htmlspecialchars($edit_data['nis'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="nisn">NISN</label></td>
            <td>: <input type="text" id="nisn" name="nisn" value="<?= htmlspecialchars($edit_data['nisn'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="nama">Nama Siswa</label></td>
            <td>: <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="jenis_kelamin">Jenis Kelamin</label></td>
            <td>: 
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="L" <?= (($edit_data['jenis_kelamin'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?= (($edit_data['jenis_kelamin'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="tanggal_lahir">Tanggal Lahir</label></td>
            <td>: <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= $edit_data['tanggal_lahir'] ?? ''; ?>" required></td>
        </tr>
        <tr>
            <td><label for="alamat">Alamat</label></td>
            <td>: <textarea id="alamat" name="alamat" rows="3" cols="30" required><?= htmlspecialchars($edit_data['alamat'] ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <td><label for="status_aktif">Status Aktif</label></td>
            <td>: <input type="checkbox" id="status_aktif" name="status_aktif" <?= (isset($edit_data) ? ($edit_data['status_aktif'] ? 'checked' : '') : 'checked'); ?>> Aktif</td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Siswa'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="siswa.php">Batal Edit</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<hr>

<!-- Form Pencarian -->
<h3>Daftar Siswa</h3>
<form method="GET" action="siswa.php">
    <label for="search">Cari Siswa:</label>
    <input type="text" id="search" name="search" placeholder="Nama, NIS, atau NISN..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
    <?php if (!empty($search)): ?>
        <a href="siswa.php">Reset</a>
    <?php endif; ?>
</form>
<br>

<!-- Tabel Data Siswa murni HTML -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>JK</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nis']); ?></td>
                    <td><?= htmlspecialchars($row['nisn']); ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                    <td><?= htmlspecialchars($row['tanggal_lahir']); ?></td>
                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                    <td><?= $row['status_aktif'] ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                        <a href="siswa.php?edit=<?= $row['id']; ?>">Edit</a> | 
                        <a href="siswa.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" align="center">Data siswa tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>