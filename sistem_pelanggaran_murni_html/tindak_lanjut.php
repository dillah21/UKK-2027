<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = intval($_POST['siswa_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'Belum Dipanggil';
    $catatan = trim($_POST['catatan'] ?? '');

    if ($siswa_id > 0) {
        $stmt = $koneksi->prepare("INSERT INTO t_tindak_lanjut (siswa_id, tanggal_tindak_lanjut, status, catatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $siswa_id, $tanggal, $status, $catatan);
        if ($stmt->execute()) {
            $message = "Status tindak lanjut berhasil diperbarui.";
        } else {
            $message = "Gagal menyimpan: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch Siswa yang Pernah Melakukan Pelanggaran
$list_siswa = $koneksi->query("SELECT DISTINCT s.id, s.nisn, s.nama FROM t_siswa s JOIN t_riwayat_pelanggaran r ON s.id = r.siswa_id ORDER BY s.nama ASC");

// Fetch List Tindak Lanjut
$sql_tl = "SELECT tl.*, s.nama AS nama_siswa, s.nisn 
           FROM t_tindak_lanjut tl 
           JOIN t_siswa s ON tl.siswa_id = s.id 
           ORDER BY tl.id DESC";
$res_tl = $koneksi->query($sql_tl);

include 'header.php';
?>

<h2>Catatan Tindak Lanjut BK / Pembinaan</h2>
<p>Pantau perkembangan penanganan siswa bermasalah dan kehadiran orang tua.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<form method="POST" action="tindak_lanjut.php">
    <table border="0" cellpadding="5">
        <tr>
            <td><label for="siswa_id">Siswa (Bermasalah)</label></td>
            <td>: 
                <select id="siswa_id" name="siswa_id" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php if ($list_siswa): ?>
                        <?php while ($s = $list_siswa->fetch_assoc()): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['nama']); ?> (NISN: <?= htmlspecialchars($s['nisn']); ?>)</option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="tanggal">Tanggal Pertemuan/Proses</label></td>
            <td>: <input type="date" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="status">Status Tindak Lanjut</label></td>
            <td>: 
                <select id="status" name="status" required>
                    <option value="Belum Dipanggil">Belum Dipanggil</option>
                    <option value="Proses Pembinaan">Proses Pembinaan BK</option>
                    <option value="Orang Tua Hadir">Orang Tua/Wali Hadir</option>
                    <option value="Selesai">Selesai / Kasus Ditutup</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="catatan">Catatan / Hasil Pembinaan</label></td>
            <td>: <textarea id="catatan" name="catatan" rows="3" cols="40" placeholder="Hasil diskusi dengan siswa/ortu..."></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit">Simpan Tindak Lanjut</button></td>
        </tr>
    </table>
</form>

<hr>

<h3>Riwayat Pembinaan & Tindak Lanjut</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Siswa</th>
            <th>Status Tindak Lanjut</th>
            <th>Catatan Pembinaan</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($res_tl && $res_tl->num_rows > 0): ?>
            <?php $no = 1; while ($row = $res_tl->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_tindak_lanjut'])); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?> (<?= htmlspecialchars($row['nisn']); ?>)</td>
                    <td><b><?= htmlspecialchars($row['status']); ?></b></td>
                    <td><?= htmlspecialchars($row['catatan'] ?? '-'); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Belum ada catatan tindak lanjut.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>