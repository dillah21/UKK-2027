<?php
include 'koneksi.php';
include 'header.php';

if (isset($_POST['submit'])) {
    $siswa_id       = $_POST['siswa_id'];
    $kelas_id       = $_POST['kelas_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];
    $guru_id        = $_POST['guru_id'];
    $tanggal        = $_POST['tanggal'];
    $keterangan     = $_POST['keterangan'];
    $tindakan       = $_POST['tindakan'];
    $status         = $_POST['status'];

    $q_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama FROM t_siswa WHERE id = '$siswa_id'"));
    $q_kelas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama FROM t_kelas WHERE id = '$kelas_id'"));
    $q_guru  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama FROM t_guru WHERE id = '$guru_id'"));
    $q_peln  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = '$pelanggaran_id'"));

    $nama_siswa       = $q_siswa['nama'] ?? '';
    $nama_kelas       = $q_kelas['nama'] ?? '';
    $nama_guru        = $q_guru['nama'] ?? '';
    $nama_pelanggaran = $q_peln['nama'] ?? '';
    $poin             = $q_peln['poin'] ?? 0;
    $kategori_id      = $q_peln['pelanggaran_kategori_id'] ?? 1;
    $q_tahun = mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
    $tahun_aktif = $q_tahun ? mysqli_fetch_assoc($q_tahun) : null;
    $tahun_ajaran_id = (int)($tahun_aktif['id'] ?? 0);
    if ($tahun_ajaran_id === 0) {
        $error = 'Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.';
    }

    if ($tahun_ajaran_id > 0) {
    $insert = "INSERT INTO t_pelanggaran_siswa 
        (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status) 
        VALUES 
        ('$tahun_ajaran_id', '$siswa_id', '$nama_siswa', '$kelas_id', '$nama_kelas', '$pelanggaran_id', '$nama_pelanggaran', '$kategori_id', '$guru_id', '$nama_guru', '$tanggal', '$keterangan', '$poin', '$tindakan', '$status')";

    if (mysqli_query($koneksi, $insert)) {
        echo "<script>alert('Data berhasil disimpan!'); window.location='pelanggaran.php';</script>";
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
    }
}
?>

<h3>Form Input Pelanggaran Siswa</h3>
<?php
$q_tahun_tampil = mysqli_query($koneksi, "SELECT nama FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahun_tampil = $q_tahun_tampil ? mysqli_fetch_assoc($q_tahun_tampil) : null;
?>
<p><strong>Tahun Ajaran Aktif:</strong> <?= htmlspecialchars($tahun_tampil['nama'] ?? 'Belum ditentukan') ?></p>

<?php if (isset($error)) echo "<p style='color:red;'>{$error}</p>"; ?>

<form method="POST" action="">
    <p>
        <label>Tanggal Tangkap/Kejadian:</label><br>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
    </p>

    <p>
        <label>Pilih Siswa:</label><br>
        <select name="siswa_id" required>
            <option value="">-- Pilih Siswa --</option>
            <?php
            $siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE status_aktif = 1");
            while ($s = mysqli_fetch_assoc($siswa)) {
                echo "<option value='{$s['id']}'>{$s['nis']} - {$s['nama']}</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label>Pilih Kelas:</label><br>
        <select name="kelas_id" required>
            <option value="">-- Pilih Kelas --</option>
            <?php
            $kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE status_aktif = 1");
            while ($k = mysqli_fetch_assoc($kelas)) {
                echo "<option value='{$k['id']}'>{$k['nama']}</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label>Jenis Pelanggaran:</label><br>
        <select name="pelanggaran_id" required>
            <option value="">-- Pilih Jenis Pelanggaran --</option>
            <?php
            $pelanggaran = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif = 1");
            while ($p = mysqli_fetch_assoc($pelanggaran)) {
                echo "<option value='{$p['id']}'>{$p['kode']} - {$p['nama']} ({$p['poin']} Poin)</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label>Guru Pelapor:</label><br>
        <select name="guru_id" required>
            <option value="">-- Pilih Guru --</option>
            <?php
            $guru = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE status_aktif = 1");
            while ($g = mysqli_fetch_assoc($guru)) {
                echo "<option value='{$g['id']}'>{$g['nama']}</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label>Keterangan / Kronologi:</label><br>
        <textarea name="keterangan" rows="3" cols="50" required></textarea>
    </p>

    <p>
        <label>Tindakan Pembinaan:</label><br>
        <textarea name="tindakan" rows="2" cols="50" required></textarea>
    </p>

    <p>
        <label>Status Pembinaan:</label><br>
        <select name="status" required>
            <option value="Proses">Proses</option>
            <option value="Selesai">Selesai</option>
        </select>
    </p>

    <p>
        <button type="submit" name="submit">Simpan Data</button>
        <a href="pelanggaran.php">Batal</a>
    </p>
</form>

<?php include 'footer.php'; ?>