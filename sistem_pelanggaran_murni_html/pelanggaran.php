<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// Cek skema kolom t_pelanggaran
$has_id_kategori = false;
$has_kategori_id = false;
$res_cols = $koneksi->query("SHOW COLUMNS FROM t_pelanggaran");
if ($res_cols) {
    while ($col = $res_cols->fetch_assoc()) {
        if ($col['Field'] === 'id_kategori') $has_id_kategori = true;
        if ($col['Field'] === 'kategori_id') $has_kategori_id = true;
    }
}

// 1. TAMBAH PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pelanggaran'])) {
    $nama = trim($_POST['nama'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $kat_id = intval($_POST['kategori_id'] ?? 0);

    if (!empty($nama) && $poin > 0) {
        $nama_esc = $koneksi->real_escape_string($nama);
        if ($has_id_kategori) {
            $sql = "INSERT INTO t_pelanggaran (nama, poin, id_kategori) VALUES ('$nama_esc', $poin, $kat_id)";
        } elseif ($has_kategori_id) {
            $sql = "INSERT INTO t_pelanggaran (nama, poin, kategori_id) VALUES ('$nama_esc', $poin, $kat_id)";
        } else {
            $sql = "INSERT INTO t_pelanggaran (nama, poin) VALUES ('$nama_esc', $poin)";
        }
        
        if ($koneksi->query($sql)) {
            $pesan_sukses = "Jenis pelanggaran berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan: " . $koneksi->error;
        }
    } else { $pesan_error = "Nama dan poin wajib diisi!"; }
}

// 2. EDIT PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_pelanggaran'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $kat_id = intval($_POST['kategori_id'] ?? 0);

    if ($id > 0 && !empty($nama) && $poin > 0) {
        $nama_esc = $koneksi->real_escape_string($nama);
        if ($has_id_kategori) {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin, id_kategori=$kat_id WHERE id=$id";
        } elseif ($has_kategori_id) {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin, kategori_id=$kat_id WHERE id=$id";
        } else {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin WHERE id=$id";
        }

        if ($koneksi->query($sql)) {
            $pesan_sukses = "Jenis pelanggaran berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui: " . $koneksi->error;
        }
    } else { $pesan_error = "Nama dan poin wajib diisi!"; }
}

// 3. HAPUS PELANGGARAN
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        if ($koneksi->query("DELETE FROM t_pelanggaran WHERE id = $id_hapus")) {
            $pesan_sukses = "Jenis pelanggaran berhasil dihapus!";
        } else { $pesan_error = "Gagal menghapus: " . $koneksi->error; }
    }
}

// AMBIL MASTER KATEGORI
$list_kategori = $koneksi->query("SELECT * FROM t_pelanggaran_kategori ORDER BY nama ASC");

