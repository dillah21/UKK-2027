
<?php
// =====================================================
// PELANGGARAN KATEGORI
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_error = "";


// =====================================================
// 1. PROSES HAPUS KATEGORI
// =====================================================

if (
    isset($_GET['action'], $_GET['id']) &&
    $_GET['action'] === 'hapus'
) {

    $id_hapus = filter_var(
        $_GET['id'],
        FILTER_VALIDATE_INT
    );

    if (!$id_hapus || $id_hapus <= 0) {

        $pesan_error = "ID kategori tidak valid.";

    } elseif (strtolower($_SESSION['role'] ?? '') !== 'admin') {

        $pesan_error = "Anda tidak memiliki izin menghapus kategori.";

    } else {

        try {

            // Deteksi nama kolom primary key
            $kolom_id = null;

            $cek_kolom = $koneksi->query(
                "SHOW COLUMNS FROM t_pelanggaran_kategori"
            );

            while ($kolom = $cek_kolom->fetch_assoc()) {

                if ($kolom['Field'] === 'id_kategori') {
                    $kolom_id = 'id_kategori';
                    break;
                }

                if ($kolom['Field'] === 'id') {
                    $kolom_id = 'id';
                }
            }

            $cek_kolom->free();

            if ($kolom_id === null) {

                throw new RuntimeException(
                    "Kolom ID kategori tidak ditemukan."
                );

            }

            // Hapus kategori menggunakan prepared statement
            $hapus = $koneksi->prepare(
                "DELETE FROM t_pelanggaran_kategori
                 WHERE `$kolom_id` = ?"
            );

            $hapus->bind_param("i", $id_hapus);
            $hapus->execute();

            if ($hapus->affected_rows > 0) {

                $pesan_sukses =
                    "Kategori pelanggaran berhasil dihapus!";

            } else {

                $pesan_error =
                    "Data kategori tidak ditemukan.";

            }

            $hapus->close();

        } catch (mysqli_sql_exception $e) {

            error_log(
                "Error hapus kategori: " . $e->getMessage()
            );

            if ((int)$e->getCode() === 1451) {

                $pesan_error =
                    "Kategori tidak dapat dihapus karena masih digunakan oleh data pelanggaran.";

            } else {

                $pesan_error =
                    "Terjadi kesalahan database saat menghapus kategori.";

            }

        } catch (Throwable $e) {

            error_log(
                "Error sistem kategori: " . $e->getMessage()
            );

            $pesan_error =
                "Terjadi kesalahan: " . $e->getMessage();

        }
    }
}


// =====================================================
// 2. AMBIL DATA KATEGORI
// =====================================================

try {

    $query = $koneksi->query(
        "SELECT * FROM t_pelanggaran_kategori"
    );

} catch (Throwable $e) {

    error_log($e->getMessage());

    $query = false;

    $pesan_error = "Gagal mengambil data kategori pelanggaran.";

}


// =====================================================
// 3. HEADER DASHBOARD
// =====================================================

include 'header.php';

?>

<!-- =====================================================
     CSS DATATABLES
===================================================== -->

<link
    rel="stylesheet"
    href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css"
