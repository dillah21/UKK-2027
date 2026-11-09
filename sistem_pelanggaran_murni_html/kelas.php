
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

$daftar_jurusan = ['RPL', 'TKJ', 'BDPM', 'TSM', 'TKR'];

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Format nama kelas
function namaKelas($tingkat, $jurusan, $nama) {
    $nama = trim((string)$nama);

    if ($nama !== '') {
        return $nama;
    }

    return trim((string)$tingkat . ' ' . strtoupper(trim((string)$jurusan)));
}


// =====================================================
// TAMBAH KELAS
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['tambah_kelas'])
) {
    $tingkat = (int)($_POST['tingkat'] ?? 0);
    $jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));
    $nama_input = trim($_POST['nama_kelas'] ?? '');

    if (
        in_array($jurusan, $daftar_jurusan, true)
        && in_array($tingkat, [10, 11, 12], true)
        && $nama_input !== ''
    ) {
        $nama = $nama_input;

        try {
            $stmt = $koneksi->prepare(
                "INSERT INTO t_kelas
                (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, 1, NOW(), NOW())"
            );

            $stmt->bind_param("sis", $nama, $tingkat, $jurusan);

            if ($stmt->execute()) {
                $pesan_sukses = 'Data kelas berhasil ditambahkan!';
            } else {
                $pesan_error = 'Gagal menambahkan kelas: ' . $stmt->error;
            }

            $stmt->close();

        } catch (Throwable $e) {
            error_log('Error tambah kelas: ' . $e->getMessage());
            $pesan_error = 'Terjadi kesalahan saat menambahkan kelas.';
        }

    } else {
        $pesan_error = 'Isi nama kelas serta pilih tingkat dan jurusan yang valid!';
    }
}


// =====================================================
// EDIT KELAS
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['edit_kelas'])
) {
    $id = (int)($_POST['id'] ?? 0);
    $tingkat = (int)($_POST['tingkat'] ?? 0);
    $jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));
    $nama_input = trim($_POST['nama_kelas'] ?? '');

    if (
        $id > 0
        && in_array($jurusan, $daftar_jurusan, true)
        && in_array($tingkat, [10, 11, 12], true)
        && $nama_input !== ''
    ) {
        $nama = $nama_input;

        try {
            $stmt = $koneksi->prepare(
                "UPDATE t_kelas
                 SET nama = ?,
                     tingkat = ?,
                     jurusan = ?,
                     updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->bind_param("sisi", $nama, $tingkat, $jurusan, $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows >= 0) {
                    $pesan_sukses = 'Data kelas berhasil diperbarui!';
                }
            } else {
                $pesan_error = 'Gagal memperbarui kelas: ' . $stmt->error;
            }

            $stmt->close();

        } catch (Throwable $e) {
            error_log('Error edit kelas: ' . $e->getMessage());
            $pesan_error = 'Terjadi kesalahan saat memperbarui kelas.';
        }

    } else {
        $pesan_error = 'Data kelas tidak valid!';
    }
}


// =====================================================
// HAPUS KELAS
// =====================================================

if (
    isset($_GET['action'], $_GET['id'])
    && $_GET['action'] === 'hapus'
) {
    $id = (int)$_GET['id'];

    if ($id <= 0) {
        $pesan_error = 'ID kelas tidak valid.';
    } else {
        try {
            // Periksa apakah kelas masih digunakan siswa
            $cek = $koneksi->prepare(
                "SELECT COUNT(*) AS total
                 FROM t_kelas_siswa
                 WHERE kelas_id = ?"
            );

            $cek->bind_param("i", $id);
            $cek->execute();

            $data_cek = $cek->get_result()->fetch_assoc();
            $cek->close();

            $jumlah_siswa = (int)($data_cek['total'] ?? 0);

            if ($jumlah_siswa > 0) {
                $pesan_error = 'Kelas tidak dapat dihapus karena masih memiliki '
                    . $jumlah_siswa
                    . ' siswa. Silakan pindahkan siswa ke kelas lain terlebih dahulu.';
            } else {
                $hapus = $koneksi->prepare(
                    "DELETE FROM t_kelas WHERE id = ?"
                );

                $hapus->bind_param("i", $id);
                $hapus->execute();

                if ($hapus->affected_rows > 0) {
                    $pesan_sukses = 'Data kelas berhasil dihapus!';
                } else {
                    $pesan_error = 'Data kelas tidak ditemukan.';
                }

                $hapus->close();
            }

        } catch (mysqli_sql_exception $e) {
            error_log('Error hapus kelas: ' . $e->getMessage());
            $pesan_error = 'Kelas tidak dapat dihapus karena masih terhubung dengan data lain.';

        } catch (Throwable $e) {
            error_log('Error sistem hapus kelas: ' . $e->getMessage());
            $pesan_error = 'Terjadi kesalahan saat menghapus kelas.';
        }
    }
}


