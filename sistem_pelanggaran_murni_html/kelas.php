
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

// Menandai kelas yang dibuat melalui form ini agar dapat ditampilkan,
// tanpa membuka kembali kelas lama lain yang sebelumnya disembunyikan.
$koneksi->query("CREATE TABLE IF NOT EXISTS t_kelas_tampil_admin (
    kelas_id INT(11) NOT NULL PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Bersihkan master kelas lama yang benar-benar tidak dipakai.
// Kelas yang masih memiliki relasi siswa, wali kelas, atau riwayat pelanggaran
// sengaja dipertahankan agar foreign key dan histori tetap utuh.
$koneksi->query("DELETE k FROM t_kelas k
    WHERE UPPER(TRIM(k.nama)) NOT IN ('12 RPL 1', '12 RPL 2')
      AND NOT EXISTS (SELECT 1 FROM t_kelas_siswa ks WHERE ks.kelas_id = k.id)
      AND NOT EXISTS (SELECT 1 FROM t_wali_kelas wk WHERE wk.kelas_id = k.id)
      AND NOT EXISTS (SELECT 1 FROM t_pelanggaran_siswa ps WHERE ps.kelas_id = k.id)
      AND NOT EXISTS (SELECT 1 FROM t_kelas_tampil_admin ta WHERE ta.kelas_id = k.id)");

$pesan_sukses = '';
$pesan_error = '';

$daftar_jurusan = ['RPL', 'TKJ', 'TKR', 'TSM', 'BDPM'];


function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Validasi nama kelas agar tidak duplikat
function kelasSudahAda($koneksi, $nama, $id = 0) {
    $sql = "SELECT id FROM t_kelas WHERE UPPER(TRIM(nama)) = UPPER(TRIM(?))";
    if ($id > 0) {
        $sql .= " AND id <> ?";
    }
    $sql .= " LIMIT 1";

    $stmt = $koneksi->prepare($sql);

    if ($id > 0) {
        $stmt->bind_param("si", $nama, $id);
    } else {
        $stmt->bind_param("s", $nama);
    }

    $stmt->execute();
    $ada = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    return $ada;
}

// =====================================================
// TAMBAH KELAS
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kelas'])) {

    $tingkat = (int)($_POST['tingkat'] ?? 0);
    $jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));
    $nama = trim($_POST['nama_kelas'] ?? '');

    if (
        !in_array($tingkat, [10, 11, 12], true) ||
        !in_array($jurusan, $daftar_jurusan, true) ||
        $nama === '' || strlen($nama) > 100
    ) {
        $pesan_error = 'Lengkapi tingkat, jurusan, dan nama kelas dengan benar!';
    } elseif (kelasSudahAda($koneksi, $nama)) {
        $pesan_error = 'Nama kelas tersebut sudah terdaftar!';
    } else {
        try {
            $stmt = $koneksi->prepare("
                INSERT INTO t_kelas
                    (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, 1, NOW(), NOW())
            ");

            $stmt->bind_param("sis", $nama, $tingkat, $jurusan);
            $stmt->execute();
            $id_kelas_baru = (int)$koneksi->insert_id;
            $stmt->close();

            // Simpan penanda supaya kelas baru muncul di tabel admin.
            $tampil = $koneksi->prepare("INSERT INTO t_kelas_tampil_admin (kelas_id) VALUES (?)");
            $tampil->bind_param('i', $id_kelas_baru);
            $tampil->execute();
            $tampil->close();

            $pesan_sukses = 'Kelas ' . $nama . ' berhasil disimpan dan ditampilkan pada daftar kelas!';

        } catch (Throwable $e) {
            error_log('Error tambah kelas: ' . $e->getMessage());
            $pesan_error = 'Gagal menambahkan kelas. Periksa kembali data kelas.';
        }
    }
}

