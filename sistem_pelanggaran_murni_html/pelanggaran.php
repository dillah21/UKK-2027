<?php
include 'koneksi.php';
include 'header.php';
?>

<h3>Daftar Pelanggaran Siswa</h3>
<p><a href="tambah_pelanggaran.php">+ Catat Pelanggaran Baru</a></p>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Pelanggaran</th>
            <th>Guru Pelapor</th>
            <th>Poin</th>
            <th>Status</th>
            <th>Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC";
        $result = mysqli_query($koneksi, $query);
        $no = 1;

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
                    <td>".htmlspecialchars($row['nama_siswa'])."</td>
                    <td>".htmlspecialchars($row['nama_kelas'])."</td>
                    <td>".htmlspecialchars($row['nama_pelanggaran'])."</td>
                    <td>".htmlspecialchars($row['nama_guru'])."</td>
                    <td>{$row['poin']} Poin</td>
                    <td>{$row['status']}</td>
                    <td>".htmlspecialchars($row['tindakan'])."</td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='9' align='center'>Belum ada data pelanggaran.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>