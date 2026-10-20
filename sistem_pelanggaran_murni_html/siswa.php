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

// 2. Proses Edit Siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_siswa'])) {
    $id = intval($_POST['id'] ?? 0);
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $jk = trim($_POST['jk'] ?? 'L');

    if ($id > 0 && !empty($nama)) {
        $stmt = $koneksi->prepare("UPDATE t_siswa SET nis = ?, nama = ?, jenis_kelamin = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nis, $nama, $jk, $id);
        
        if ($stmt->execute()) {
            $stmt->close();

            // Update atau Insert relasi kelas di t_kelas_siswa
            $cek_ks = $koneksi->query("SELECT id FROM t_kelas_siswa WHERE siswa_id = $id AND status_aktif = 1");
            if ($cek_ks && $cek_ks->num_rows > 0) {
                if ($kelas_id > 0) {
                    $stmt_up = $koneksi->prepare("UPDATE t_kelas_siswa SET kelas_id = ? WHERE siswa_id = ? AND status_aktif = 1");
                    $stmt_up->bind_param("ii", $kelas_id, $id);
                    $stmt_up->execute();
                    $stmt_up->close();
                } else {
                    $koneksi->query("UPDATE t_kelas_siswa SET status_aktif = 0 WHERE siswa_id = $id AND status_aktif = 1");
                }
            } else if ($kelas_id > 0) {
                $ta_q = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
                $ta_id = ($ta_q && $ta_q->num_rows > 0) ? $ta_q->fetch_assoc()['id'] : 1;
                $tgl_mulai = date('Y-m-d');
                $tgl_selesai = date('Y-m-d', strtotime('+1 year'));
                $status_aktif = 1;

                $stmt_ins = $koneksi->prepare("INSERT INTO t_kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_ins->bind_param("iiissii", $id, $ta_id, $kelas_id, $tgl_mulai, $tgl_selesai, $status_aktif);
                $stmt_ins->execute();
                $stmt_ins->close();
            }

            $pesan_sukses = "Data siswa berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui data: " . $koneksi->error;
        }
    } else {
        $pesan_error = "Nama siswa tidak boleh kosong!";
    }
}

// 3. Proses Hapus Siswa
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    if ($id_hapus > 0) {
        // Hapus relasi kelas dan data siswa
        @$koneksi->query("DELETE FROM t_kelas_siswa WHERE siswa_id = $id_hapus");
        if ($koneksi->query("DELETE FROM t_siswa WHERE id = $id_hapus")) {
            $pesan_sukses = "Data siswa berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus data: " . $koneksi->error;
        }
    }
}

// Ambil Pilihan Kelas untuk Dropdown
$list_kelas = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");

// Ambil Data Siswa beserta Kelasnya menggunakan JOIN lewat t_kelas_siswa
$result_siswa = $koneksi->query("
    SELECT s.*, k.id AS kelas_id, k.nama AS nama_kelas 
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
                                <?php if ($result_siswa && $result_siswa->num_rows > 0): $no = 1; while ($row = $result_siswa->fetch_assoc()): 
                                    $id = $row['id'];
                                    $nis = htmlspecialchars($row['nis'] ?? '');
                                    $nama = htmlspecialchars($row['nama'] ?? '');
                                    $nama_kelas = htmlspecialchars($row['nama_kelas'] ?? '-');
                                    $kelas_id_aktif = $row['kelas_id'] ?? 0;
                                    $jk = $row['jenis_kelamin'] ?? 'L';
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center"><?= !empty($nis) ? $nis : '-'; ?></td>
                                        <td><?= $nama; ?></td>
                                        <td class="text-center"><?= $nama_kelas; ?></td>
                                        <td class="text-center"><?= $jk; ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editSiswa<?= $id; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusSiswa<?= $id; ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT SISWA -->
                                    <div class="modal fade" id="editSiswa<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form method="POST" action="siswa.php" class="modal-content">
                                                <input type="hidden" name="id" value="<?= $id; ?>">
                                                <div class="modal-header bg-warning text-dark">
                                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Data Siswa</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">NIS / No Induk</label>
                                                        <input type="text" class="form-control" name="nis" value="<?= $nis; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="nama" required value="<?= $nama; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Kelas</label>
                                                        <select class="form-control" name="kelas_id">
                                                            <option value="0">-- Pilih Kelas --</option>
                                                            <?php 
                                                            // Reset pointer atau query ulang list kelas untuk setiap modal
                                                            $list_kelas_m = $koneksi->query("SELECT * FROM t_kelas ORDER BY nama ASC");
                                                            if ($list_kelas_m && $list_kelas_m->num_rows > 0): 
                                                                while ($kls = $list_kelas_m->fetch_assoc()): 
                                                            ?>
                                                                    <option value="<?= $kls['id']; ?>" <?= ($kelas_id_aktif == $kls['id']) ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($kls['nama']); ?>
                                                                    </option>
                                                            <?php 
                                                                endwhile; 
                                                            endif; 
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Jenis Kelamin</label>
                                                        <select class="form-control" name="jk">
                                                            <option value="L" <?= ($jk == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                                            <option value="P" <?= ($jk == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit_siswa" class="btn btn-warning btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- MODAL HAPUS SISWA -->
                                    <div class="modal fade" id="hapusSiswa<?= $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-trash mr-2"></i> Hapus Siswa</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    Apakah Anda yakin ingin menghapus data siswa <strong><?= $nama; ?></strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                    <a href="siswa.php?action=hapus&id=<?= $id; ?>" class="btn btn-danger btn-sm">Ya, Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                <button type="submit" name="tambah_siswa" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan</button>
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