// =====================================================
// EDIT KELAS
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kelas'])) {

    $id = (int)($_POST['id'] ?? 0);
    $tingkat = (int)($_POST['tingkat'] ?? 0);
    $jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));
    $nama = trim($_POST['nama_kelas'] ?? '');

    if (
        $id <= 0 ||
        !in_array($tingkat, [10, 11, 12], true) ||
        !in_array($jurusan, $daftar_jurusan, true) ||
        $nama === '' ||
        strlen($nama) > 100
    ) {
        $pesan_error = 'Data kelas tidak valid!';
    } elseif (kelasSudahAda($koneksi, $nama, $id)) {
        $pesan_error = 'Nama kelas tersebut sudah digunakan kelas lain!';
    } else {
        try {
            $stmt = $koneksi->prepare("
                UPDATE t_kelas
                SET nama = ?,
                    tingkat = ?,
                    jurusan = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->bind_param("sisi", $nama, $tingkat, $jurusan, $id);
            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                $pesan_sukses = 'Data kelas berhasil diperbarui!';
            }

            $stmt->close();

        } catch (Throwable $e) {
            error_log('Error edit kelas: ' . $e->getMessage());
            $pesan_error = 'Gagal memperbarui data kelas.';
        }
    }
}

// =====================================================
// HAPUS KELAS
// =====================================================

