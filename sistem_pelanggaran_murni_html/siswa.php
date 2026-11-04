<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| FORMAT NAMA KELAS
|--------------------------------------------------------------------------
| Contoh data lama:
| tingkat = 11
| jurusan = TKR
| nama    = 11 MPLB 10
|
| Tampilan: 11 TKR 10
*/
function formatKelas($tingkat, $jurusan, $nama) {
    $tingkat = trim((string)$tingkat);
    $jurusan = trim((string)$jurusan);
    $nama = trim((string)$nama);

    // Ambil angka rombel terakhir dari nama kelas lama.
    if (preg_match('/(\d+)\s*$/', $nama, $matches)) {
        $rombel = $matches[1];
    } else {
        $rombel = '';
    }

    $bagian = array_filter(
        [$tingkat, $jurusan, $rombel],
        function ($value) {
            return $value !== '';
        }
    );

    return implode(' ', $bagian);
}

// Ambil tahun ajaran aktif
function getTahunAjaranAktif($koneksi) {
    $result = $koneksi->query(
        "SELECT id FROM t_tahun_ajaran
         WHERE status_aktif = 1
         ORDER BY tanggal_mulai DESC, id DESC
         LIMIT 1"
    );

    if ($result && $result->num_rows > 0) {
        return (int)$result->fetch_assoc()['id'];
    }

    return 0;
}

// ======================================================
// TAMBAH SISWA
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['tambah_siswa'])) {

    $nis_raw = trim($_POST['nis'] ?? '');
    $nis = preg_replace('/\\D+/', '', $nis_raw);
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $jk = $_POST['jk'] ?? 'L';

    if (strlen($nis_raw) > 15) {
        $pesan_error = 'NIS maksimal 15 digit!';
    } elseif ($nama === '') {
        $pesan_error = 'Nama siswa tidak boleh kosong!';
    } elseif (!in_array($jk, ['L', 'P'], true)) {
        $pesan_error = 'Jenis kelamin tidak valid!';
    } else {
        $nisn = '00' . random_int(10000000, 99999999);
        $tanggal_lahir = '2008-01-01';
        $alamat = '-';
        $status_aktif = 1;

        $stmt = $koneksi->prepare(
            "INSERT INTO t_siswa
            (nis, nisn, nama, jenis_kelamin, tanggal_lahir,
             alamat, status_aktif)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'ssssssi',
            $nis,
            $nisn,
            $nama,
            $jk,
            $tanggal_lahir,
            $alamat,
            $status_aktif
        );

        if ($stmt->execute()) {
            $siswa_id = $koneksi->insert_id;

            if ($kelas_id > 0) {
                $ta_id = getTahunAjaranAktif($koneksi);
                $mulai = date('Y-m-d');
                $selesai = date('Y-m-d', strtotime('+1 year'));

                $cek = $koneksi->prepare(
                    "SELECT id FROM t_kelas WHERE id = ?"
                );
                $cek->bind_param('i', $kelas_id);
                $cek->execute();
                $valid = $cek->get_result()->num_rows > 0;
                $cek->close();

                if ($valid) {
                    $status_relasi = 1;

                    $relasi = $koneksi->prepare(
                        "INSERT INTO t_kelas_siswa
                        (siswa_id, tahun_ajaran_id, kelas_id,
                         tanggal_mulai, tanggal_selesai, status_aktif)
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );

                    $relasi->bind_param(
                        'iiissi',
                        $siswa_id,
                        $ta_id,
                        $kelas_id,
                        $mulai,
                        $selesai,
                        $status_relasi
                    );

                    if (!$relasi->execute()) {
                        $pesan_error =
                            'Siswa tersimpan, tetapi relasi kelas gagal dibuat.';
                    }

                    $relasi->close();
                }
            }

            if ($pesan_error === '') {
                $pesan_sukses = 'Data siswa berhasil ditambahkan!';
            }
        } else {
            $pesan_error = 'Gagal menyimpan data siswa: ' . $stmt->error;
        }

        $stmt->close();
    }
}

