<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (file_exists('header.php')) include 'header.php';
if (file_exists('sidebar.php')) include 'sidebar.php';
?>

<style>
    /* Mengunci warna header tabel agar tetap hitam pekat & tidak berubah abu-abu saat disentuh/hover */
    .table-fixed-header, 
    .table-fixed-header th {
        background-color: #212529 !important;
        color: #ffffff !important;
    }
    .table-fixed-header th:hover, 
    .table-fixed-header th:focus {
        background-color: #212529 !important;
        color: #ffffff !important;
    }
</style>

<div class="content-wrapper" style="padding: 15px;">
    <section class="content">
        <div class="container-fluid">
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Rekapitulasi Penghargaan & Prestasi Siswa</h5>
                    <div>
                        <a href="prestasi_tambah.php" class="btn btn-light btn-sm text-success fw-bold">
                            <i class="fas fa-plus"></i> Tambah Prestasi Baru
                        </a>
                        <a href="cetak_prestasi.php" target="_blank" class="btn btn-dark btn-sm">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <p class="text-muted">Berikut adalah daftar seluruh pencapaian, perlombaan, atau prestasi yang diraih oleh siswa.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-fixed-header text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="23%">Nama Siswa & Kelas</th>
                                    <th width="25%">Nama Prestasi / Kegiatan</th>
                                    <th width="15%">Tingkat (Lomba)</th>
                                    <th width="8%">Poin</th>
                                    <th width="15%">Keterangan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $result = false;

                                // Menangkap error query agar tidak langsung fatal crash jika tabel belum ada
                                try {
                                    $query = "SELECT prestasi.*, t_siswa.nama AS nama_siswa, t_kelas.nama AS nama_kelas 
                                              FROM prestasi 
                                              JOIN t_siswa ON prestasi.id_siswa = t_siswa.id 
                                              LEFT JOIN t_kelas_siswa ON t_siswa.id = t_kelas_siswa.siswa_id AND t_kelas_siswa.status_aktif = 1
                                              LEFT JOIN t_kelas ON t_kelas_siswa.kelas_id = t_kelas.id 
                                              ORDER BY prestasi.tanggal DESC";
                                    $result = mysqli_query($koneksi, $query);
                                } catch (Exception $e) {
                                    try {
                                        $query_alt = "SELECT prestasi.*, t_siswa.nama AS nama_siswa 
                                                      FROM prestasi 
                                                      JOIN t_siswa ON prestasi.id_siswa = t_siswa.id 
                                                      ORDER BY prestasi.tanggal DESC";
                                        $result = mysqli_query($koneksi, $query_alt);
                                    } catch (Exception $ex) {
                                        $result = false;
                                    }
                                }

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($d = mysqli_fetch_assoc($result)) {
                                        $nama_kelas = isset($d['nama_kelas']) && !empty($d['nama_kelas']) ? $d['nama_kelas'] : '-';
                                        
                                        // Mencari ID unik untuk proses hapus
                                        $id_hapus = isset($d['id_prestasi']) ? $d['id_prestasi'] : (isset($d['id']) ? $d['id'] : '');
                                        
                                        // Format tanggal
                                        $tgl = $d['tanggal'];
                                        $tgl_tampil = (!empty($tgl) && $tgl != '0000-00-00') ? date('d-m-Y', strtotime($tgl)) : '-';
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td class="text-center"><?php echo $tgl_tampil; ?></td>
                                        <td><strong><?php echo htmlspecialchars($d['nama_siswa']); ?></strong><br><small class="text-muted">Kelas: <?php echo htmlspecialchars($nama_kelas); ?></small></td>
                                        <td><?php echo htmlspecialchars($d['nama_prestasi']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($d['tingkat']); ?></span>
                                        </td>
                                        <td class="text-center fw-bold text-success">+<?php echo htmlspecialchars($d['poin']); ?></td>
                                        <td><?php echo !empty($d['keterangan']) ? htmlspecialchars($d['keterangan']) : '<span class="text-muted">-</span>'; ?></td>
                                        <td class="text-center">
                                            <a href="prestasi_hapus.php?id=<?php echo $id_hapus; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data prestasi ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Belum ada data prestasi siswa di database atau tabel belum dibuat.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php 
if (file_exists('footer.php')) include 'footer.php'; 
?>