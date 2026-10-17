<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";

// 1. Proses Tambah Siswa Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $jk = trim($_POST['jk'] ?? 'L');

    if (!empty($nama)) {
        // Coba insert sederhana dengan deteksi kolom aman
        $sql_insert = "INSERT INTO t_siswa (nama) VALUES ('" . $koneksi->real_escape_string($nama) . "')";
        
        // Eksekusi insert jika berhasil
        if ($koneksi->query($sql_insert)) {
            $id_baru = $koneksi->insert_id;
            
            // Update opsional untuk NIS, kelas, & JK agar tidak error jika kolom beda nama
            if (!empty($nis)) {
                @$koneksi->query("UPDATE t_siswa SET nis='$nis' WHERE id=$id_baru");
                @$koneksi->query("UPDATE t_siswa SET no_induk='$nis' WHERE id=$id_baru");
            }
            if ($kelas_id > 0) {
                @$koneksi->query("UPDATE t_siswa SET kelas_id=$kelas_id WHERE id=$id_baru");
                @$koneksi->query("UPDATE t_siswa SET id_kelas=$kelas_id WHERE id=$id_baru");
            }
            if (!empty($jk)) {
                @$koneksi->query("UPDATE t_siswa SET jk='$jk' WHERE id=$id_baru");
                @$koneksi->query("UPDATE t_siswa SET jenis_kelamin='$jk' WHERE id=$id_baru");
            }
            $pesan_sukses = "Data siswa berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan data: " . $koneksi->error;
        }
    } else {
        $pesan_error = "Nama siswa tidak boleh kosong!";
    }
}

// 2. Ambil Pilihan Kelas
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");

// 3. Ambil Data Siswa (Sederhana & Anti-Crash)
$result_siswa = $koneksi->query("SELECT * FROM t_siswa ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Sistem Pelanggaran Siswa</title>
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
        <!-- SIDEBAR RESMI (LOGOUT TETAP DI BAWAH) -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="siswa.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'siswa.php') ? 'active' : ''; ?>"><i class="fas fa-user-graduate mr-2"></i> Data Siswa</a></li>
                <li class="nav-item mb-2"><a href="guru.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'guru.php') ? 'active' : ''; ?>"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</a></li>
                <li class="nav-item mb-2"><a href="kelas.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'kelas.php') ? 'active' : ''; ?>"><i class="fas fa-school mr-2"></i> Kelas</a></li>
                <li class="nav-item mb-2"><a href="wali_kelas.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'wali_kelas.php') ? 'active' : ''; ?>"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</a></li>
                <li class="nav-item mb-2"><a href="tahun_ajaran.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'tahun_ajaran.php') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran_kategori.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'pelanggaran_kategori.php') ? 'active' : ''; ?>"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'pelanggaran.php') ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="laporan.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap</a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF']) == 'about_me.php') ? 'active' : ''; ?>"><i class="fas fa-info-circle mr-2"></i> Tentang Saya</a></li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger p-2"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Siswa</h1>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahSiswa">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa Baru
                </button>
            </div>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $pesan_sukses; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $pesan_error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Tabel Data Siswa -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Siswa Terdaftar</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">NIS / No Induk</th>
                                    <th>Nama Siswa</th>
                                    <th style="width: 15%;">L/P</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_siswa && $result_siswa->num_rows > 0): $no = 1; while ($row = $result_siswa->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['nis'] ?? $row['no_induk'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($row['nama'] ?? $row['nama_siswa'] ?? '-'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jk'] ?? $row['jenis_kelamin'] ?? '-'); ?></td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data siswa. Silakan klik tombol "Tambah Siswa Baru".</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
            </footer>
        </main>
    </div>
</div>

<!-- MODAL FORM TAMBAH SISWA -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="siswa.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIS / No Induk</label>
                    <input type="text" class="form-control" name="nis" placeholder="Contoh: 12345">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap Siswa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" required placeholder="Masukkan nama lengkap siswa">
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <select class="form-control" name="kelas_id">
                        <option value="0">-- Pilih Kelas --</option>
                        <?php if ($list_kelas && $list_kelas->num_rows > 0): ?>
                            <?php while ($kls = $list_kelas->fetch_assoc()): ?>
                                <option value="<?= $kls['id']; ?>"><?= htmlspecialchars($kls['nama']); ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jk">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_siswa" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JS JQUERY BOOTSTRAP UNTUK MODAL INTERAKTIF -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>