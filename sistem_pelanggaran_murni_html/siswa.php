
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

// FORMAT NAMA KELAS
function formatKelas($tingkat, $jurusan, $nama) {
    $tingkat = trim((string)$tingkat);
    $jurusan = trim((string)$jurusan);
    $nama = trim((string)$nama);

    if (preg_match('/(\d+)\s*$/', $nama, $matches)) {
        $rombel = $matches[1];
    } else {
        $rombel = '';
    }

    return implode(' ', array_filter(
        [$tingkat, $jurusan, $rombel],
        function ($value) {
            return $value !== '';
        }
    ));
}

// TAHUN AJARAN AKTIF
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

// ======================================
// TAMBAH SISWA
// ======================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {

    $nis_raw = trim($_POST['nis'] ?? '');
    $nis = preg_replace('/\D+/', '', $nis_raw);
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
            (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
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

                $cek = $koneksi->prepare(
                    "SELECT id FROM t_kelas WHERE id = ?"
                );

                $cek->bind_param('i', $kelas_id);
                $cek->execute();

                $valid = $cek->get_result()->num_rows > 0;
                $cek->close();

                if ($valid && $ta_id > 0) {

                    $mulai = date('Y-m-d');
                    $selesai = date('Y-m-d', strtotime('+1 year'));
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
                        $pesan_error = 'Siswa tersimpan, tetapi relasi kelas gagal dibuat.';
                    }

                    $relasi->close();

                } elseif (!$valid) {
                    $pesan_error = 'Siswa tersimpan, tetapi kelas tidak ditemukan.';
                } else {
                    $pesan_error = 'Siswa tersimpan, tetapi tahun ajaran aktif belum tersedia.';
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

// ======================================
// EDIT SISWA
// ======================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_siswa'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nis_raw = trim($_POST['nis'] ?? '');
    $nis = preg_replace('/\D+/', '', $nis_raw);
    $nama = trim($_POST['nama'] ?? '');
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $jk = $_POST['jk'] ?? 'L';

    if ($id <= 0 || $nama === '' || strlen($nis_raw) > 15) {

        $pesan_error = strlen($nis_raw) > 15
            ? 'NIS maksimal 15 digit!'
            : 'Data siswa tidak valid!';

    } elseif (!in_array($jk, ['L', 'P'], true)) {

        $pesan_error = 'Jenis kelamin tidak valid!';

    } else {

        $cek_siswa = $koneksi->prepare(
            "SELECT id FROM t_siswa
             WHERE id = ? AND status_aktif = 1"
        );

        $cek_siswa->bind_param('i', $id);
        $cek_siswa->execute();

        $siswa_ada = $cek_siswa->get_result()->num_rows > 0;
        $cek_siswa->close();

        if (!$siswa_ada) {

            $pesan_error = 'Data siswa aktif tidak ditemukan!';

        } else {

            $koneksi->begin_transaction();

            try {

                $stmt = $koneksi->prepare(
                    "UPDATE t_siswa
                     SET nis = ?, nama = ?, jenis_kelamin = ?
                     WHERE id = ? AND status_aktif = 1"
                );

                $stmt->bind_param('sssi', $nis, $nama, $jk, $id);

                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }

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
                        throw new Exception('Kelas tidak ditemukan!');
                    }

                    $cek_relasi = $koneksi->prepare(
                        "SELECT id FROM t_kelas_siswa
                         WHERE siswa_id = ?
                         AND status_aktif = 1
                         ORDER BY id DESC
                         LIMIT 1"
                    );

                    $cek_relasi->bind_param('i', $id);
                    $cek_relasi->execute();

                    $hasil = $cek_relasi->get_result();
                    $relasi_data = $hasil->fetch_assoc();

                    $cek_relasi->close();

                    if ($relasi_data) {

                        $relasi_id = (int)$relasi_data['id'];

                        $update = $koneksi->prepare(
                            "UPDATE t_kelas_siswa
                             SET kelas_id = ?
                             WHERE id = ?"
                        );

                        $update->bind_param('ii', $kelas_id, $relasi_id);

                        if (!$update->execute()) {
                            throw new Exception($update->error);
                        }

                        $update->close();

                    } else {

                        $ta_id = getTahunAjaranAktif($koneksi);

                        if ($ta_id <= 0) {
                            throw new Exception('Tahun ajaran aktif belum tersedia!');
                        }

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

                        if (!$insert->execute()) {
                            throw new Exception($insert->error);
                        }

                        $insert->close();
                    }

                } else {

                    $nonaktif = $koneksi->prepare(
                        "UPDATE t_kelas_siswa
                         SET status_aktif = 0
                         WHERE siswa_id = ?
                         AND status_aktif = 1"
                    );

                    $nonaktif->bind_param('i', $id);

                    if (!$nonaktif->execute()) {
                        throw new Exception($nonaktif->error);
                    }

                    $nonaktif->close();
                }

                $koneksi->commit();

                $pesan_sukses = 'Data siswa berhasil diperbarui!';

            } catch (Exception $e) {

                $koneksi->rollback();
                $pesan_error = 'Gagal memperbarui data siswa: ' . $e->getMessage();
            }
        }
    }
}

