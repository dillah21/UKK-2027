<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// 1. TAMBAH GURU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_guru'])) {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    if (!empty($nama)) {
        $sql = "INSERT INTO t_guru (nama) VALUES ('".$koneksi->real_escape_string($nama)."')";
        if ($koneksi->query($sql)) {
            $id_baru = $koneksi->insert_id;
            if (!empty($nip)) {
                @$koneksi->query("UPDATE t_guru SET nip='$nip' WHERE id=$id_baru");
                @$koneksi->query("UPDATE t_guru SET nik='$nip' WHERE id=$id_baru");
            }
            $pesan_sukses = "Data guru berhasil ditambahkan!";
        } else { $pesan_error = "Gagal menyimpan: " . $koneksi->error; }
    } else { $pesan_error = "Nama guru wajib diisi!"; }
}

// 2. EDIT GURU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_guru'])) {
    $id = intval($_POST['id'] ?? 0);
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    if ($id > 0 && !empty($nama)) {
        @$koneksi->query("UPDATE t_guru SET nama='".$koneksi->real_escape_string($nama)."' WHERE id=$id");
        @$koneksi->query("UPDATE t_guru SET nama_guru='".$koneksi->real_escape_string($nama)."' WHERE id=$id");
        @$koneksi->query("UPDATE t_guru SET nip='$nip' WHERE id=$id");
        @$koneksi->query("UPDATE t_guru SET nik='$nip' WHERE id=$id");
        $pesan_sukses = "Data guru berhasil diperbarui!";
    } else { $pesan_error = "Nama guru tidak boleh kosong!"; }
}

// 3. HAPUS GURU
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        if ($koneksi->query("DELETE FROM t_guru WHERE id = $id_hapus")) {
            $pesan_sukses = "Data guru berhasil dihapus!";
        } else { $pesan_error = "Gagal menghapus: " . $koneksi->error; }
    }
}

$result_guru = $koneksi->query("SELECT * FROM t_guru ORDER BY id DESC");

// Panggil Header Terpusat
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Guru</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalGuru"><i class="fas fa-plus mr-1"></i> Tambah Guru Baru</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?><div class="alert alert-success alert-dismissible fade show"><?= $pesan_sukses; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
            <?php if (!empty($pesan_error)): ?><div class="alert alert-danger alert-dismissible fade show"><?= $pesan_error; ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelGuru" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 25%;">NIP / NIK</th>
                                    <th>Nama Lengkap Guru</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_guru && $result_guru->num_rows > 0): $no=1; while ($r = $result_guru->fetch_assoc()): 
                                    $id = $r['id'];
                                    $nip = htmlspecialchars($r['nip'] ?? $r['nik'] ?? '');
                                    $nama = htmlspecialchars($r['nama'] ?? $r['nama_guru'] ?? '');
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center"><?= !empty($nip) ? $nip : '-'; ?></td>
                                        <td><?= $nama; ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editGuru<?= $id; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusGuru<?= $id; ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Guru -->
                                    <div class="modal fade" id="editGuru<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form method="POST" action="guru.php" class="modal-content">
                                                <input type="hidden" name="id" value="<?= $id; ?>">
                                                <div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Data Guru</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">
                                                    <div class="form-group"><label>NIP / NIK</label><input type="text" name="nip" class="form-control" value="<?= $nip; ?>"></div>
                                                    <div class="form-group"><label>Nama Guru <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="<?= $nama; ?>" required></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="edit_guru" class="btn btn-warning btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus Guru -->
                                    <div class="modal fade" id="hapusGuru<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Guru</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">Yakin ingin menghapus guru <strong><?= $nama; ?></strong>?</div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><a href="guru.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a></div>
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

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="guru.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-chalkboard-teacher mr-2"></i> Tambah Guru Baru</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group"><label>NIP / NIK</label><input type="text" name="nip" class="form-control" placeholder="Nomor NIP / NIK"></div>
                <div class="form-group"><label>Nama Guru <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Nama lengkap beserta gelar"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_guru" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
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
        $('#tabelGuru').DataTable({
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