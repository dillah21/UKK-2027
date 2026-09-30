<?php
include 'koneksi.php';
include 'header.php';

$q_siswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_siswa WHERE status_aktif = 1");
$t_siswa = mysqli_fetch_assoc($q_siswa)['total'] ?? 0;

$q_guru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru WHERE status_aktif = 1");
$t_guru = mysqli_fetch_assoc($q_guru)['total'] ?? 0;

$q_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
$t_pelanggaran = mysqli_fetch_assoc($q_pelanggaran)['total'] ?? 0;

$q_proses = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa WHERE status = 'Proses'");
$t_proses = mysqli_fetch_assoc($q_proses)['total'] ?? 0;
?>

<h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>!</h3>
<p>Dashboard Ringkasan Data Pelanggaran Siswa</p>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <td><strong>Total Siswa Aktif:</strong> <?= $t_siswa; ?></td>
        <td><strong>Total Guru:</strong> <?= $t_guru; ?></td>
        <td><strong>Total Catatan Pelanggaran:</strong> <?= $t_pelanggaran; ?></td>
        <td><strong>Dalam Pembinaan (Proses):</strong> <?= $t_proses; ?></td>
    </tr>
</table>

<br>
<h3>5 Pelanggaran Terakhir</h3>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Pelanggaran</th>
            <th>Poin</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $q_recent = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC LIMIT 5");
        if ($q_recent && mysqli_num_rows($q_recent) > 0) {
            while ($r = mysqli_fetch_assoc($q_recent)) {
                echo "<tr>
                    <td>".date('d-m-Y', strtotime($r['tanggal']))."</td>
                    <td>".htmlspecialchars($r['nama_siswa'])."</td>
                    <td>".htmlspecialchars($r['nama_kelas'])."</td>
                    <td>".htmlspecialchars($r['nama_pelanggaran'])."</td>
                    <td>{$r['poin']} Poin</td>
                    <td>{$r['status']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6' align='center'>Belum ada catatan pelanggaran terbaru.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>