// =====================================================
// AMBIL DATA KELAS
// =====================================================

$result_kelas = $koneksi->query(
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     ORDER BY id ASC"
);

include 'header.php';
?>

<!-- JUDUL HALAMAN -->
<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">

    <h1 class="h2">Manajemen Data Kelas</h1>

    <button type="button"
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            data-target="#modalKelas">

        <i class="fas fa-plus mr-1"></i>
        Tambah Kelas Baru

    </button>

</div>


<!-- PESAN SUKSES -->
<?php if ($pesan_sukses !== ''): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <?= e($pesan_sukses) ?>

        <button type="button"
                class="close"
                data-dismiss="alert">

            <span>&times;</span>

        </button>

    </div>

<?php endif; ?>


<!-- PESAN ERROR -->
<?php if ($pesan_error !== ''): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <?= e($pesan_error) ?>

        <button type="button"
                class="close"
                data-dismiss="alert">

            <span>&times;</span>

        </button>

    </div>

<?php endif; ?>


<!-- =====================================================
     TABEL DATA KELAS
===================================================== -->

<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table id="tabelKelas"
                   class="table table-striped table-bordered mb-0"
                   style="width:100%">

                <thead class="thead-dark text-center">

                    <tr>
                        <th style="width:10%">No</th>
                        <th>Nama Kelas</th>
                        <th style="width:20%">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($result_kelas): ?>

                    <?php while ($r = $result_kelas->fetch_assoc()): ?>

                        <?php
                        $id = (int)$r['id'];
                        $tingkat = (int)($r['tingkat'] ?? 0);
                        $jurusan = strtoupper(trim($r['jurusan'] ?? ''));
                        $nama = $r['nama'] ?? '';

                        $tampil = namaKelas(
                            $tingkat,
                            $jurusan,
                            $nama
                        );
                        ?>

                        <tr>

                            <td class="text-center">
                                <?= $id ?>
                            </td>

                            <td>
                                <?= e($tampil) ?>
                            </td>

                            <td class="text-center">

                                <!-- TOMBOL EDIT -->
                                <button type="button"
                                        class="btn btn-warning btn-sm btn-edit-kelas"
                                        data-id="<?= $id ?>"
                                        data-tingkat="<?= $tingkat ?>"
                                        data-jurusan="<?= e($jurusan) ?>"
                                        data-nama="<?= e($nama) ?>">

                                    <i class="fas fa-edit"></i>

                                </button>


                                <!-- TOMBOL HAPUS -->
                                <button type="button"
                                        class="btn btn-danger btn-sm btn-hapus-kelas"
                                        data-id="<?= $id ?>"
                                        data-nama="<?= e($tampil) ?>">

                                    <i class="fas fa-trash"></i>

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


<!-- =====================================================
     MODAL EDIT KELAS
     DILETAKKAN DI LUAR TABEL
===================================================== -->

