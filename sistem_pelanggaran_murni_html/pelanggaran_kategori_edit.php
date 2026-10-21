<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: pelanggaran_kategori.php?error=akses_ditolak");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: pelanggaran_kategori.php");
    exit();
}

$data = null;
$result_cek = @mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori LIMIT 1");
if ($result_cek) {
    $row_sample = mysqli_fetch_assoc($result_cek);
    if ($row_sample) {
        $kolom_id = '';
        if (array_key_exists('id_kategori', $row_sample)) {
            $kolom_id = 'id_kategori';
        } elseif (array_key_exists('id', $row_sample)) {
            $kolom_id = 'id';
        } else {
            $keys = array_keys($row_sample);
            $kolom_id = $keys[0];
        }

        $result = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE $kolom_id = $id LIMIT 1");
        if ($result) {
            $data = mysqli_fetch_assoc($result);
        }
    }
}

if (!$data) {
    header("Location: pelanggaran_kategori.php");
    exit();
}

$nama_kat = $data['nama_kategori'] ?? $data['kategori'] ?? $data['nama'] ?? '';
$poin = $data['poin'] ?? $data['poin_pelanggaran'] ?? $data['point'] ?? 10;
$hukuman = $data['sanksi'] ?? $data['hukuman'] ?? $data['deskripsi'] ?? $data['keterangan'] ?? '';

$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $poin_baru = intval($_POST['poin'] ?? 10);
    $sanksi_baru = trim($_POST['sanksi'] ?? '');

    if (!empty($nama_kategori)) {
        @$koneksi->query("UPDATE t_pelanggaran_kategori SET nama_kategori='$nama_kategori', poin=$poin_baru, sanksi='$sanksi_baru' WHERE id_kategori=$id");
        @$koneksi->query("UPDATE t_pelanggaran_kategori SET nama_kategori='$nama_kategori', poin=$poin_baru, sanksi='$sanksi_baru' WHERE id=$id");
        
        header("Location: pelanggaran_kategori.php?status=sukses_edit");
        exit();
    } else {
        $pesan_error = "Nama kategori tidak boleh kosong!";
    }
}

include 'header.php';
?>

<div class="content-wrapper" style="padding: 15px;">
    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-edit mr-2"></i> Edit Kategori Pelanggaran</h1>
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
                            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($nama_kat); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Bobot Poin</label>
                            <input type="number" name="poin" class="form-control" value="<?= htmlspecialchars($poin); ?>" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Sanksi / Hukuman</label>
                            <textarea name="sanksi" class="form-control" rows="3"><?= htmlspecialchars($hukuman); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning text-white font-weight-bold"><i class="fas fa-save mr-1"></i> Perbarui Kategori</button>
                        <a href="pelanggaran_kategori.php" class="btn btn-light border">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>