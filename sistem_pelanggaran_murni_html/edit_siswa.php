<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_error = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: siswa.php");
    exit;
}

// 1. Proses Update Data Siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_siswa'])) {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $jk = trim($_POST['jk'] ?? 'L');

    if (!empty($nama)) {
        $stmt = $koneksi->prepare("UPDATE t_siswa SET nis = ?, nama = ?, jenis_kelamin = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nis, $nama, $jk, $id);
        
        if ($stmt->execute()) {
            $stmt->close();

            // Update atau Insert relasi kelas di t_kelas_siswa
            $cek_ks = $koneksi->query("SELECT id FROM t_kelas_siswa WHERE siswa_id = $id AND status_aktif = 1");
            if ($cek_ks && $cek_ks->num_rows > 0) {
                if ($kelas_id > 0) {
                    $stmt_up = $koneksi->prepare("UPDATE t_kelas_siswa SET kelas_id = ? WHERE siswa_id = ? AND status_aktif = 1");
                    $stmt_up->bind_param("ii", $kelas_id, $id);
                    $stmt_up->execute();
                    $stmt_up->close();
                } else {
                    $koneksi->query("UPDATE t_kelas_siswa SET status_aktif = 0 WHERE siswa_id = $id AND status_aktif = 1");
                }
            } else if ($kelas_id > 0) {
                $ta_q = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
                $ta_id = ($ta_q && $ta_q->num_rows > 0) ? $ta_q->fetch_assoc()['id'] : 1;
                $tgl_mulai = date('Y-m-d');
                $tgl_selesai = date('Y-m-d', strtotime('+1 year'));
                $status_aktif = 1;

                $stmt_ins = $koneksi->prepare("INSERT INTO t_kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_ins->bind_param("iiissii", $id, $ta_id, $kelas_id, $tgl_mulai, $tgl_selesai, $status_aktif);
                $stmt_ins->execute();
                $stmt_ins->close();
            }

            header("Location: siswa.php?status=sukses_update");
            exit;
        } else {
            $pesan_error = "Gagal memperbarui data: " . $koneksi->error;
        }
    } else {
        $pesan_error = "Nama siswa tidak boleh kosong!";
    }
}

// 2. Ambil Data Siswa yang Akan Diedit beserta Kelas Aktifnya
$query_siswa = $koneksi->query("
    SELECT s.*, ks.kelas_id 
    FROM t_siswa s 
    LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id AND ks.status_aktif = 1 
    WHERE s.id = $id LIMIT 1
");

if (!$query_siswa || $query_siswa->num_rows === 0) {
    header("Location: siswa.php");
    exit;
}
$row = $query_siswa->fetch_assoc();

// 3. Ambil Pilihan Kelas
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");

// Panggil Header Terpusat
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Siswa</h1>
            </div>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $pesan_error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- Card Form Edit dengan Ukuran Compact/Kecil -->
            <div class="card shadow-sm border-warning mb-4" style="max-width: 600px;">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Data Siswa</h6>
                    <a href="siswa.php" class="text-dark font-weight-bold" style="text-decoration: none; font-size: 1.1rem;">&times;</a>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="">
                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label font-weight-bold text-sm">NIS / No Induk</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" name="nis" value="<?= htmlspecialchars($row['nis'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label font-weight-bold text-sm">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" name="nama" required value="<?= htmlspecialchars($row['nama'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label font-weight-bold text-sm">Kelas</label>
                            <div class="col-sm-8">
                                <select class="form-control form-control-sm" name="kelas_id">
                                    <option value="0">-- Pilih Kelas --</option>
                                    <?php if ($list_kelas && $list_kelas->num_rows > 0): ?>
                                        <?php while ($kls = $list_kelas->fetch_assoc()): ?>
                                            <option value="<?= $kls['id']; ?>" <?= ($row['kelas_id'] == $kls['id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($kls['nama']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold text-sm">Jenis Kelamin</label>
                            <div class="col-sm-8">
                                <select class="form-control form-control-sm" name="jk">
                                    <option value="L" <?= ($row['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($row['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-sm-8 offset-sm-4">
                                <a href="siswa.php" class="btn btn-secondary btn-sm px-3">Batal</a>
                                <button type="submit" name="update_siswa" class="btn btn-warning btn-sm px-3 font-weight-bold">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
            </footer>
        </main>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>