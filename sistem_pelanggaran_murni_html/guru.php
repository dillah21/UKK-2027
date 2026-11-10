
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


// =====================================================
// HELPER
// =====================================================

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}


// =====================================================
// 1. TAMBAH GURU
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_guru'])) {

    $nip_raw = trim($_POST['nip'] ?? '');
    $nip = preg_replace('/\D+/', '', $nip_raw);
    $nama = trim($_POST['nama'] ?? '');

    if (
        $nama === '' ||
        strlen($nip_raw) > 15 ||
        ($nip_raw !== '' && strlen($nip) !== strlen($nip_raw))
    ) {

        $pesan_error = strlen($nip_raw) > 15
            ? 'NIP maksimal 15 digit!'
            : 'Nama wajib diisi dan NIP hanya boleh berisi angka.';

    } else {

        try {

            $koneksi->begin_transaction();

            $suffix = bin2hex(random_bytes(4));

            $email_baru = 'guru.' . $suffix . '@sekolah.sch.id';

            $password_sementara = bin2hex(random_bytes(6));

            $hash = password_hash($password_sementara, PASSWORD_DEFAULT);

            $token = bin2hex(random_bytes(20));


            // BUAT AKUN PENGGUNA
            $user = $koneksi->prepare("
                INSERT INTO t_users
                (name, email, password, remember_token, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'guru', NOW(), NOW())
            ");

            $user->bind_param(
                'ssss',
                $nama,
                $email_baru,
                $hash,
                $token
            );

            $user->execute();

            $user_id_baru = $koneksi->insert_id;

            $user->close();


            // SIMPAN DATA GURU
            $status_aktif = 1;

            $guru = $koneksi->prepare("
                INSERT INTO t_guru
                (nip, nama, email, status_aktif, user_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $guru->bind_param(
                'sssii',
                $nip,
                $nama,
                $email_baru,
                $status_aktif,
                $user_id_baru
            );

            $guru->execute();

            $guru->close();


            $koneksi->commit();

            $pesan_sukses = 'Guru berhasil ditambahkan. Akun login: '
                . $email_baru
                . ' | Password sementara: '
                . $password_sementara;

        } catch (Throwable $e) {

            $koneksi->rollback();

            error_log('Error tambah guru: ' . $e->getMessage());

            $pesan_error = 'Gagal menambahkan guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 2. EDIT GURU
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_guru'])) {

    $id = (int)($_POST['id'] ?? 0);

    $nip_raw = trim($_POST['nip'] ?? '');

    $nip = preg_replace('/\D+/', '', $nip_raw);

    $nama = trim($_POST['nama'] ?? '');


    if (
        $id <= 0 ||
        $nama === '' ||
        strlen($nip_raw) > 15 ||
        ($nip_raw !== '' && strlen($nip) !== strlen($nip_raw))
    ) {

        $pesan_error = strlen($nip_raw) > 15
            ? 'NIP maksimal 15 digit!'
            : 'Data guru tidak valid. NIP hanya boleh berisi angka.';

    } else {

        try {

            $koneksi->begin_transaction();


            // CEK DATA GURU
            $cek_guru = $koneksi->prepare("
                SELECT id
                FROM t_guru
                WHERE id = ?
            ");

            $cek_guru->bind_param('i', $id);

            $cek_guru->execute();

            $data_guru = $cek_guru->get_result()->fetch_assoc();

            $cek_guru->close();


            if (!$data_guru) {

                throw new Exception('Data guru tidak ditemukan.');

            }


            // UPDATE DATA GURU
            $stmt = $koneksi->prepare("
                UPDATE t_guru
                SET nip = ?,
                    nama = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->bind_param('ssi', $nip, $nama, $id);

            $stmt->execute();

            $stmt->close();


            // UPDATE NAMA AKUN PENGGUNA
            $user = $koneksi->prepare("
                UPDATE t_users u
                INNER JOIN t_guru g ON g.user_id = u.id
                SET u.name = ?,
                    u.updated_at = NOW()
                WHERE g.id = ?
            ");

            $user->bind_param('si', $nama, $id);

            $user->execute();

            $user->close();


            $koneksi->commit();

            $pesan_sukses = 'Data guru berhasil diperbarui.';

        } catch (Throwable $e) {

            $koneksi->rollback();

            error_log('Error edit guru: ' . $e->getMessage());

            $pesan_error = 'Gagal memperbarui guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 3. HAPUS GURU
// =====================================================

if (
    isset($_GET['action'], $_GET['id']) &&
    $_GET['action'] === 'hapus'
) {

    $id_hapus = (int)$_GET['id'];

    if ($id_hapus <= 0) {

        $pesan_error = 'ID guru tidak valid.';

    } else {

        try {

            $koneksi->begin_transaction();


            // CEK APAKAH GURU MASIH MENJADI WALI KELAS
            $cek = $koneksi->prepare("
                SELECT COUNT(*) AS total
                FROM t_wali_kelas
                WHERE guru_id = ?
            ");

            $cek->bind_param('i', $id_hapus);

            $cek->execute();

            $hasil = $cek->get_result()->fetch_assoc();

            $cek->close();


            if ((int)($hasil['total'] ?? 0) > 0) {

                $koneksi->rollback();

                $pesan_error = 'Guru tidak dapat dihapus karena masih terdaftar sebagai wali kelas. Silakan pindahkan atau lepaskan penugasannya terlebih dahulu.';

            } else {


                // HAPUS DATA GURU
                $hapus = $koneksi->prepare("
                    DELETE FROM t_guru
                    WHERE id = ?
                ");

                $hapus->bind_param('i', $id_hapus);

                $hapus->execute();


                if ($hapus->affected_rows > 0) {

                    $koneksi->commit();

                    $pesan_sukses = 'Data guru berhasil dihapus!';

                } else {

                    $koneksi->rollback();

                    $pesan_error = 'Data guru tidak ditemukan.';
                }

                $hapus->close();
            }

        } catch (Throwable $e) {

            $koneksi->rollback();

            error_log('Error hapus guru: ' . $e->getMessage());

            $pesan_error = 'Gagal menghapus data guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 4. AMBIL DATA GURU
// =====================================================

$result_guru = $koneksi->query("
    SELECT *
    FROM t_guru
    WHERE id BETWEEN 1 AND 37
    ORDER BY id ASC
");


// =====================================================
// 5. HEADER DASHBOARD
// =====================================================

include 'header.php';

?>


<!-- =====================================================
     JUDUL HALAMAN
===================================================== -->

<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">

    <h1 class="h2">Manajemen Data Guru</h1>

    <button
        type="button"
        class="btn btn-primary btn-sm"
        data-toggle="modal"
        data-target="#modalGuru"
    >

        <i class="fas fa-plus mr-1"></i>
        Tambah Guru Baru

    </button>

</div>


<!-- =====================================================
     NOTIFIKASI SUKSES
===================================================== -->

<?php if ($pesan_sukses !== ''): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <?= e($pesan_sukses) ?>

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>

    </div>

<?php endif; ?>


<!-- =====================================================
     NOTIFIKASI ERROR
===================================================== -->

<?php if ($pesan_error !== ''): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <?= e($pesan_error) ?>

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>

    </div>

<?php endif; ?>


<!-- =====================================================
     TABEL DATA GURU
===================================================== -->

<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table
                id="tabelGuru"
                class="table table-striped table-bordered mb-0"
                style="width:100%;"
            >

                <thead class="thead-dark text-center">

                    <tr>

                        <th style="width:5%;">No</th>

                        <th style="width:25%;">NIP / NIK</th>

                        <th>Nama Lengkap Guru</th>

                        <th style="width:15%;">Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php if ($result_guru && $result_guru->num_rows > 0): ?>

                    <?php
                    $no = 1;

                    while ($r = $result_guru->fetch_assoc()):

                        $id = (int)$r['id'];

                        $nip_asli = $r['nip'] ?? $r['nik'] ?? '';

                        $nama_asli = $r['nama'] ?? $r['nama_guru'] ?? '';
                    ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++ ?>
                            </td>


                            <td class="text-center">

                                <?= $nip_asli !== ''
                                    ? e($nip_asli)
                                    : '-' ?>

                            </td>


                            <td>
                                <?= e($nama_asli) ?>
                            </td>


                            <td class="text-center">

                                <!-- TOMBOL EDIT -->
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm btn-edit-guru"
                                    data-id="<?= $id ?>"
                                    data-nip="<?= e($nip_asli) ?>"
                                    data-nama="<?= e($nama_asli) ?>"
                                    title="Edit"
                                >

                                    <i class="fas fa-edit"></i>

                                </button>


                                <!-- TOMBOL HAPUS -->
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-hapus-guru"
                                    data-id="<?= $id ?>"
                                    data-nama="<?= e($nama_asli) ?>"
                                    title="Hapus"
                                >

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
     MODAL EDIT GURU
     SATU MODAL UNTUK SEMUA DATA GURU
===================================================== -->

<div
    class="modal fade"
    id="modalEditGuru"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog" role="document">

        <form method="POST" action="guru.php" class="modal-content">

            <input
                type="hidden"
                name="id"
                id="edit_id"
            >


            <div class="modal-header bg-warning text-dark">

                <h5 class="modal-title">

                    <i class="fas fa-edit mr-2"></i>
                    Edit Data Guru

                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                <div class="form-group">

                    <label>NIP / NIK</label>

                    <input
                        type="text"
                        name="nip"
                        id="edit_nip"
                        maxlength="15"
                        inputmode="numeric"
                        pattern="[0-9]{0,15}"
                        title="Maksimal 15 digit dan hanya angka"
                        class="form-control"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Nama Guru
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama"
                        id="edit_nama"
                        class="form-control"
                        required
                    >

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-dismiss="modal"
                >

                    Batal

                </button>


                <button
                    type="submit"
                    name="edit_guru"
                    class="btn btn-warning btn-sm"
                >

                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================================
     MODAL HAPUS GURU
     SATU MODAL UNTUK SEMUA DATA GURU
===================================================== -->

<div
    class="modal fade"
    id="modalHapusGuru"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">

                    <i class="fas fa-trash mr-2"></i>
                    Hapus Data Guru

                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                >

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                Yakin ingin menghapus guru

                <strong id="hapus_nama_guru"></strong>?

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-dismiss="modal"
                >

                    Batal

                </button>


                <a
                    href="#"
                    id="tombolKonfirmasiHapusGuru"
                    class="btn btn-danger btn-sm"
                >

                    <i class="fas fa-trash mr-1"></i>
                    Ya, Hapus

                </a>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="mt-5 py-3 text-center text-muted border-top small">

    &copy; 2026 - Sistem Informasi Pelanggaran Siswa

    &bull; Developed by

    <strong>Azril Akbar Fawji Abdillah</strong>

</footer>


</main>
</div>
</div>


<!-- =====================================================
     MODAL TAMBAH GURU
===================================================== -->

<div
    class="modal fade"
    id="modalGuru"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog" role="document">

        <form method="POST" action="guru.php" class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">

                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                    Tambah Guru Baru

                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                >

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                <div class="form-group">

                    <label>NIP / NIK</label>

                    <input
                        type="text"
                        name="nip"
                        maxlength="15"
                        inputmode="numeric"
                        pattern="[0-9]{0,15}"
                        title="Maksimal 15 digit dan hanya angka"
                        class="form-control"
                        placeholder="Nomor NIP / NIK"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Nama Guru
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        required
                        placeholder="Nama lengkap beserta gelar"
                    >

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-dismiss="modal"
                >

                    Batal

                </button>


                <button
                    type="submit"
                    name="tambah_guru"
                    class="btn btn-primary btn-sm"
                >

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

    $('#tabelGuru').DataTable({

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
    // TOMBOL EDIT GURU
    // =================================================

    $(document).on('click', '.btn-edit-guru', function () {

        var id = $(this).attr('data-id');

        var nip = $(this).attr('data-nip');

        var nama = $(this).attr('data-nama');


        // ISI FORM EDIT
        $('#edit_id').val(id);

        $('#edit_nip').val(nip);

        $('#edit_nama').val(nama);


        // TAMPILKAN MODAL EDIT
        $('#modalEditGuru').modal('show');

    });



    // =================================================
    // TOMBOL HAPUS GURU
    // =================================================

    $(document).on('click', '.btn-hapus-guru', function () {

        var id = $(this).attr('data-id');

        var nama = $(this).attr('data-nama');


        // TAMPILKAN NAMA GURU
        $('#hapus_nama_guru').text(nama);


        // ATUR LINK HAPUS
        $('#tombolKonfirmasiHapusGuru').attr(
            'href',
            'guru.php?action=hapus&id=' + encodeURIComponent(id)
        );


        // TAMPILKAN MODAL HAPUS
        $('#modalHapusGuru').modal('show');

    });


});

</script>


</body>
</html>