<div class="modal fade"
     id="modalEditKelas"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST"
              action="kelas.php"
              class="modal-content">

            <input type="hidden"
                   name="id"
                   id="edit_id">

            <div class="modal-header bg-warning">

                <h5 class="modal-title">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Data Kelas
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                <div class="form-group">

                    <label>Tingkat</label>

                    <select name="tingkat"
                            id="edit_tingkat"
                            class="form-control"
                            required>

                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>Jurusan</label>

                    <select name="jurusan"
                            id="edit_jurusan"
                            class="form-control"
                            required>

                        <?php foreach ($daftar_jurusan as $j): ?>

                            <option value="<?= e($j) ?>">
                                <?= e($j) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Nama Kelas</label>

                    <input type="text"
                           name="nama_kelas"
                           id="edit_nama_kelas"
                           class="form-control"
                           maxlength="100"
                           required
                           placeholder="Contoh: 10 RPL 1">

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">

                    Batal

                </button>

                <button type="submit"
                        name="edit_kelas"
                        class="btn btn-warning btn-sm">

                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================================
     MODAL HAPUS KELAS
     DILETAKKAN DI LUAR TABEL
===================================================== -->

<div class="modal fade"
     id="modalHapusKelas"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">
                    <i class="fas fa-trash mr-1"></i>
                    Hapus Kelas
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                Yakin ingin menghapus kelas

                <strong id="hapus_nama_kelas"></strong>?

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">

                    Batal

                </button>

                <a href="#"
                   id="tombolKonfirmasiHapus"
                   class="btn btn-danger btn-sm">

                    <i class="fas fa-trash mr-1"></i>
                    Ya, Hapus

                </a>

            </div>

        </div>

    </div>

</div>


<!-- FOOTER -->
<footer class="mt-5 py-3 text-center text-muted border-top small">

    &copy; 2026 - Sistem Informasi Pelanggaran Siswa

    &bull; Developed by

    <strong>Azril Akbar Fawji Abdillah</strong>

</footer>


<!-- PENUTUP STRUKTUR DASHBOARD -->
</main>
</div>
</div>


<!-- =====================================================
     MODAL TAMBAH KELAS
===================================================== -->

<div class="modal fade"
     id="modalKelas"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST"
              action="kelas.php"
              class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Kelas Baru
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                <div class="form-group">

                    <label>Tingkat</label>

                    <select name="tingkat"
                            class="form-control"
                            required>

                        <option value="">
                            -- Pilih Tingkat --
                        </option>

                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>Jurusan</label>

                    <select name="jurusan"
                            class="form-control"
                            required>

                        <option value="">
                            -- Pilih Jurusan --
                        </option>

                        <?php foreach ($daftar_jurusan as $j): ?>

                            <option value="<?= e($j) ?>">
                                <?= e($j) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Nama Kelas</label>

                    <input type="text"
                           name="nama_kelas"
                           class="form-control"
                           maxlength="100"
                           required
                           placeholder="Contoh: 10 RPL 1">

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-dismiss="modal">

                    Batal

                </button>

                <button type="submit"
                        name="tambah_kelas"
                        class="btn btn-primary btn-sm">

                    <i class="fas fa-save mr-1"></i>
                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


<script>
$(document).ready(function () {

    // =================================================
    // DATATABLES
    // =================================================

    $('#tabelKelas').DataTable({

        order: [],

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


    // =================================================
    // TOMBOL EDIT KELAS
    // =================================================

    $(document).on('click', '.btn-edit-kelas', function () {

        var id = $(this).attr('data-id');

        var tingkat = $(this).attr('data-tingkat');

        var jurusan = $(this).attr('data-jurusan');

        var nama = $(this).attr('data-nama');


        // Masukkan data ke modal
        $('#edit_id').val(id);

        $('#edit_tingkat').val(tingkat);

        $('#edit_jurusan').val(jurusan);

        $('#edit_nama_kelas').val(nama);


        // Tampilkan modal
        $('#modalEditKelas').modal('show');

    });


    // =================================================
    // TOMBOL HAPUS KELAS
    // =================================================

    $(document).on('click', '.btn-hapus-kelas', function () {

        var id = $(this).attr('data-id');

        var nama = $(this).attr('data-nama');


        // Tampilkan nama kelas
        $('#hapus_nama_kelas').text(nama);


        // Atur link hapus
        $('#tombolKonfirmasiHapus').attr(
            'href',
            'kelas.php?action=hapus&id=' + encodeURIComponent(id)
        );


        // Tampilkan modal hapus
        $('#modalHapusKelas').modal('show');

    });

});
</script>

</body>
</html>
