<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// 1. TAMBAH KELAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kelas'])) {
    $nama = trim($_POST['nama'] ?? '');
    if (!empty($nama)) {
        $sql = "INSERT INTO t_kelas (nama) VALUES ('".$koneksi->real_escape_string($nama)."')";
        if ($koneksi->query($sql)) { $pesan_sukses = "Data kelas berhasil ditambahkan!"; }
        else { $pesan_error = "Gagal menyimpan: " . $koneksi->error; }
    } else { $pesan_error = "Nama kelas tidak boleh kosong!"; }
}

// 2. EDIT KELAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kelas'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    if ($id > 0 && !empty($nama)) {
        if ($koneksi->query("UPDATE t_kelas SET nama='".$koneksi->real_escape_string($nama)."' WHERE id=$id")) {
            $pesan_sukses = "Data kelas berhasil diperbarui!";
        } else { $pesan_error = "Gagal memperbarui: " . $koneksi->error; }
    } else { $pesan_error = "Nama kelas wajib diisi!"; }
}

// 3. HAPUS KELAS
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        if ($koneksi->query("DELETE FROM t_kelas WHERE id = $id_hapus")) {
            $pesan_sukses = "Data kelas berhasil dihapus!";
        } else { $pesan_error = "Gagal menghapus: " . $koneksi->error; }
    }
}

$result_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY id DESC");

// Panggil Header Terpusat
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Kelas</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalKelas"><i class="fas fa-plus mr-1"></i> Tambah Kelas Baru</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?><div class="alert alert-success alert-dismissible fade show"><?= $pesan_sukses; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
            <?php if (!empty($pesan_error)): ?><div class="alert alert-danger alert-dismissible fade show"><?= $pesan_error; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelKelas" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width:10%;">No</th>
                                    <th>Nama Kelas</th>
                                    <th style="width:20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_kelas && $result_kelas->num_rows > 0): $no=1; while ($r = $result_kelas->fetch_assoc()): 
                                    $id = $r['id']; $nama = htmlspecialchars($r['nama']);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= $nama; ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editKelas<?= $id; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusKelas<?= $id; ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Kelas -->
                                    <div class="modal fade" id="editKelas<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form method="POST" action="kelas.php" class="modal-content">
                                                <input type="hidden" name="id" value="<?= $id; ?>">
                                                <div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Data Kelas</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">
                                                    <div class="form-group"><label>Nama Kelas <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="<?= $nama; ?>" required></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="edit_kelas" class="btn btn-warning btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus Kelas -->
                                    <div class="modal fade" id="hapusKelas<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Kelas</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">Yakin ingin menghapus kelas <strong><?= $nama; ?></strong>?</div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><a href="kelas.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a></div>
                                            </div>
                                        </div>
                                    </div>
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

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalKelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="kelas.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-school mr-2"></i> Tambah Kelas Baru</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group"><label>Nama Kelas <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Contoh: XII RPL 1"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_kelas" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
        </form>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tabelKelas').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>
</body>
</html>