
<?php
require_once 'auth.php';
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role_login = strtolower(trim($_SESSION['role'] ?? ''));
$is_guru = ($role_login === 'guru');
$user_id_login = (int)($_SESSION['user_id'] ?? 0);
$guru_id_login = 0;
$nama_guru_login = '';
$show_guru_pelapor = false;

if ($is_guru) {
    $qg = $koneksi->prepare("SELECT id, nama FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1");
    $qg->bind_param('i', $user_id_login);
    $qg->execute();
    $g = $qg->get_result()->fetch_assoc();
    $qg->close();

    $guru_id_login = (int)($g['id'] ?? 0);
    $nama_guru_login = trim((string)($g['nama'] ?? ''));
    $show_guru_pelapor = (strcasecmp($nama_guru_login, 'Ari Sandriana, S.Kom') === 0
        || strcasecmp($nama_guru_login, 'Ari Sandriana') === 0);
}

$pilihan_tindakan = [
    'Teguran lisan',
    'Teguran tertulis',
    'Pembinaan oleh wali kelas',
    'Pembinaan oleh guru BK',
    'Pemanggilan orang tua/wali',
    'Membuat surat pernyataan',
    'Pembinaan bersama wali kelas dan guru BK'
];

$message = '';
$message_type = 'info';

$edit_id = (int)($_GET['edit'] ?? $_POST['pelanggaran_siswa_id'] ?? 0);
$edit_data = null;