// ======================================
// HAPUS SISWA (SOFT DELETE)
// Tidak menghapus riwayat pelanggaran
// ======================================
if (isset($_GET['action'], $_GET['id'])
    && $_GET['action'] === 'hapus') {

    $id_hapus = (int)$_GET['id'];

    if ($id_hapus > 0) {

        $stmt = $koneksi->prepare(
            "UPDATE t_siswa
             SET status_aktif = 0
             WHERE id = ?"
        );

        $stmt->bind_param('i', $id_hapus);

        if ($stmt->execute()) {
            $pesan_sukses = 'Siswa berhasil dinonaktifkan. Riwayat pelanggaran tetap tersimpan.';
        } else {
            $pesan_error = 'Gagal menonaktifkan siswa: ' . $stmt->error;
        }

        $stmt->close();
    }
}

// ======================================
// AMBIL DATA KELAS
// ======================================
$list_kelas = $koneksi->query(
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     ORDER BY tingkat, jurusan, nama"
);

// ======================================
// AMBIL DATA SISWA AKTIF SAJA
// ======================================
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
     WHERE s.status_aktif = 1
     ORDER BY s.id ASC"
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

                            <td class="text-center">
                                <?= e($nis ?: '-') ?>
                            </td>

                            <td><?= e($nama) ?></td>

                            <td class="text-center">
                                <?= e($label_kelas) ?>
                            </td>

                            <td class="text-center">
                                <?= e($jk) ?>
                            </td>

                            <td class="text-center">

                                <button type="button"
                                        class="btn btn-warning btn-sm btn-edit-siswa"
                                        data-id="<?= $id ?>"
                                        data-nis="<?= e($nis) ?>"
                                        data-nama="<?= e($nama) ?>"
                                        data-jk="<?= e($jk) ?>"
                                        data-kelas="<?= $kelas_id_aktif ?>">

                                    <i class="fas fa-edit"></i>

                                </button>

                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#hapusSiswa<?= $id ?>">

                                    <i class="fas fa-user-slash"></i>

                                </button>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ======================================
     MODAL EDIT SISWA
====================================== -->

<div class="modal fade" id="editSiswa" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST" action="siswa.php" class="modal-content">

            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header bg-warning">

                <h5 class="modal-title">Edit Data Siswa</h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>NIS / No Induk</label>
                    <input type="text"
                           name="nis"
                           id="edit_nis"
                           class="form-control"
                           maxlength="15"
                           inputmode="numeric"
                           pattern="[0-9]{0,15}">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap Siswa</label>
                    <input type="text"
                           name="nama"
                           id="edit_nama"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Kelas</label>

                    <select name="kelas_id"
                            id="edit_kelas"
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

                            <option value="<?= (int)$kls['id'] ?>">
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

                    <select name="jk" id="edit_jk" class="form-control">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                        name="edit_siswa"
                        class="btn btn-warning btn-sm">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

<!-- ======================================
     MODAL NONAKTIFKAN SISWA
====================================== -->

<?php
$result_hapus = $koneksi->query(
    "SELECT id, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY id ASC"
);

if ($result_hapus):
    while ($hapus = $result_hapus->fetch_assoc()):
        $id_hapus_modal = (int)$hapus['id'];
?>

<div class="modal fade"
     id="hapusSiswa<?= $id_hapus_modal ?>"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">Nonaktifkan Siswa</h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                Yakin ingin menonaktifkan siswa
                <strong><?= e($hapus['nama']) ?></strong>?

                <p class="text-muted small mt-2 mb-0">
                    Riwayat pelanggaran siswa tetap disimpan.
                </p>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                    Batal
                </button>

                <a href="siswa.php?action=hapus&id=<?= $id_hapus_modal ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menonaktifkan siswa ini?')">
                    Ya, Nonaktifkan
                </a>

            </div>

        </div>

    </div>

</div>

<?php
    endwhile;
endif;
?>

<!-- ======================================
     MODAL TAMBAH SISWA
====================================== -->

<div class="modal fade" id="modalTambahSiswa"
     tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST" action="siswa.php" class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">Tambah Siswa Baru</h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>NIS / No Induk</label>

                    <input type="text"
                           name="nis"
                           class="form-control"
                           maxlength="15"
                           inputmode="numeric"
                           pattern="[0-9]{0,15}">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap Siswa</label>

                    <input type="text"
                           name="nama"
                           class="form-control"
                           required>
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

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                        name="tambah_siswa"
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

    // EDIT SISWA
    $(document).on('click', '.btn-edit-siswa', function () {

        let tombol = $(this);

        $('#edit_id').val(tombol.attr('data-id'));
        $('#edit_nis').val(tombol.attr('data-nis'));
        $('#edit_nama').val(tombol.attr('data-nama'));
        $('#edit_jk').val(tombol.attr('data-jk'));
        $('#edit_kelas').val(tombol.attr('data-kelas'));

        $('#editSiswa').modal('show');

    });

});
</script>

</body>
</html>
