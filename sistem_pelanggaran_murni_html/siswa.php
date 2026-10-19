<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";

// 1. Proses Tambah Siswa Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $jk = trim($_POST['jk'] ?? 'L');

    if (!empty($nama)) {
        $nisn_dummy = '00' . rand(10000000, 99999999);
        $tgl_lahir_dummy = '2008-01-01';
        $alamat_dummy = '-';
        $status_aktif = 1;

        $stmt = $koneksi->prepare("INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $nis, $nisn_dummy, $nama, $jk, $tgl_lahir_dummy, $alamat_dummy, $status_aktif);
        
        if ($stmt->execute()) {
            $siswa_id = $koneksi->insert_id;
            
            if ($kelas_id > 0) {
                $ta_q = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
                $ta_id = ($ta_q && $ta_q->num_rows > 0) ? $ta_q->fetch_assoc()['id'] : 1;
                
                $tgl_mulai = date('Y-m-d');
                $tgl_selesai = date('Y-m-d', strtotime('+1 year'));
                
                $stmt_ks = $koneksi->prepare("INSERT INTO t_kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_ks->bind_param("iiissii", $siswa_id, $ta_id, $kelas_id, $tgl_mulai, $tgl_selesai, $status_aktif);
                $stmt_ks->execute();
                $stmt_ks->close();
            }
            
            $pesan_sukses = "Data siswa berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan data: " . $koneksi->error;
        }
        $stmt->close();
    } else {
        $pesan_error = "Nama siswa tidak boleh kosong!";
    }
}

// 2. Ambil Pilihan Kelas
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");

// 3. Ambil Data Siswa beserta Kelasnya menggunakan JOIN lewat t_kelas_siswa
$result_siswa = $koneksi->query("
    SELECT s.*, k.nama AS nama_kelas 
    FROM t_siswa s 
    LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id 
    ORDER BY s.id DESC
");

// Panggil Header Terpusat
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Data Siswa</h1>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahSiswa">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa Baru
                </button>
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

            <!-- Tabel Data Siswa -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Siswa Terdaftar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelSiswa" class="table table-striped table-bordered mb-0" style="width:100%;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">NIS / No Induk</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th style="width: 10%;">L/P</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_siswa && $result_siswa->num_rows > 0): $no = 1; while ($row = $result_siswa->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jenis_kelamin'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <a href="edit_siswa.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="hapus_siswa.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?');" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-5 py-3 text-center text-muted border-top small">
                &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
            </footer>
        </main>
    </div>
</div>

<!-- MODAL FORM TAMBAH SISWA -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="siswa.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIS / No Induk</label>
                    <input type="text" class="form-control" name="nis" placeholder="Contoh: 12345">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap Siswa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" required placeholder="Masukkan nama lengkap siswa">
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <select class="form-control" name="kelas_id">
                        <option value="0">-- Pilih Kelas --</option>
                        <?php if ($list_kelas && $list_kelas->num_rows > 0): ?>
                            <?php while ($kls = $list_kelas->fetch_assoc()): ?>
                                <option value="<?= $kls['id']; ?>"><?= htmlspecialchars($kls['nama']); ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jk">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_siswa" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan Data</button>
            </div>
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
        $('#tabelSiswa').DataTable({
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