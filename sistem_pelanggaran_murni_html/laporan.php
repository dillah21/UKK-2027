
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

function lp_h($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

// ========================================
// IDENTITAS SEKOLAH
// ========================================

$namaSekolah = 'SMK NEGERI / SWASTA UKK 2026';
$alamatSekolah = 'Jl. Pendidikan No. 123, Kota Tasikmalaya, Jawa Barat';
$teleponSekolah = '(0265) 123456';
$tahunAjaran = '2026/2027';

// ========================================
// AMBIL DATA PELANGGARAN SELURUH KELAS
// ========================================

$sql = "
    SELECT
        p.id AS id_pelanggaran,
        p.tanggal,
        p.siswa_id,
        p.poin,
        p.keterangan AS deskripsi,
        p.tindakan,
        p.status AS tindak_lanjut,
        COALESCE(s.nama, p.nama_siswa, 'Siswa') AS nama_siswa,
        COALESCE(s.nisn, '-') AS nisn,
        COALESCE(k.nama, p.nama_kelas, '-') AS nama_kelas,
        COALESCE(kat.nama, p.nama_pelanggaran, '-') AS nama_kategori
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_siswa s
        ON p.siswa_id = s.id
    LEFT JOIN t_kelas k
        ON p.kelas_id = k.id
    LEFT JOIN t_pelanggaran_kategori kat
        ON p.pelanggaran_kategori_id = kat.id
    WHERE 1=1
    ORDER BY p.tanggal DESC, p.id DESC
";

$result = $koneksi->query($sql);

$laporan = [];

if ($result) {
    $laporan = $result->fetch_all(MYSQLI_ASSOC);
}

// ========================================
// HITUNG RINGKASAN
// ========================================

$totalPoin = 0;
$siswaUnik = [];

foreach ($laporan as $item) {

    $totalPoin += (int)($item['poin'] ?? 0);

    if (!empty($item['siswa_id'])) {
        $siswaUnik[(string)$item['siswa_id']] = true;
    }
}

$totalCatatan = count($laporan);
$totalSiswa = count($siswaUnik);

$role_user = $_SESSION['role'] ?? 'Wali Kelas';
$nama_user = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Wali Kelas';

// ========================================
// TANGGAL CETAK
// ========================================

$bulanIndonesia = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$tanggalCetak = date('d') . ' ' .
    $bulanIndonesia[(int)date('n')] . ' ' .
    date('Y');

include 'header.php';
?>

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<style>

/* ========================================
   TAMPILAN WEB
======================================== */

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

#tabelLaporan th {
    background-color: #343a40 !important;
    color: #fff !important;
    text-align: center;
}

.cetak-only {
    display: none !important;
}

/* ========================================
   FORMAT CETAK A4 LANDSCAPE
======================================== */

