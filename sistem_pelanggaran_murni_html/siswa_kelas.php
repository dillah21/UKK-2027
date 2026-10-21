
<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Tambah dan edit penempatan siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $siswa_id = intval($_POST['siswa_id'] ?? 0);
    $tahun_ajaran_id = intval($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'tambah') {
        $stmt = $koneksi->prepare(
            "INSERT INTO t_kelas_siswa
            (siswa_id, tahun_ajaran_id, kelas_id,
             tanggal_mulai, tanggal_selesai, status_aktif)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iiissi",
            $siswa_id,
            $tahun_ajaran_id,
            $kelas_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_aktif
        );

        $message = $stmt->execute()
            ? 'Penempatan siswa berhasil ditambahkan.'
            : 'Gagal menambahkan penempatan siswa.';

        $stmt->close();

    } elseif ($action === 'edit' && $id > 0) {
        $stmt = $koneksi->prepare(
            "UPDATE t_kelas_siswa
             SET siswa_id=?, tahun_ajaran_id=?, kelas_id=?,
                 tanggal_mulai=?, tanggal_selesai=?,
                 status_aktif=?, updated_at=NOW()
             WHERE id=?"
        );

        $stmt->bind_param(
            "iiissii",
            $siswa_id,
            $tahun_ajaran_id,
            $kelas_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_aktif,
            $id
        );

        $message = $stmt->execute()
            ? 'Penempatan siswa berhasil diperbarui.'
            : 'Gagal memperbarui penempatan siswa.';

        $stmt->close();
    }
}

// Hapus penempatan
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);

    $stmt = $koneksi->prepare(
        "DELETE FROM t_kelas_siswa WHERE id=?"
    );
    $stmt->bind_param("i", $id_hapus);

    $message = $stmt->execute()
        ? 'Penempatan siswa berhasil dihapus.'
        : 'Gagal menghapus penempatan siswa.';

    $stmt->close();
}

// Ambil data untuk diedit
$edit_data = null;

if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);

    $stmt = $koneksi->prepare(
        "SELECT * FROM t_kelas_siswa WHERE id=?"
    );
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();

    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Data pilihan dropdown
$list_siswa = $koneksi->query(
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

$list_kelas = $koneksi->query(
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$list_ta = $koneksi->query(
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

// Pencarian data penempatan
$search = trim($_GET['search'] ?? '');

$sql = "SELECT
            ks.*,
            s.nama AS nama_siswa,
            s.nis,
            k.nama AS nama_kelas,
            ta.nama AS nama_ta
        FROM t_kelas_siswa ks
        JOIN t_siswa s ON ks.siswa_id = s.id
        JOIN t_kelas k ON ks.kelas_id = k.id
        JOIN t_tahun_ajaran ta
            ON ks.tahun_ajaran_id = ta.id";

if ($search !== '') {
    $sql .= " WHERE s.nama LIKE ?
              OR s.nis LIKE ?
              OR k.nama LIKE ?
              OR ta.nama LIKE ?
              ORDER BY ks.id DESC";

    $param = "%{$search}%";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param(
        "ssss",
        $param,
        $param,
        $param,
        $param
    );
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY ks.id DESC";
    $result = $koneksi->query($sql);
}

include 'header.php';
?>

<h2>Kelola Penempatan Siswa</h2>

<?php if ($message !== ''): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<h3>
    <?= $edit_data
        ? 'Edit Penempatan Siswa'
        : 'Tambah Penempatan Siswa'; ?>
</h3>

<form method="POST" action="kelas_siswa.php">
    <input type="hidden" name="action"
        value="<?= $edit_data ? 'edit' : 'tambah' ?>">

    <?php if ($edit_data): ?>
        <input type="hidden" name="id"
            value="<?= (int)$edit_data['id'] ?>">
    <?php endif; ?>

    <table cellpadding="6">
        <tr>
            <td>Siswa</td>
            <td>
                <select name="siswa_id" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($s = $list_siswa->fetch_assoc()): ?>
                        <option value="<?= (int)$s['id'] ?>"
                            <?= (($edit_data['siswa_id'] ?? '') == $s['id'])
                                ? 'selected' : '' ?>>
                            <?= htmlspecialchars(
                                $s['nis'] . ' - ' . $s['nama']
                            ) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Kelas</td>
            <td>
                <select name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php while ($k = $list_kelas->fetch_assoc()): ?>
                        <option value="<?= (int)$k['id'] ?>"
                            <?= (($edit_data['kelas_id'] ?? '') == $k['id'])
                                ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Tahun Ajaran</td>
            <td>
                <select name="tahun_ajaran_id" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php while ($ta = $list_ta->fetch_assoc()): ?>
                        <option value="<?= (int)$ta['id'] ?>"
                            <?= (($edit_data['tahun_ajaran_id'] ?? '') == $ta['id'])
                                ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Tanggal Mulai</td>
            <td>
                <input type="date" name="tanggal_mulai"
                    value="<?= htmlspecialchars(
                        $edit_data['tanggal_mulai']
                        ?? date('Y-07-01')
                    ) ?>" required>
            </td>
        </tr>

        <tr>
            <td>Tanggal Selesai</td>
            <td>
                <input type="date" name="tanggal_selesai"
                    value="<?= htmlspecialchars(
                        $edit_data['tanggal_selesai']
                        ?? date('Y-06-30', strtotime('+1 year'))
                    ) ?>" required>
            </td>
        </tr>

        <tr>
            <td>Status</td>
            <td>
                <label>
                    <input type="checkbox" name="status_aktif"
                        <?= ($edit_data === null
                            || !empty($edit_data['status_aktif']))
                            ? 'checked' : '' ?>>
                    Aktif
                </label>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <button type="submit">
                    <?= $edit_data
                        ? 'Simpan Perubahan'
                        : 'Tambah Penempatan' ?>
                </button>

                <?php if ($edit_data): ?>
                    <a href="kelas_siswa.php">Batal</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<hr>

<h3>Daftar Penempatan Siswa</h3>

<form method="GET" action="kelas_siswa.php">
    <input type="text" name="search"
        placeholder="Cari nama, NIS, kelas..."
        value="<?= htmlspecialchars($search) ?>">

    <button type="submit">Cari</button>
    <a href="kelas_siswa.php">Reset</a>
</form>

<br>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tahun Ajaran</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nis']) ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                    <td><?= htmlspecialchars($row['nama_ta']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_mulai']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_selesai']) ?></td>
                    <td>
                        <?= $row['status_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                    </td>
                    <td>
                        <a href="kelas_siswa.php?edit=<?= (int)$row['id'] ?>">
                            Edit
                        </a>
                        |
                        <a href="kelas_siswa.php?hapus=<?= (int)$row['id'] ?>"
                           onclick="return confirm('Yakin ingin menghapus penempatan ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" align="center">
                    Data penempatan siswa tidak ditemukan.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