>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<div class="content-wrapper" style="padding: 15px;">

    <section class="content">

        <div class="container-fluid">


            <!-- JUDUL HALAMAN -->

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">

                    <i class="fas fa-tags mr-2"></i>

                    Kategori Pelanggaran

                </h1>


                <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>

                    <a
                        href="pelanggaran_kategori_tambah.php"
                        class="btn btn-primary btn-sm"
                    >

                        <i class="fas fa-plus mr-1"></i>

                        Tambah Kategori Baru

                    </a>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 NOTIFIKASI BERHASIL
            ================================================== -->

            <?php if (!empty($pesan_sukses) || isset($_GET['status'])): ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                    <i class="fas fa-check-circle mr-1"></i>

                    <?= htmlspecialchars(
                        !empty($pesan_sukses)
                            ? $pesan_sukses
                            : "Data kategori berhasil diproses!",
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close"
                    >

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 NOTIFIKASI ERROR
            ================================================== -->

            <?php if (!empty($pesan_error)): ?>

                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <i class="fas fa-exclamation-circle mr-1"></i>

                    <?= htmlspecialchars(
                        $pesan_error,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close"
                    >

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 TABEL KATEGORI
            ================================================== -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body">

                    <div class="table-responsive">

                        <table
                            id="tabelKategori"
                            class="table table-bordered table-striped table-hover align-middle mb-0"
                            style="width:100%;"
                        >

                            <thead class="thead-dark text-center">

                                <tr>

                                    <th width="50">
                                        No
                                    </th>

                                    <th class="text-left">
                                        Nama Kategori Pelanggaran
                                    </th>

                                    <th width="100">
                                        Poin
                                    </th>

                                    <th class="text-left">
                                        Sanksi / Hukuman
                                    </th>

                                    <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>

                                        <th width="120">
                                            Aksi
                                        </th>

                                    <?php endif; ?>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $no = 1;

                                if ($query && $query->num_rows > 0):

                                    while ($row = $query->fetch_assoc()):

                                        // Ambil ID kategori
                                        $id_kat = (int)(
                                            $row['id_kategori']
                                            ?? $row['id']
                                            ?? 0
                                        );

                                        // Nama kategori
                                        $nama_kat = $row['nama_kategori']
                                            ?? $row['kategori']
                                            ?? $row['nama']
                                            ?? '';

                                        // Poin pelanggaran
                                        $poin = $row['poin']
                                            ?? $row['poin_pelanggaran']
                                            ?? $row['point']
                                            ?? 10;

                                        // Sanksi
                                        $hukuman = $row['sanksi']
                                            ?? $row['hukuman']
                                            ?? $row['deskripsi']
                                            ?? $row['keterangan']
                                            ?? 'Teguran lisan';

                                ?>

                                    <tr>

                                        <!-- NOMOR -->

                                        <td class="text-center">

                                            <?= $no++; ?>

                                        </td>


                                        <!-- NAMA KATEGORI -->

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $nama_kat,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>

                                            </strong>

                                        </td>


                                        <!-- POIN -->

                                        <td class="text-center font-weight-bold text-danger">

                                            <span class="badge badge-danger p-2">

                                                +<?= htmlspecialchars(
                                                    (string)$poin,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?> Poin

                                            </span>

                                        </td>


                                        <!-- SANKSI -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $hukuman,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </td>


                                        <!-- AKSI -->

                                        <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>

                                            <td class="text-center">

                                                <!-- TOMBOL EDIT -->

                                                <a
                                                    href="pelanggaran_kategori_edit.php?id=<?= $id_kat; ?>"
                                                    class="btn btn-warning btn-sm text-white"
                                                    title="Edit Kategori"
                                                >

                                                    <i class="fas fa-edit"></i>

                                                </a>


                                                <!-- TOMBOL HAPUS -->

                                                <a
                                                    href="pelanggaran_kategori.php?action=hapus&id=<?= $id_kat; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    title="Hapus Kategori"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')"
                                                >

                                                    <i class="fas fa-trash"></i>

                                                </a>

                                            </td>

                                        <?php endif; ?>

                                    </tr>

                                <?php

                                    endwhile;

                                endif;

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


        </div>

    </section>

</div>


<!-- =====================================================
     JAVASCRIPT DATATABLES
===================================================== -->

<script>

$(document).ready(function() {

    $('#tabelKategori').DataTable({

        "order": [[0, "asc"]],

        "pageLength": 10,

        "language": {

            "search": "Cari:",

            "lengthMenu": "Tampilkan _MENU_ data per halaman",

            "zeroRecords": "Data kategori tidak ditemukan",

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


<!-- =====================================================
     FOOTER DASHBOARD
===================================================== -->

<?php include 'footer.php'; ?>
