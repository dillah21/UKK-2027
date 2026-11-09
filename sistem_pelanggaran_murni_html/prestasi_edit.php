<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'auth.php';
require_once 'koneksi.php';

function pe_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

// Dukung dua variasi primary key yang dipakai pada database lama.
$kolom = $koneksi->query("SHOW COLUMNS FROM prestasi");
$pk = 'id';
while ($c = $kolom->fetch_assoc()) {
    if ($c['Field'] === 'id_prestasi') { $pk = 'id_prestasi'; break; }
}
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) { header('Location: prestasi.php'); exit; }

$stmt = $koneksi->prepare("SELECT * FROM prestasi WHERE `$pk` = ? LIMIT 1");
$stmt->bind_param('i', $id); $stmt->execute();
$data = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$data) { header('Location: prestasi.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $nama = trim($_POST['nama_prestasi'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $ket = trim($_POST['keterangan'] ?? '');
    if ($id_siswa <= 0 || $nama === '' || $tingkat === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error = 'Mohon lengkapi seluruh data dengan benar.';
    } else {
        $up = $koneksi->prepare("UPDATE prestasi SET id_siswa=?, nama_prestasi=?, tingkat=?, tanggal=?, poin=?, keterangan=? WHERE `$pk`=?");
        $up->bind_param('isssisi', $id_siswa, $nama, $tingkat, $tanggal, $poin, $ket, $id);
        if ($up->execute()) { header('Location: prestasi.php?status=edit_sukses'); exit; }
        $error = 'Gagal memperbarui data: ' . $up->error; $up->close();
        $data = array_merge($data, ['id_siswa'=>$id_siswa,'nama_prestasi'=>$nama,'tingkat'=>$tingkat,'tanggal'=>$tanggal,'poin'=>$poin,'keterangan'=>$ket]);
    }
}
$siswa = $koneksi->query("SELECT id, nama FROM t_siswa ORDER BY nama ASC");
include 'header.php';
?>
<div class="container-fluid py-3">
 <div class="d-flex justify-content-between align-items-center mb-3"><h2>Edit Prestasi Siswa</h2><a href="prestasi.php" class="btn btn-secondary btn-sm">Kembali</a></div>
 <?php if ($error): ?><div class="alert alert-danger"><?= pe_h($error) ?></div><?php endif; ?>
 <div class="card shadow-sm"><div class="card-body">
  <form method="POST" action="prestasi_edit.php?id=<?= $id ?>">
   <input type="hidden" name="id" value="<?= $id ?>">
   <div class="form-group"><label>Siswa</label><select name="id_siswa" class="form-control" required><option value="">-- Pilih Siswa --</option><?php while($s=$siswa->fetch_assoc()): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$data['id_siswa']===(int)$s['id']?'selected':'' ?>><?= pe_h($s['nama']) ?></option><?php endwhile; ?></select></div>
   <div class="form-group"><label>Nama Prestasi / Kegiatan</label><input name="nama_prestasi" class="form-control" required value="<?= pe_h($data['nama_prestasi']) ?>"></div>
   <div class="form-group"><label>Tingkat Lomba</label><select name="tingkat" class="form-control" required><?php foreach(['Sekolah','Kabupaten/Kota','Provinsi','Nasional','Internasional'] as $t): ?><option value="<?= pe_h($t) ?>" <?= $data['tingkat']===$t?'selected':'' ?>><?= pe_h($t) ?></option><?php endforeach; ?></select></div>
   <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" required value="<?= pe_h($data['tanggal']) ?>"></div>
   <div class="form-group"><label>Poin Reward</label><input type="number" name="poin" class="form-control" required value="<?= (int)$data['poin'] ?>"></div>
   <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3"><?= pe_h($data['keterangan']) ?></textarea></div>
   <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button><a href="prestasi.php" class="btn btn-light border">Batal</a>
  </form>
 </div></div>
</div>
<?php include 'footer.php'; ?>
