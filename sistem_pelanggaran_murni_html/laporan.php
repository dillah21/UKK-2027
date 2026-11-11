<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

function lp_h($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

// Kueri langsung mengambil dari t_pelanggaran_siswa dengan fallback ke data tabel relasi
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
    LEFT JOIN t_siswa s ON p.siswa_id = s.id
    LEFT JOIN t_kelas k ON p.kelas_id = k.id
    LEFT JOIN t_pelanggaran_kategori kat ON p.pelanggaran_kategori_id = kat.id
    WHERE UPPER(TRIM(COALESCE(k.nama, p.nama_kelas))) IN ('12 RPL 1', '12 RPL 2')
    ORDER BY p.tanggal DESC, p.id DESC
";

$result = $koneksi->query($sql);
$laporan = [];
if ($result) {
    $laporan = $result->fetch_all(MYSQLI_ASSOC);
}

$totalPoin = 0;
$siswaUnik = [];

foreach ($laporan as $item) {
    $totalPoin += (int)($item['poin'] ?? 0);
    if (!empty($item['siswa_id'])) {
        $siswaUnik[(string)$item['siswa_id']] = true;
    }
}

include 'header.php';
?>

<!-- CSS DATATABLES BOOTSTRAP 4 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<style>
    .laporan-title { font-weight: 700; color: #173b70; }
    .laporan-card { border: 0; border-radius: 10px; overflow: hidden; }
    .laporan-card .card-header { background: #174780; color: #fff; padding: 15px 18px; }
    .ringkasan-card { border: 0; border-radius: 10px; }
    #tabelLaporan th, #tabelLaporan td { vertical-align: middle; }
    #tabelLaporan th { background-color: #343a40 !important; color: #fff !important; text-align: center; }

    @media print {
        .sidebar, nav, .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
            display: none !important;
        }
        body { background: #fff !important; }
        .container-fluid { width: 100% !important; max-width: 100% !important; }
        .laporan-card { box-shadow: none !important; }
        table { font-size: 10px !important; }
    }
</style>

<div class="container-fluid py-3">

    <!-- JUDUL -->
    <div class="d-flex justify-content-between align-items-center flex-wrap border-bottom pb-3 mb-4">
        <div>
            <h1 class="h3 laporan-title">
                <i class="fas fa-file-alt mr-2"></i> Laporan & Rekap Pelanggaran
            </h1>
            <p class="text-muted mb-0">Rekap catatan pelanggaran siswa</p>
        </div>
        <button onclick="window.print()" class="btn btn-secondary btn-sm mt-2">
            <i class="fas fa-print mr-1"></i> Cetak Laporan
        </button>
    </div>

    <!-- KARTU RINGKASAN -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Total Catatan</div>
                    <h3 class="font-weight-bold"><?= count($laporan) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Total Poin Pelanggaran</div>
                    <h3 class="font-weight-bold text-danger"><?= $totalPoin ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm ringkasan-card h-100">
                <div class="card-body">
                    <div class="text-muted">Siswa Tercatat</div>
                    <h3 class="font-weight-bold text-primary"><?= count($siswaUnik) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL LAPORAN -->
    <div class="card shadow-sm laporan-card mb-4">
        <div class="card-header font-weight-bold">
            <i class="fas fa-list mr-2"></i> Rekapitulasi Jenis Pelanggaran
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelLaporan" class="table table-bordered table-striped table-hover mb-0" style="width:100%">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                Belum ada data laporan pelanggaran.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($laporan as $i => $row): ?>
                            <?php
                            $tgl = '-';
                            if (!empty($row['tanggal'])) {
                                $ts = strtotime($row['tanggal']);
                                if ($ts !== false) { $tgl = date('d-m-Y', $ts); }
                            }
                            ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td class="text-center"><?= lp_h($tgl) ?></td>
                                <td>
                                    <strong><?= lp_h($row['nama_siswa']) ?></strong><br>
                                    <small class="text-muted">Kelas: <?= lp_h($row['nama_kelas']) ?></small>
                                </td>
                                <td class="text-center"><?= lp_h($row['nisn']) ?></td>
                                <td><?= lp_h($row['nama_kategori']) ?></td>
                                <td><?= lp_h($row['deskripsi']) ?></td>
                                <td class="text-center text-danger font-weight-bold"><?= (int)$row['poin'] ?></td>
                                <td><?= lp_h($row['tindakan']) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= ($row['tindak_lanjut'] === 'Selesai') ? 'success' : 'warning' ?>">
                                        <?= lp_h($row['tindak_lanjut']) ?>
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
</div>

<!-- JAVASCRIPT DATATABLES -->
<script>
$(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#tabelLaporan')) {
        $('#tabelLaporan').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Semua']],
            order: [],
            autoWidth: false,
            language: {
                search: 'Cari laporan:',
                lengthMenu: 'Tampilkan _MENU_ data',
                zeroRecords: 'Data tidak ditemukan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data tersedia',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>