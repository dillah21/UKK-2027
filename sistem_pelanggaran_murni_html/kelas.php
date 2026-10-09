<?php
include 'koneksi.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    if ($aksi === 'simpan' || $aksi === 'ubah') {
        if ($nama === '') {
            $error = 'Nama kelas wajib diisi.';
        } elseif ($aksi === 'simpan') {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO t_kelas (nama, status_aktif) VALUES (?, 1)");
            mysqli_stmt_bind_param($stmt, 's', $nama);
            if (mysqli_stmt_execute($stmt)) { header('Location: kelas.php?pesan=tambah'); exit; }
            $error = 'Gagal menyimpan kelas. Pastikan nama kelas belum digunakan.';
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = mysqli_prepare($koneksi, "UPDATE t_kelas SET nama=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'si', $nama, $id);
            if (mysqli_stmt_execute($stmt)) { header('Location: kelas.php?pesan=ubah'); exit; }
            $error = 'Gagal mengubah kelas.';
        }
    } elseif ($aksi === 'status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = (int)($_POST['status_aktif'] ?? 0);
        $stmt = mysqli_prepare($koneksi, "UPDATE t_kelas SET status_aktif=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ii', $status, $id);
        if (mysqli_stmt_execute($stmt)) { header('Location: kelas.php?pesan=status'); exit; }
        $error = 'Gagal mengubah status kelas.';
    }
}
include 'header.php';
$edit = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt = mysqli_prepare($koneksi, "SELECT id, nama FROM t_kelas WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id_edit);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
if (isset($_GET['pesan'])) {
    $pesan = ['tambah'=>'Kelas berhasil ditambahkan.','ubah'=>'Kelas berhasil diubah.','status'=>'Status kelas berhasil diperbarui.'];
    $success = $pesan[$_GET['pesan']] ?? '';
}
?>
<h3>Kelola Kelas</h3>
<?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<form method="post">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'simpan' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
    <label>Nama Kelas</label><br>
    <input type="text" name="nama" maxlength="100" required value="<?= htmlspecialchars($edit['nama'] ?? '') ?>" placeholder="Contoh: X RPL 1">
    <button type="submit"><?= $edit ? 'Simpan Perubahan' : 'Tambah Kelas' ?></button>
    <?php if ($edit): ?> <a href="kelas.php">Batal</a><?php endif; ?>
</form>
<h4>Daftar Kelas</h4>
<table border="1" cellpadding="7" cellspacing="0" width="100%">
<thead><tr><th>No</th><th>Nama Kelas</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php
$result = mysqli_query($koneksi, "SELECT id, nama, status_aktif FROM t_kelas ORDER BY nama");
$no = 1;
if ($result && mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int)$row['id'];
        echo '<tr><td>'.$no++.'</td><td>'.htmlspecialchars($row['nama']).'</td><td>'.((int)$row['status_aktif'] ? 'Aktif' : 'Nonaktif').'</td><td>';
        echo '<a href="kelas.php?edit='.$id.'">Edit</a> ';
        echo '<form method="post" style="display:inline"><input type="hidden" name="aksi" value="status"><input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="status_aktif" value="'.((int)$row['status_aktif'] ? 0 : 1).'"><button type="submit">'.((int)$row['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan').'</button></form>';
        echo '</td></tr>';
    }
} else echo '<tr><td colspan="4" align="center">Belum ada data kelas.</td></tr>';
?>
</tbody></table>
<?php include 'footer.php'; ?>
