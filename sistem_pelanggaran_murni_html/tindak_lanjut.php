<?php
require_once 'auth.php';
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$message = '';
$message_type = 'info';
$edit_id = (int)($_GET['edit'] ?? $_POST['id'] ?? 0);
$edit_data = null;

// Buat tabel baru dengan kolom kelas dan tahun ajaran.
$koneksi->query("CREATE TABLE IF NOT EXISTS t_tindak_lanjut (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    kelas_id INT NOT NULL DEFAULT 0,
    tahun_ajaran_id INT NOT NULL DEFAULT 0,
    tanggal_tindak_lanjut DATE NOT NULL,
    status VARCHAR(40) NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tindak_lanjut_siswa (siswa_id),
    INDEX idx_tindak_lanjut_kelas (kelas_id),
    INDEX idx_tindak_lanjut_tahun (tahun_ajaran_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Migrasi ringan untuk instalasi yang sudah memiliki tabel lama.
$kolom_baru = [
    'kelas_id' => "ALTER TABLE t_tindak_lanjut ADD COLUMN kelas_id INT NOT NULL DEFAULT 0 AFTER siswa_id",
    'tahun_ajaran_id' => "ALTER TABLE t_tindak_lanjut ADD COLUMN tahun_ajaran_id INT NOT NULL DEFAULT 0 AFTER kelas_id"
];
foreach ($kolom_baru as $kolom => $sql_alter) {
    $cek_kolom = $koneksi->query("SHOW COLUMNS FROM t_tindak_lanjut LIKE '" . $koneksi->real_escape_string($kolom) . "'");
    if ($cek_kolom->num_rows === 0) $koneksi->query($sql_alter);
}

// Ambil tahun ajaran aktif.
$tahun_result = $koneksi->query("SELECT id, nama FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC, id DESC LIMIT 1");
$tahun_aktif = $tahun_result->fetch_assoc();
$tahun_ajaran_aktif_id = (int)($tahun_aktif['id'] ?? 0);

if ($edit_id > 0) {
    $qedit = $koneksi->prepare("SELECT * FROM t_tindak_lanjut WHERE id = ? LIMIT 1");
    $qedit->bind_param('i', $edit_id);
    $qedit->execute();
    $edit_data = $qedit->get_result()->fetch_assoc();
    $qedit->close();
    if (!$edit_data) {
        $edit_id = 0;
        $message = 'Data tindak lanjut tidak ditemukan.';
        $message_type = 'warning';
    }
}

$status_valid = ['Belum Dipanggil', 'Proses Pembinaan', 'Orang Tua Hadir', 'Selesai'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $tanggal_valid = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) &&
        checkdate((int)substr($tanggal, 5, 2), (int)substr($tanggal, 8, 2), (int)substr($tanggal, 0, 4));

    if ($tahun_ajaran_aktif_id <= 0) {
        $message = 'Belum ada tahun ajaran aktif. Hubungi administrator.';
        $message_type = 'danger';
    } elseif ($siswa_id <= 0 || $kelas_id <= 0 || !$tanggal_valid || !in_array($status, $status_valid, true)) {
        $message = 'Data tindak lanjut tidak valid. Periksa siswa, kelas, tanggal, dan status.';
        $message_type = 'danger';
    } else {
        $cek = $koneksi->prepare("SELECT id FROM t_siswa WHERE id = ? LIMIT 1");
        $cek->bind_param('i', $siswa_id);
        $cek->execute();
        $siswa_ada = $cek->get_result()->num_rows > 0;
        $cek->close();

        $cek = $koneksi->prepare("SELECT id FROM t_kelas WHERE id = ? AND status_aktif = 1 LIMIT 1");
        $cek->bind_param('i', $kelas_id);
        $cek->execute();
        $kelas_ada = $cek->get_result()->num_rows > 0;
        $cek->close();

        if (!$siswa_ada || !$kelas_ada) {
            $message = 'Siswa atau kelas tidak ditemukan / tidak aktif.';
            $message_type = 'danger';
        } else {
            if ($edit_id > 0 && $edit_data) {
                $stmt = $koneksi->prepare("UPDATE t_tindak_lanjut SET siswa_id=?, kelas_id=?, tahun_ajaran_id=?, tanggal_tindak_lanjut=?, status=? WHERE id=?");
                $stmt->bind_param('iiissi', $siswa_id, $kelas_id, $tahun_ajaran_aktif_id, $tanggal, $status, $edit_id);
                $stmt->execute();
                $stmt->close();
                $message = 'Perubahan tindak lanjut berhasil disimpan.';
                $message_type = 'success';
                $edit_data = null;
                $edit_id = 0;
            } else {
                $stmt = $koneksi->prepare("INSERT INTO t_tindak_lanjut (siswa_id, kelas_id, tahun_ajaran_id, tanggal_tindak_lanjut, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('iiiss', $siswa_id, $kelas_id, $tahun_ajaran_aktif_id, $tanggal, $status);
                $stmt->execute();
                $stmt->close();
                $message = 'Tindak lanjut berhasil ditambahkan.';
                $message_type = 'success';
            }
        }
    }

    if ($message_type === 'danger' && $edit_id > 0 && $edit_data) {
        $edit_data['siswa_id'] = $siswa_id;
        $edit_data['kelas_id'] = $kelas_id;
        $edit_data['tanggal_tindak_lanjut'] = $tanggal;
        $edit_data['status'] = $status;
    }
}

$list_siswa = $koneksi->query("SELECT id, nisn, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama ASC");
$list_kelas = $koneksi->query("SELECT id, nama FROM t_kelas WHERE status_aktif = 1 ORDER BY nama ASC");
$res_tl = $koneksi->query("SELECT tl.*, s.nama AS nama_siswa, s.nisn, k.nama AS nama_kelas, ta.nama AS nama_tahun_ajaran
    FROM t_tindak_lanjut tl
    INNER JOIN t_siswa s ON tl.siswa_id = s.id
    LEFT JOIN t_kelas k ON tl.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta ON tl.tahun_ajaran_id = ta.id
    ORDER BY tl.tanggal_tindak_lanjut DESC, tl.id DESC");

function tl_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
include 'header.php';
?>
<div class="container-fluid py-3">
    <h2>Tindakan / Tindak Lanjut BK</h2>
    <p class="text-muted">Pantau perkembangan pembinaan siswa berdasarkan kelas dan tahun ajaran aktif.</p>
    <?php if ($message !== ''): ?><div class="alert alert-<?= tl_h($message_type) ?> alert-dismissible fade show" role="alert"><?= tl_h($message) ?><button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><?php endif; ?>

    <div class="card shadow-sm mb-4"><div class="card-body">
        <form method="POST" action="tindak_lanjut.php<?= ($edit_id > 0 && $edit_data) ? '?edit=' . (int)$edit_id : '' ?>">
            <?php if ($edit_id > 0 && $edit_data): ?><input type="hidden" name="id" value="<?= (int)$edit_id ?>"><?php endif; ?>
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
                <div class="form-group col-md-6">
                    <label for="kelas_id">Kelas</label>
                    <select id="kelas_id" name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = $list_kelas->fetch_assoc()): ?>
                            <option value="<?= (int)$k['id'] ?>" <?= (int)($edit_data['kelas_id'] ?? 0) === (int)$k['id'] ? 'selected' : '' ?>><?= tl_h($k['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Tahun Ajaran Aktif</label>
                    <input type="text" class="form-control" value="<?= tl_h($tahun_aktif['nama'] ?? 'Belum ada tahun ajaran aktif') ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= tl_h($edit_data['tanggal_tindak_lanjut'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Belum Dipanggil" <?= ($edit_data['status'] ?? '') === 'Belum Dipanggil' ? 'selected' : '' ?>>Belum Dipanggil</option>
                        <option value="Proses Pembinaan" <?= ($edit_data['status'] ?? '') === 'Proses Pembinaan' ? 'selected' : '' ?>>Proses Pembinaan BK</option>
                        <option value="Orang Tua Hadir" <?= ($edit_data['status'] ?? '') === 'Orang Tua Hadir' ? 'selected' : '' ?>>Orang Tua/Wali Hadir</option>
                        <option value="Selesai" <?= ($edit_data['status'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai / Kasus Ditutup</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i><?= ($edit_id > 0 && $edit_data) ? 'Simpan Perubahan' : 'Simpan Tindak Lanjut' ?></button>
            <?php if ($edit_id > 0 && $edit_data): ?><a href="tindak_lanjut.php" class="btn btn-light border">Batal Edit</a><?php endif; ?>
        </form>
    </div></div>

    <div class="card shadow-sm"><div class="card-header"><strong>Riwayat Tindakan / Tindak Lanjut</strong></div>
        <div class="card-body table-responsive">
            <table id="tabelTindakLanjut" class="table table-bordered table-striped table-hover">
                <thead class="thead-dark"><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Tahun Ajaran</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if ($res_tl->num_rows): $no = 1; while ($row = $res_tl->fetch_assoc()): ?>
                    <tr><td><?= $no++ ?></td><td><?= tl_h(date('d-m-Y', strtotime($row['tanggal_tindak_lanjut']))) ?></td><td><?= tl_h($row['nama_siswa']) ?> (<?= tl_h($row['nisn']) ?>)</td><td><?= tl_h($row['nama_kelas'] ?: '-') ?></td><td><?= tl_h($row['nama_tahun_ajaran'] ?: '-') ?></td><td><?= tl_h($row['status']) ?></td><td><a class="btn btn-warning btn-sm" href="tindak_lanjut.php?edit=<?= (int)$row['id'] ?>"><i class="fas fa-edit"></i> Edit</a></td></tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center">Belum ada data tindak lanjut.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
(function () {
    function initTabelTindakLanjut() {
        if (window.jQuery && window.jQuery.fn.DataTable && !window.jQuery.fn.dataTable.isDataTable('#tabelTindakLanjut')) {
            window.jQuery('#tabelTindakLanjut').DataTable({
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
                order: [],
                autoWidth: false,
                language: {
                    search: 'Cari:', searchPlaceholder: 'Cari siswa, kelas, atau status...',
                    lengthMenu: 'Tampilkan _MENU_ data', zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data tersedia', infoFiltered: '(difilter dari _MAX_ total data)',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
                }
            });
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initTabelTindakLanjut);
    else initTabelTindakLanjut();
})();
</script>
<?php include 'footer.php'; ?>
