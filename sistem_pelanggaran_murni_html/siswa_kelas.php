<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

// Ambil Data Siswa Khusus Wali Kelas
$sql = "SELECT s.id, s.nisn, s.nama AS nama_siswa, k.nama AS nama_kelas,
               COALESCE(SUM(p.poin), 0) AS total_poin
        FROM t_siswa s
        JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
        JOIN t_kelas k ON ks.kelas_id = k.id
        LEFT JOIN t_riwayat_pelanggaran r ON s.id = r.siswa_id
        LEFT JOIN t_pelanggaran p ON r.pelanggaran_id = p.id
        WHERE k.wali_kelas_id = $user_id
        GROUP BY s.id, s.nisn, s.nama, k.nama
        ORDER BY total_poin DESC, s.nama ASC";

$result = $koneksi->query($sql);

include 'header.php';
?>

<h2>Monitoring Pelanggaran Kelas Bimbingan</h2>
<p>Daftar seluruh siswa di kelas bimbingan Anda beserta akumulasi poin pelanggarannya.</p>

<hr>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Total Poin</th>
            <th>Status Sanksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <?php
                $poin = $row['total_poin'];
                $status = 'Aman';
                if ($poin >= 50) {
                    $status = 'Skorsing / SP3';
                } elseif ($poin >= 25) {
                    $status = 'Panggilan Ortu / SP2';
                } elseif ($poin >= 10) {
                    $status = 'Teguran / SP1';
                }
                ?>
                <tr>
                    <td align="center"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nisn']); ?></td>
                    <td><b><?= htmlspecialchars($row['nama_siswa']); ?></b></td>
                    <td align="center"><?= htmlspecialchars($row['nama_kelas']); ?></td>
                    <td align="center"><b><?= $poin; ?> Poin</b></td>
                    <td><b><?= $status; ?></b></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" align="center">Belum ada data siswa di kelas bimbingan Anda.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>