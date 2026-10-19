<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Panggil header dinamis
include 'header.php';

// Query untuk mengambil data rekapitulasi pelanggaran siswa lengkap
$query_laporan = mysqli_query($koneksi, "
    SELECT 
        p.*, 
        s.nama_siswa, 
        s.nisn,
        k.nama_kelas,
        kat.nama_kategori
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_siswa s ON p.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN t_pelanggaran_kategori kat ON p.id_kategori = kat.id_kategori
    ORDER BY p.tanggal DESC
");

if (!$query_laporan) {
    $query_laporan = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY id DESC");
}
?>

<!-- TAMBAHKAN CSS DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-file-alt mr-2"></i> Laporan & Rekap Pelanggaran</h1>
    <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print mr-1"></i> Cetak Laporan</button>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white font-weight-bold d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list mr-2"></i> Rekapitulasi Data, Tindakan, & Tindak Lanjut Pelanggaran</span>
    </div>
    <div class="card-body">
        <p class="text-muted">Berikut adalah rekapitulasi seluruh catatan pelanggaran beserta tindakan penanganan dan status tindak lanjut siswa.</p>
        
        <div class="table-responsive">
            <!-- BERIKAN ID 'tabelLaporan' PADA TABEL -->
            <table id="tabelLaporan" class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="thead-dark text-center">
                    <tr>
                        <th width="40">No</th>
                        <th width="90">Tanggal</th>
                        <th>Nama Siswa & Kelas</th>
                        <th>Kategori Pelanggaran</th>
                        <th>Deskripsi</th>
                        <th width="60">Poin</th>
                        <th>Tindakan / Sanksi</th>
                        <th>Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if ($query_laporan && mysqli_num_rows($query_laporan) > 0):
                        while ($row = mysqli_fetch_assoc($query_laporan)): 
                            $tgl = isset($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
                            $nama_siswa = $row['nama_siswa'] ?? ('Siswa ID ' . ($row['id_siswa'] ?? ''));
                            $kelas = $row['nama_kelas'] ?? '-';
                            $kategori = $row['nama_kategori'] ?? $row['kategori'] ?? '-';
                            $deskripsi = $row['catatan'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? '-';
                            $poin = $row['poin'] ?? $row['poin_pelanggaran'] ?? 10;
                            
                            $tindakan = $row['tindakan'] ?? $row['sanksi'] ?? 'Teguran Lisan & Pencatatan Poin';
                            $tindak_lanjut = $row['tindak_lanjut'] ?? $row['penanganan'] ?? 'Pemanggilan Orang Tua / Pembinaan BK';
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($tgl); ?></td>
                            <td>
                                <strong><?= htmlspecialchars($nama_siswa); ?></strong><br>
                                <small class="text-muted">Kelas: <?= htmlspecialchars($kelas); ?></small>
                            </td>
                            <td><span class="badge badge-warning p-2"><?= htmlspecialchars($kategori); ?></span></td>
                            <td><?= htmlspecialchars($deskripsi); ?></td>
                            <td class="text-center font-weight-bold text-danger">+<?= htmlspecialchars($poin); ?></td>
                            <td>
                                <span class="badge badge-info p-2">
                                    <i class="fas fa-gavel mr-1"></i> <?= htmlspecialchars($tindakan); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-danger p-2">
                                    <i class="fas fa-user-check mr-1"></i> <?= htmlspecialchars($tindak_lanjut); ?>
                                </span>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada catatan laporan pelanggaran siswa.</td>
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
        $('#tabelLaporan').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
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