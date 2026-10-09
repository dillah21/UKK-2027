<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah / Edit Kategori Pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'tambah') {
        $stmt = $koneksi->prepare("INSERT INTO t_pelanggaran_kategori (nama, deskripsi, status_aktif) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $deskripsi, $status_aktif);
        if ($stmt->execute()) {
            $message = "Kategori pelanggaran berhasil ditambahkan.";
        } else {
            $message = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'edit' && !empty($id)) {
        $stmt = $koneksi->prepare("UPDATE t_pelanggaran_kategori SET nama=?, deskripsi=?, status_aktif=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssii", $nama, $deskripsi, $status_aktif, $id);
        if ($stmt->execute()) {
            $message = "Kategori pelanggaran berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handling Hapus Kategori Pelanggaran
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_pelanggaran_kategori WHERE id=?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        $message = "Kategori pelanggaran berhasil dihapus.";
    } else {
        $message = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
}

// Handling Mode Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $stmt = $koneksi->prepare("SELECT * FROM t_pelanggaran_kategori WHERE id=?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handling Pencarian & Fetch Data
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $param = "%" . $search . "%";
    $stmt = $koneksi->prepare("SELECT * FROM t_pelanggaran_kategori WHERE nama LIKE ? OR deskripsi LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
}

include 'header.php';
?>

<h2>Kelola Pelanggaran Kategori</h2>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<!-- Form Tambah / Edit Kategori -->
<h3><?= $edit_data ? 'Edit Pelanggaran Kategori' : 'Tambah Pelanggaran Kategori Baru'; ?></h3>
<form method="POST" action="pelanggaran_kategori.php">
    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah'; ?>">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
    <?php endif; ?>

    <table border="0" cellpadding="5">
        <tr>
            <td><label for="nama">Nama Kategori</label></td>
            <td>: <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <td><label for="deskripsi">Deskripsi</label></td>
            <td>: <textarea id="deskripsi" name="deskripsi" rows="3" cols="30" required><?= htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <td><label for="status_aktif">Status Aktif</label></td>
            <td>: <input type="checkbox" id="status_aktif" name="status_aktif" <?= (isset($edit_data) ? ($edit_data['status_aktif'] ? 'checked' : '') : 'checked'); ?>> Aktif</td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Kategori'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="pelanggaran_kategori.php">Batal Edit</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<hr>

<!-- Form Pencarian -->
<h3>Daftar Pelanggaran Kategori</h3>
<form method="GET" action="pelanggaran_kategori.php">
    <label for="search">Cari Kategori:</label>
    <input type="text" id="search" name="search" placeholder="Nama atau deskripsi..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
    <?php if (!empty($search)): ?>
        <a href="pelanggaran_kategori.php">Reset</a>
    <?php endif; ?>
</form>
<br>

<!-- Tabel Data Kategori Pelanggaran -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                    <td><?= $row['status_aktif'] ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                        <a href="pelanggaran_kategori.php?edit=<?= $row['id']; ?>">Edit</a> | 
                        <a href="pelanggaran_kategori.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Data pelanggaran kategori tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>