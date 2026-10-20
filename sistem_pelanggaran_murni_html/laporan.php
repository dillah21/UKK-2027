
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

/* =========================
   FUNGSI BANTUAN
========================= */

function lp_h($value)
{
    return htmlspecialchars(
        (string)($value ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}

function lp_columns($koneksi, $table)
{
    $columns = [];

    try {
        $result = $koneksi->query("SHOW COLUMNS FROM `$table`");

        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    } catch (Throwable $e) {
        error_log('Laporan: ' . $e->getMessage());
    }

    return $columns;
}

function lp_pick($columns, $candidates)
{
    foreach ($candidates as $candidate) {
        if (in_array($candidate, $columns, true)) {
            return $candidate;
        }
    }

    return null;
}

function lp_select($alias, $column, $output)
{
    if ($column === null) {
        return "NULL AS `$output`";
    }

    return "`$alias`.`$column` AS `$output`";
}

/* =========================
   PERIKSA TABEL DATABASE
========================= */

$pelanggaranCols = lp_columns($koneksi, 't_pelanggaran_siswa');
$siswaCols       = lp_columns($koneksi, 't_siswa');
$kelasCols       = lp_columns($koneksi, 't_kelas');
$kategoriCols    = lp_columns($koneksi, 't_pelanggaran_kategori');

$errors = [];

if (!$pelanggaranCols) {
    $errors[] = 'Tabel t_pelanggaran_siswa tidak ditemukan.';
}

if (!$siswaCols) {
    $errors[] = 'Tabel t_siswa tidak ditemukan.';
}

if (!$kelasCols) {
    $errors[] = 'Tabel t_kelas tidak ditemukan.';
}

if (!$kategoriCols) {
    $errors[] = 'Tabel t_pelanggaran_kategori tidak ditemukan.';
}

/* =========================
   DETEKSI NAMA KOLOM
========================= */

$idPelanggaran = lp_pick(
    $pelanggaranCols,
    ['id_pelanggaran', 'id', 'id_catatan']
);

$tanggal = lp_pick(
    $pelanggaranCols,
    ['tanggal', 'tanggal_pelanggaran', 'tgl_pelanggaran']
);

$idSiswaP = lp_pick(
    $pelanggaranCols,
    ['id_siswa', 'siswa_id']
);

$idKategoriP = lp_pick(
    $pelanggaranCols,
    ['id_kategori', 'id_pelanggaran_kategori', 'kategori_id']
);

$poinP = lp_pick(
    $pelanggaranCols,
    ['poin', 'poin_pelanggaran']
);

$deskripsiP = lp_pick(
    $pelanggaranCols,
    ['catatan', 'deskripsi', 'keterangan', 'uraian']
);

$tindakanP = lp_pick(
    $pelanggaranCols,
    ['tindakan', 'sanksi']
);

$lanjutP = lp_pick(
    $pelanggaranCols,
    ['tindak_lanjut', 'penanganan', 'hasil_penanganan']
);

$idSiswa = lp_pick($siswaCols, ['id_siswa', 'id']);

$namaSiswa = lp_pick(
    $siswaCols,
    ['nama_siswa', 'nama', 'nama_lengkap']
);

$nisn = lp_pick($siswaCols, ['nisn', 'nis']);

$idKelasS = lp_pick($siswaCols, ['id_kelas', 'kelas_id']);

$idKelas = lp_pick($kelasCols, ['id_kelas', 'id']);

$namaKelas = lp_pick(
    $kelasCols,
    ['nama_kelas', 'nama', 'kelas']
);

$idKategori = lp_pick(
    $kategoriCols,
    ['id_kategori', 'id_pelanggaran_kategori', 'id']
);

$namaKategori = lp_pick(
    $kategoriCols,
    ['nama_kategori', 'nama_pelanggaran', 'kategori', 'nama']
);

/* =========================
   AMBIL DATA LAPORAN
========================= */

$laporan = [];
$errorLaporan = '';

if (!$errors) {
    try {
        $selects = [
            lp_select('p', $idPelanggaran, 'id_pelanggaran'),
            lp_select('p', $tanggal, 'tanggal'),
            lp_select('p', $idSiswaP, 'id_siswa'),
            lp_select('p', $poinP, 'poin'),
            lp_select('p', $deskripsiP, 'deskripsi'),
            lp_select('p', $tindakanP, 'tindakan'),
            lp_select('p', $lanjutP, 'tindak_lanjut'),
            lp_select('s', $namaSiswa, 'nama_siswa'),
            lp_select('s', $nisn, 'nisn'),
            lp_select('k', $namaKelas, 'nama_kelas'),
            lp_select('kat', $namaKategori, 'nama_kategori')
        ];

        $sql = 'SELECT ' . implode(', ', $selects)
             . ' FROM t_pelanggaran_siswa p ';

        if ($idSiswaP && $idSiswa) {
            $sql .= " LEFT JOIN t_siswa s
                      ON p.`$idSiswaP` = s.`$idSiswa` ";
        } else {
            $sql .= ' LEFT JOIN t_siswa s ON 1 = 0 ';
        }

        if ($idKelasS && $idKelas) {
            $sql .= " LEFT JOIN t_kelas k
                      ON s.`$idKelasS` = k.`$idKelas` ";
        } else {
            $sql .= ' LEFT JOIN t_kelas k ON 1 = 0 ';
        }

        if ($idKategoriP && $idKategori) {
            $sql .= " LEFT JOIN t_pelanggaran_kategori kat
                      ON p.`$idKategoriP` = kat.`$idKategori` ";
        } else {
            $sql .= ' LEFT JOIN t_pelanggaran_kategori kat ON 1 = 0 ';
        }

        if ($tanggal) {
            $sql .= " ORDER BY p.`$tanggal` DESC ";

            if ($idPelanggaran) {
                $sql .= ", p.`$idPelanggaran` DESC ";
            }
        } elseif ($idPelanggaran) {
            $sql .= " ORDER BY p.`$idPelanggaran` DESC ";
        }

        $result = $koneksi->query($sql);
        $laporan = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Throwable $e) {
        error_log('Query laporan gagal: ' . $e->getMessage());
        $errorLaporan = 'Data laporan gagal dimuat. Periksa struktur tabel database.';
    }
}

/* =========================
   RINGKASAN
========================= */

$totalPoin = 0;
$siswaUnik = [];

foreach ($laporan as $item) {
    $totalPoin += (int)($item['poin'] ?? 0);

    if ($item['id_siswa'] !== null) {
        $siswaUnik[(string)$item['id_siswa']] = true;
    }
}

include 'header.php';
?>

<!-- CSS DATATABLES BOOTSTRAP 4 -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<style>
    .laporan-title {
        font-weight: 700;
        color: #173b70;
    }

    .laporan-card {
        border: 0;
        border-radius: 10px;
        overflow: hidden;
    }

    .laporan-card .card-header {
        background: #174780;
        color: #fff;
        padding: 15px 18px;
    }

    .ringkasan-card {
        border: 0;
        border-radius: 10px;
    }

    #tabelLaporan th,
    #tabelLaporan td {
        vertical-align: middle;
    }

    /* PAGINATION KOTAK - PAKSA CSS */
    #tabelLaporan_wrapper .dataTables_paginate {
        display: block !important;
        margin-top: 20px !important;
        padding-top: 10px !important;
        text-align: right !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate .pagination {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        padding-left: 0 !important;
        margin: 0 !important;
        list-style: none !important;
        border-radius: 5px !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate .page-item {
        display: block !important;
        margin: 0 !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate .page-link {
        position: relative !important;
        display: block !important;
        padding: 8px 13px !important;
        margin: 0 !important;
        color: #007bff !important;
        background: #fff !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0 !important;
        text-decoration: none !important;
        line-height: 1.5 !important;
        box-shadow: none !important;
        outline: none !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-item + .page-item .page-link {
        border-left: 0 !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-item:first-child .page-link {
        border-radius: 5px 0 0 5px !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-item:last-child .page-link {
        border-radius: 0 5px 5px 0 !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-item.active .page-link {
        z-index: 1 !important;
        color: #fff !important;
        background: #007bff !important;
        border-color: #007bff !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-item.disabled .page-link {
        color: #6c757d !important;
        background: #fff !important;
        cursor: not-allowed !important;
    }

    #tabelLaporan_wrapper .dataTables_paginate
    .page-link:hover {
        color: #0056b3 !important;
        background: #e9ecef !important;
        border-color: #dee2e6 !important;
    }

    @media (max-width: 576px) {
        #tabelLaporan_wrapper .dataTables_paginate {
            text-align: center !important;
        }

        #tabelLaporan_wrapper .dataTables_paginate .page-link {
            padding: 6px 9px !important;
        }
    }

    @media print {
        .sidebar,
        nav,
        .btn,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_paginate,
        .dataTables_info {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .container,
        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
        }

        .laporan-card {
            box-shadow: none !important;
        }

        table {
            font-size: 10px !important;
        }
    }
</style>

<div class="container-fluid py-3">

    <!-- JUDUL -->
    <div class="d-flex justify-content-between align-items-center
                flex-wrap border-bottom pb-3 mb-4">
        <div>
            <h1 class="h3 laporan-title">
                <i class="fas fa-file-alt mr-2"></i>
                Laporan & Rekap Pelanggaran
            </h1>

            <p class="text-muted mb-0">
                Rekap catatan pelanggaran siswa
            </p>
        </div>

        <button onclick="window.print()"
                class="btn btn-secondary btn-sm mt-2">
            <i class="fas fa-print mr-1"></i>
            Cetak Laporan
        </button>
    </div>

    <!-- ERROR -->
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong>Struktur database belum lengkap.</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $err): ?>
                    <li><?= lp_h($err) ?></li>
                <?php endforeach; ?>
            </ul>
            Pastikan nama database dan tabel sesuai dengan project.
        </div>
    <?php endif; ?>

    <?php if ($errorLaporan !== ''): ?>
        <div class="alert alert-danger">
            <?= lp_h($errorLaporan) ?>
        </div>
    <?php endif; ?>

    <!-- KARTU RINGKASAN -->
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Total Catatan</div>
                    <h3 class="font-weight-bold">
                        <?= count($laporan) ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Total Poin Pelanggaran</div>
                    <h3 class="font-weight-bold text-danger">
                        <?= $totalPoin ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Siswa Tercatat</div>
                    <h3 class="font-weight-bold text-primary">
                        <?= count($siswaUnik) ?>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL LAPORAN -->
    <div class="card shadow-sm laporan-card mb-4">

        <div class="card-header font-weight-bold">
            <i class="fas fa-list mr-2"></i>
            Rekapitulasi Data Pelanggaran
        </div>

        <div class="card-body">

            <p class="text-muted">
                Cari dan urutkan catatan pelanggaran siswa.
            </p>

            <div class="table-responsive">
                <table id="tabelLaporan"
                       class="table table-bordered table-striped table-hover mb-0"
                       style="width:100%">

                    <thead class="thead-dark text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa & Kelas</th>
                            <th>NISN</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Poin</th>
                            <th>Tindakan / Sanksi</th>
                            <th>Tindak Lanjut</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$laporan): ?>

                        <tr>
                            <td colspan="9"
                                class="text-center text-muted py-4">
                                Belum ada data laporan pelanggaran.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($laporan as $i => $row): ?>
                            <?php
                            $tgl = '-';

                            if (!empty($row['tanggal'])) {
                                $timestamp = strtotime($row['tanggal']);

                                if ($timestamp !== false) {
                                    $tgl = date('d-m-Y', $timestamp);
                                }
                            }

                            $nama = $row['nama_siswa']
                                ?? ('Siswa ID ' . ($row['id_siswa'] ?? '-'));

                            $kelas = $row['nama_kelas'] ?? '-';
                            $nisnSiswa = $row['nisn'] ?? '-';
                            $kategori = $row['nama_kategori'] ?? '-';
                            $deskripsi = $row['deskripsi'] ?? '-';
                            $poin = (int)($row['poin'] ?? 0);
                            $tindakan = $row['tindakan'] ?? '-';
                            $lanjut = $row['tindak_lanjut'] ?? '-';
                            ?>

                            <tr>
                                <td class="text-center">
                                    <?= $i + 1 ?>
                                </td>

                                <td class="text-center">
                                    <?= lp_h($tgl) ?>
                                </td>

                                <td>
                                    <strong><?= lp_h($nama) ?></strong><br>
                                    <small class="text-muted">
                                        Kelas: <?= lp_h($kelas) ?>
                                    </small>
                                </td>

                                <td><?= lp_h($nisnSiswa) ?></td>
                                <td><?= lp_h($kategori) ?></td>
                                <td><?= lp_h($deskripsi) ?></td>

                                <td class="text-center text-danger font-weight-bold">
                                    <?= $poin ?>
                                </td>

                                <td><?= lp_h($tindakan) ?></td>
                                <td><?= lp_h($lanjut) ?></td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted small py-3">
        &copy; 2026 - Sistem Informasi Pelanggaran Siswa
        &bull; Developed by
        <strong>Azril Akbar Fawji Abdillah</strong>
    </footer>

</div>

<!-- JAVASCRIPT DATATABLES -->
<!-- Mengandalkan jQuery yang biasanya sudah dimuat oleh header/footer -->
<script>
(function () {
    function jalankanDataTables() {
        if (!window.jQuery) {
            console.error(
                'jQuery belum dimuat. Pastikan header.php memuat jQuery.'
            );
            return;
        }

        if (!window.jQuery.fn.DataTable) {
            console.error(
                'DataTables belum dimuat. Periksa koneksi internet dan urutan script.'
            );
            return;
        }

        if (!window.jQuery.fn.dataTable.isDataTable('#tabelLaporan')) {
            window.jQuery('#tabelLaporan').DataTable({
                pageLength: 10,

                lengthMenu: [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, 'Semua']
                ],

                order: [],

                autoWidth: false,

                pagingType: 'full_numbers',

                language: {
                    search: 'Cari laporan:',
                    searchPlaceholder: 'Nama siswa, kategori, tanggal...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
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
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            jalankanDataTables
        );
    } else {
        jalankanDataTables();
    }
})();
</script>

<?php include 'footer.php'; ?>
