<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Hanya Admin yang boleh menambah kategori
if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: pelanggaran_kategori.php?error=akses_ditolak");
    exit();
}

$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $poin = intval($_POST['poin'] ?? 10);
    $sanksi = trim($_POST['sanksi'] ?? '');

    if (!empty($nama_kategori)) {
        // Simpan ke database
        $query_insert = mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori (nama_kategori, poin, sanksi) VALUES ('$nama_kategori', '$poin', '$sanksi')");
        
        if (!$query_insert) {
            // Fallback jika struktur kolom di database hanya nama_kategori
            $query_insert = mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori (nama_kategori) VALUES ('$nama_kategori')");
        }

        if ($query_insert) {
            header("Location: pelanggaran_kategori.php?status=sukses_tambah");
            exit();
        } else {
            $pesan_error = "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
        }
    } else {
        $pesan_error = "Nama kategori tidak boleh kosong!";
    }
}

include 'header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus-circle mr-2"></i> Tambah Kategori Pelanggaran</h1>
    <a href="pelanggaran_kategori.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
</div>

<?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($pesan_error); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Kategori Pelanggaran <span class="text-danger">*</span></label>
                <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Kedisiplinan / Kehadiran" required>
            </div>
            <div class="form-group">
                <label>Bobot Poin</label>
                <input type="number" name="poin" class="form-control" value="10" min="1" required>
            </div>
            <div class="form-group">
                <label>Sanksi / Hukuman</label>
                <textarea name="sanksi" class="form-control" rows="3" placeholder="Contoh: Teguran lisan & pembersihan area sekolah"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Kategori</button>
            <a href="pelanggaran_kategori.php" class="btn btn-light border">Batal</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>