$ownWhere = $is_guru ? ' AND ps.guru_id = ' . $guru_id_login : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_tindakan'])) {
    $id = (int)($_POST['pelanggaran_siswa_id'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (
        $id <= 0 ||
        !in_array($tindakan, $pilihan_tindakan, true) ||
        !in_array($status, ['Proses', 'Selesai'], true)
    ) {
        $message = 'Pilih catatan pelanggaran, tindakan pembinaan, dan status yang valid.';
        $message_type = 'danger';
    } else {
        $cek_sql = "
            SELECT ps.id
            FROM t_pelanggaran_siswa ps
            INNER JOIN t_kelas k ON k.id = ps.kelas_id
            WHERE ps.id = ?
            AND UPPER(TRIM(k.nama)) IN ('12 RPL 1', '12 RPL 2')
            AND COALESCE(LOWER(TRIM(ps.status)), 'proses') <> 'selesai'
            " . ($is_guru ? ' AND ps.guru_id = ?' : '') . "
            LIMIT 1
        ";

        $cek = $koneksi->prepare($cek_sql);

        if ($is_guru) {
            $cek->bind_param('ii', $id, $guru_id_login);
        } else {
            $cek->bind_param('i', $id);
        }

        $cek->execute();
        $boleh_update = $cek->get_result()->num_rows > 0;
        $cek->close();

        if (!$boleh_update) {
            $message = 'Catatan tidak ditemukan, bukan milik akun ini, atau statusnya sudah Selesai.';
            $message_type = 'danger';
        } else {
            $stmt = $koneksi->prepare("
                UPDATE t_pelanggaran_siswa
                SET tindakan = ?, status = ?
                WHERE id = ?
            ");

            $stmt->bind_param('ssi', $tindakan, $status, $id);
            $stmt->execute();
            $stmt->close();

            $message = 'Tindakan pembinaan berhasil disimpan.';
            $message_type = 'success';
            $edit_id = 0;
        }
    }
}

// Ambil data untuk form edit
if ($edit_id > 0) {
    $sql = "
        SELECT ps.*, s.nis, s.nisn
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s ON s.id = ps.siswa_id
        INNER JOIN t_kelas k ON k.id = ps.kelas_id
        WHERE ps.id = ?
        AND UPPER(TRIM(k.nama)) IN ('12 RPL 1', '12 RPL 2')
        AND COALESCE(LOWER(TRIM(ps.status)), 'proses') <> 'selesai'
        " . ($is_guru ? ' AND ps.guru_id = ?' : '') . "
        LIMIT 1
    ";

    $q = $koneksi->prepare($sql);

    if ($is_guru) {
        $q->bind_param('ii', $edit_id, $guru_id_login);
    } else {
        $q->bind_param('i', $edit_id);
    }

    $q->execute();
    $edit_data = $q->get_result()->fetch_assoc();
    $q->close();

    if (!$edit_data) {
        $edit_id = 0;
        $message = 'Catatan tidak ditemukan, bukan milik akun ini, atau statusnya sudah Selesai.';
        $message_type = 'warning';
    }
}

// Ambil daftar catatan pelanggaran
$sql_list = "
    SELECT ps.*, s.nis, s.nisn
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s ON s.id = ps.siswa_id
    INNER JOIN t_kelas k ON k.id = ps.kelas_id
    WHERE UPPER(TRIM(k.nama)) IN ('12 RPL 1', '12 RPL 2')
    " . $ownWhere . "
    ORDER BY ps.tanggal DESC, ps.id DESC
";

$res_tl = $koneksi->query($sql_list);

function tl_h($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

include 'header.php';
?>

<div class="container-fluid py-3">

    <h2>Tindakan / Tindak Lanjut BK</h2>

    <p class="text-muted">
        Data pada halaman ini bersumber dari catatan yang sudah dibuat melalui menu Catat Pelanggaran.
    </p>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= tl_h($message_type) ?> alert-dismissible fade show" role="alert">
            <?= tl_h($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($edit_data): ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <strong>Isi Tindakan Pembinaan</strong>
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-4">
                        <label>Siswa</label>
                        <input
                            class="form-control"
                            value="<?= tl_h($edit_data['nama_siswa']) ?> (NIS: <?= tl_h($edit_data['nis']) ?>)"
                            readonly
                        >
                    </div>

                    <div class="form-group col-md-4">
                        <label>Kelas</label>
                        <input
                            class="form-control"
                            value="<?= tl_h($edit_data['nama_kelas']) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group col-md-4">
                        <label>Jenis Pelanggaran</label>
                        <input
                            class="form-control"
                            value="<?= tl_h($edit_data['nama_pelanggaran']) ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group col-md-4">
                        <label>Tanggal Kejadian</label>
                        <input
                            class="form-control"
                            value="<?= tl_h(date('d-m-Y', strtotime($edit_data['tanggal']))) ?>"
                            readonly
                        >
                    </div>

                    <?php if (!$is_guru || $show_guru_pelapor): ?>
                    <div class="form-group col-md-4">
                        <label>Guru Pelapor</label>
                        <input class="form-control" value="<?= tl_h($edit_data['nama_guru'] ?? '-') ?>" readonly>
                    </div>
                    <?php endif; ?>

                    <div class="form-group col-md-4">
                        <label>Poin</label>
                        <input
                            class="form-control"
                            value="<?= (int)$edit_data['poin'] ?> poin"
                            readonly
                        >
                    </div>

                </div>

                <form method="POST" action="tindak_lanjut.php">

                    <input
                        type="hidden"
                        name="pelanggaran_siswa_id"
                        value="<?= (int)$edit_data['id'] ?>"
                    >

                    <div class="form-row">

                        <div class="form-group col-md-8">
                            <label>Tindakan Pembinaan</label>

                            <select name="tindakan" class="form-control" required>
                                <option value="">-- Pilih Tindakan Pembinaan --</option>

                                <?php foreach ($pilihan_tindakan as $opsi): ?>
                                    <option
                                        value="<?= tl_h($opsi) ?>"
                                        <?= ($edit_data['tindakan'] ?? '') === $opsi ? 'selected' : '' ?>
                                    >
                                        <?= tl_h($opsi) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Status Pembinaan</label>

                            <select name="status" class="form-control" required>
                                <option
                                    value="Proses"
                                    <?= ($edit_data['status'] ?? '') === 'Proses' ? 'selected' : '' ?>
                                >
                                    Proses
                                </option>

                                <option
                                    value="Selesai"
                                    <?= ($edit_data['status'] ?? '') === 'Selesai' ? 'selected' : '' ?>
                                >
                                    Selesai
                                </option>
                            </select>
                        </div>

                    </div>

                    <button type="submit" name="simpan_tindakan" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Tindakan
                    </button>

                    <a href="tindak_lanjut.php" class="btn btn-light border">
                        Batal
                    </a>

                </form>

            </div>
        </div>

    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Data Catatan Pelanggaran</strong>
        </div>

        <div class="card-body table-responsive">

            <table id="tabelTindakLanjut" class="table table-bordered table-striped table-hover">

                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Pelanggaran</th>
                        <?php if (!$is_guru || $show_guru_pelapor): ?><th>Guru Pelapor</th><?php endif; ?>
                        <th>Poin</th>
                        <th>Tindakan Pembinaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php while ($row = $res_tl->fetch_assoc()): ?>

                        <tr>

                            <td><?= $no++ ?></td>

                            <td>
                                <?= tl_h(date('d-m-Y', strtotime($row['tanggal']))) ?>
                            </td>

                            <td>
                                <?= tl_h($row['nama_siswa']) ?>
                                (<?= tl_h($row['nis'] ?? '') ?>)
                            </td>

                            <td>
                                <?= tl_h($row['nama_kelas']) ?>
                            </td>

                            <td>
                                <?= tl_h($row['nama_pelanggaran']) ?>
                            </td>

                            <?php if (!$is_guru || $show_guru_pelapor): ?>
                            <td>
                                <?= tl_h($row['nama_guru'] ?? '-') ?>
                            </td>
                            <?php endif; ?>

                            <td class="text-center">
                                <?= (int)$row['poin'] ?>
                            </td>

                            <td>
                                <?= tl_h($row['tindakan'] ?: 'Belum ditentukan') ?>
                            </td>

                            <td>
                                <span class="badge <?= ($row['status'] ?? '') === 'Selesai' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= tl_h($row['status'] ?: 'Proses') ?>
                                </span>
                            </td>

                            <td>
                                <?php if (strtolower(trim((string)($row['status'] ?? ''))) !== 'selesai'): ?>

                                    <a
                                        class="btn btn-warning btn-sm"
                                        href="tindak_lanjut.php?edit=<?= (int)$row['id'] ?>"
                                    >
                                        <i class="fas fa-edit"></i>
                                        <?= trim((string)$row['tindakan']) === '' ? 'Isi Tindakan' : 'Edit' ?>
                                    </a>

                                <?php else: ?>

                                    <span class="text-muted">-</span>

                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>