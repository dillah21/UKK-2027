<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Panggil header dinamis
include 'header.php';

// Query mengambil data prestasi dari database dengan relasi ke siswa dan kelas
$query_prestasi = mysqli_query($koneksi, "
    SELECT 
        pr.*, 
        s.nama_siswa, 
        s.nisn,
        k.nama_kelas
    FROM t_prestasi pr
    LEFT JOIN t_siswa s ON pr.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k ON s.id_kelas = k.id_kelas
    ORDER BY pr.tanggal DESC
");

// Fallback jika kueri memerlukan penyesuaian nama tabel/kolom
if (!$query_prestasi) {
    $query_prestasi = mysqli_query($koneksi, "SELECT * FROM t_prestasi ORDER BY id DESC");
}
?>

<!-- TAMBAHKAN CSS DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-trophy mr-2 text-warning"></i> Data Prestasi Siswa</h1>
    <?php if (strtolower($_SESSION['role'] ?? '') === 'admin' || strtolower($_SESSION['role'] ?? '') === 'guru'): ?>
        <a href="prestasi_tambah.php" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Prestasi Baru</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white font-weight-bold d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list mr-2"></i> Rekapitulasi Penghargaan & Prestasi Siswa</span>
        <button onclick="window.print()" class="btn btn-light btn-sm text-success font-weight-bold"><i class="fas fa-print mr-1"></i> Cetak</button>
    </div>
    <div class="card-body">
        <p class="text-muted">Berikut adalah daftar seluruh pencapaian, perlombaan, atau prestasi yang diraih oleh siswa.</p>
        
        <div class="table-responsive">
            <table id="tabelPrestasi" class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="thead-dark text-center">
                    <tr>
                        <th width="40">No</th>
                        <th width="90">Tanggal</th>
                        <th>Nama Siswa & Kelas</th>
                        <th>Nama Prestasi / Kegiatan</th>
                        <th>Tingkat (Lomba)</th>
                        <th width="60">Poin</th>
                        <th>Keterangan</th>
                        <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                            <th width="100">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if ($query_prestasi && mysqli_num_rows($query_prestasi) > 0):
                        while ($row = mysqli_fetch_assoc($query_prestasi)): 
                            $tgl = isset($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
                            $nama_siswa = $row['nama_siswa'] ?? ('Siswa ID ' . ($row['id_siswa'] ?? ''));
                            $kelas = $row['nama_kelas'] ?? '-';
                            $nama_prestasi = $row['nama_prestasi'] ?? $row['judul'] ?? 'Juara Lomba';
                            $tingkat = $row['tingkat'] ?? $row['level'] ?? 'Kabupaten / Kota';
                            $poin = $row['poin'] ?? $row['poin_prestasi'] ?? 10;
                            $keterangan = $row['keterangan'] ?? $row['deskripsi'] ?? '-';
                            $id_prestasi = $row['id_prestasi'] ?? $row['id'] ?? 0;
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($tgl); ?></td>
                            <td>
                                <strong><?= htmlspecialchars($nama_siswa); ?></strong><br>
                                <small class="text-muted">Kelas: <?= htmlspecialchars($kelas); ?></small>
                            </td>
                            <td><span class="badge badge-success p-2"><i class="fas fa-award mr-1"></i> <?= htmlspecialchars($nama_prestasi); ?></span></td>
                            <td><?= htmlspecialchars($tingkat); ?></td>
                            <td class="text-center font-weight-bold text-success">+<?= htmlspecialchars($poin); ?></td>
                            <td><?= htmlspecialchars($keterangan); ?></td>
                            <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                                <td class="text-center">
                                    <a href="prestasi_edit.php?id=<?= $id_prestasi; ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="prestasi_hapus.php?id=<?= $id_prestasi; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data prestasi ini?');"><i class="fas fa-trash"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="<?= (strtolower($_SESSION['role'] ?? '') === 'admin') ? 8 : 7; ?>" class="text-center text-muted py-4">Belum ada data prestasi siswa di database.</td>
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
        $('#tabelPrestasi').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data prestasi tidak ditemukan",
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