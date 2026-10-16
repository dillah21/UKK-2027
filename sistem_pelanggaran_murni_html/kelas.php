<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Hapus Kelas
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_kelas WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_hapus);
        if ($stmt->execute()) {
            $message = "Data kelas berhasil dihapus.";
        } else {
            $message = "Gagal menghapus data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Deteksi kolom foreign key yang valid pada tabel t_siswa untuk menghitung jumlah siswa per kelas
$col_siswa_cols = [];
$res_sc = $koneksi->query("SHOW COLUMNS FROM t_siswa");
if ($res_sc) {
    while ($row_sc = $res_sc->fetch_assoc()) {
        $col_siswa_cols[] = $row_sc['Field'];
    }
}
$col_k_fk = in_array('kelas_id', $col_siswa_cols) ? 'kelas_id' : (in_array('id_kelas', $col_siswa_cols) ? 'id_kelas' : (in_array('kelas', $col_siswa_cols) ? 'kelas' : ''));

// Ambil data kelas
$result_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - Sistem Pelanggaran Siswa</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; }
        .sidebar a { color: #cfd2d6; text-decoration: none; }
        .sidebar a:hover, .sidebar .active { color: #fff; background-color: #007bff; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="siswa.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'siswa.php' ? 'active' : ''; ?>"><i class="fas fa-user-graduate mr-2"></i> Data Siswa</a></li>
                <li class="nav-item mb-2"><a href="guru.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'guru.php' ? 'active' : ''; ?>"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</a></li>
                <li class="nav-item mb-2"><a href="kelas.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'kelas.php' ? 'active' : ''; ?>"><i class="fas fa-school mr-2"></i> Kelas</a></li>
                <li class="nav-item mb-2"><a href="wali_kelas.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'wali_kelas.php' ? 'active' : ''; ?>"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</a></li>
                <li class="nav-item mb-2"><a href="tahun_ajaran.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'tahun_ajaran.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran_kategori.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'pelanggaran_kategori.php' ? 'active' : ''; ?>"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'pelanggaran.php' ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="laporan.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap</a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link p-2 <?= basename($_SERVER['PHP_SELF']) == 'about_me.php' ? 'active' : ''; ?>"><i class="fas fa-info-circle mr-2"></i> Tentang Saya</a></li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger p-2"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Kelas</h1>
                <a href="tambah_kelas.php" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Kelas Baru</a>
            </div>
            <p class="text-muted">Kelola daftar tingkat kelas dan rombongan belajar di sekolah.</p>

            <!-- Notifikasi -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Tabel Data Kelas -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Seluruh Kelas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 8%;">No</th>
                                    <th style="width: 60%;">Nama Kelas</th>
                                    <th style="width: 18%;">Jumlah Siswa</th>
                                    <th style="width: 14%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_kelas && $result_kelas->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result_kelas->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><strong><?= htmlspecialchars($row['nama'] ?? '-'); ?></strong></td>
                                            <td class="text-center">
                                                <?php 
                                                $id_k = $row['id'];
                                                $nama_k = $row['nama'];
                                                $hitung = 0;
                                                
                                                if ($col_k_fk) {
                                                    if ($col_k_fk === 'kelas') {
                                                        $res_jml = @$koneksi->query("SELECT COUNT(*) as total FROM t_siswa WHERE kelas = '$nama_k'");
                                                    } else {
                                                        $res_jml = @$koneksi->query("SELECT COUNT(*) as total FROM t_siswa WHERE $col_k_fk = $id_k");
                                                    }
                                                    if ($res_jml) {
                                                        $hitung = $res_jml->fetch_assoc()['total'];
                                                    }
                                                }
                                                echo $hitung . " Siswa";
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="kelas.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kelas ini?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data kelas yang terdaftar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa
            </footer>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
