<?php
require_once 'auth.php';
require_once 'koneksi.php';

include 'header.php';

// Query Rekap Total Poin Siswa
$sql = "SELECT s.id, s.nisn, s.nama AS nama_siswa, k.nama AS nama_kelas,
        COALESCE(SUM(p.poin), 0) AS total_poin
        FROM t_siswa s
        LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        LEFT JOIN t_riwayat_pelanggaran r ON s.id = r.siswa_id
        LEFT JOIN t_pelanggaran p ON r.pelanggaran_id = p.id
        GROUP BY s.id
        ORDER BY total_poin DESC, s.nama ASC";

$result = $koneksi->query($sql);

// Fungsi Menentukan Status Sanksi
function getStatusSanksi($poin) {
    if ($poin >= 100) {
        return "Dikembalikan ke Orang Tua / DO";
    } elseif ($poin >= 50) {
        return "Surat Peringatan III (Skorsing)";
    } elseif ($poin >= 30) {
        return "Surat Peringatan II";
    } elseif ($poin >= 15) {
        return "Surat Peringatan I";
    } elseif ($poin > 0) {
        return "Teguran Lisan";
    } else {
        return "Bersih (Tidak Ada Poin)";
    }
}
?>

<h2>Rekapitulasi Poin & Status Sanksi Siswa</h2>
<p>Daftar akumulasi poin pelanggaran beserta rekomendasi sanksi otomatis.</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Total Poin</th>
            <th>Rekomendasi Sanksi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nisn']); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas'] ?? 'Belum Ada Kelas'); ?></td>
                    <td><b><?= $row['total_poin']; ?></b></td>
                    <td><b><?= getStatusSanksi($row['total_poin']); ?></b></td>
                    <td>
                        <?php if ($row['total_poin'] >= 15): ?>
                            <a href="cetak_sp.php?siswa_id=<?= $row['id']; ?>" target="_blank">Cetak Surat SP</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" align="center">Belum ada data rekap poin.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>