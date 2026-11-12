<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role_user = $_SESSION['role'] ?? 'Guru';
$nama_user = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Wali Kelas / Guru BK';

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
$totalPoin = 0;
$siswaUnik = [];

if ($result) {
    $laporan = $result->fetch_all(MYSQLI_ASSOC);
}

foreach ($laporan as $item) {
    $totalPoin += (int)($item['poin'] ?? 0);
    if (!empty($item['siswa_id'])) {
        $siswaUnik[(string)$item['siswa_id']] = true;
    }
}

function lp_h($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Rekapitulasi Pelanggaran</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop-surat h5 { margin: 0; font-size: 11pt; font-weight: bold; }
        .kop-surat h4 { margin: 2px 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 9pt; }

        .judul-dokumen {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-dokumen h4 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .judul-dokumen p { margin: 2px 0; font-size: 10pt; }

        .tabel-info {
            width: 100%;
            margin-bottom: 12px;
            font-size: 10.5pt;
        }
        .tabel-info td { padding: 2px 4px; vertical-align: top; }

        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        table.tabel-data th, table.tabel-data td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        table.tabel-data th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            text-align: center;
            font-weight: bold;
        }
        table.tabel-data tfoot td {
            font-weight: bold;
            background-color: #fafafa !important;
        }

        .ttd-wrapper {
            margin-top: 25px;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-kanan {
            float: right;
            width: 260px;
            text-align: center;
            font-size: 11pt;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #f8f9fa; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; font-family: sans-serif; font-size: 13px;">
        <span>Pratinjau Dokumen Siap Cetak (Format Resmi A4)</span>
        <div>
            <button onclick="window.print()" style="padding: 6px 14px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cetak / Simpan PDF</button>
            <button onclick="window.close()" style="padding: 6px 14px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">Tutup</button>
        </div>
    </div>

    <div class="kop-surat">
        <h5>PEMERINTAH PROVINSI JAWA BARAT &bull; DINAS PENDIDIKAN</h5>
        <h4>SMK NEGERI / SWASTA UKK 2026</h4>
        <p>Jl. Pendidikan No. 123, Kota Tasikmalaya, Jawa Barat &bull; Telp. (0265) 123456</p>
    </div>

    <div class="judul-dokumen">
        <h4>LAPORAN REKAPITULASI PELANGGARAN SISWA</h4>
        <p>Tahun Ajaran: 2026/2027 (Kelas: 12 RPL 1 & 12 RPL 2)</p>
    </div>

    <table class="tabel-info">
        <tr><td width="180">Total Catatan Pelanggaran</td><td width="10">:</td><td><strong><?= count($laporan); ?> Catatan</strong></td></tr>
        <tr><td>Total Siswa Tercatat</td><td>:</td><td><strong><?= count($siswaUnik); ?> Siswa</strong></td></tr>
        <tr><td>Total Akumulasi Poin</td><td>:</td><td><strong><?= $totalPoin; ?> Poin</strong></td></tr>
    </table>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="12%">Tanggal</th>
                <th width="23%">Nama Siswa & Kelas</th>
                <th width="12%">NISN</th>
                <th width="18%">Kategori / Pelanggaran</th>
                <th width="12%">Tindakan</th>
                <th width="9%">Status</th>
                <th width="9%">Poin</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporan)): ?>
                <tr><td colspan="8" align="center">Belum ada data laporan pelanggaran.</td></tr>
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
                        <td align="center"><?= $i + 1; ?></td>
                        <td align="center"><?= lp_h($tgl); ?></td>
                        <td>
                            <strong><?= lp_h($row['nama_siswa']); ?></strong><br>
                            <small>Kelas: <?= lp_h($row['nama_kelas']); ?></small>
                        </td>
                        <td align="center"><?= lp_h($row['nisn']); ?></td>
                        <td><?= lp_h($row['nama_kategori']); ?></td>
                        <td><?= lp_h($row['tindakan']); ?></td>
                        <td align="center"><?= lp_h($row['tindak_lanjut']); ?></td>
                        <td align="center"><strong><?= (int)$row['poin']; ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right" style="padding-right: 10px;">Total Akumulasi Poin Keseluruhan:</td>
                <td align="center"><strong><?= $totalPoin; ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-wrapper">
        <div class="ttd-kanan">
            <p style="margin-bottom: 5px;">Tasikmalaya, <?= date('d F Y'); ?></p>
            <p style="margin-bottom: 60px;">
                Mengetahui,<br>
                <strong><?= ucwords(str_replace(['_', '-'], ' ', $role_user)); ?> / Wali Kelas</strong>
            </p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                <?= lp_h($nama_user); ?>
            </p>
            <p style="margin: 0; font-size: 10pt;">NIP. 1980020002</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>