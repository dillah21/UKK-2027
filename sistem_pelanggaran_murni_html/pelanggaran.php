<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; require_once 'koneksi.php';

$pesan_sukses = ""; $pesan_error = "";

// Cek skema kolom t_pelanggaran
$has_id_kategori = false;
$has_kategori_id = false;
$res_cols = $koneksi->query("SHOW COLUMNS FROM t_pelanggaran");
if ($res_cols) {
    while ($col = $res_cols->fetch_assoc()) {
        if ($col['Field'] === 'id_kategori') $has_id_kategori = true;
        if ($col['Field'] === 'kategori_id') $has_kategori_id = true;
    }
}

// 1. TAMBAH PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pelanggaran'])) {
    $nama = trim($_POST['nama'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $kat_id = intval($_POST['kategori_id'] ?? 0);

    if (!empty($nama) && $poin > 0) {
        $nama_esc = $koneksi->real_escape_string($nama);
        if ($has_id_kategori) {
            $sql = "INSERT INTO t_pelanggaran (nama, poin, id_kategori) VALUES ('$nama_esc', $poin, $kat_id)";
        } elseif ($has_kategori_id) {
            $sql = "INSERT INTO t_pelanggaran (nama, poin, kategori_id) VALUES ('$nama_esc', $poin, $kat_id)";
        } else {
            $sql = "INSERT INTO t_pelanggaran (nama, poin) VALUES ('$nama_esc', $poin)";
        }
        
        if ($koneksi->query($sql)) {
            $pesan_sukses = "Jenis pelanggaran berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan: " . $koneksi->error;
        }
    } else { $pesan_error = "Nama dan poin wajib diisi!"; }
}

// 2. EDIT PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_pelanggaran'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $kat_id = intval($_POST['kategori_id'] ?? 0);

    if ($id > 0 && !empty($nama) && $poin > 0) {
        $nama_esc = $koneksi->real_escape_string($nama);
        if ($has_id_kategori) {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin, id_kategori=$kat_id WHERE id=$id";
        } elseif ($has_kategori_id) {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin, kategori_id=$kat_id WHERE id=$id";
        } else {
            $sql = "UPDATE t_pelanggaran SET nama='$nama_esc', poin=$poin WHERE id=$id";
        }

        if ($koneksi->query($sql)) {
            $pesan_sukses = "Jenis pelanggaran berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui: " . $koneksi->error;
        }
    } else { $pesan_error = "Nama dan poin wajib diisi!"; }
}

// 3. HAPUS PELANGGARAN
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        if ($koneksi->query("DELETE FROM t_pelanggaran WHERE id = $id_hapus")) {
            $pesan_sukses = "Jenis pelanggaran berhasil dihapus!";
        } else { $pesan_error = "Gagal menghapus: " . $koneksi->error; }
    }
}

// AMBIL MASTER KATEGORI
$list_kategori = $koneksi->query("SELECT * FROM t_pelanggaran_kategori ORDER BY nama ASC");

// QUERY TAMPIL DATA DINAMIS
if ($has_id_kategori) {
    $sql_p = "SELECT p.*, COALESCE(k.nama, '-') AS nama_kategori, p.id_kategori AS kat_id_curr FROM t_pelanggaran p LEFT JOIN t_pelanggaran_kategori k ON p.id_kategori = k.id ORDER BY p.id DESC";
} elseif ($has_kategori_id) {
    $sql_p = "SELECT p.*, COALESCE(k.nama, '-') AS nama_kategori, p.kategori_id AS kat_id_curr FROM t_pelanggaran p LEFT JOIN t_pelanggaran_kategori k ON p.kategori_id = k.id ORDER BY p.id DESC";
} else {
    $sql_p = "SELECT p.*, '-' AS nama_kategori, 0 AS kat_id_curr FROM t_pelanggaran p ORDER BY p.id DESC";
}
$result_pelanggaran = $koneksi->query($sql_p);

// Panggil Header Terpusat
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Jenis & Master Pelanggaran</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPelanggaran"><i class="fas fa-plus mr-1"></i> Tambah Pelanggaran Baru</button>
            </div>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $pesan_sukses; ?>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $pesan_error; ?>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelPelanggaran" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Jenis Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th style="width: 15%;">Poin</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_pelanggaran && $result_pelanggaran->num_rows > 0): $no=1; while ($r = $result_pelanggaran->fetch_assoc()): 
                                    $id = $r['id']; 
                                    $nama = htmlspecialchars($r['nama'] ?? ''); 
                                    $kat = htmlspecialchars($r['nama_kategori'] ?? '-'); 
                                    $poin = htmlspecialchars($r['poin'] ?? 0);
                                    $kat_id_curr = $r['kat_id_curr'] ?? 0;
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= $nama; ?></td>
                                        <td class="text-center"><?= $kat; ?></td>
                                        <td class="text-center font-weight-bold text-danger"><?= $poin; ?> Poin</td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPel<?= $id; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusPel<?= $id; ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Pelanggaran -->
                                    <div class="modal fade" id="editPel<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form method="POST" action="pelanggaran.php" class="modal-content">
                                                <input type="hidden" name="id" value="<?= $id; ?>">
                                                <div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Pelanggaran</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">
                                                    <div class="form-group"><label>Nama Pelanggaran <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="<?= $nama; ?>" required></div>
                                                    <?php if ($has_id_kategori || $has_kategori_id): ?>
                                                    <div class="form-group">
                                                        <label>Kategori</label>
                                                        <select name="kategori_id" class="form-control">
                                                            <option value="0">-- Pilih Kategori --</option>
                                                            <?php if ($list_kategori && $list_kategori->num_rows > 0): $list_kategori->data_seek(0); while ($k = $list_kategori->fetch_assoc()): ?>
                                                                <option value="<?= $k['id']; ?>" <?= ($kat_id_curr == $k['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($k['nama']); ?></option>
                                                            <?php endwhile; endif; ?>
                                                        </select>
                                                    </div>
                                                    <?php endif; ?>
                                                    <div class="form-group"><label>Poin <span class="text-danger">*</span></label><input type="number" name="poin" class="form-control" value="<?= $poin; ?>" required></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="edit_pelanggaran" class="btn btn-warning btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus Pelanggaran -->
                                    <div class="modal fade" id="hapusPel<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Pelanggaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                                                <div class="modal-body">Hapus jenis pelanggaran <strong><?= $nama; ?></strong>?</div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><a href="pelanggaran.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
            </footer>
        </main>
    </div>
</div>

<!-- Modal Tambah Pelanggaran -->
<div class="modal fade" id="modalPelanggaran" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="pelanggaran.php" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i> Tambah Pelanggaran Baru</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body">
                <div class="form-group"><label>Nama Pelanggaran <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Contoh: Datang Terlambat"></div>
                <?php if ($has_id_kategori || $has_kategori_id): ?>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control">
                        <option value="0">-- Pilih Kategori --</option>
                        <?php if ($list_kategori && $list_kategori->num_rows > 0): $list_kategori->data_seek(0); while ($k = $list_kategori->fetch_assoc()): ?>
                            <option value="<?= $k['id']; ?>"><?= htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="form-group"><label>Poin Pelanggaran <span class="text-danger">*</span></label><input type="number" name="poin" class="form-control" required placeholder="Contoh: 10"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" name="tambah_pelanggaran" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button></div>
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
        $('#tabelPelanggaran').DataTable({
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