@media print {

    @page {
        size: A4 landscape;
        margin: 1.2cm 1cm;
    }

    html,
    body {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        color: #000 !important;
        font-family: Arial, Helvetica, sans-serif !important;
        font-size: 9pt !important;
        line-height: 1.35 !important;
    }

    /* Sembunyikan elemen website */

    .sidebar,
    .topbar,
    nav,
    .navbar,
    .btn,
    button,
    .dataTables_filter,
    .dataTables_length,
    .dataTables_paginate,
    .dataTables_info,
    .card-header,
    .no-print,
    .web-only {
        display: none !important;
    }

    /* Atur lebar halaman */

    .main-content,
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Elemen khusus cetak */

    .cetak-only {
        display: block !important;
    }

    /* KOP SURAT */

    .kop-surat-cetak {
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }

    .kop-surat-cetak h5 {
        margin: 0;
        font-size: 11pt;
        font-weight: bold;
    }

    .kop-surat-cetak h4 {
        margin: 3px 0;
        font-size: 14pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .kop-surat-cetak p {
        margin: 0;
        font-size: 9pt;
    }

    /* JUDUL LAPORAN */

    .judul-dokumen-cetak {
        text-align: center;
        margin-bottom: 15px;
    }

    .judul-dokumen-cetak h4 {
        font-size: 12pt;
        font-weight: bold;
        text-decoration: underline;
        margin: 0;
    }

    .judul-dokumen-cetak p {
        font-size: 10pt;
        margin: 4px 0;
    }

    /* RINGKASAN */

    .ringkasan-cetak {
        margin-bottom: 15px;
        font-size: 9pt;
    }

    .ringkasan-cetak table {
        width: 55%;
        border-collapse: collapse;
    }

    .ringkasan-cetak td {
        padding: 2px 4px;
    }

    /* KARTU DAN TABEL */

    .card,
    .card-body,
    .table-responsive {
        width: 100% !important;
        overflow: visible !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    table#tabelLaporan {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        font-size: 8pt !important;
        margin-top: 10px !important;
    }

    /* Header tabel berulang */

    #tabelLaporan thead {
        display: table-header-group !important;
    }

    #tabelLaporan tfoot {
        display: table-footer-group !important;
    }

    /* Hindari baris terpotong */

    #tabelLaporan tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    #tabelLaporan th,
    #tabelLaporan td {
        border: 1px solid #333 !important;
        padding: 5px 4px !important;
        color: #000 !important;
        vertical-align: top !important;
        overflow-wrap: anywhere !important;
        background: #fff !important;
    }

    #tabelLaporan th {
        text-align: center !important;
        font-weight: bold !important;
        background: #eaeaea !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* PENGATURAN LEBAR KOLOM */

    #tabelLaporan th:nth-child(1),
    #tabelLaporan td:nth-child(1) {
        width: 4% !important;
        text-align: center !important;
    }

    #tabelLaporan th:nth-child(2),
    #tabelLaporan td:nth-child(2) {
        width: 8% !important;
        text-align: center !important;
    }

    #tabelLaporan th:nth-child(3),
    #tabelLaporan td:nth-child(3) {
        width: 14% !important;
    }

    #tabelLaporan th:nth-child(4),
    #tabelLaporan td:nth-child(4) {
        width: 9% !important;
        text-align: center !important;
    }

    #tabelLaporan th:nth-child(5),
    #tabelLaporan td:nth-child(5) {
        width: 12% !important;
    }

    #tabelLaporan th:nth-child(6),
    #tabelLaporan td:nth-child(6) {
        width: 17% !important;
    }

    #tabelLaporan th:nth-child(7),
    #tabelLaporan td:nth-child(7) {
        width: 5% !important;
        text-align: center !important;
    }

    #tabelLaporan th:nth-child(8),
    #tabelLaporan td:nth-child(8) {
        width: 16% !important;
    }

    #tabelLaporan th:nth-child(9),
    #tabelLaporan td:nth-child(9) {
        width: 15% !important;
        text-align: center !important;
    }

    /* STATUS */

    .badge {
        background: transparent !important;
        color: #000 !important;
        padding: 0 !important;
        font-weight: bold !important;
    }

    /* TANDA TANGAN */

    .footer-ttd-cetak {
        margin-top: 25px !important;
        width: 100%;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .footer-ttd-cetak > div {
        color: #000 !important;
    }

    * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

</style>

<div class="container-fluid py-3">

    <!-- =====================================
         KOP SURAT
    ====================================== -->

    <div class="cetak-only kop-surat-cetak">

        <h5>PEMERINTAH PROVINSI JAWA BARAT</h5>

        <h5>DINAS PENDIDIKAN</h5>

        <h4><?= lp_h($namaSekolah); ?></h4>

        <p>
            <?= lp_h($alamatSekolah); ?>
            &bull;
            Telp. <?= lp_h($teleponSekolah); ?>
        </p>

    </div>

    <!-- =====================================
         JUDUL LAPORAN
    ====================================== -->

    <div class="cetak-only judul-dokumen-cetak">

        <h4>
            LAPORAN REKAPITULASI PELANGGARAN SISWA
        </h4>

        <p>
            Kelas XII RPL 1 dan XII RPL 2
            <br>
            Tahun Ajaran: <?= lp_h($tahunAjaran); ?>
        </p>

    </div>

    <!-- =====================================
         RINGKASAN CETAK
    ====================================== -->

    <div class="cetak-only ringkasan-cetak">

        <table>

            <tr>
                <td width="180">Total Catatan Pelanggaran</td>
                <td width="10">:</td>
                <td>
                    <strong><?= $totalCatatan; ?> Catatan</strong>
                </td>
            </tr>

            <tr>
                <td>Total Siswa Tercatat</td>
                <td>:</td>
                <td>
                    <strong><?= $totalSiswa; ?> Siswa</strong>
                </td>
            </tr>

            <tr>
                <td>Total Akumulasi Poin</td>
                <td>:</td>
                <td>
                    <strong><?= $totalPoin; ?> Poin</strong>
                </td>
            </tr>

        </table>

    </div>

    <!-- =====================================
         JUDUL HALAMAN WEB
    ====================================== -->

    <div class="d-flex justify-content-between align-items-center flex-wrap border-bottom pb-3 mb-4 web-only">

        <div>

            <h1 class="h3 laporan-title">

                <i class="fas fa-file-alt mr-2"></i>
                Laporan & Rekap Pelanggaran

            </h1>

            <p class="text-muted mb-0">
                Rekap pelanggaran seluruh kelas
            </p>

        </div>

        <button onclick="window.print()"
            class="btn btn-secondary btn-sm mt-2 no-print">

            <i class="fas fa-print mr-1"></i>
            Cetak Laporan

        </button>

    </div>

    <!-- =====================================
         RINGKASAN WEB
    ====================================== -->

    <div class="row mb-4 web-only">

        <div class="col-md-4 mb-3">

            <div class="card shadow-sm ringkasan-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Catatan
                    </div>

                    <h3 class="font-weight-bold">
                        <?= $totalCatatan; ?>
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card shadow-sm ringkasan-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Poin Pelanggaran
                    </div>

                    <h3 class="font-weight-bold text-danger">
                        <?= $totalPoin; ?>
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card shadow-sm ringkasan-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Siswa Tercatat
                    </div>

                    <h3 class="font-weight-bold text-primary">
                        <?= $totalSiswa; ?>
                    </h3>

                </div>

            </div>

        </div>

    </div>

    <!-- =====================================
         TABEL LAPORAN
    ====================================== -->

    <div class="card shadow-sm laporan-card mb-4">

        <div class="card-header font-weight-bold web-only">

            <i class="fas fa-list mr-2"></i>
            Rekapitulasi Jenis Pelanggaran

        </div>

        <div class="card-body">

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
                            <th>Kategori / Pelanggaran</th>
                            <th>Deskripsi</th>
                            <th>Poin</th>
                            <th>Tindakan / Sanksi</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (empty($laporan)): ?>

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

                                $ts = strtotime($row['tanggal']);

                                if ($ts !== false) {
                                    $tgl = date('d-m-Y', $ts);
                                }

                            }

                            $status = trim((string)($row['tindak_lanjut'] ?? ''));

                            ?>

                            <tr>

                                <td class="text-center">
                                    <?= $i + 1; ?>
                                </td>

                                <td class="text-center">
                                    <?= lp_h($tgl); ?>
                                </td>

                                <td>

                                    <strong>
                                        <?= lp_h($row['nama_siswa']); ?>
                                    </strong>

                                    <br>

                                    <small>
                                        Kelas: <?= lp_h($row['nama_kelas']); ?>
                                    </small>

                                </td>

                                <td class="text-center">
                                    <?= lp_h($row['nisn']); ?>
                                </td>

                                <td>
                                    <?= lp_h($row['nama_kategori']); ?>
                                </td>

                                <td>
                                    <?= lp_h($row['deskripsi']); ?>
                                </td>

                                <td class="text-center font-weight-bold">
                                    <?= (int)$row['poin']; ?>
                                </td>

                                <td>
                                    <?= lp_h($row['tindakan']); ?>
                                </td>

                                <td class="text-center">

                                    <span class="badge badge-<?= ($status === 'Selesai') ? 'success' : 'warning'; ?>">

                                        <?= lp_h($status ?: '-'); ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- =====================================
         TANDA TANGAN CETAK
    ====================================== -->

    <div class="cetak-only footer-ttd-cetak">

        <div style="float:right; width:260px; text-align:center; font-size:10pt;">

            <p style="margin-bottom:5px;">
                Tasikmalaya, <?= lp_h($tanggalCetak); ?>
            </p>

            <p style="margin-bottom:65px;">

                Mengetahui,

                <br>

                <strong>
                    <?= lp_h(ucwords(str_replace(['_', '-'], ' ', $role_user))); ?>
                </strong>

            </p>

            <p style="margin:0; font-weight:bold; text-decoration:underline;">

                <?= lp_h($nama_user); ?>

            </p>

            <p style="margin:0; font-size:9pt;">
                NIP. -
            </p>

        </div>

        <div style="clear:both;"></div>

    </div>

</div>

<!-- =====================================
     JAVASCRIPT DATATABLES
===================================== -->

<script>

$(document).ready(function () {

    let tabel = $('#tabelLaporan').DataTable({

        pageLength: 10,

        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            [5, 10, 25, 50, 100, 'Semua']
        ],

        order: [],

        autoWidth: false,

        language: {

            search: 'Cari laporan:',

            lengthMenu: 'Tampilkan _MENU_ data',

            zeroRecords: 'Data tidak ditemukan',

            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

            infoEmpty: 'Tidak ada data tersedia',

            paginate: {

                first: 'Pertama',

                last: 'Terakhir',

                next: 'Selanjutnya',

                previous: 'Sebelumnya'

            }

        }

    });

    // Simpan jumlah data halaman

    let panjangHalaman = tabel.page.len();

    // Sebelum cetak: tampilkan seluruh data

    window.addEventListener('beforeprint', function () {

        panjangHalaman = tabel.page.len();

        tabel.page.len(-1).draw();

    });

    // Setelah cetak: kembalikan tampilan awal

    window.addEventListener('afterprint', function () {

        tabel.page.len(panjangHalaman).draw();

    });

});

</script>

<?php include 'footer.php'; ?>
