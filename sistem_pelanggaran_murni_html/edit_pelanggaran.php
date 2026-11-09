<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data pelanggaran berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data pelanggaran tidak ditemukan!'); window.location='pelanggaran.php';</script>";
    exit;
}

// Proses update saat form disubmit
if (isset($_POST['update'])) {
    $kode         = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori_id  = intval($_POST['pelanggaran_kategori_id']);
    $poin         = intval($_POST['poin']);
    $deskripsi    = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status_aktif = intval($_POST['status_aktif']);

    $update = mysqli_query($koneksi, "UPDATE t_pelanggaran SET 
        pelanggaran_kategori_id = '$kategori_id',
        kode = '$kode',
        nama = '$nama',
        poin = '$poin',
        deskripsi = '$deskripsi',
        status_aktif = '$status_aktif',
        updated_at = NOW()
        WHERE id = $id");

    if ($update) {
        echo "<script>alert('Jenis pelanggaran berhasil diupdate!'); window.location='pelanggaran.php';</script>";
        exit;
    } else {
        $pesan_error = "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
}

include 'header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Jenis Pelanggaran</h2>
        <a href="pelanggaran.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $pesan_error; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Kode Pelanggaran</label>
                    <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($data['kode']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Nama Pelanggaran</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Kategori Pelanggaran</label>
                    <select name="pelanggaran_kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        $kat_query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1");
                        while ($kat = mysqli_fetch_assoc($kat_query)) {
                            $selected = ($kat['id'] == $data['pelanggaran_kategori_id']) ? 'selected' : '';
                            echo '<option value="'.$kat['id'].'" '.$selected.'>'.htmlspecialchars($kat['nama']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Poin</label>
                    <input type="number" name="poin" class="form-control" value="<?= htmlspecialchars($data['poin']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Deskripsi / Sanksi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-muted">Status Aktif</label>
                    <select name="status_aktif" class="form-control" required>
                        <option value="1" <?= ($data['status_aktif'] == 1) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= ($data['status_aktif'] == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" name="update" class="btn btn-primary px-4">Simpan Perubahan</button>
                <a href="pelanggaran.php" class="btn btn-secondary px-4">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>