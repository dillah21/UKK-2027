<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Proses simpan data saat tombol submit ditekan
if (isset($_POST['simpan'])) {
    $id_siswa = mysqli_real_escape_string($koneksi, $_POST['id_siswa']);
    $nama_prestasi = mysqli_real_escape_string($koneksi, $_POST['nama_prestasi']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $poin = mysqli_real_escape_string($koneksi, $_POST['poin']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    // Menyesuaikan dengan tabel prestasi dan kolom t_siswa
    $query = "INSERT INTO prestasi (id_siswa, nama_prestasi, tingkat, tanggal, poin, keterangan) 
              VALUES ('$id_siswa', '$nama_prestasi', '$tingkat', '$tanggal', '$poin', '$keterangan')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data prestasi berhasil disimpan!'); window.location='prestasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Sertakan header & sidebar proyek Anda
if (file_exists('header.php')) include 'header.php';
if (file_exists('sidebar.php')) include 'sidebar.php';
?>

<div class="content-wrapper" style="padding: 20px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Prestasi Siswa</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Siswa</label>
                                <select name="id_siswa" class="form-select form-control" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    <?php
                                    // Mengambil data dari tabel t_siswa dan relasi kelasnya melalui t_kelas_siswa / t_kelas
                                    $query_siswa = "SELECT t_siswa.id, t_siswa.nama, t_kelas.nama AS nama_kelas 
                                                    FROM t_siswa 
                                                    LEFT JOIN t_kelas_siswa ON t_siswa.id = t_kelas_siswa.siswa_id AND t_kelas_siswa.status_aktif = 1
                                                    LEFT JOIN t_kelas ON t_kelas_siswa.kelas_id = t_kelas.id
                                                    ORDER BY t_siswa.nama ASC";
                                    
                                    $result_siswa = mysqli_query($koneksi, $query_siswa);

                                    // Fallback jika relasi tabel kelas berbeda
                                    if (!$result_siswa || mysqli_num_rows($result_siswa) == 0) {
                                        $result_siswa = mysqli_query($koneksi, "SELECT id, nama FROM t_siswa ORDER BY nama ASC");
                                    }

                                    if ($result_siswa && mysqli_num_rows($result_siswa) > 0) {
                                        while ($s = mysqli_fetch_assoc($result_siswa)) {
                                            $kelas_text = isset($s['nama_kelas']) && !empty($s['nama_kelas']) ? " (Kelas " . $s['nama_kelas'] . ")" : "";
                                            echo "<option value='" . $s['id'] . "'>" . htmlspecialchars($s['nama']) . $kelas_text . "</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>Belum ada data siswa di tabel t_siswa</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Prestasi / Kegiatan</label>
                                <input type="text" name="nama_prestasi" class="form-control" placeholder="Contoh: Juara 1 LKS Web Technologies" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tingkat Lomba</label>
                                <select name="tingkat" class="form-select form-control" required>
                                    <option value="Sekolah">Sekolah</option>
                                    <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                                    <option value="Provinsi">Provinsi</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Poin Reward</label>
                                <input type="number" name="poin" class="form-control" value="10" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan Tambahan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Diselenggarakan oleh Dinas Pendidikan"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="prestasi.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) include 'footer.php'; 
?>