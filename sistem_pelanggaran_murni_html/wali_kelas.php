<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah / Edit Wali Kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $guru_id = intval($_POST['guru_id'] ?? 0);

    if ($kelas_id > 0 && $guru_id > 0) {
        if ($action === 'tambah') {
            // Cek apakah kelas sudah punya wali
            $check = $koneksi->query("SELECT id FROM t_wali_kelas WHERE kelas_id = $kelas_id");
            if ($check && $check->num_rows > 0) {
                $message = "Kelas ini sudah memiliki wali kelas!";
            } else {
                $stmt = $koneksi->prepare("INSERT INTO t_wali_kelas (kelas_id, guru_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $kelas_id, $guru_id);
                if ($stmt->execute()) {
                    $message = "Wali kelas berhasil ditambahkan.";
                } else {
                    $message = "Gagal menambahkan wali kelas: " . $stmt->error;
                }
                $stmt->close();
            }
        } elseif ($action === 'edit' && !empty($id)) {
            $stmt = $koneksi->prepare("UPDATE t_wali_kelas SET kelas_id=?, guru_id=? WHERE id=?");
            $stmt->bind_param("iii", $kelas_id, $guru_id, $id);
            if ($stmt->execute()) {
                $message = "Wali kelas berhasil diperbarui.";
            } else {
                $message = "Gagal memperbarui wali kelas: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "Silakan pilih kelas dan guru dengan benar!";
    }
}

// Handling Hapus Wali Kelas
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_wali_kelas WHERE id=?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        $message = "Wali kelas berhasil dihapus.";
    } else {
        $message = "Gagal menghapus wali kelas: " . $stmt->error;
    }
    $stmt->close();
}

// Handling Mode Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $stmt = $koneksi->prepare("SELECT * FROM t_wali_kelas WHERE id=?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch Master Kelas & Master Guru untuk Option Dropdown
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");
$list_guru = $koneksi->query("SELECT * FROM t_guru WHERE status_aktif = 1 ORDER BY nama ASC");

// Fetch Data List Wali Kelas dengan JOIN t_kelas dan t_guru
$sql_wali = "SELECT w.id, k.nama AS nama_kelas, g.nip, g.nama AS nama_guru 
            FROM t_wali_kelas w
            JOIN t_kelas k ON w.kelas_id = k.id
            JOIN t_guru g ON w.guru_id = g.id
            ORDER BY k.nama ASC";
$result_wali = $koneksi->query($sql_wali);

include 'header.php';
?>

<h2>Kelola Wali Kelas</h2>
<p>Atur guru yang menjadi wali untuk setiap kelas aktif. Satu kelas memiliki satu wali kelas.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<!-- Form Tambah / Edit Wali Kelas -->
<h3><?= $edit_data ? 'Edit Wali Kelas' : 'Tambah Wali Kelas Baru'; ?></h3>
<form method="POST" action="wali_kelas.php">
    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah'; ?>">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
    <?php endif; ?>

    <table border="0" cellpadding="5">
        <tr>
            <td><label for="kelas_id">Kelas</label></td>
            <td>: 
                <select id="kelas_id" name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php if ($list_kelas): ?>
                        <?php while ($k = $list_kelas->fetch_assoc()): ?>
                            <option value="<?= $k['id']; ?>" <?= (isset($edit_data) && $edit_data['kelas_id'] == $k['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="guru_id">Nama Wali Kelas (Guru)</label></td>
            <td>: 
                <select id="guru_id" name="guru_id" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php if ($list_guru): ?>
                        <?php while ($g = $list_guru->fetch_assoc()): ?>
                            <option value="<?= $g['id']; ?>" <?= (isset($edit_data) && $edit_data['guru_id'] == $g['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($g['nama']); ?> (NIP: <?= htmlspecialchars($g['nip']); ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit"><?= $edit_data ? 'Simpan Perubahan' : 'Simpan Wali Kelas'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="wali_kelas.php">Batal Edit</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<hr>

<!-- Tabel Daftar Wali Kelas -->
<h3>Daftar Wali Kelas</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Kelas</th>
            <th>Nama Wali Kelas</th>
            <th>NIP</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_wali && $result_wali->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result_wali->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                    <td><?= htmlspecialchars($row['nip']); ?></td>
                    <td>
                        <a href="wali_kelas.php?edit=<?= $row['id']; ?>">Edit</a> | 
                        <a href="wali_kelas.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus wali kelas ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Belum ada data wali kelas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>