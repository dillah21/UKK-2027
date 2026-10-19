<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Panggil header dinamis
include 'header.php';

// Hitung Statistik Card
$q_siswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_siswa");
$total_siswa = ($q_siswa) ? (mysqli_fetch_assoc($q_siswa)['total'] ?? 0) : 0;

$q_guru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru");
$total_guru = ($q_guru) ? (mysqli_fetch_assoc($q_guru)['total'] ?? 0) : 0;

$q_kat = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_kategori");
$total_kategori = ($q_kat) ? (mysqli_fetch_assoc($q_kat)['total'] ?? 0) : 0;

$q_kasus = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
$total_kasus = ($q_kasus) ? (mysqli_fetch_assoc($q_kasus)['total'] ?? 0) : 0;

// Query Hitung Total Prestasi (Fallback jika tabel t_prestasi belum ada)
$q_prestasi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_prestasi");
$total_prestasi = ($q_prestasi) ? (mysqli_fetch_assoc($q_prestasi)['total'] ?? 0) : 0;

// Query 5 Pelanggaran Terbaru
$query_recent = mysqli_query($koneksi, "
    SELECT 
        p.*, 
        s.nama_siswa, 
        k.nama_kelas,
        kat.nama_kategori
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_siswa s ON p.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN t_pelanggaran_kategori kat ON p.id_kategori = kat.id_kategori
    ORDER BY p.tanggal DESC, p.id_pelanggaran DESC
    LIMIT 5
");

// Query 5 Prestasi Terbaru
$query_recent_prestasi = mysqli_query($koneksi, "
    SELECT 
        pr.*, 
        s.nama_siswa, 
        k.nama_kelas 
    FROM t_prestasi pr
    LEFT JOIN t_siswa s ON pr.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k ON s.id_kelas = k.id_kelas
    ORDER BY pr.tanggal DESC
    LIMIT 5
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Selamat datang, <?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Pengguna')); ?></h1>
</div>

<!-- STATISTIC CARDS -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 border-0" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">TOTAL SISWA</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_siswa; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 border-0" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">TOTAL PRESTASI SISWA</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_prestasi; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 border-0" style="border-left: 4px solid #36b9cc !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">KATEGORI PELANGGARAN</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_kategori; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 border-0" style="border-left: 4px solid #e74a3b !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">TOTAL KASUS PELANGGARAN</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $total_kasus; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KARTU SAMBUTAN & QUICK ACTION -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="card-title text-primary font-weight-bold">Halo, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna'); ?>! 👋</h5>
        <p class="card-text text-muted mb-3">
            Anda berhasil masuk sebagai level <strong><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'User')); ?></strong>. Silakan gunakan navigasi menu di sebelah kiri atau pintasan di bawah ini untuk mengelola data operasional sistem.
        </p>
        
        <!-- TOMBOL PINTASAN CEPAAT (QUICK ACTION) -->
        <div class="pt-2 border-top">
            <span class="text-xs font-weight-bold text-secondary d-block mb-2"><i class="fas fa-bolt text-warning mr-1"></i> Pintasan Cepat (Quick Action):</span>
            <div class="btn-group flex-wrap gap-2">
                <a href="laporan.php" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                    <i class="fas fa-file-alt mr-1"></i> Lihat Rekap Laporan
                </a>
                <a href="prestasi.php" class="btn btn-outline-success btn-sm mr-2 mb-2">
                    <i class="fas fa-trophy mr-1"></i> Data Prestasi Siswa
                </a>
                <a href="pelanggaran_kategori.php" class="btn btn-outline-info btn-sm mr-2 mb-2">
                    <i class="fas fa-tags mr-1"></i> Data Kategori Pelanggaran
                </a>
                <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="pelanggaran_kategori_tambah.php" class="btn btn-outline-success btn-sm mr-2 mb-2">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Kategori
                    </a>
                <?php endif; ?>
                <a href="profile.php" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-user-cog mr-1"></i> Pengaturan Profil
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- TABEL RINGKASAN PRESTASI TERBARU -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-trophy mr-2"></i> 5 Catatan Prestasi Terbaru
                </h6>
                <a href="prestasi.php" class="btn btn-sm btn-success">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Siswa & Kelas</th>
                                <th>Nama Prestasi / Lomba</th>
                                <th width="80" class="text-center">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no_p = 1;
                            if ($query_recent_prestasi && mysqli_num_rows($query_recent_prestasi) > 0):
                                while ($row_p = mysqli_fetch_assoc($query_recent_prestasi)):
                                    $nama_siswa_p = $row_p['nama_siswa'] ?? ('Siswa ID ' . ($row_p['id_siswa'] ?? ''));
                                    $kelas_p = $row_p['nama_kelas'] ?? '-';
                                    $judul_prestasi = $row_p['nama_prestasi'] ?? $row_p['juduL'] ?? $row_p['keterangan'] ?? 'Juara Lomba';
                                    $poin_p = $row_p['poin'] ?? $row_p['poin_prestasi'] ?? 10;
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no_p++; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($nama_siswa_p); ?></strong><br>
                                        <small class="text-muted">Kelas: <?= htmlspecialchars($kelas_p); ?></small>
                                    </td>
                                    <td><span class="badge badge-success p-2"><?= htmlspecialchars($judul_prestasi); ?></span></td>
                                    <td class="text-center font-weight-bold text-success">+<?= htmlspecialchars($poin_p); ?></td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada catatan prestasi siswa.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RINGKASAN PELANGGARAN TERBARU -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-history mr-2"></i> 5 Catatan Pelanggaran Terbaru
                </h6>
                <a href="laporan.php" class="btn btn-sm btn-danger">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Siswa & Kelas</th>
                                <th>Kategori</th>
                                <th width="80" class="text-center">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if ($query_recent && mysqli_num_rows($query_recent) > 0):
                                while ($row = mysqli_fetch_assoc($query_recent)):
                                    $nama_siswa = $row['nama_siswa'] ?? ('Siswa ID ' . ($row['id_siswa'] ?? ''));
                                    $kelas = $row['nama_kelas'] ?? '-';
                                    $kategori = $row['nama_kategori'] ?? $row['kategori'] ?? 'Pelanggaran';
                                    $poin = $row['poin'] ?? $row['poin_pelanggaran'] ?? 10;
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($nama_siswa); ?></strong><br>
                                        <small class="text-muted">Kelas: <?= htmlspecialchars($kelas); ?></small>
                                    </td>
                                    <td><span class="badge badge-warning p-2"><?= htmlspecialchars($kategori); ?></span></td>
                                    <td class="text-center font-weight-bold text-danger">+<?= htmlspecialchars($poin); ?></td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada catatan pelanggaran terbaru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>