<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

// Handling Form Tambah Prestasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = intval($_POST['siswa_id'] ?? 0);
    $nama_prestasi = trim($_POST['nama_prestasi'] ?? '');
    $poin_bonus = intval($_POST['poin_bonus'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if ($siswa_id > 0 && !empty($nama_prestasi) && $poin_bonus > 0) {
        $stmt = $koneksi->prepare("INSERT INTO t_prestasi (siswa_id, nama_prestasi, poin_bonus, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $siswa_id, $nama_prestasi, $poin_bonus, $tanggal);
        if ($stmt->execute()) {
            $message = "Data prestasi/pengurangan poin berhasil ditambahkan!";
            
            // Catat Log Aktivitas
            $log_stmt = $koneksi->prepare("INSERT INTO t_log_aktivitas (nama_user, aktivitas) VALUES ('Admin', ?)");
            $log_txt = "Menambahkan prestasi untuk Siswa ID: $siswa_id (-$poin_bonus poin)";
            $log_stmt->bind_param("s", $log_txt);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            $message = "Gagal menyimpan data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Lengkapi semua form dengan benar!";
    }
}

// Fetch Master Siswa
$list_siswa = $koneksi->query("SELECT id, nisn, nama FROM t_siswa ORDER BY nama ASC");

// Fetch List Prestasi dengan JOIN
$sql_prestasi = "SELECT pr.*, s.nama AS nama_siswa, s.nisn 
                 FROM t_prestasi pr 
                 JOIN t_siswa s ON pr.siswa_id = s.id 
                 ORDER BY pr.tanggal DESC, pr.id DESC";
$result_prestasi = $koneksi->query($sql_prestasi);

include 'header.php';
?>

<h2>Apresiasi Siswa & Pengurangan Poin</h2>
<p>Catat prestasi atau tindakan positif siswa untuk memberikan pengurangan poin pelanggaran.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<form method="POST" action="prestasi.php">
    <table border="0" cellpadding="5">
        <tr>
            <td><label for="siswa_id">Pilih Siswa</label></td>
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
            <td><label for="nama_prestasi">Keterangan Prestasi/Apresiasi</label></td>
            <td>: <input type="text" id="nama_prestasi" name="nama_prestasi" placeholder="Misal: Juara 1 Lomba / Piket Bersih" required size="40"></td>
        </tr>
        <tr>
            <td><label for="poin_bonus">Pengurangan Poin</label></td>
            <td>: <input type="number" id="poin_bonus" name="poin_bonus" min="1" max="100" placeholder="Nilai Poin (Contoh: 10)" required></td>
        </tr>
        <tr>
            <td><label for="tanggal">Tanggal</label></td>
            <td>: <input type="date" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit">Simpan Prestasi</button></td>
        </tr>
    </table>
</form>

<hr>

<h3>Riwayat Prestasi & Pengurangan Poin</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Prestasi / Apresiasi</th>
            <th>Poin Bonus (Pengurangan)</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_prestasi && $result_prestasi->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result_prestasi->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                    <td><?= htmlspecialchars($row['nisn']); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?= htmlspecialchars($row['nama_prestasi']); ?></td>
                    <td><b>-<?= $row['poin_bonus']; ?> Poin</b></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" align="center">Belum ada data prestasi.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>