// ======================================================
// EDIT SISWA
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['edit_siswa'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nis_raw = trim($_POST['nis'] ?? '');
    $nis = preg_replace('/\\D+/', '', $nis_raw);
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $jk = $_POST['jk'] ?? 'L';

    if ($id <= 0 || $nama === '' || strlen($nis_raw) > 15) {
        $pesan_error = strlen($nis_raw) > 15 ? 'NIS maksimal 15 digit!' : 'Data siswa tidak valid!';
    } elseif (!in_array($jk, ['L', 'P'], true)) {
        $pesan_error = 'Jenis kelamin tidak valid!';
    } else {
        $stmt = $koneksi->prepare(
            "UPDATE t_siswa
             SET nis = ?, nama = ?, jenis_kelamin = ?
             WHERE id = ?"
        );

        $stmt->bind_param('sssi', $nis, $nama, $jk, $id);

        if ($stmt->execute()) {
            $stmt->close();

            if ($kelas_id > 0) {
                $cek = $koneksi->prepare(
                    "SELECT id FROM t_kelas WHERE id = ?"
                );
                $cek->bind_param('i', $kelas_id);
                $cek->execute();
                $kelas_valid = $cek->get_result()->num_rows > 0;
                $cek->close();

                if (!$kelas_valid) {
                    $pesan_error = 'Kelas tidak ditemukan!';
                } else {
                    $cek_relasi = $koneksi->prepare(
                        "SELECT id FROM t_kelas_siswa
                         WHERE siswa_id = ? AND status_aktif = 1
                         LIMIT 1"
                    );
                    $cek_relasi->bind_param('i', $id);
                    $cek_relasi->execute();
                    $hasil_relasi = $cek_relasi->get_result();
                    $cek_relasi->close();

                    if ($hasil_relasi->num_rows > 0) {
                        $relasi_id = (int)$hasil_relasi->fetch_assoc()['id'];

                        $update = $koneksi->prepare(
                            "UPDATE t_kelas_siswa
                             SET kelas_id = ?
                             WHERE id = ?"
                        );
                        $update->bind_param('ii', $kelas_id, $relasi_id);
                        $update->execute();
                        $update->close();
                    } else {
                        $ta_id = getTahunAjaranAktif($koneksi);
                        $mulai = date('Y-m-d');
                        $selesai = date('Y-m-d', strtotime('+1 year'));
                        $status_relasi = 1;

                        $insert = $koneksi->prepare(
                            "INSERT INTO t_kelas_siswa
                            (siswa_id, tahun_ajaran_id, kelas_id,
                             tanggal_mulai, tanggal_selesai, status_aktif)
                            VALUES (?, ?, ?, ?, ?, ?)"
                        );

                        $insert->bind_param(
                            'iiissi',
                            $id,
                            $ta_id,
                            $kelas_id,
                            $mulai,
                            $selesai,
                            $status_relasi
                        );

                        $insert->execute();
                        $insert->close();
                    }

                    $pesan_sukses = 'Data siswa berhasil diperbarui!';
                }
            } else {
                $nonaktif = $koneksi->prepare(
                    "UPDATE t_kelas_siswa
                     SET status_aktif = 0
                     WHERE siswa_id = ? AND status_aktif = 1"
                );
                $nonaktif->bind_param('i', $id);
                $nonaktif->execute();
                $nonaktif->close();

                $pesan_sukses = 'Data siswa berhasil diperbarui!';
            }
        } else {
            $pesan_error = 'Gagal memperbarui data siswa.';
            $stmt->close();
        }
    }
}

// ======================================================
// HAPUS SISWA
// ======================================================
if (isset($_GET['action'], $_GET['id'])
    && $_GET['action'] === 'hapus') {

    $id_hapus = (int)$_GET['id'];

    if ($id_hapus > 0) {
        $stmt = $koneksi->prepare(
            "DELETE FROM t_kelas_siswa WHERE siswa_id = ?"
        );
        $stmt->bind_param('i', $id_hapus);
        $stmt->execute();
        $stmt->close();

        $stmt = $koneksi->prepare(
            "DELETE FROM t_siswa WHERE id = ?"
        );
        $stmt->bind_param('i', $id_hapus);

        if ($stmt->execute()) {
            $pesan_sukses = 'Data siswa berhasil dihapus!';
        } else {
            $pesan_error = 'Gagal menghapus data siswa: ' . $stmt->error;
        }

        $stmt->close();
    }
}

// ======================================================
// AMBIL PILIHAN KELAS
// ======================================================
$list_kelas = $koneksi->query(
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     ORDER BY tingkat, jurusan, nama"
);

// ======================================================
// AMBIL SISWA DAN RELASI KELAS
// ======================================================
$result_siswa = $koneksi->query(
    "SELECT
        s.*,
        k.id AS kelas_id,
        k.nama AS nama_kelas,
        k.tingkat AS tingkat_kelas,
        k.jurusan AS jurusan_kelas
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks
        ON s.id = ks.siswa_id
        AND ks.status_aktif = 1
     LEFT JOIN t_kelas k
        ON ks.kelas_id = k.id
     ORDER BY s.id DESC"
);

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Data Siswa</h1>
    <button type="button" class="btn btn-primary btn-sm"
            data-toggle="modal" data-target="#modalTambahSiswa">
        <i class="fas fa-plus mr-1"></i> Tambah Siswa Baru
    </button>
</div>

