<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Panggil header dinamis
include 'header.php';

// Query mengambil data kategori dari database
$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY id_kategori DESC");
if (!$query) {
    $query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
}
?>

<!-- TAMBAHKAN CSS DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tags mr-2"></i> Kategori Pelanggaran</h1>
    <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
        <a href="pelanggaran_kategori_tambah.php" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Kategori Baru</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <!-- BERIKAN ID 'tabelKategori' PADA TABEL -->
            <table id="tabelKategori" class="table table-striped table-hover align-middle mb-0">
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
                    if ($query && mysqli_num_rows($query) > 0):
                        while ($row = mysqli_fetch_assoc($query)): 
                            $id_kat = $row['id_kategori'] ?? $row['id'] ?? array_values($row)[0] ?? 0;
                            
                            // Deteksi Nama Kategori
                            $nama_kat = $row['nama_kategori'] ?? $row['kategori'] ?? $row['nama'] ?? '';
                            if (empty($nama_kat)) {
                                $values = array_values($row);
                                $nama_kat = $values[1] ?? 'Data Kategori';
                            }

                            // Deteksi Poin & Sanksi / Hukuman dari database
                            $poin = $row['poin'] ?? $row['poin_pelanggaran'] ?? $row['point'] ?? 10;
                            $hukuman = $row['sanksi'] ?? $row['hukuman'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? 'Teguran lisan & pencatatan poin pelanggaran';
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
                                    <a href="pelanggaran_kategori_edit.php?id=<?= $id_kat; ?>" class="btn btn-warning btn-sm text-white"><i class="fas fa-edit"></i></a>
                                    <a href="pelanggaran_kategori_hapus.php?id=<?= $id_kat; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?');"><i class="fas fa-trash"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data kategori pelanggaran di database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAMBAHKAN SCRIPT JAVASCRIPT DATATABLES -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelKategori').DataTable({
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