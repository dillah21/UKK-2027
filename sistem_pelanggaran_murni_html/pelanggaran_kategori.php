<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";

// PROSES HAPUS KATEGORI
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        @$koneksi->query("DELETE FROM t_pelanggaran_kategori WHERE id_kategori = $id_hapus");
        @$koneksi->query("DELETE FROM t_pelanggaran_kategori WHERE id = $id_hapus");
        $pesan_sukses = "Kategori pelanggaran berhasil dihapus!";
    }
}

include 'header.php';
$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
?>

<!-- TAMBAHKAN CSS DATATABLES BOOTSTRAP 4 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="content-wrapper" style="padding: 15px;">
    <section class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</h1>
                <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="pelanggaran_kategori_tambah.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Kategori Baru
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($pesan_sukses) || isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= !empty($pesan_sukses) ? $pesan_sukses : "Data kategori berhasil diproses!"; ?>
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
                        <table id="tabelKategori" class="table table-bordered table-striped table-hover align-middle mb-0" style="width:100%;">
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
                                if ($query && mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        $id_kat = $row['id_kategori'] ?? $row['id'] ?? 0;
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
                                                <a href="pelanggaran_kategori_edit.php?id=<?= $id_kat; ?>" class="btn btn-warning btn-sm text-white" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="pelanggaran_kategori.php?action=hapus&id=<?= $id_kat; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php 
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- PASTIKAN JQUERY & DATATABLES JS TERMUAT DENGAN BENAR -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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