
<?php
require_once 'auth.php';
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$pesan_sukses = '';
$pesan_error = '';

function pel_h($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// ========================================
// PROSES TAMBAH, EDIT DAN HAPUS
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // TAMBAH DAN EDIT
        if (isset($_POST['simpan_pelanggaran']) ||
            isset($_POST['edit_pelanggaran'])) {

            $id = (int)($_POST['id'] ?? 0);
            $kode = strtoupper(trim($_POST['kode'] ?? ''));
            $nama = trim($_POST['nama'] ?? '');
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $poin = (int)($_POST['poin'] ?? 0);
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

            if (
                $kode === '' ||
                $nama === '' ||
                $kategori_id <= 0 ||
                $poin <= 0 ||
                $deskripsi === ''
            ) {
                throw new Exception('Semua data wajib diisi.');
            }

            // Validasi kategori
            $cek = $koneksi->prepare(
                "SELECT id FROM t_pelanggaran_kategori WHERE id=?"
            );

            $cek->bind_param('i', $kategori_id);
            $cek->execute();

            if ($cek->get_result()->num_rows == 0) {
                throw new Exception('Kategori tidak ditemukan.');
            }

            $cek->close();

            // PROSES EDIT
            if (isset($_POST['edit_pelanggaran'])) {

                if ($id <= 0) {
                    throw new Exception('ID tidak valid.');
                }

                $stmt = $koneksi->prepare("
                    UPDATE t_pelanggaran
                    SET kode=?,
                        nama=?,
                        poin=?,
                        deskripsi=?,
                        pelanggaran_kategori_id=?,
                        status_aktif=?,
                        updated_at=NOW()
                    WHERE id=?
                ");

                $stmt->bind_param(
                    'ssisiii',
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi,
                    $kategori_id,
                    $status_aktif,
                    $id
                );

                $stmt->execute();
                $stmt->close();

                $pesan_sukses = 'Data pelanggaran berhasil diperbarui.';

            } else {

                // PROSES TAMBAH
                $stmt = $koneksi->prepare("
                    INSERT INTO t_pelanggaran
                    (
                        kode,
                        nama,
                        poin,
                        deskripsi,
                        pelanggaran_kategori_id,
                        status_aktif,
                        created_at,
                        updated_at
                    )
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");

                $stmt->bind_param(
                    'ssisii',
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi,
                    $kategori_id,
                    $status_aktif
                );

                $stmt->execute();
                $stmt->close();

                $pesan_sukses = 'Jenis pelanggaran berhasil ditambahkan.';
            }

        }

        // ========================================
        // PROSES HAPUS
        // ========================================

        elseif (isset($_POST['hapus_pelanggaran'])) {

            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID pelanggaran tidak valid.');
            }

            // Pastikan data tersedia
            $cek = $koneksi->prepare(
                "SELECT id FROM t_pelanggaran WHERE id=?"
            );

            $cek->bind_param('i', $id);
            $cek->execute();

            if ($cek->get_result()->num_rows == 0) {
                throw new Exception('Data pelanggaran tidak ditemukan.');
            }

            $cek->close();

            // Coba hapus permanen
            try {

                $stmt = $koneksi->prepare(
                    "DELETE FROM t_pelanggaran WHERE id=?"
                );

                $stmt->bind_param('i', $id);
                $stmt->execute();

                $jumlah = $stmt->affected_rows;
                $stmt->close();

                if ($jumlah > 0) {
                    $pesan_sukses = 'Data pelanggaran berhasil dihapus.';
                }

            } catch (mysqli_sql_exception $e) {

                // Jika data digunakan tabel lain,
                // nonaktifkan daripada menghapus permanen.

                if ((int)$e->getCode() === 1451) {

                    $stmt = $koneksi->prepare("
                        UPDATE t_pelanggaran
                        SET status_aktif=0,
                            updated_at=NOW()
                        WHERE id=?
                    ");

                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();

                    $pesan_sukses =
                        'Data tidak dapat dihapus permanen karena sudah digunakan. Data berhasil dinonaktifkan.';

                } else {
                    throw $e;
                }
            }
        }

    } catch (Throwable $e) {

        $pesan_error = 'Proses gagal: ' . $e->getMessage();

    }
}

// ========================================
// AMBIL DATA KATEGORI
// ========================================

$list_kategori = $koneksi->query("
    SELECT id, nama
    FROM t_pelanggaran_kategori
    WHERE status_aktif=1
    ORDER BY nama ASC
");

$kategori_rows = $list_kategori->fetch_all(MYSQLI_ASSOC);

// ========================================
// AMBIL DATA PELANGGARAN
// ========================================

$result_pelanggaran = $koneksi->query("
    SELECT
        p.*,
        k.nama AS nama_kategori
    FROM t_pelanggaran p
    LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
    ORDER BY p.id DESC
");

$pelanggaran_rows = $result_pelanggaran->fetch_all(MYSQLI_ASSOC);

include 'header.php';
?>

<style>
.card-pelanggaran {
    border: none;
    border-radius: 14px;
    box-shadow: 0 3px 15px rgba(0,0,0,.06);
}

.form-control {
    border-radius: 8px;
}

.btn {
    border-radius: 8px;
}

.table th {
    white-space: nowrap;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.modal-content {
    border: none;
    border-radius: 14px;
    overflow: hidden;
}

.modal-header {
    padding: 18px 22px;
}

.modal-body {
    padding: 22px;
}

.modal-footer {
    padding: 15px 22px;
}
</style>

<div class="container-fluid py-3">

    <!-- JUDUL -->
    <div class="d-flex justify-content-between align-items-center flex-wrap border-bottom pb-3 mb-3">

        <h1 class="h3">Jenis Pelanggaran</h1>

        <button
            type="button"
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            data-target="#modalTambah">

            <i class="fas fa-plus mr-1"></i>
            Tambah Pelanggaran

        </button>

    </div>

    <!-- PESAN -->
    <?php if ($pesan_sukses): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i>
            <?= pel_h($pesan_sukses) ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>

    <?php endif; ?>

    <?php if ($pesan_error): ?>

        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= pel_h($pesan_error) ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>

    <?php endif; ?>

    <!-- TABEL -->
    <div class="card card-pelanggaran">

        <div class="card-body table-responsive">

            <table
                id="tabelPelanggaran"
                class="table table-bordered table-striped table-hover">

                <thead class="thead-dark">

                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($pelanggaran_rows as $i => $r): ?>

                    <tr>

                        <td><?= $i + 1 ?></td>

                        <td><?= pel_h($r['kode']) ?></td>

                        <td><?= pel_h($r['nama']) ?></td>

                        <td>
                            <?= pel_h($r['nama_kategori'] ?? '-') ?>
                        </td>

                        <td><?= (int)$r['poin'] ?></td>

                        <td>

                            <?php if ((int)$r['status_aktif'] === 1): ?>

                                <span class="badge badge-success">
                                    Aktif
                                </span>

                            <?php else: ?>

                                <span class="badge badge-secondary">
                                    Tidak Aktif
                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="text-nowrap">

                            <button
                                type="button"
                                class="btn btn-warning btn-sm"
                                data-toggle="modal"
                                data-target="#editPel<?= (int)$r['id'] ?>">

                                <i class="fas fa-edit"></i>
                                Edit

                            </button>

                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                data-toggle="modal"
                                data-target="#hapusPel<?= (int)$r['id'] ?>">

                                <i class="fas fa-trash"></i>
                                Hapus

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ====================================== -->
<!-- MODAL TAMBAH -->
<!-- ====================================== -->

<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST" class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Tambah Pelanggaran
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Kode Pelanggaran</label>
                    <input
                        type="text"
                        name="kode"
                        class="form-control"
                        maxlength="30"
                        required>
                </div>

                <div class="form-group">
                    <label>Nama Pelanggaran</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        maxlength="150"
                        required>
                </div>

                <div class="form-group">

                    <label>Kategori</label>

                    <select name="kategori_id" class="form-control" required>

                        <option value="">-- Pilih Kategori --</option>

                        <?php foreach ($kategori_rows as $k): ?>

                            <option value="<?= (int)$k['id'] ?>">
                                <?= pel_h($k['nama']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">
                    <label>Poin</label>
                    <input
                        type="number"
                        name="poin"
                        min="1"
                        class="form-control"
                        required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="3"
                        required></textarea>
                </div>

                <div class="form-check">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        name="status_aktif"
                        id="aktifTambah"
                        checked>

                    <label class="form-check-label" for="aktifTambah">
                        Aktif
                    </label>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                    Batal
                </button>

                <button
                    type="submit"
                    name="simpan_pelanggaran"
                    value="1"
                    class="btn btn-primary">

                    <i class="fas fa-save mr-1"></i>
                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- ====================================== -->
<!-- MODAL EDIT DAN HAPUS -->
<!-- DILETAKKAN DI LUAR TABEL -->
<!-- ====================================== -->

<?php foreach ($pelanggaran_rows as $r): ?>

    <!-- MODAL EDIT -->

    <div
        class="modal fade"
        id="editPel<?= (int)$r['id'] ?>"
        tabindex="-1"
        role="dialog">

        <div class="modal-dialog" role="document">

            <form method="POST" class="modal-content">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Edit Pelanggaran
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int)$r['id'] ?>">

                    <div class="form-group">

                        <label>Kode Pelanggaran</label>

                        <input
                            type="text"
                            name="kode"
                            class="form-control"
                            maxlength="30"
                            value="<?= pel_h($r['kode']) ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Nama Pelanggaran</label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            maxlength="150"
                            value="<?= pel_h($r['nama']) ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Kategori</label>

                        <select name="kategori_id" class="form-control" required>

                            <?php foreach ($kategori_rows as $k): ?>

                                <option
                                    value="<?= (int)$k['id'] ?>"
                                    <?= (int)$r['pelanggaran_kategori_id'] === (int)$k['id'] ? 'selected' : '' ?>>

                                    <?= pel_h($k['nama']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Poin</label>

                        <input
                            type="number"
                            name="poin"
                            min="1"
                            class="form-control"
                            value="<?= (int)$r['poin'] ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Deskripsi</label>

                        <textarea
                            name="deskripsi"
                            class="form-control"
                            rows="3"
                            required><?= pel_h($r['deskripsi']) ?></textarea>

                    </div>

                    <div class="form-check">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            name="status_aktif"
                            id="aktif<?= (int)$r['id'] ?>"
                            <?= (int)$r['status_aktif'] === 1 ? 'checked' : '' ?>>

                        <label
                            class="form-check-label"
                            for="aktif<?= (int)$r['id'] ?>">

                            Aktif

                        </label>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Batal
                    </button>

                    <button
                        type="submit"
                        name="edit_pelanggaran"
                        value="1"
                        class="btn btn-warning">

                        <i class="fas fa-save mr-1"></i>
                        Perbarui

                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- MODAL HAPUS -->

    <div
        class="modal fade"
        id="hapusPel<?= (int)$r['id'] ?>"
        tabindex="-1"
        role="dialog">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <form method="POST" class="modal-content">

                <div class="modal-header bg-danger text-white">

                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Konfirmasi Hapus
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body text-center">

                    <i
                        class="fas fa-trash-alt text-danger mb-3"
                        style="font-size:45px">
                    </i>

                    <p>Apakah kamu yakin ingin menghapus pelanggaran:</p>

                    <h5><?= pel_h($r['nama']) ?></h5>

                    <p class="text-muted small">
                        Data yang sudah digunakan pada transaksi lain
                        akan dinonaktifkan, bukan dihapus permanen.
                    </p>

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int)$r['id'] ?>">

                </div>

                <div class="modal-footer justify-content-center">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        name="hapus_pelanggaran"
                        value="1"
                        class="btn btn-danger">

                        <i class="fas fa-trash mr-1"></i>
                        Ya, Hapus

                    </button>

                </div>

            </form>

        </div>

    </div>

<?php endforeach; ?>


<script>
$(document).ready(function () {

    if ($.fn.DataTable) {

        if ($.fn.DataTable.isDataTable('#tabelPelanggaran')) {
            $('#tabelPelanggaran').DataTable().destroy();
        }

        $('#tabelPelanggaran').DataTable({

            pageLength: 10,

            ordering: true,

            searching: true,

            language: {

                search: 'Cari:',

                lengthMenu: 'Tampilkan _MENU_ data per halaman',

                zeroRecords: 'Data tidak ditemukan',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty: 'Tidak ada data',

                infoFiltered: '(difilter dari _MAX_ total data)',

                paginate: {
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }

            }

        });

    }

});
</script>

<?php include 'footer.php'; ?>
