<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";

// 1. PROSES TAMBAH KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama = trim($_POST['nama_kategori'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $sanksi = trim($_POST['sanksi'] ?? '');

    if (!empty($nama)) {
        $stmt = $koneksi->prepare("INSERT INTO t_pelanggaran_kategori (nama_kategori, poin, sanksi) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sis", $nama, $poin, $sanksi);
            if ($stmt->execute()) {
                $pesan_sukses = "Kategori pelanggaran berhasil ditambahkan!";
            } else {
                $pesan_error = "Gagal menyimpan: " . $koneksi->error;
            }
            $stmt->close();
        } else {
            $koneksi->query("INSERT INTO t_pelanggaran_kategori (nama_kategori, poin, sanksi) VALUES ('$nama', $poin, '$sanksi')");
            $pesan_sukses = "Kategori pelanggaran berhasil ditambahkan!";
        }
    } else {
        $pesan_error = "Nama kategori wajib diisi!";
    }
}

// 2. PROSES EDIT KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kategori'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_kategori'] ?? '');
    $poin = intval($_POST['poin'] ?? 0);
    $sanksi = trim($_POST['sanksi'] ?? '');

    if ($id > 0 && !empty($nama)) {
        // Coba update fleksibel berdasarkan berbagai kemungkinan nama primary key
        @$koneksi->query("UPDATE t_pelanggaran_kategori SET nama_kategori='$nama', poin=$poin, sanksi='$sanksi' WHERE id_kategori=$id");
        @$koneksi->query("UPDATE t_pelanggaran_kategori SET nama_kategori='$nama', poin=$poin, sanksi='$sanksi' WHERE id=$id");
        
        $pesan_sukses = "Kategori pelanggaran berhasil diperbarui!";
    } else {
        $pesan_error = "Nama kategori tidak boleh kosong!";
    }
}

// 3. PROSES HAPUS KATEGORI
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        @$koneksi->query("DELETE FROM t_pelanggaran_kategori WHERE id_kategori = $id_hapus");
        @$koneksi->query("DELETE FROM t_pelanggaran_kategori WHERE id = $id_hapus");
        $pesan_sukses = "Kategori pelanggaran berhasil dihapus!";
    }
}

// Panggil header dinamis
include 'header.php';

// Query mengambil data kategori dari database
$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
?>

<!-- TAMBAHKAN CSS DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</h1>
    <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahKategori">
            <i class="fas fa-plus mr-1"></i> Tambah Kategori Baru
        </button>
    <?php endif; ?>
</div>

<?php if (!empty($pesan_sukses)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $pesan_sukses; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $pesan_error; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelKategori" class="table table-striped table-hover align-middle mb-0" style="width:100%;">
                <thead class="thead-dark text-center">
                    <tr>
                        <th width="50">No</th>
                        <th class="text-left">Nama Kategori Pelanggaran</th>
                        <th width="100">Poin</th>
                        <th class="text-left">Sanksi / Hukuman</th>
                        <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                            <th width="120">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $all_rows = [];
                    if ($query && mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $all_rows[] = $row;
                        }
                    }

                    foreach ($all_rows as $row): 
                        // Deteksi otomatis kolom ID apa pun yang ada di database
                        $id_kat = $row['id_kategori'] ?? $row['id'] ?? array_values($row)[0] ?? 0;
                        if (empty($id_kat)) {
                            $id_kat = $no;
                        }

                        $nama_kat = $row['nama_kategori'] ?? $row['kategori'] ?? $row['nama'] ?? '';
                        $poin = $row['poin'] ?? $row['poin_pelanggaran'] ?? $row['point'] ?? 10;
                        $hukuman = $row['sanksi'] ?? $row['hukuman'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? 'Teguran lisan';
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($nama_kat); ?></strong></td>
                            <td class="text-center font-weight-bold text-danger">
                                <span class="badge badge-danger p-2">+<?= htmlspecialchars($poin); ?> Poin</span>
                            </td>
                            <td><?= htmlspecialchars($hukuman); ?></td>
                            <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm text-white" data-toggle="modal" data-target="#editKategori<?= $id_kat; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusKategori<?= $id_kat; ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- KUMPULAN MODAL EDIT & HAPUS YANG DISINKRONKAN DENGAN ID BARIS -->
<?php foreach ($all_rows as $row): 
    $id_kat = $row['id_kategori'] ?? $row['id'] ?? array_values($row)[0] ?? 0;
    if (empty($id_kat)) { continue; }

    $nama_kat = $row['nama_kategori'] ?? $row['kategori'] ?? $row['nama'] ?? '';
    $poin = $row['poin'] ?? $row['poin_pelanggaran'] ?? $row['point'] ?? 10;
    $hukuman = $row['sanksi'] ?? $row['hukuman'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? 'Teguran lisan';
?>
    <!-- MODAL EDIT KATEGORI -->
    <div class="modal fade" id="editKategori<?= $id_kat; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="pelanggaran_kategori.php" class="modal-content">
                <input type="hidden" name="id" value="<?= $id_kat; ?>">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Kategori Pelanggaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Kategori Pelanggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" required value="<?= htmlspecialchars($nama_kat); ?>">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Jumlah Poin</label>
                        <input type="number" class="form-control" name="poin" value="<?= htmlspecialchars($poin); ?>">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Sanksi / Hukuman</label>
                        <textarea class="form-control" name="sanksi" rows="2"><?= htmlspecialchars($hukuman); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" name="edit_kategori" class="btn btn-warning btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS KATEGORI -->
    <div class="modal fade" id="hapusKategori<?= $id_kat; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-trash mr-2"></i> Hapus Kategori</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-left">
                    Apakah Anda yakin ingin menghapus kategori <strong><?= htmlspecialchars($nama_kat); ?></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <a href="pelanggaran_kategori.php?action=hapus&id=<?= $id_kat; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- MODAL TAMBAH KATEGORI -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="pelanggaran_kategori.php" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-tags mr-2"></i> Tambah Kategori Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kategori Pelanggaran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_kategori" required placeholder="Contoh: Terlambat hadir">
                </div>
                <div class="form-group">
                    <label>Jumlah Poin</label>
                    <input type="number" class="form-control" name="poin" value="10" placeholder="10">
                </div>
                <div class="form-group">
                    <label>Sanksi / Hukuman</label>
                    <textarea class="form-control" name="sanksi" rows="2" placeholder="Deskripsi sanksi..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_kategori" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JAVASCRIPT DATATABLES -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelKategori').DataTable({
            "order": [[0, "asc"]],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data kategori tidak ditemukan",
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

<?php include 'footer.php'; ?>