<?php if ($pesan_sukses !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= e($pesan_sukses) ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= e($pesan_error) ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Siswa Terdaftar</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelSiswa"
                   class="table table-striped table-bordered mb-0"
                   style="width:100%">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>NIS / No Induk</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>L/P</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_siswa): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result_siswa->fetch_assoc()): ?>
                        <?php
                        $id = (int)$row['id'];
                        $nis = $row['nis'] ?? '';
                        $nama = $row['nama'] ?? '';
                        $jk = $row['jenis_kelamin'] ?? 'L';
                        $kelas_id_aktif = (int)($row['kelas_id'] ?? 0);

                        $label_kelas = '-';

                        if ($kelas_id_aktif > 0) {
                            $label_kelas = formatKelas(
                                $row['tingkat_kelas'] ?? '',
                                $row['jurusan_kelas'] ?? '',
                                $row['nama_kelas'] ?? ''
                            );
                        }
                        ?>

                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= e($nis ?: '-') ?></td>
                            <td><?= e($nama) ?></td>
                            <td class="text-center"><?= e($label_kelas) ?></td>
                            <td class="text-center"><?= e($jk) ?></td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editSiswa<?= $id ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#hapusSiswa<?= $id ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT SISWA -->
                        <div class="modal fade" id="editSiswa<?= $id ?>"
                             tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form method="POST" action="siswa.php"
                                      class="modal-content">

                                    <input type="hidden" name="id"
                                           value="<?= $id ?>">

                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Edit Data Siswa</h5>
                                        <button type="button" class="close"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>NIS / No Induk</label>
                                            <input type="text" name="nis"
                                                   class="form-control"
                                                   value="<?= e($nis) ?>" maxlength="15" inputmode="numeric" pattern="[0-9]{0,15}" title="Maksimal 15 digit">
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Lengkap Siswa</label>
                                            <input type="text" name="nama"
                                                   class="form-control"
                                                   required
                                                   value="<?= e($nama) ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Kelas</label>
                                            <select name="kelas_id"
                                                    class="form-control">
                                                <option value="0">-- Pilih Kelas --</option>

                                                <?php
                                                $kelas_edit = $koneksi->query(
                                                    "SELECT id, nama, tingkat, jurusan
                                                     FROM t_kelas
                                                     ORDER BY tingkat, jurusan, nama"
                                                );

                                                if ($kelas_edit):
                                                    while ($kls = $kelas_edit->fetch_assoc()):
                                                ?>
                                                    <option
                                                        value="<?= (int)$kls['id'] ?>"
                                                        <?= ((int)$kls['id'] === $kelas_id_aktif) ? 'selected' : '' ?>>
                                                        <?= e(formatKelas(
                                                            $kls['tingkat'],
                                                            $kls['jurusan'],
                                                            $kls['nama']
                                                        )) ?>
                                                    </option>
                                                <?php
                                                    endwhile;
                                                endif;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <select name="jk" class="form-control">
                                                <option value="L" <?= $jk === 'L' ? 'selected' : '' ?>>
                                                    Laki-laki
                                                </option>
                                                <option value="P" <?= $jk === 'P' ? 'selected' : '' ?>>
                                                    Perempuan
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-dismiss="modal">
                                            Batal
                                        </button>
                                        <button type="submit" name="edit_siswa"
                                                class="btn btn-warning btn-sm">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- MODAL HAPUS SISWA -->
                        <div class="modal fade" id="hapusSiswa<?= $id ?>"
                             tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Hapus Siswa</h5>
                                        <button type="button"
                                                class="close text-white"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        Yakin ingin menghapus data siswa
                                        <strong><?= e($nama) ?></strong>?
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-dismiss="modal">
                                            Batal
                                        </button>
                                        <a href="siswa.php?action=hapus&id=<?= $id ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                            Ya, Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SISWA -->
<div class="modal fade" id="modalTambahSiswa"
     tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="siswa.php" class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Siswa Baru</h5>
                <button type="button" class="close text-white"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>NIS / No Induk</label>
                    <input type="text" name="nis"
                           class="form-control" maxlength="15" inputmode="numeric" pattern="[0-9]{0,15}" title="Maksimal 15 digit">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap Siswa</label>
                    <input type="text" name="nama"
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kelas</label>
                    <select name="kelas_id" class="form-control">
                        <option value="0">-- Pilih Kelas --</option>

                        <?php if ($list_kelas): ?>
                            <?php while ($kls = $list_kelas->fetch_assoc()): ?>
                                <option value="<?= (int)$kls['id'] ?>">
                                    <?= e(formatKelas(
                                        $kls['tingkat'],
                                        $kls['jurusan'],
                                        $kls['nama']
                                    )) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jk" class="form-control">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                    Batal
                </button>
                <button type="submit" name="tambah_siswa"
                        class="btn btn-primary btn-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<footer class="mt-5 py-3 text-center text-muted border-top small">
    &copy; 2026 - Sistem Informasi Pelanggaran Siswa
    &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data per halaman',
            zeroRecords: 'Tidak ada data yang ditemukan',
            info: 'Menampilkan halaman _PAGE_ dari _PAGES_',
            infoEmpty: 'Tidak ada data tersedia',
            infoFiltered: '(disaring dari _MAX_ total data)',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Selanjutnya',
                previous: 'Sebelumnya'
            }
        }
    });
});
</script>

</body>
</html>