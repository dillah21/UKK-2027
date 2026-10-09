<?php
include 'koneksi.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan' || $aksi === 'ubah') {
        $nama = trim($_POST['nama'] ?? '');
        $mulai = $_POST['tanggal_mulai'] ?? '';
        $selesai = $_POST['tanggal_selesai'] ?? '';
        if ($nama === '' || $mulai === '' || $selesai === '') {
            $error = 'Semua kolom wajib diisi.';
        } elseif ($mulai > $selesai) {
            $error = 'Tanggal mulai tidak boleh melewati tanggal selesai.';
        } else {
            if ($aksi === 'simpan') {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, 0)");
                mysqli_stmt_bind_param($stmt, 'sss', $nama, $mulai, $selesai);
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = mysqli_prepare($koneksi, "UPDATE t_tahun_ajaran SET nama=?, tanggal_mulai=?, tanggal_selesai=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sssi', $nama, $mulai, $selesai, $id);
            }
            if (mysqli_stmt_execute($stmt)) { header('Location: tahun_ajaran.php?pesan='.($aksi === 'simpan' ? 'tambah' : 'ubah')); exit; }
            $error = 'Gagal menyimpan tahun ajaran. Periksa apakah nama tahun ajaran sudah ada.';
        }
    } elseif ($aksi === 'aktifkan') {
        $id = (int)($_POST['id'] ?? 0);
        mysqli_begin_transaction($koneksi);
        $ok1 = mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET status_aktif=0");
        $stmt = mysqli_prepare($koneksi, "UPDATE t_tahun_ajaran SET status_aktif=1 WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok2 = mysqli_stmt_execute($stmt);
        if ($ok1 && $ok2) { mysqli_commit($koneksi); header('Location: tahun_ajaran.php?pesan=aktif'); exit; }
        mysqli_rollback($koneksi);
        $error = 'Gagal mengaktifkan tahun ajaran.';
    }
}
include 'header.php';
$edit = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt = mysqli_prepare($koneksi, "SELECT id, nama, tanggal_mulai, tanggal_selesai FROM t_tahun_ajaran WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id_edit);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
if (isset($_GET['pesan'])) {
    $pesan = ['tambah'=>'Tahun ajaran berhasil ditambahkan.','ubah'=>'Tahun ajaran berhasil diubah.','aktif'=>'Tahun ajaran aktif berhasil diperbarui.'];
    $success = $pesan[$_GET['pesan']] ?? '';
}
?>
<h3>Kelola Tahun Ajaran</h3>
<?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<form method="post">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'simpan' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
    <p><label>Nama Tahun Ajaran</label><br><input name="nama" required maxlength="30" placeholder="2026/2027" value="<?= htmlspecialchars($edit['nama'] ?? '') ?>"></p>
    <p><label>Tanggal Mulai</label><br><input type="date" name="tanggal_mulai" required value="<?= htmlspecialchars($edit['tanggal_mulai'] ?? '') ?>"></p>
    <p><label>Tanggal Selesai</label><br><input type="date" name="tanggal_selesai" required value="<?= htmlspecialchars($edit['tanggal_selesai'] ?? '') ?>"></p>
    <button type="submit"><?= $edit ? 'Simpan Perubahan' : 'Tambah Tahun Ajaran' ?></button>
    <?php if ($edit): ?> <a href="tahun_ajaran.php">Batal</a><?php endif; ?>
</form>
<h4>Daftar Tahun Ajaran</h4>
<table border="1" cellpadding="7" cellspacing="0" width="100%">
<thead><tr><th>No</th><th>Tahun Ajaran</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php
$result = mysqli_query($koneksi, "SELECT id, nama, tanggal_mulai, tanggal_selesai, status_aktif FROM t_tahun_ajaran ORDER BY tanggal_mulai DESC");
$no = 1;
if ($result && mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int)$row['id'];
        echo '<tr><td>'.$no++.'</td><td>'.htmlspecialchars($row['nama']).'</td><td>'.htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_mulai']))).'</td><td>'.htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_selesai']))).'</td><td>'.((int)$row['status_aktif'] ? 'Aktif' : 'Nonaktif').'</td><td>';
        echo '<a href="tahun_ajaran.php?edit='.$id.'">Edit</a> ';
        if (!(int)$row['status_aktif']) echo '<form method="post" style="display:inline" onsubmit="return confirm(\'Aktifkan tahun ajaran ini? Tahun ajaran aktif saat ini akan dinonaktifkan.\')"><input type="hidden" name="aksi" value="aktifkan"><input type="hidden" name="id" value="'.$id.'"><button type="submit">Jadikan Aktif</button></form>';
        else echo '<strong>Sedang Aktif</strong>';
        echo '</td></tr>';
    }
} else echo '<tr><td colspan="6" align="center">Belum ada data tahun ajaran.</td></tr>';
?>
</tbody></table>
<?php include 'footer.php'; ?>
