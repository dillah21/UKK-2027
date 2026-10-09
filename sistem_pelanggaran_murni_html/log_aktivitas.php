<?php
require_once 'auth.php';
require_once 'koneksi.php';

include 'header.php';

// Fetch Log Aktivitas
$result_log = $koneksi->query("SELECT * FROM t_log_aktivitas ORDER BY waktu DESC LIMIT 50");
?>

<h2>Audit Log Aktivitas System</h2>
<p>Catatan riwayat aksi pengguna/admin demi transparansi dan keamanan data.</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Waktu / Tanggal</th>
            <th>Pengguna</th>
            <th>Aktivitas</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_log && $result_log->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result_log->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y H:i:s', strtotime($row['waktu'])); ?></td>
                    <td><?= htmlspecialchars($row['nama_user']); ?></td>
                    <td><?= htmlspecialchars($row['aktivitas']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" align="center">Belum ada log aktivitas terrekam.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>