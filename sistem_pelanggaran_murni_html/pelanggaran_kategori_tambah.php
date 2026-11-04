<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'auth.php';
require_once 'koneksi.php';

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: pelanggaran_kategori.php?error=akses_ditolak");
    exit();
}

$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);
    if ($nama === '') {
        $pesan_error = 'Nama kategori wajib diisi!';
    } else {
        $stmt = $koneksi->prepare("INSERT INTO t_pelanggaran_kategori (nama, deskripsi, status_aktif) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $deskripsi, $status_aktif);
        if ($stmt->execute()) {
            header("Location: pelanggaran_kategori.php?status=sukses_tambah");
            exit();
        }
        $pesan_error = "Gagal menyimpan kategori: " . $stmt->error;
        $stmt->close();
    }
}
include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus-circle mr-2"></i>Tambah Kategori Pelanggaran</h1>
    <a href="pelanggaran_kategori.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger"><?= htmlspecialchars($pesan_error); ?></div><?php endif; ?>
<div class="card shadow-sm border-0 mb-4" style="max-width:700px;">
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" maxlength="100" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status_aktif" class="form-control">
                    <option value="1" selected>Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan Kategori</button>
            <a href="pelanggaran_kategori.php" class="btn btn-light border">Batal</a>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
