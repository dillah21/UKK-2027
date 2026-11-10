<?php
require_once 'auth.php';
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$role_login = strtolower(trim($_SESSION['role'] ?? ''));
$is_guru = ($role_login === 'guru');
$error = '';
$success = '';

$pilihan_tindakan = [
    'Teguran lisan',
    'Teguran tertulis',
    'Pembinaan oleh wali kelas',
    'Pembinaan oleh guru BK',
    'Pemanggilan orang tua/wali',
    'Membuat surat pernyataan',
    'Pembinaan bersama wali kelas dan guru BK'
        ];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $siswa_id = (int)($_POST['siswa_id'] ?? 0);
        $kelas_id = (int)($_POST['kelas_id'] ?? 0);
        $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);
        $guru_id = (int)($_POST['guru_id'] ?? 0);
        $tanggal = trim($_POST['tanggal'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        // Untuk role Guru, identitas pelapor diambil dari akun yang sedang login.
        // Relasi t_guru.user_id mencegah guru memilih nama pelapor lain.
        if ($is_guru) {
            $user_id_login = (int)($_SESSION['user_id'] ?? 0);
            $stmt_guru_login = $koneksi->prepare(
                "SELECT id FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1"
            );
            $stmt_guru_login->bind_param('i', $user_id_login);
            $stmt_guru_login->execute();
            $guru_login = $stmt_guru_login->get_result()->fetch_assoc();
            $stmt_guru_login->close();

            if (!$guru_login) {
                throw new Exception('Akun Guru belum terhubung dengan data Guru aktif. Hubungi administrator.');
            }
            $guru_id = (int)$guru_login['id'];
            $keterangan = '';
        }
        $tindakan = trim($_POST['tindakan'] ?? '');

        $status = trim($_POST['status'] ?? '');

        if ($siswa_id <= 0 || $kelas_id <= 0 || $pelanggaran_id <= 0 || $guru_id <= 0 ||
            !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tanggal) ||
            (!$is_guru && $keterangan === '') || !in_array($tindakan, $pilihan_tindakan, true) ||
            !in_array($status, ['Proses', 'Selesai'], true)) {
            throw new Exception('Mohon isi semua data dengan benar.');
        }

        $get = $koneksi->prepare("SELECT nama FROM t_siswa WHERE id = ? AND status_aktif = 1");
        $get->bind_param('i', $siswa_id); $get->execute();
        $siswa = $get->get_result()->fetch_assoc(); $get->close();
        if (!$siswa) throw new Exception('Siswa tidak ditemukan atau tidak aktif.');

        $get = $koneksi->prepare("SELECT nama FROM t_kelas WHERE id = ? AND status_aktif = 1");
        $get->bind_param('i', $kelas_id); $get->execute();
        $kelas = $get->get_result()->fetch_assoc(); $get->close();
        if (!$kelas) throw new Exception('Kelas tidak ditemukan atau tidak aktif.');

        $get = $koneksi->prepare("SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = ? AND status_aktif = 1");
        $get->bind_param('i', $pelanggaran_id); $get->execute();
        $pelanggaran = $get->get_result()->fetch_assoc(); $get->close();
        if (!$pelanggaran) throw new Exception('Jenis pelanggaran tidak ditemukan atau tidak aktif.');

        $get = $koneksi->prepare("SELECT nama FROM t_guru WHERE id = ? AND status_aktif = 1");
        $get->bind_param('i', $guru_id); $get->execute();
        $guru = $get->get_result()->fetch_assoc(); $get->close();
        if (!$guru) throw new Exception('Guru tidak ditemukan atau tidak aktif.');

        $get = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC, id DESC LIMIT 1");
        $tahun = $get->fetch_assoc();
        $tahun_ajaran_id = (int)($tahun['id'] ?? 0);
        if ($tahun_ajaran_id <= 0) throw new Exception('Belum ada tahun ajaran aktif.');

        $stmt = $koneksi->prepare("
            INSERT INTO t_pelanggaran_siswa
            (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas,
             pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id,
             guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $poin = (int)$pelanggaran['poin'];
        $kategori_id = (int)$pelanggaran['pelanggaran_kategori_id'];
        $nama_siswa = $siswa['nama']; $nama_kelas = $kelas['nama'];
        $nama_pelanggaran = $pelanggaran['nama']; $nama_guru = $guru['nama'];
        $stmt->bind_param(
            'iisisisiisssiss',
            $tahun_ajaran_id, $siswa_id, $nama_siswa, $kelas_id, $nama_kelas,
            $pelanggaran_id, $nama_pelanggaran, $kategori_id, $guru_id,
            $nama_guru, $tanggal, $keterangan, $poin, $tindakan, $status
        );
        $stmt->execute();
        $stmt->close();
        $success = 'Data pelanggaran berhasil disimpan.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

function tp_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
$siswa_list = $koneksi->query("SELECT id, nis, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama");
$kelas_list = $koneksi->query("SELECT id, nama FROM t_kelas WHERE status_aktif = 1 ORDER BY nama");
$pelanggaran_list = $koneksi->query("SELECT id, kode, nama, poin FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY nama");
$guru_list = $koneksi->query("SELECT id, nama FROM t_guru WHERE status_aktif = 1 ORDER BY nama");
$tahun_result = $koneksi->query("SELECT nama FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC, id DESC LIMIT 1");
$tahun_tampil = $tahun_result->fetch_assoc();

include 'header.php';
?>
<div class="container-fluid py-3">
    <h3>Form Input Pelanggaran Siswa</h3>
    <p><strong>Tahun Ajaran Aktif:</strong> <?= tp_h($tahun_tampil['nama'] ?? 'Belum ditentukan') ?></p>
    <?php if ($error): ?><div class="alert alert-danger"><?= tp_h($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= tp_h($success) ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST" action="tambah_pelanggaran.php">
            <div class="form-row">
                <div class="form-group col-md-4"><label>Tanggal Kejadian</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                <div class="form-group col-md-4"><label>Siswa</label><select name="siswa_id" class="form-control" required><option value="">-- Pilih Siswa --</option><?php while($r=$siswa_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nis'].' - '.$r['nama']) ?></option><?php endwhile; ?></select></div>
                <div class="form-group col-md-4"><label>Kelas</label><select name="kelas_id" class="form-control" required><option value="">-- Pilih Kelas --</option><?php while($r=$kelas_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nama']) ?></option><?php endwhile; ?></select></div>
                <div class="form-group col-md-6"><label>Jenis Pelanggaran</label><select name="pelanggaran_id" class="form-control" required><option value="">-- Pilih Pelanggaran --</option><?php while($r=$pelanggaran_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['kode'].' - '.$r['nama'].' ('.$r['poin'].' poin)') ?></option><?php endwhile; ?></select></div>
                <?php if (!$is_guru): ?>
                <div class="form-group col-md-6"><label>Guru Pelapor</label><select name="guru_id" class="form-control" required><option value="">-- Pilih Guru --</option><?php while($r=$guru_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nama']) ?></option><?php endwhile; ?></select></div>
                <div class="form-group col-12"><label>Keterangan / Kronologi</label><textarea name="keterangan" class="form-control" rows="3" required></textarea></div>
                <?php endif; ?>
                <div class="form-group col-12"><label>Tindakan Pembinaan</label><select name="tindakan" class="form-control" required><option value="">-- Pilih Tindakan Pembinaan --</option><?php foreach ($pilihan_tindakan as $opsi): ?><option value="<?= tp_h($opsi) ?>"><?= tp_h($opsi) ?></option><?php endforeach; ?></select></div>
                <div class="form-group col-md-4"><label>Status Pembinaan</label><select name="status" class="form-control" required><option value="Proses">Proses</option><option value="Selesai">Selesai</option></select></div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Data</button>
            <a href="pelanggaran.php" class="btn btn-secondary">Batal</a>
        </form>
    </div></div>
</div>
<?php include 'footer.php'; ?>
