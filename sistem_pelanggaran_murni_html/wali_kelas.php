<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// 1. TAMBAH WALI KELAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_walikelas'])) {
    $guru_id = intval($_POST['guru_id'] ?? 0);
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $ta_id = intval($_POST['tahun_ajaran_id'] ?? 0);
    $tanggal_mulai = '2026-07-01';
    $tanggal_selesai = '2027-06-30';
    $status_aktif = 1;

    if ($guru_id > 0 && $kelas_id > 0 && $ta_id > 0) {
        $stmt = $koneksi->prepare("INSERT INTO t_wali_kelas (guru_id, kelas_id, tahun_ajaran_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissi", $guru_id, $kelas_id, $ta_id, $tanggal_mulai, $tanggal_selesai, $status_aktif);
        
        if ($stmt->execute()) {
            $pesan_sukses = "Data wali kelas berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $pesan_error = "Silakan pilih guru, kelas, dan tahun ajaran dengan lengkap!";
    }
}

// 2. HAPUS WALI KELAS
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        $stmt = $koneksi->prepare("DELETE FROM t_wali_kelas WHERE id = ?");
        $stmt->bind_param("i", $id_hapus);
        if ($stmt->execute()) {
            $pesan_sukses = "Data wali kelas berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result_wk = $koneksi->query("
    SELECT wk.*, g.nama AS nama_guru, k.nama AS nama_kelas, ta.nama AS tahun_ajaran 
    FROM t_wali_kelas wk
    LEFT JOIN t_guru g ON wk.guru_id = g.id
    LEFT JOIN t_kelas k ON wk.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id
    ORDER BY wk.id DESC
");

$list_guru = $koneksi->query("SELECT * FROM t_guru ORDER BY nama ASC");
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");
$list_ta = $koneksi->query("SELECT * FROM t_tahun_ajaran ORDER BY id DESC");

include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Wali Kelas</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalWaliKelas"><i class="fas fa-plus mr-1"></i> Tambah Wali Kelas</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?><div class="alert alert-success alert-dismissible fade show"><?= $pesan_sukses; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
            <?php if (!empty($pesan_error)): ?><div class="alert alert-danger alert-dismissible fade show"><?= $pesan_error; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelWaliKelas" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Guru</th>
                                    <th>Kelas Binaan</th>
                                    <th>Tahun Ajaran</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_wk && $result_wk->num_rows > 0): $no=1; while ($r = $result_wk->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($r['nama_guru'] ?? '-'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($r['nama_kelas'] ?? '-'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($r['tahun_ajaran'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <a href="wali_kelas.php?action=hapus&id=<?= $r['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="mt-5 py-3 text-center text-muted border-top small">&copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong></footer>
        </main>
    </div>
</div>

<!-- Modal Tambah Wali Kelas -->
<div class="modal fade" id="modalWaliKelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="wali_kelas.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-user-tie mr-2"></i> Tambah Wali Kelas</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Guru <span class="text-danger">*</span></label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php if ($list_guru && $list_guru->num_rows > 0): while ($g = $list_guru->fetch_assoc()): ?>
                            <option value="<?= $g['id']; ?>"><?= htmlspecialchars($g['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Kelas <span class="text-danger">*</span></label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php if ($list_kelas && $list_kelas->num_rows > 0): while ($k = $list_kelas->fetch_assoc()): ?>
                            <option value="<?= $k['id']; ?>"><?= htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="tahun_ajaran_id" class="form-control" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php if ($list_ta && $list_ta->num_rows > 0): while ($ta = $list_ta->fetch_assoc()): ?>
                            <option value="<?= $ta['id']; ?>"><?= htmlspecialchars($ta['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_walikelas" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
        </form>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelWaliKelas').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
            }
        });
    });
</script>
</body>
</html>