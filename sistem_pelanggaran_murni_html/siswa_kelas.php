<?php
require_once 'auth.php';
require_once 'koneksi.php';

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$message = '';
$message_type = 'success';
$edit_data = null;

function ks_h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function ks_redirect_message($text, $type = 'success') {
    // Pesan ditampilkan pada halaman yang sama setelah proses selesai.
    return [$text, $type];
}

// Proses tambah dan edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_penempatan'])) {
    $action = $_POST['action'] ?? 'tambah';
    $id = (int)($_POST['id'] ?? 0);
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    try {
        if ($siswa_id <= 0 || $tahun_ajaran_id <= 0 || $kelas_id <= 0 ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai)) {
            throw new Exception('Lengkapi semua pilihan dan tanggal dengan benar.');
        }
        if ($tanggal_selesai < $tanggal_mulai) {
            throw new Exception('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        $q = $koneksi->prepare("SELECT id FROM t_siswa WHERE id=? LIMIT 1");
        $q->bind_param('i', $siswa_id); $q->execute();
        $valid = $q->get_result()->fetch_assoc(); $q->close();
        if (!$valid) throw new Exception('Data siswa tidak ditemukan.');

        $q = $koneksi->prepare("SELECT id FROM t_kelas WHERE id=? AND UPPER(TRIM(nama)) IN ('12 RPL 1','12 RPL 2') LIMIT 1");
        $q->bind_param('i', $kelas_id); $q->execute();
        $valid = $q->get_result()->fetch_assoc(); $q->close();
        if (!$valid) throw new Exception('Kelas yang dipilih tidak valid.');

        $q = $koneksi->prepare("SELECT id FROM t_tahun_ajaran WHERE id=? LIMIT 1");
        $q->bind_param('i', $tahun_ajaran_id); $q->execute();
        $valid = $q->get_result()->fetch_assoc(); $q->close();
        if (!$valid) throw new Exception('Tahun ajaran yang dipilih tidak ditemukan.');

        if ($action === 'edit' && $id > 0) {
            $stmt = $koneksi->prepare("UPDATE t_kelas_siswa
                SET siswa_id=?, tahun_ajaran_id=?, kelas_id=?, tanggal_mulai=?,
                    tanggal_selesai=?, status_aktif=?, updated_at=NOW()
                WHERE id=?");
            $stmt->bind_param('iiissii', $siswa_id, $tahun_ajaran_id, $kelas_id,
                $tanggal_mulai, $tanggal_selesai, $status_aktif, $id);
            $stmt->execute();
            if ($stmt->affected_rows >= 0) $message = 'Penempatan siswa berhasil diperbarui.';
            $stmt->close();
        } elseif ($action === 'tambah') {
            $stmt = $koneksi->prepare("INSERT INTO t_kelas_siswa
                (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iiissi', $siswa_id, $tahun_ajaran_id, $kelas_id,
                $tanggal_mulai, $tanggal_selesai, $status_aktif);
            $stmt->execute();
            $message = 'Penempatan siswa berhasil ditambahkan.';
            $stmt->close();
        } else {
            throw new Exception('Permintaan tidak valid.');
        }
    } catch (Throwable $e) {
        $message = 'Gagal menyimpan penempatan siswa: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Hapus data penempatan
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    if ($id_hapus > 0) {
        try {
            $stmt = $koneksi->prepare("DELETE FROM t_kelas_siswa WHERE id=?");
            $stmt->bind_param('i', $id_hapus);
            $stmt->execute();
            $message = $stmt->affected_rows > 0
                ? 'Penempatan siswa berhasil dihapus.'
                : 'Data penempatan tidak ditemukan.';
            $stmt->close();
        } catch (Throwable $e) {
            $message = 'Gagal menghapus penempatan siswa. Data mungkin masih digunakan oleh riwayat lain.';
            $message_type = 'danger';
        }
    }
}

// Ambil data untuk mode edit
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt = $koneksi->prepare("SELECT * FROM t_kelas_siswa WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id_edit);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$edit_data) {
        $message = 'Data penempatan yang akan diedit tidak ditemukan.';
        $message_type = 'warning';
    }
}

$list_siswa = $koneksi->query("SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC");
$list_kelas = $koneksi->query("SELECT id, nama FROM t_kelas
    WHERE UPPER(TRIM(nama)) IN ('12 RPL 1','12 RPL 2') ORDER BY nama ASC");
$list_ta = $koneksi->query("SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");

// Daftar penempatan dan pencarian
$search = trim($_GET['search'] ?? '');
$sql = "SELECT ks.*, s.nama AS nama_siswa, s.nis, k.nama AS nama_kelas, ta.nama AS nama_ta
        FROM t_kelas_siswa ks
        INNER JOIN t_siswa s ON s.id=ks.siswa_id
        INNER JOIN t_kelas k ON k.id=ks.kelas_id
        INNER JOIN t_tahun_ajaran ta ON ta.id=ks.tahun_ajaran_id
        WHERE UPPER(TRIM(k.nama)) IN ('12 RPL 1','12 RPL 2')";
if ($search !== '') {
    $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ? OR k.nama LIKE ? OR ta.nama LIKE ?)";
    $param = '%' . $search . '%';
    $stmt = $koneksi->prepare($sql . ' ORDER BY ks.id DESC');
    $stmt->bind_param('ssss', $param, $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query($sql . ' ORDER BY ks.id DESC');
}

include 'header.php';
?>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Penempatan Siswa</h3>
            <p class="text-muted mb-0">Atur kelas dan tahun ajaran siswa.</p>
        </div>
        <span class="badge badge-primary p-2">XII RPL 1 &amp; XII RPL 2</span>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= ks_h($message_type) ?> alert-dismissible fade show" role="alert">
            <?= ks_h($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-user-graduate mr-2 text-primary"></i>
            <?= $edit_data ? 'Edit Penempatan Siswa' : 'Tambah Penempatan Siswa' ?>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= ks_h(basename($_SERVER['PHP_SELF'])) ?>">
                <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= (int)$edit_data['id'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="siswa_id">Siswa</label>
                        <select class="form-control" id="siswa_id" name="siswa_id" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = $list_siswa->fetch_assoc()): ?>
                                <option value="<?= (int)$s['id'] ?>" <?= ((int)($edit_data['siswa_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                                    <?= ks_h($s['nis'] . ' - ' . $s['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="kelas_id">Kelas</label>
                        <select class="form-control" id="kelas_id" name="kelas_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = $list_kelas->fetch_assoc()): ?>
                                <option value="<?= (int)$k['id'] ?>" <?= ((int)($edit_data['kelas_id'] ?? 0) === (int)$k['id']) ? 'selected' : '' ?>>
                                    <?= ks_h($k['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tahun_ajaran_id">Tahun Ajaran</label>
                        <select class="form-control" id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php while ($ta = $list_ta->fetch_assoc()): ?>
                                <option value="<?= (int)$ta['id'] ?>" <?= ((int)($edit_data['tahun_ajaran_id'] ?? 0) === (int)$ta['id']) ? 'selected' : '' ?>>
                                    <?= ks_h($ta['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status_aktif">Status Penempatan</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="status_aktif" name="status_aktif"
                                <?= ($edit_data === null || !empty($edit_data['status_aktif'])) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="status_aktif">Aktif</label>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                            value="<?= ks_h($edit_data['tanggal_mulai'] ?? date('Y-07-01')) ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                            value="<?= ks_h($edit_data['tanggal_selesai'] ?? date('Y-06-30', strtotime('+1 year'))) ?>" required>
                    </div>
                </div>
                <div class="d-flex flex-wrap">
                    <button type="submit" name="simpan_penempatan" value="1" class="btn btn-primary mr-2">
                        <i class="fas fa-save mr-1"></i><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Penempatan' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="<?= ks_h(basename($_SERVER['PHP_SELF'])) ?>" class="btn btn-light">
                            <i class="fas fa-times mr-1"></i>Batal Edit
                        </a>
                    <?php else: ?>
                        <button type="reset" class="btn btn-light"><i class="fas fa-undo mr-1"></i>Reset</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <span><i class="fas fa-list mr-2 text-primary"></i>Daftar Penempatan Siswa</span>
            <span class="text-muted small">Total data ditampilkan: <?= (int)$result->num_rows ?></span>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= ks_h(basename($_SERVER['PHP_SELF'])) ?>" class="form-row align-items-end mb-3">
                <div class="form-group col-md-6 mb-2">
                    <label for="search">Cari data</label>
                    <input type="search" class="form-control" id="search" name="search"
                        placeholder="Nama siswa, NIS, kelas, tahun ajaran..." value="<?= ks_h($search) ?>">
                </div>
                <div class="form-group col-auto mb-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Cari</button>
                    <a href="<?= ks_h(basename($_SERVER['PHP_SELF'])) ?>" class="btn btn-light">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th>
                            <th>Tahun Ajaran</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th>
                            <th>Status</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= ks_h($row['nis']) ?></td>
                                <td><?= ks_h($row['nama_siswa']) ?></td>
                                <td><?= ks_h($row['nama_kelas']) ?></td>
                                <td><?= ks_h($row['nama_ta']) ?></td>
                                <td><?= ks_h($row['tanggal_mulai']) ?></td>
                                <td><?= ks_h($row['tanggal_selesai']) ?></td>
                                <td>
                                    <?php if ((int)$row['status_aktif'] === 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <a class="btn btn-sm btn-warning" href="?edit=<?= (int)$row['id'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a class="btn btn-sm btn-danger" href="?hapus=<?= (int)$row['id'] ?>"
                                        onclick="return confirm('Yakin ingin menghapus penempatan siswa ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Data penempatan siswa tidak ditemukan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
