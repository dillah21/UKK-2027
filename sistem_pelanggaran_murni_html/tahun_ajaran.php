
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";

/*
|--------------------------------------------------------------------------
| FUNGSI VALIDASI TANGGAL
|--------------------------------------------------------------------------
*/
function tanggalValid($tanggal) {
    if (empty($tanggal)) {
        return false;
    }

    $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

    return $date && $date->format('Y-m-d') === $tanggal;
}

/*
|--------------------------------------------------------------------------
| TAMBAH TAHUN AJARAN DEFAULT JIKA BELUM ADA
|--------------------------------------------------------------------------
| Tanggal hanya diisi saat data belum tersedia.
| Data tanggal yang sudah diatur manual tidak ditimpa.
|--------------------------------------------------------------------------
*/
for ($tahun = 2021; $tahun <= 2026; $tahun++) {

    $nama_default = $tahun . '/' . ($tahun + 1);
    $mulai = $tahun . '-07-01';
    $selesai = ($tahun + 1) . '-06-30';
    $status_default = 1;

    $cek = $koneksi->prepare(
        "SELECT id FROM t_tahun_ajaran WHERE nama = ? LIMIT 1"
    );

    $cek->bind_param("s", $nama_default);
    $cek->execute();

    $data_ada = $cek->get_result()->fetch_assoc();

    $cek->close();

    if (!$data_ada) {

        $insert = $koneksi->prepare(
            "INSERT INTO t_tahun_ajaran
            (nama, tanggal_mulai, tanggal_selesai, status_aktif)
            VALUES (?, ?, ?, ?)"
        );

        $insert->bind_param(
            "sssi",
            $nama_default,
            $mulai,
            $selesai,
            $status_default
        );

        $insert->execute();
        $insert->close();
    }
}

