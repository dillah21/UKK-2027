<?php
require_once 'auth.php';
require_once 'koneksi.php';

// Handling Filter Siswa & Pencarian
$search = trim($_GET['search'] ?? '');
$filter_siswa_id = intval($_GET['siswa_id'] ?? 0);

// Query Detail Riwayat Pelanggaran
$sql = "SELECT ps.*, s.nis, s.nama AS nama_siswa, k.nama AS nama_kelas, g.nama AS nama_guru, pk.nama AS nama_kategori
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k ON ps.kelas_id = k.id
        LEFT JOIN t_guru g ON ps.guru_id = g.id
        LEFT JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_kategori_id = pk.id";

if ($filter_siswa_id > 0) {
    $sql .= " WHERE ps.siswa_id = " . $filter_siswa_id;
} elseif (!empty($search)) {
    $sql .= " WHERE s.nama LIKE '%" . $koneksi->real_escape_string($search) . "%' 
               OR s.nis LIKE '%" . $koneksi->real_escape_string($search) . "%'";
}

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";
$result = $koneksi->query($sql);

include 'header.php';
?>

<h2>Riwayat Pelanggaran Siswa</h2>

<!-- Form Pencarian -->
<form method="GET" action="riwayat_pelanggaran.php">
    <label for="search">Cari Siswa (Nama / NIS):</label>
    <input type="text" id="search" name="search" placeholder="Masukkan Nama atau NIS..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
    <?php if (!empty($search) || $filter_siswa_id > 0): ?>
        <a href="riwayat_pelanggaran.php">Reset Filter</a>
    <?php endif; ?>
</form>

<br>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Kategori</th>
            <th>Nama Pelanggaran</th>
            <th>Poin</th>
            <th>Tindakan / Pembinaan</th>
            <th>Status</th>
            <th>Guru Pelapor</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                    <td><?= htmlspecialchars($row['nis']); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggaran']); ?></td>
                    <td align="center"><b><?= $row['poin']; ?></b></td>
                    <td><?= htmlspecialchars($row['tindakan']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11" align="center">Belum ada catatan riwayat pelanggaran.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>