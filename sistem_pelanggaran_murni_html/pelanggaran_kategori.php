<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// 1. TAMBAH KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama = trim($_POST['nama'] ?? '');
    if (!empty($nama)) {
        $sql = "INSERT INTO t_pelanggaran_kategori (nama) VALUES ('".$koneksi->real_escape_string($nama)."')";
        if ($koneksi->query($sql)) { $pesan_sukses = "Kategori berhasil ditambahkan!"; }
        else { $pesan_error = "Gagal menyimpan: " . $koneksi->error; }
    } else { $pesan_error = "Nama kategori wajib diisi!"; }
}

// 2. EDIT KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kategori'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    if ($id > 0 && !empty($nama)) {
        if ($koneksi->query("UPDATE t_pelanggaran_kategori SET nama='".$koneksi->real_escape_string($nama)."' WHERE id=$id")) {
            $pesan_sukses = "Kategori berhasil diperbarui!";
        } else { $pesan_error = "Gagal memperbarui: " . $koneksi->error; }
    } else { $pesan_error = "Nama kategori wajib diisi!"; }
}

// 3. HAPUS KATEGORI
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        if ($koneksi->query("DELETE FROM t_pelanggaran_kategori WHERE id = $id_hapus")) {
            $pesan_sukses = "Kategori berhasil dihapus!";
        } else { $pesan_error = "Gagal menghapus: " . $koneksi->error; }
    }
}

$result_kategori = $koneksi->query("SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Pelanggaran - Sistem Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>body { background-color: #f8f9fa; } .sidebar { min-height: 100vh; background-color: #212529; color: #fff; } .sidebar a { color: #cfd2d6; text-decoration: none; } .sidebar a:hover, .sidebar .active { color: #fff; background-color: #007bff; border-radius: 4px; }</style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''?>"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="siswa.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='siswa.php'?'active':''?>"><i class="fas fa-user-graduate mr-2"></i> Data Siswa</a></li>
                <li class="nav-item mb-2"><a href="guru.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='guru.php'?'active':''?>"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</a></li>
                <li class="nav-item mb-2"><a href="kelas.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='kelas.php'?'active':''?>"><i class="fas fa-school mr-2"></i> Kelas</a></li>
                <li class="nav-item mb-2"><a href="wali_kelas.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='wali_kelas.php'?'active':''?>"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</a></li>
                <li class="nav-item mb-2"><a href="tahun_ajaran.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='tahun_ajaran.php'?'active':''?>"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran_kategori.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='pelanggaran_kategori.php'?'active':''?>"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='pelanggaran.php'?'active':''?>"><i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="laporan.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='laporan.php'?'active':''?>"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap</a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF'])=='about_me.php'?'active':''?>"><i class="fas fa-info-circle mr-2"></i> Tentang Saya</a></li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger p-2"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Kategori Pelanggaran</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalKategori"><i class="fas fa-plus mr-1"></i> Tambah Kategori Baru</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?><div class="alert alert-success alert-dismissible fade show"><?= $pesan_sukses; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
            <?php if (!empty($pesan_error)): ?><div class="alert alert-danger alert-dismissible fade show"><?= $pesan_error; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="thead-dark text-center">
                            <tr><th style="width:10%;">No</th><th>Nama Kategori Pelanggaran</th><th style="width:20%;">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($result_kategori && $result_kategori->num_rows > 0): $no=1; while ($r = $result_kategori->fetch_assoc()): 
                                $id = $r['id']; $nama = htmlspecialchars($r['nama']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= $nama; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editKat<?= $id; ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusKat<?= $id; ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>

                                <!-- Modal Edit Kategori -->
                                <div class="modal fade" id="editKat<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="pelanggaran_kategori.php" class="modal-content">
                                            <input type="hidden" name="id" value="<?= $id; ?>">
                                            <div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Kategori</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                                            <div class="modal-body">
                                                <div class="form-group"><label>Nama Kategori <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="<?= $nama; ?>" required></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="edit_kategori" class="btn btn-warning btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Hapus Kategori -->
                                <div class="modal fade" id="hapusKat<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Kategori</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                                            <div class="modal-body">Hapus kategori <strong><?= $nama; ?></strong>?</div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><a href="pelanggaran_kategori.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada kategori.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-5 py-3 text-center text-muted border-top small">&copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong></footer>
        </main>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="pelanggaran_kategori.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-tags mr-2"></i> Tambah Kategori Pelanggaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group"><label>Nama Kategori <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Contoh: Kerapian / Kehadiran"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_kategori" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>