if (
    isset($_GET['action'], $_GET['id']) &&
    $_GET['action'] === 'hapus'
) {
    $id = (int)$_GET['id'];

    if ($id <= 0) {
        $pesan_error = 'ID kelas tidak valid.';
    } else {
        try {
            // Cek siswa yang masih terhubung dengan kelas
            $cek = $koneksi->prepare("
                SELECT COUNT(*) AS total
                FROM t_kelas_siswa
                WHERE kelas_id = ?
            ");

            $cek->bind_param("i", $id);
            $cek->execute();

            $data_cek = $cek->get_result()->fetch_assoc();
            $cek->close();

            $jumlah_siswa = (int)($data_cek['total'] ?? 0);

            if ($jumlah_siswa > 0) {
                $pesan_error = 'Kelas tidak dapat dihapus karena masih memiliki '
                    . $jumlah_siswa
                    . ' siswa. Pindahkan siswa terlebih dahulu.';
            } else {
                $hapus = $koneksi->prepare("
                    DELETE FROM t_kelas WHERE id = ?
                ");

                $hapus->bind_param("i", $id);
                $hapus->execute();

                if ($hapus->affected_rows > 0) {
                    $pesan_sukses = 'Kelas berhasil dihapus!';
                } else {
                    $pesan_error = 'Data kelas tidak ditemukan.';
                }

                $hapus->close();
            }

        } catch (Throwable $e) {
            error_log('Error hapus kelas: ' . $e->getMessage());
            $pesan_error = 'Kelas tidak dapat dihapus karena masih terhubung dengan data lain.';
        }
    }
}

// =====================================================
// TAMPILKAN DUA KELAS UTAMA DAN KELAS BARU YANG DITAMBAHKAN
// MELALUI FORM INI. Master kelas lama yang tidak terpakai sudah dibersihkan.
// =====================================================

$result_kelas = $koneksi->query("
    SELECT k.id, k.nama, k.tingkat, k.jurusan
    FROM t_kelas k
    WHERE UPPER(TRIM(k.nama)) IN ('12 RPL 1', '12 RPL 2')
       OR EXISTS (
            SELECT 1 FROM t_kelas_tampil_admin ta
            WHERE ta.kelas_id = k.id
       )
    ORDER BY k.tingkat ASC, k.jurusan ASC, k.nama ASC, k.id ASC
");

include 'header.php';
?>

<div class="container-fluid py-3">

    <!-- JUDUL DAN TOMBOL TAMBAH -->
    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap">

        <div>
            <h1 class="h2 mb-1">Manajemen Data Kelas</h1>
            <p class="text-muted mb-0">
                Daftar menampilkan 12 RPL 1, 12 RPL 2, serta kelas baru yang ditambahkan melalui form ini. Master kelas lama yang tidak memiliki relasi sudah dibersihkan; kelas yang masih terkait riwayat tetap dijaga.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary mt-2"
                data-toggle="modal"
                data-target="#modalKelas">

            <i class="fas fa-plus mr-1"></i>
            Tambah Kelas

        </button>

    </div>

    <!-- PESAN SUKSES -->
    <?php if ($pesan_sukses !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= e($pesan_sukses) ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- PESAN ERROR -->
    <?php if ($pesan_error !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= e($pesan_error) ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- TABEL DATA KELAS -->
    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>
                <i class="fas fa-school mr-1"></i>
                Daftar Kelas
            </strong>

            <span class="badge badge-primary">
                <?= $result_kelas->num_rows ?> Kelas
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="tabelKelas"
                       class="table table-striped table-bordered table-hover mb-0"
                       style="width:100%">

                    <thead class="thead-dark text-center">
                        <tr>
                            <th style="width:7%">No</th>
                            <th>Nama Kelas</th>
                            <th style="width:15%">Tingkat</th>
                            <th style="width:15%">Jurusan</th>
                            <th style="width:20%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php $no = 1; ?>

                    <?php while ($r = $result_kelas->fetch_assoc()): ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++ ?>
                            </td>

                            <td>
                                <strong><?= e($r['nama']) ?></strong>
                            </td>

                            <td class="text-center">
                                <?= e($r['tingkat']) ?>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-info">
                                    <?= e($r['jurusan']) ?>
                                </span>
                            </td>

                            <td class="text-center">

                                <button type="button"
                                        class="btn btn-warning btn-sm btn-edit-kelas"
                                        data-id="<?= (int)$r['id'] ?>"
                                        data-tingkat="<?= (int)$r['tingkat'] ?>"
                                        data-jurusan="<?= e($r['jurusan']) ?>"
                                        data-nama="<?= e($r['nama']) ?>">

                                    <i class="fas fa-edit"></i>
                                    Edit

                                </button>

                                <button type="button"
                                        class="btn btn-danger btn-sm btn-hapus-kelas"
                                        data-id="<?= (int)$r['id'] ?>"
                                        data-nama="<?= e($r['nama']) ?>">

                                    <i class="fas fa-trash"></i>
                                    Hapus

                                </button>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<!-- =====================================================
     MODAL TAMBAH KELAS
===================================================== -->

<div class="modal fade" id="modalKelas" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST" action="kelas.php" class="modal-content">

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
                    <select name="tingkat" class="form-control" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jurusan</label>
                    <select name="jurusan" class="form-control" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($daftar_jurusan as $j): ?>
                            <option value="<?= e($j) ?>"><?= e($j) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="nama_kelas_baru"
                           class="form-control" maxlength="100"
                           placeholder="Contoh: 12 TKJ 1" required>
                    <small class="form-text text-muted">
                        Masukkan nama kelas yang ingin ditambahkan, misalnya 12 RPL 3.
                    </small>
                </div>

                <div class="alert alert-info mb-0">
                    Setelah disimpan, kelas baru akan langsung muncul pada tabel Daftar Kelas.
                </div>
            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                        name="tambah_kelas"
                        class="btn btn-primary">

                    <i class="fas fa-save mr-1"></i>
                    Simpan Kelas

                </button>

            </div>

        </form>

    </div>
</div>

<!-- =====================================================
     MODAL EDIT KELAS
===================================================== -->

<div class="modal fade" id="modalEditKelas" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST" action="kelas.php" class="modal-content">

            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header bg-warning">

                <h5 class="modal-title">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Data Kelas
                </h5>

                <button type="button" class="close" data-dismiss="modal">
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
                        <option value="">-- Pilih Tingkat --</option>
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
                           required>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                        name="edit_kelas"
                        class="btn btn-warning">

                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>
</div>

<!-- =====================================================
     MODAL HAPUS KELAS
===================================================== -->

<div class="modal fade" id="modalHapusKelas" tabindex="-1" role="dialog">

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
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>

                <a href="#"
                   id="tombolKonfirmasiHapus"
                   class="btn btn-danger">

                    <i class="fas fa-trash mr-1"></i>
                    Ya, Hapus

                </a>

            </div>

        </div>

    </div>
</div>

<!-- JAVASCRIPT -->

<script>
$(document).ready(function () {

    $('#tabelKelas').DataTable({
        order: [],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data per halaman',
            zeroRecords: 'Tidak ada data kelas yang ditemukan',
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

    // EDIT KELAS
    $(document).on('click', '.btn-edit-kelas', function () {

        $('#edit_id').val($(this).attr('data-id'));
        $('#edit_tingkat').val($(this).attr('data-tingkat'));
        $('#edit_jurusan').val($(this).attr('data-jurusan'));
        $('#edit_nama_kelas').val($(this).attr('data-nama'));

        $('#modalEditKelas').modal('show');
    });

    // HAPUS KELAS
    $(document).on('click', '.btn-hapus-kelas', function () {

        var id = $(this).attr('data-id');
        var nama = $(this).attr('data-nama');

        $('#hapus_nama_kelas').text(nama);

        $('#tombolKonfirmasiHapus').attr(
            'href',
            'kelas.php?action=hapus&id=' + encodeURIComponent(id)
        );

        $('#modalHapusKelas').modal('show');
    });

});
</script>

<?php include 'footer.php'; ?>