/*
|--------------------------------------------------------------------------
| TAMBAH TAHUN AJARAN
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_ta'])) {

    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

    preg_match('/^(\d{4})\/(\d{4})$/', $nama, $m);

    $awal = isset($m[1]) ? (int)$m[1] : 0;
    $akhir = isset($m[2]) ? (int)$m[2] : 0;

    $format_valid = (
        $awal >= 1900 &&
        $akhir === $awal + 1
    );

    $tanggal_valid = (
        tanggalValid($tanggal_mulai) &&
        tanggalValid($tanggal_selesai) &&
        $tanggal_mulai <= $tanggal_selesai
    );

    if (!$format_valid) {

        $pesan_error = "Format tahun ajaran harus berurutan, misalnya 2026/2027!";

    } elseif (!$tanggal_valid) {

        $pesan_error = "Tanggal mulai dan tanggal selesai tidak valid!";

    } else {

        $cek = $koneksi->prepare(
            "SELECT id FROM t_tahun_ajaran WHERE nama = ? LIMIT 1"
        );

        $cek->bind_param("s", $nama);
        $cek->execute();

        $duplikat = $cek->get_result()->num_rows > 0;

        $cek->close();

        if ($duplikat) {

            $pesan_error = "Tahun ajaran tersebut sudah tersedia!";

        } else {

            $status_aktif = ($awal <= 2026) ? 1 : 0;

            $stmt = $koneksi->prepare(
                "INSERT INTO t_tahun_ajaran
                (nama, tanggal_mulai, tanggal_selesai, status_aktif)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssi",
                $nama,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif
            );

            if ($stmt->execute()) {

                $pesan_sukses = "Tahun ajaran berhasil ditambahkan!";

            } else {

                $pesan_error = "Gagal menyimpan: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

/*
|--------------------------------------------------------------------------
| EDIT TAHUN AJARAN
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_ta'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $mulai = $_POST['tanggal_mulai'] ?? '';
    $selesai = $_POST['tanggal_selesai'] ?? '';

    preg_match('/^(\d{4})\/(\d{4})$/', $nama, $m);

    $awal = isset($m[1]) ? (int)$m[1] : 0;
    $akhir = isset($m[2]) ? (int)$m[2] : 0;

    $format_valid = (
        $awal >= 1900 &&
        $akhir === $awal + 1
    );

    $tanggal_valid = (
        tanggalValid($mulai) &&
        tanggalValid($selesai) &&
        $mulai <= $selesai
    );

    if ($id <= 0 || !$format_valid) {

        $pesan_error = "Format tahun ajaran tidak valid!";

    } elseif (!$tanggal_valid) {

        $pesan_error = "Tanggal mulai dan selesai tidak valid!";

    } else {

        $cek = $koneksi->prepare(
            "SELECT id FROM t_tahun_ajaran
             WHERE nama = ? AND id <> ? LIMIT 1"
        );

        $cek->bind_param("si", $nama, $id);
        $cek->execute();

        $duplikat = $cek->get_result()->num_rows > 0;

        $cek->close();

        if ($duplikat) {

            $pesan_error = "Nama tahun ajaran tersebut sudah digunakan.";

        } else {

            $status = ($awal <= 2026) ? 1 : 0;

            $up = $koneksi->prepare(
                "UPDATE t_tahun_ajaran
                 SET nama = ?,
                     tanggal_mulai = ?,
                     tanggal_selesai = ?,
                     status_aktif = ?
                 WHERE id = ?"
            );

            $up->bind_param(
                "sssii",
                $nama,
                $mulai,
                $selesai,
                $status,
                $id
            );

            if ($up->execute()) {

                $pesan_sukses = "Tahun ajaran berhasil diperbarui.";

            } else {

                $pesan_error = "Gagal memperbarui: " . $up->error;
            }

            $up->close();
        }
    }
}

/*
|--------------------------------------------------------------------------
| HAPUS TAHUN AJARAN
|--------------------------------------------------------------------------
*/
if (
    isset($_GET['action']) &&
    $_GET['action'] === 'hapus' &&
    isset($_GET['id'])
) {

    $id_hapus = (int)$_GET['id'];

    if ($id_hapus > 0) {

        $stmt = $koneksi->prepare(
            "DELETE FROM t_tahun_ajaran WHERE id = ?"
        );

        $stmt->bind_param("i", $id_hapus);

        if ($stmt->execute()) {

            $pesan_sukses = "Tahun ajaran berhasil dihapus!";

        } else {

            $pesan_error = "Gagal menghapus: " . $stmt->error;
        }

        $stmt->close();
    }
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA TAHUN AJARAN
|--------------------------------------------------------------------------
*/
$result_ta = $koneksi->query(
    "SELECT * FROM t_tahun_ajaran
     ORDER BY tanggal_mulai ASC, id ASC"
);

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">

    <h1 class="h2">Manajemen Tahun Ajaran</h1>

    <button
        class="btn btn-primary btn-sm"
        data-toggle="modal"
        data-target="#modalTA">

        <i class="fas fa-plus mr-1"></i>
        Tambah Tahun Ajaran

    </button>

</div>

<?php if (!empty($pesan_sukses)): ?>

<div class="alert alert-success alert-dismissible fade show">

    <?= htmlspecialchars($pesan_sukses, ENT_QUOTES, 'UTF-8'); ?>

    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>

</div>

<?php endif; ?>


<?php if (!empty($pesan_error)): ?>

<div class="alert alert-danger alert-dismissible fade show">

    <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8'); ?>

    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>

</div>

<?php endif; ?>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table
                id="tabelTA"
                class="table table-striped table-bordered mb-0"
                style="width:100%;">

                <thead class="thead-dark text-center">

                    <tr>
                        <th style="width:5%;">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status Aktif</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if ($result_ta && $result_ta->num_rows > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($r = $result_ta->fetch_assoc()): ?>

                            <tr>

                                <td class="text-center">
                                    <?= $no++; ?>
                                </td>

                                <td class="text-center font-weight-bold">
                                    <?= htmlspecialchars($r['nama'] ?? '-'); ?>
                                </td>

                                <td class="text-center">
                                    <?= !empty($r['tanggal_mulai'])
                                        ? date('d-m-Y', strtotime($r['tanggal_mulai']))
                                        : '-'; ?>
                                </td>

                                <td class="text-center">
                                    <?= !empty($r['tanggal_selesai'])
                                        ? date('d-m-Y', strtotime($r['tanggal_selesai']))
                                        : '-'; ?>
                                </td>

                                <td class="text-center">

                                    <?php if ($r['status_aktif'] == 1): ?>

                                        <span class="badge badge-success p-2">
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span class="badge badge-secondary p-2">
                                            Tidak Aktif
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editTA<?= (int)$r['id']; ?>">

                                        <i class="fas fa-edit"></i>
                                        Edit

                                    </button>

                                    <a
                                        href="tahun_ajaran.php?action=hapus&id=<?= (int)$r['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?');">

                                        <i class="fas fa-trash"></i>
                                        Hapus

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<footer class="mt-5 py-3 text-center text-muted border-top small">

    &copy; 2026 - Sistem Informasi Pelanggaran Siswa
    &bull; Developed by
    <strong>Azril Akbar Fawji Abdillah</strong>

</footer>

</main>
</div>
</div>


<!-- =====================================================
     MODAL EDIT TAHUN AJARAN
===================================================== -->

<?php

$edit_rows = $koneksi->query(
    "SELECT id, nama, tanggal_mulai, tanggal_selesai
     FROM t_tahun_ajaran
     ORDER BY id ASC"
);

while ($er = $edit_rows->fetch_assoc()):

?>

<div
    class="modal fade"
    id="editTA<?= (int)$er['id']; ?>"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">

    <div class="modal-dialog" role="document">

        <form
            method="POST"
            action="tahun_ajaran.php"
            class="modal-content">

            <div class="modal-header bg-warning">

                <h5 class="modal-title">
                    Edit Tahun Ajaran
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$er['id']; ?>">

                <div class="form-group">

                    <label>Tahun Ajaran</label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        value="<?= htmlspecialchars($er['nama'], ENT_QUOTES, 'UTF-8'); ?>"
                        pattern="[0-9]{4}/[0-9]{4}"
                        required>

                </div>

                <div class="form-group">

                    <label>Tanggal Mulai</label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        class="form-control"
                        value="<?= htmlspecialchars($er['tanggal_mulai'], ENT_QUOTES, 'UTF-8'); ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>Tanggal Selesai</label>

                    <input
                        type="date"
                        name="tanggal_selesai"
                        class="form-control"
                        value="<?= htmlspecialchars($er['tanggal_selesai'], ENT_QUOTES, 'UTF-8'); ?>"
                        required>

                </div>

                <small class="text-muted">
                    Status ditentukan otomatis berdasarkan tahun awal.
                    Tahun awal sampai 2026 berstatus Aktif.
                </small>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-dismiss="modal">

                    Batal

                </button>

                <button
                    type="submit"
                    name="edit_ta"
                    class="btn btn-warning btn-sm">

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

<?php endwhile; ?>


<!-- =====================================================
     MODAL TAMBAH TAHUN AJARAN
===================================================== -->

<div
    class="modal fade"
    id="modalTA"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">

    <div class="modal-dialog" role="document">

        <form
            method="POST"
            action="tahun_ajaran.php"
            class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">

                    <i class="fas fa-calendar-alt mr-2"></i>
                    Tambah Tahun Ajaran

                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">

                    <label>
                        Tahun Ajaran <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        required
                        placeholder="Contoh: 2026/2027"
                        pattern="[0-9]{4}/[0-9]{4}">

                </div>

                <div class="form-group">

                    <label>
                        Tanggal Mulai <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        class="form-control"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Tanggal Selesai <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal_selesai"
                        class="form-control"
                        required>

                </div>

                <small class="form-text text-muted">

                    Status ditentukan otomatis:
                    tahun awal sampai 2026 berstatus Aktif,
                    sedangkan tahun setelah 2026 berstatus Tidak Aktif.

                </small>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-dismiss="modal">

                    Batal

                </button>

                <button
                    type="submit"
                    name="tambah_ta"
                    class="btn btn-primary btn-sm">

                    <i class="fas fa-save mr-1"></i>
                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- BOOTSTRAP JS -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>


<script>

// DARK MODE

(function () {

    const root = document.documentElement;
    const button = document.getElementById('themeToggle');

    const saved = localStorage.getItem('sip-theme');

    const preferred = window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches;

    function applyTheme(theme) {

        const dark = theme === 'dark';

        root.setAttribute(
            'data-theme',
            dark ? 'dark' : 'light'
        );

        if (button) {

            button.innerHTML = dark
                ? '<i class="fas fa-sun" aria-hidden="true"></i>'
                : '<i class="fas fa-moon" aria-hidden="true"></i>';

            button.setAttribute(
                'aria-label',
                dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'
            );

            button.title = dark
                ? 'Gunakan mode terang'
                : 'Gunakan mode gelap';
        }
    }

    applyTheme(saved || (preferred ? 'dark' : 'light'));

    if (button) {

        button.addEventListener('click', function () {

            const next =
                root.getAttribute('data-theme') === 'dark'
                    ? 'light'
                    : 'dark';

            localStorage.setItem('sip-theme', next);

            applyTheme(next);

        });
    }

})();


// DATATABLE

if (
    window.jQuery &&
    window.jQuery.fn.DataTable &&
    !window.jQuery.fn.dataTable.isDataTable('#tabelTA')
) {

    window.jQuery('#tabelTA').DataTable({

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

}

</script>

</body>
</html>
