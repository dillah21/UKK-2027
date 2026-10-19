<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// 1. TAMBAH TAHUN AJARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_ta'])) {
    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '2026-07-01');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '2027-06-30');
    $status_aktif = intval($_POST['status_aktif'] ?? 0);

    if (!empty($nama)) {
        if ($status_aktif == 1) {
            $koneksi->query("UPDATE t_tahun_ajaran SET status_aktif = 0");
        }
        $stmt = $koneksi->prepare("INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nama, $tanggal_mulai, $tanggal_selesai, $status_aktif);
        
        if ($stmt->execute()) {
            $pesan_sukses = "Tahun ajaran berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $pesan_error = "Tahun ajaran wajib diisi!";
    }
}

// 2. HAPUS TAHUN AJARAN
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        $stmt = $koneksi->prepare("DELETE FROM t_tahun_ajaran WHERE id = ?");
        $stmt->bind_param("i", $id_hapus);
        if ($stmt->execute()) {
            $pesan_sukses = "Tahun ajaran berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result_ta = $koneksi->query("SELECT * FROM t_tahun_ajaran ORDER BY id DESC");

include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Tahun Ajaran</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTA"><i class="fas fa-plus mr-1"></i> Tambah Tahun Ajaran</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?><div class="alert alert-success alert-dismissible fade show"><?= $pesan_sukses; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
            <?php if (!empty($pesan_error)): ?><div class="alert alert-danger alert-dismissible fade show"><?= $pesan_error; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelTA" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th>Tahun Ajaran</th>
                                    <th style="width: 25%;">Status Aktif</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_ta && $result_ta->num_rows > 0): $no=1; while ($r = $result_ta->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center font-weight-bold"><?= htmlspecialchars($r['nama'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <?php if (isset($r['status_aktif']) && $r['status_aktif'] == 1): ?>
                                                <span class="badge badge-success p-2">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary p-2">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="tahun_ajaran.php?action=hapus&id=<?= $r['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?');"><i class="fas fa-trash"></i> Hapus</a>
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

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="modalTA" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="tahun_ajaran.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-calendar-alt mr-2"></i> Tambah Tahun Ajaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Contoh: 2026/2027">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status_aktif" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0" selected>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_ta" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
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
        $('#tabelTA').DataTable({
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