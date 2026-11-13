<?php
require_once 'auth.php';
require_once 'koneksi.php';

$siswa_id = (int)($_GET['siswa_id'] ?? 0);
if ($siswa_id <= 0) {
    http_response_code(400);
    exit('ID siswa tidak valid.');
}

$stmt = $koneksi->prepare("SELECT s.id, s.nis, s.nisn, s.nama, COALESCE(k.nama, '-') AS nama_kelas FROM t_siswa s LEFT JOIN t_kelas_siswa ks ON ks.siswa_id=s.id AND ks.status_aktif=1 LEFT JOIN t_kelas k ON k.id=ks.kelas_id WHERE s.id = ? ORDER BY ks.id DESC LIMIT 1");
$stmt->bind_param('i', $siswa_id);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$siswa) {
    http_response_code(404);
    exit('Data siswa tidak ditemukan.');
}

$stmt = $koneksi->prepare("
    SELECT p.tanggal, p.nama_pelanggaran, p.poin, p.nama_kelas
    FROM t_pelanggaran_siswa p
    WHERE p.siswa_id = ?
    ORDER BY p.tanggal ASC, p.id ASC
");
$stmt->bind_param('i', $siswa_id);
$stmt->execute();
$res_riwayat = $stmt->get_result();

$total_poin = 0;
$riwayat_list = [];
$nama_kelas = (string)($siswa['nama_kelas'] ?? '-');
while ($r = $res_riwayat->fetch_assoc()) {
    $total_poin += (int)$r['poin'];
    $riwayat_list[] = $r;
    if ($nama_kelas === '-' && !empty($r['nama_kelas'])) $nama_kelas = $r['nama_kelas'];
}
$stmt->close();

$stmt = $koneksi->prepare("
    SELECT g.nama, g.nip
    FROM t_wali_kelas w
    INNER JOIN t_kelas_siswa ks
        ON ks.kelas_id = w.kelas_id
       AND ks.tahun_ajaran_id = w.tahun_ajaran_id
    INNER JOIN t_guru g ON g.id = w.guru_id
    WHERE ks.siswa_id = ? AND w.status_aktif = 1
      AND ks.status_aktif = 1
    ORDER BY w.id DESC LIMIT 1
");
$stmt->bind_param('i', $siswa_id);
$stmt->execute();
$wali = $stmt->get_result()->fetch_assoc() ?: ['nama' => '(...........................)', 'nip' => '-'];
$stmt->close();

$tingkat_sp = "SURAT PERINGATAN I";
if ($total_poin >= 100) {
    $tingkat_sp = "SURAT PENGEMBALIAN KEPADA ORANG TUA";
} elseif ($total_poin >= 50) {
    $tingkat_sp = "SURAT PERINGATAN III";
} elseif ($total_poin >= 30) {
    $tingkat_sp = "SURAT PERINGATAN II";
}
function sp_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= sp_h($tingkat_sp) ?> - <?= sp_h($siswa['nama']) ?></title>
<style>
body{font-family:"Times New Roman",serif;margin:40px;color:#000}
.header-surat{text-align:center;border-bottom:3px double #000;padding-bottom:10px;margin-bottom:20px}
.header-surat h2{margin:0;font-size:18px;text-transform:uppercase}
.header-surat p{margin:2px 0;font-size:13px}
.judul-sp{text-align:center;font-size:16px;font-weight:bold;text-decoration:underline;margin:20px 0 10px}
table{width:100%;border-collapse:collapse;margin:15px 0}
.data-siswa td{padding:4px;vertical-align:top;font-size:14px}
.detail-poin th,.detail-poin td{border:1px solid #000;padding:6px;font-size:13px;text-align:left}
.ttd-box{margin-top:40px}.ttd-box table{text-align:center;font-size:14px}
.no-print{margin-bottom:20px}@media print{.no-print{display:none}}
</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()">Cetak Surat Ini</button> <button onclick="window.close()">Tutup</button></div>
<div class="header-surat">
<h2>PEMERINTAH PROVINSI / YAYASAN PENDIDIKAN</h2>
<h2>SMK NEGERI / SWASTA UKK 2026</h2>
<p>Jl. Pendidikan No. 123, Kabupaten/Kota, Indonesia</p>
</div>
<div class="judul-sp"><?= sp_h($tingkat_sp) ?></div>
<p>Berdasarkan catatan poin pelanggaran tata tertib sekolah, disampaikan kepada orang tua/wali dari siswa:</p>
<table class="data-siswa">
<tr><td width="20%">Nama Siswa</td><td width="2%">:</td><td><b><?= sp_h($siswa['nama']) ?></b></td></tr>
<tr><td>NIS</td><td>:</td><td><?= sp_h($siswa['nis'] ?? '-') ?></td></tr>
<tr><td>NISN</td><td>:</td><td><?= sp_h($siswa['nisn'] ?? '-') ?></td></tr>
<tr><td>Kelas</td><td>:</td><td><?= sp_h($nama_kelas) ?></td></tr>
<tr><td>Total Poin Pelanggaran</td><td>:</td><td><b><?= $total_poin ?> Poin</b></td></tr>
</table>
<p>Rincian pelanggaran:</p>
<table class="detail-poin">
<thead><tr><th width="5%">No</th><th width="20%">Tanggal</th><th>Nama Pelanggaran</th><th width="15%">Poin</th></tr></thead>
<tbody>
<?php if ($riwayat_list): foreach ($riwayat_list as $i => $r): ?>
<tr><td><?= $i+1 ?></td><td><?= sp_h(date('d-m-Y', strtotime($r['tanggal']))) ?></td><td><?= sp_h($r['nama_pelanggaran']) ?></td><td><?= (int)$r['poin'] ?></td></tr>
<?php endforeach; else: ?><tr><td colspan="4" align="center">Tidak ada catatan rincian.</td></tr><?php endif; ?>
</tbody></table>
<p>Demikian surat peringatan ini disampaikan agar menjadi perhatian siswa serta orang tua/wali murid.</p>
<div class="ttd-box"><table><tr>
<td width="50%">Mengetahui,<br>Wali Kelas<br><br><br><br><b><u><?= sp_h($wali['nama']) ?></u></b><br>NIP. <?= sp_h($wali['nip']) ?></td>
<td width="50%">, <?= date('d F Y') ?><br>Guru Bimbingan Konseling (BK)<br><br><br><br><b><u>(...........................................)</u></b><br>NIP. -</td>
</tr></table></div>
</body></html>