// QUERY TAMPIL DATA DINAMIS
if ($has_id_kategori) {
    $sql_p = "SELECT p.*, COALESCE(k.nama, '-') AS nama_kategori, p.id_kategori AS kat_id_curr FROM t_pelanggaran p LEFT JOIN t_pelanggaran_kategori k ON p.id_kategori = k.id ORDER BY p.id DESC";
} elseif ($has_kategori_id) {
    $sql_p = "SELECT p.*, COALESCE(k.nama, '-') AS nama_kategori, p.kategori_id AS kat_id_curr FROM t_pelanggaran p LEFT JOIN t_pelanggaran_kategori k ON p.kategori_id = k.id ORDER BY p.id DESC";
} else {
    $sql_p = "SELECT p.*, '-' AS nama_kategori, 0 AS kat_id_curr FROM t_pelanggaran p ORDER BY p.id DESC";
}
$result_pelanggaran = $koneksi->query($sql_p);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggaran - Sistem Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>body { background-color: #f8f9fa; } .sidebar { min-height: 100vh; background-color: #212529; color: #fff; } .sidebar a { color: #cfd2d6; text-decoration: none; } .sidebar a:hover, .sidebar .active { color: #fff; background-color: #007bff; border-radius: 4px; }</style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR RESMI -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'active':''; ?>"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="siswa.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='siswa.php')?'active':''; ?>"><i class="fas fa-user-graduate mr-2"></i> Data Siswa</a></li>
                <li class="nav-item mb-2"><a href="guru.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='guru.php')?'active':''; ?>"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</a></li>
                <li class="nav-item mb-2"><a href="kelas.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='kelas.php')?'active':''; ?>"><i class="fas fa-school mr-2"></i> Kelas</a></li>
                <li class="nav-item mb-2"><a href="wali_kelas.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='wali_kelas.php')?'active':''; ?>"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</a></li>
                <li class="nav-item mb-2"><a href="tahun_ajaran.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='tahun_ajaran.php')?'active':''; ?>"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran_kategori.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='pelanggaran_kategori.php')?'active':''; ?>"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="pelanggaran.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='pelanggaran.php')?'active':''; ?>"><i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran</a></li>
                <li class="nav-item mb-2"><a href="laporan.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='laporan.php')?'active':''; ?>"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap</a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link p-2 <?= (basename($_SERVER['PHP_SELF'])=='about_me.php')?'active':''; ?>"><i class="fas fa-info-circle mr-2"></i> Tentang Saya</a></li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger p-2"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Jenis & Master Pelanggaran</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPelanggaran"><i class="fas fa-plus mr-1"></i> Tambah Pelanggaran Baru</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $pesan_sukses; ?>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $pesan_error; ?>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Jenis Pelanggaran</th>
                                <th>Kategori</th>
                                <th style="width: 15%;">Poin</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_pelanggaran && $result_pelanggaran->num_rows > 0): $no=1; while ($r = $result_pelanggaran->fetch_assoc()): 
                                $id = $r['id']; 
                                $nama = htmlspecialchars($r['nama'] ?? ''); 
                                $kat = htmlspecialchars($r['nama_kategori'] ?? '-'); 
                                $poin = htmlspecialchars($r['poin'] ?? 0);
                                $kat_id_curr = $r['kat_id_curr'] ?? 0;
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= $nama; ?></td>
                                    <td class="text-center"><?= $kat; ?></td>
                                    <td class="text-center font-weight-bold text-danger"><?= $poin; ?> Poin</td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPel<?= $id; ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusPel<?= $id; ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>

                                <!-- Modal Edit Pelanggaran -->
                                <div class="modal fade" id="editPel<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="pelanggaran.php" class="modal-content">
                                            <input type="hidden" name="id" value="<?= $id; ?>">
                                            <div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Pelanggaran</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                                            <div class="modal-body">
                                                <div class="form-group"><label>Nama Pelanggaran <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="<?= $nama; ?>" required></div>
                                                <?php if ($has_id_kategori || $has_kategori_id): ?>
                                                <div class="form-group">
                                                    <label>Kategori</label>
                                                    <select name="kategori_id" class="form-control">
                                                        <option value="0">-- Pilih Kategori --</option>
                                                        <?php if ($list_kategori && $list_kategori->num_rows > 0): $list_kategori->data_seek(0); while ($k = $list_kategori->fetch_assoc()): ?>
                                                            <option value="<?= $k['id']; ?>" <?= ($kat_id_curr == $k['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($k['nama']); ?></option>
                                                        <?php endwhile; endif; ?>
                                                    </select>
                                                </div>
                                                <?php endif; ?>
                                                <div class="form-group"><label>Poin <span class="text-danger">*</span></label><input type="number" name="poin" class="form-control" value="<?= $poin; ?>" required></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="edit_pelanggaran" class="btn btn-warning btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Hapus Pelanggaran -->
                                <div class="modal fade" id="hapusPel<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Pelanggaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                                            <div class="modal-body">Hapus jenis pelanggaran <strong><?= $nama; ?></strong>?</div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><a href="pelanggaran.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada jenis pelanggaran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
            </footer>
        </main>
    </div>
</div>

<!-- Modal Tambah Pelanggaran -->
<div class="modal fade" id="modalPelanggaran" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="pelanggaran.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i> Tambah Pelanggaran Baru</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group"><label>Nama Pelanggaran <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Contoh: Datang Terlambat"></div>
                <?php if ($has_id_kategori || $has_kategori_id): ?>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control">
                        <option value="0">-- Pilih Kategori --</option>
                        <?php if ($list_kategori && $list_kategori->num_rows > 0): $list_kategori->data_seek(0); while ($k = $list_kategori->fetch_assoc()): ?>
                            <option value="<?= $k['id']; ?>"><?= htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="form-group"><label>Poin Pelanggaran <span class="text-danger">*</span></label><input type="number" name="poin" class="form-control" required placeholder="Contoh: 10"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_pelanggaran" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>