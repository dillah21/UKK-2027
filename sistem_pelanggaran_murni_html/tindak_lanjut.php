<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Tabel fitur pembinaan dibuat otomatis bila belum tersedia.
$koneksi->query("
    CREATE TABLE IF NOT EXISTS t_tindak_lanjut (
        id INT AUTO_INCREMENT PRIMARY KEY,
        siswa_id INT NOT NULL,
        tanggal_tindak_lanjut DATE NOT NULL,
        status VARCHAR(40) NOT NULL,
        catatan TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_tindak_lanjut_siswa (siswa_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id_get = (int)$_GET['edit'];
    $qedit = $koneksi->prepare("SELECT * FROM t_tindak_lanjut WHERE id=?");
    $qedit->bind_param('i', $edit_id_get); $qedit->execute();
    $edit_data = $qedit->get_result()->fetch_assoc(); $qedit->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = (int)($_POST['id'] ?? 0);
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? date('Y-m-d'));
    $status = trim($_POST['status'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');
    $status_valid = ['Belum Dipanggil', 'Proses Pembinaan', 'Orang Tua Hadir', 'Selesai'];

    if ($siswa_id <= 0 || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tanggal) || !in_array($status, $status_valid, true)) {
        $message = 'Data tindak lanjut tidak valid.';
    } else {
        $cek = $koneksi->prepare("SELECT id FROM t_siswa WHERE id = ? LIMIT 1");
        $cek->bind_param('i', $siswa_id);
        $cek->execute();
        $ada = $cek->get_result()->num_rows > 0;
        $cek->close();

        if (!$ada) {
            $message = 'Siswa tidak ditemukan.';
        } else {
            if ($edit_id > 0) {
                $stmt = $koneksi->prepare("UPDATE t_tindak_lanjut SET siswa_id=?, tanggal_tindak_lanjut=?, status=?, catatan=? WHERE id=?");
                $stmt->bind_param('isssi', $siswa_id, $tanggal, $status, $catatan, $edit_id);
                $stmt->execute(); $stmt->close();
                $message = 'Tindak lanjut berhasil diperbarui.';
            } else {
                $stmt = $koneksi->prepare("INSERT INTO t_tindak_lanjut (siswa_id, tanggal_tindak_lanjut, status, catatan) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('isss', $siswa_id, $tanggal, $status, $catatan);
                $stmt->execute(); $stmt->close();
                $message = 'Status tindak lanjut berhasil disimpan.';
            }
        }
    }
}

$list_siswa = $koneksi->query("
    SELECT DISTINCT s.id, s.nisn, s.nama
    FROM t_siswa s
    INNER JOIN t_pelanggaran_siswa p ON p.siswa_id = s.id
    ORDER BY s.nama ASC
");

$res_tl = $koneksi->query("
    SELECT tl.*, s.nama AS nama_siswa, s.nisn
    FROM t_tindak_lanjut tl
    INNER JOIN t_siswa s ON tl.siswa_id = s.id
    ORDER BY tl.tanggal_tindak_lanjut DESC, tl.id DESC
");

function tl_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
include 'header.php';
?>
<div class="container-fluid py-3">
    <h2>Catatan Tindak Lanjut BK / Pembinaan</h2>
    <p class="text-muted">Pantau perkembangan pembinaan dan komunikasi dengan orang tua/wali.</p>
    <?php if ($message !== ''): ?><div class="alert alert-info"><?= tl_h($message) ?></div><?php endif; ?>
    <div class="card shadow-sm mb-4"><div class="card-body">
        <form method="POST" action="tindak_lanjut.php">
            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= (int)$edit_data["id"] ?>"><?php endif; ?>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="siswa_id">Siswa</label>
                    <select id="siswa_id" name="siswa_id" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = $list_siswa->fetch_assoc()): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= (int)($edit_data['siswa_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= tl_h($s['nama']) ?> (NISN: <?= tl_h($s['nisn']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= tl_h($edit_data["tanggal_tindak_lanjut"] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Belum Dipanggil" <?= ($edit_data["status"] ?? "") === "Belum Dipanggil" ? "selected" : "" ?>>Belum Dipanggil</option>
                        <option value="Proses Pembinaan" <?= ($edit_data["status"] ?? "") === "Proses Pembinaan" ? "selected" : "" ?>>Proses Pembinaan BK</option>
                        <option value="Orang Tua Hadir" <?= ($edit_data["status"] ?? "") === "Orang Tua Hadir" ? "selected" : "" ?>>Orang Tua/Wali Hadir</option>
                        <option value="Selesai" <?= ($edit_data["status"] ?? "") === "Selesai" ? "selected" : "" ?>>Selesai / Kasus Ditutup</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan / Hasil Pembinaan</label>
                <textarea id="catatan" name="catatan" class="form-control" rows="3"><?= tl_h($edit_data["catatan"] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Simpan Perubahan' : 'Simpan Tindak Lanjut' ?></button>
            <?php if ($edit_data): ?><a href="tindak_lanjut.php" class="btn btn-light border">Batal Edit</a><?php endif; ?>
        </form>
    </div></div>
    <div class="card shadow-sm"><div class="card-header"><strong>Riwayat Pembinaan & Tindak Lanjut</strong></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark"><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Status</th><th>Catatan Pembinaan</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if ($res_tl->num_rows): $no=1; while ($row=$res_tl->fetch_assoc()): ?>
                <tr><td><?= $no++ ?></td><td><?= tl_h(date('d-m-Y', strtotime($row['tanggal_tindak_lanjut']))) ?></td><td><?= tl_h($row['nama_siswa']) ?> (<?= tl_h($row['nisn']) ?>)</td><td><?= tl_h($row['status']) ?></td><td><?= nl2br(tl_h($row['catatan'] ?: '-')) ?></td><td><a class="btn btn-warning btn-sm" href="tindak_lanjut.php?edit=<?= (int)$row['id'] ?>"><i class="fas fa-edit"></i> Edit</a></td></tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" class="text-center">Belum ada catatan tindak lanjut.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
</div>
<?php include 'footer.php'; ?>
