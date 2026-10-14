<?php
// Pastikan sesi aktif dan matikan error display agar tampilan bersih
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', '0');
error_reporting(0);

require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Tambah / Edit Siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? 'L';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'tambah') {
        $stmt = $koneksi->prepare("INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $nis, $nisn, $nama, $jenis_kelamin, $tanggal_lahir, $alamat, $status_aktif);
        if ($stmt->execute()) {
            $message = "Data siswa berhasil ditambahkan.";
        } else {
            $message = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'edit' && !empty($id)) {
        $stmt = $koneksi->prepare("UPDATE t_siswa SET nis=?, nisn=?, nama=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, status_aktif=? WHERE id=?");
        $stmt->bind_param("ssssssii", $nis, $nisn, $nama, $jenis_kelamin, $tanggal_lahir, $alamat, $status_aktif, $id);
        if ($stmt->execute()) {
            $message = "Data siswa berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handling Hapus Siswa
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM t_siswa WHERE id=?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        $message = "Data siswa berhasil dihapus.";
    } else {
        $message = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
}

// Handling Mode Edit (Ambil Data Siswa yang Akan Diedit)
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $stmt = $koneksi->prepare("SELECT * FROM t_siswa WHERE id=?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handling Pencarian & Fetch Data
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $param = "%" . $search . "%";
    $stmt = $koneksi->prepare("SELECT * FROM t_siswa WHERE nama LIKE ? OR nis LIKE ? OR nisn LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("SELECT * FROM t_siswa ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa - Sistem Pelanggaran</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }
        .sidebar a {
            color: #cfd2d6;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar .active {
            color: #fff;
            background-color: #007bff;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <h4 class="text-white mb-4">My Website</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link p-2">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="siswa.php" class="nav-link active p-2">Data Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link p-2">Data Guru</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelas.php" class="nav-link p-2">Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="laporan.php" class="nav-link p-2">Laporan</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about.php" class="nav-link p-2">About Me</a>
                </li>
            </ul>
            <hr class="bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger p-2">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 ml-sm-auto col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Kelola Data Siswa</h1>
            </div>

            <!-- Pesan Notifikasi -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- FORM TAMBAH / EDIT SISWA -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?= $edit_data ? 'Edit Data Siswa' : 'Tambah Siswa Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="siswa.php">
                        <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group row">
                            <label for="nis" class="col-sm-2 col-form-label">NIS</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nis" name="nis" value="<?= htmlspecialchars($edit_data['nis'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nisn" class="col-sm-2 col-form-label">NISN</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nisn" name="nisn" value="<?= htmlspecialchars($edit_data['nisn'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nama" class="col-sm-2 col-form-label">Nama Siswa</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="L" <?= (($edit_data['jenis_kelamin'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= (($edit_data['jenis_kelamin'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= $edit_data['tanggal_lahir'] ?? ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><<?= htmlspecialchars($edit_data['alamat'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Status Aktif</label>
                            <div class="col-sm-10">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" <?= (!isset($edit_data) || !empty($edit_data['status_aktif'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_aktif">Aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Siswa'; ?></button>
                                <?php if ($edit_data): ?>
                                    <a href="siswa.php" class="btn btn-secondary ml-2">Batal Edit</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FORM PENCARIAN & TABEL DATA SISWA -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Siswa</h5>
                    <form method="GET" action="siswa.php" class="form-inline">
                        <input type="text" class="form-control form-control-sm mr-2" id="search" name="search" placeholder="Cari Nama/NIS..." value="<?= htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline-secondary btn-sm mr-1">Cari</button>
                        <?php if (!empty($search)): ?>
                            <a href="siswa.php" class="btn btn-outline-danger btn-sm">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['nis']); ?></td>
                                            <td><?= htmlspecialchars($row['nisn']); ?></td>
                                            <td><?= htmlspecialchars($row['nama']); ?></td>
                                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                            <td><?= htmlspecialchars($row['tanggal_lahir']); ?></td>
                                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                                            <td>
                                                <span class="badge <?= $row['status_aktif'] ? 'badge-success' : 'badge-secondary'; ?>">
                                                    <?= $row['status_aktif'] ? 'Aktif' : 'Non-Aktif'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="siswa.php?edit=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="siswa.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-3">Data siswa tidak ditemukan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>