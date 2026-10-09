<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil Parameter Filter dari Method GET
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
$kelas_id = intval($_GET['kelas_id'] ?? 0);

// Base Query Laporan
$sql = "SELECT r.id, r.tanggal, s.nisn, s.nama AS nama_siswa, k.nama AS nama_kelas, 
               p.nama AS nama_pelanggaran, p.poin
        FROM t_riwayat_pelanggaran r
        JOIN t_siswa s ON r.siswa_id = s.id
        LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        JOIN t_pelanggaran p ON r.pelanggaran_id = p.id
        WHERE 1=1";

// Terapkan Filter
if (!empty($tgl_mulai)) {
    $sql .= " AND r.tanggal >= '" . $koneksi->real_escape_string($tgl_mulai) . "'";
}

if (!empty($tgl_selesai)) {
    $sql .= " AND r.tanggal <= '" . $koneksi->real_escape_string($tgl_selesai) . "'";
}

if ($kelas_id > 0) {
    $sql .= " AND k.id = " . $kelas_id;
}

$sql .= " ORDER BY r.tanggal DESC, r.id DESC";
$result = $koneksi->query($sql);

// Fetch Master Kelas untuk Dropdown
$list_kelas = $koneksi->query("SELECT id, nama FROM t_kelas ORDER BY nama ASC");

// Query String untuk URL Export Excel
$export_param = "tgl_mulai=" . urlencode($tgl_mulai) . "&tgl_selesai=" . urlencode($tgl_selesai) . "&kelas_id=" . $kelas_id;

include 'header.php';
?>

<h2>Laporan Pelanggaran Siswa</h2>
<p>Saring, cetak, atau unduh rekapitulasi data pelanggaran siswa ke format Excel/CSV.</p>

<!-- Form Filter -->
<form method="GET" action="laporan.php">
    <table border="0" cellpadding="5">
        <tr>
            <td><label for="tgl_mulai">Dari Tanggal</label></td>
            <td>: <input type="date" id="tgl_mulai" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai); ?>"></td>
            
            <td><label for="tgl_selesai">Sampai Tanggal</label></td>
            <td>: <input type="date" id="tgl_selesai" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai); ?>"></td>
        </tr>
        <tr>
            <td><label for="kelas_id">Pilih Kelas</label></td>
            <td>: 
                <select id="kelas_id" name="kelas_id">
                    <option value="0">-- Semua Kelas --</option>
                    <?php if ($list_kelas): ?>
                        <?php while ($k = $list_kelas->fetch_assoc()): ?>
                            <option value="<?= $k['id']; ?>" <?= ($kelas_id == $k['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </td>

            <td colspan="2">
                <button type="submit">Filter Data</button>
                <a href="laporan.php">Reset Filter</a> |
                <button type="button" onclick="window.print()">Cetak Laporan</button> |
                <a href="export_excel.php?<?= $export_param; ?>" target="_blank"><b>Unduh Excel / CSV</b></a>
            </td>
        </tr>
    </table>
</form>

<hr>

<!-- Tabel Hasil Laporan -->
<h3>Hasil Laporan Pelanggaran</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Nama Pelanggaran</th>
            <th>Poin</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_poin_semua = 0;
        if ($result && $result->num_rows > 0): 
            $no = 1;
            while ($row = $result->fetch_assoc()): 
                $total_poin_semua += $row['poin'];
        ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                    <td><?= htmlspecialchars($row['nisn']); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggaran']); ?></td>
                    <td><b><?= $row['poin']; ?></b></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="6" align="right"><b>Total Akumulasi Poin Pelanggaran:</b></td>
                <td><b><?= $total_poin_semua; ?> Poin</b></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="7" align="center">Tidak ada data pelanggaran yang sesuai filter.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>