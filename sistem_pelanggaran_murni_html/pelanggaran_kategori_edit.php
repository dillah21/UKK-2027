<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'auth.php';
require_once 'koneksi.php';

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: pelanggaran_kategori.php?error=akses_ditolak");
    exit();
}
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: pelanggaran_kategori.php"); exit(); }

$stmt = $koneksi->prepare("SELECT id, nama, deskripsi, status_aktif FROM t_pelanggaran_kategori WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) { header("Location: pelanggaran_kategori.php"); exit(); }

$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);
    if ($nama === '') {
        $pesan_error = 'Nama kategori wajib diisi!';
    } else {
        $up = $koneksi->prepare("UPDATE t_pelanggaran_kategori SET nama = ?, deskripsi = ?, status_aktif = ?, updated_at = NOW() WHERE id = ?");
        $up->bind_param("ssii", $nama, $deskripsi, $status_aktif, $id);
        if ($up->execute()) {
            header("Location: pelanggaran_kategori.php?status=sukses_edit");
            exit();
        }
        $pesan_error = "Gagal memperbarui kategori: " . $up->error;
        $up->close();
    }
    $data['nama'] = $nama;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}
include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit mr-2"></i>Edit Kategori Pelanggaran</h1>
    <a href="pelanggaran_kategori.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger"><?= htmlspecialchars($pesan_error); ?></div><?php endif; ?>
<div class="card shadow-sm border-0 mb-4" style="max-width:700px;">
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" maxlength="100" required value="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status_aktif" class="form-control">
                    <option value="1" <?= (int)$data['status_aktif'] === 1 ? 'selected' : ''; ?>>Aktif</option>
                    <option value="0" <?= (int)$data['status_aktif'] === 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save mr-1"></i>Perbarui Kategori</button>
            <a href="pelanggaran_kategori.php" class="btn btn-light border">Batal</a>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
