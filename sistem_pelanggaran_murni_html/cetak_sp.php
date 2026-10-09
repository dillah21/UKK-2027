<?php
require_once 'auth.php';
require_once 'koneksi.php';

$siswa_id = intval($_GET['siswa_id'] ?? 0);

if ($siswa_id <= 0) {
    die("ID Siswa tidak valid.");
}

// Fetch Data Siswa & Wali Kelas
$query_siswa = "SELECT s.*, k.nama AS nama_kelas, g.nama AS nama_wali, g.nip AS nip_wali
                FROM t_siswa s
                LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
                LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                LEFT JOIN t_wali_kelas w ON k.id = w.kelas_id
                LEFT JOIN t_guru g ON w.guru_id = g.id
                WHERE s.id = $siswa_id";
$res_siswa = $koneksi->query($query_siswa);
$siswa = $res_siswa->fetch_assoc();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// Fetch Riwayat Pelanggaran Siswa
$query_riwayat = "SELECT r.tanggal, p.nama_pelanggaran, p.poin 
                 FROM t_riwayat_pelanggaran r
                 JOIN t_pelanggaran p ON r.pelanggaran_id = p.id
                 WHERE r.siswa_id = $siswa_id
                 ORDER BY r.tanggal ASC";
$res_riwayat = $koneksi->query($query_riwayat);

$total_poin = 0;
$riwayat_list = [];
if ($res_riwayat) {
    while ($r = $res_riwayat->fetch_assoc()) {
        $total_poin += $r['poin'];
        $riwayat_list[] = $r;
    }
}

// Tentukan Jenis SP
$tingkat_sp = "SURAT PERINGATAN I";
if ($total_poin >= 100) {
    $tingkat_sp = "SURAT PENGEMBALIAN KEPADA ORANG TUA";
} elseif ($total_poin >= 50) {
    $tingkat_sp = "SURAT PERINGATAN III";
} elseif ($total_poin >= 30) {
    $tingkat_sp = "SURAT PERINGATAN II";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak <?= $tingkat_sp; ?> - <?= htmlspecialchars($siswa['nama']); ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 40px; color: #000; }
        .header-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header-surat h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header-surat p { margin: 2px 0; font-size: 13px; }
        .judul-sp { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin: 20px 0 10px 0; }
        table.data-siswa { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        table.data-siswa td { padding: 4px; vertical-align: top; font-size: 14px; }
        table.detail-poin { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.detail-poin th, table.detail-poin td { border: 1px solid #000; padding: 6px; font-size: 13px; text-align: left; }
        .ttd-box { width: 100%; margin-top: 40px; }
        .ttd-box table { width: 100%; text-align: center; font-size: 14px; }
        .no-print { margin-bottom: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">Cetak Surat Ini</button>
    <button onclick="window.close()">Tutup</button>
</div>

<div class="header-surat">
    <h2>PEMERINTAH PROVINSI / YAYASAN PENDIDIKAN</h2>
    <h2>SMK NEGERI / SWASTA UKK 2026</h2>
    <p>Jl. Pendidikan No. 123, Kabupaten/Kota, Indonesia</p>
</div>

<div class="judul-sp"><?= $tingkat_sp; ?></div>

<p>Bahwa berdasarkan catatan poin pelanggaran tata tertib sekolah, disampaikan kepada orang tua/wali dari siswa:</p>

<table class="data-siswa">
    <tr>
        <td width="20%">Nama Siswa</td>
        <td width="2%">:</td>
        <td><b><?= htmlspecialchars($siswa['nama']); ?></b></td>
    </tr>
    <tr>
        <td>NISN</td>
        <td>:</td>
        <td><?= htmlspecialchars($siswa['nisn']); ?></td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td><?= htmlspecialchars($siswa['nama_kelas'] ?? '-'); ?></td>
    </tr>
    <tr>
        <td>Total Poin Pelanggaran</td>
        <td>:</td>
        <td><b><?= $total_poin; ?> Poin</b></td>
    </tr>
</table>

<p>Rincian pelanggaran yang telah dilakukan adalah sebagai berikut:</p>

<table class="detail-poin">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Tanggal</th>
            <th>Nama Pelanggaran</th>
            <th width="15%">Poin</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($riwayat_list)): ?>
            <?php $n=1; foreach ($riwayat_list as $r): ?>
                <tr>
                    <td><?= $n++; ?></td>
                    <td><?= date('d-m-Y', strtotime($r['tanggal'])); ?></td>
                    <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                    <td><?= $r['poin']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" align="center">Tidak ada catatan rincian.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p>Demikian surat peringatan ini disampaikan agar dapat menjadi perhatian bagi siswa serta orang tua/wali murid.</p>

<div class="ttd-box">
    <table>
        <tr>
            <td width="50%">
                Mengetahui,<br>Wali Kelas<br><br><br><br>
                <b><u><?= htmlspecialchars($siswa['nama_wali'] ?? '(...........................)'); ?></u></b><br>
                NIP. <?= htmlspecialchars($siswa['nip_wali'] ?? '-'); ?>
            </td>
            <td width="50%">
                , <?= date('d F Y'); ?><br>Guru Bimbingan Konseling (BK)<br><br><br><br>
                <b><u>(...........................................)</u></b><br>
                NIP. -
            </td>
        </tr>
    </table>
</div>

</body>
</html>