<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'auth.php';
require_once 'koneksi.php';
try {
    $result = $koneksi->query("SELECT p.*, s.nama AS nama_siswa FROM prestasi p INNER JOIN t_siswa s ON p.id_siswa=s.id ORDER BY p.tanggal DESC");
} catch (Throwable $e) {
    error_log('Cetak prestasi gagal: '.$e->getMessage()); http_response_code(500);
    exit('Data prestasi tidak dapat dimuat. Periksa struktur tabel database.');
}
function cp_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cetak Data Prestasi Siswa</title><style>
body{font-family:Arial,sans-serif;color:#222;padding:24px}h3{text-align:center;margin-bottom:4px}.sub{text-align:center;margin-bottom:22px;color:#555}
table{width:100%;border-collapse:collapse}th,td{border:1px solid #555;padding:7px;font-size:12px}th{background:#eee;text-align:center}
@media print{.no-print{display:none}body{padding:0}@page{size:A4 landscape;margin:12mm}}
</style></head><body>
<div class="no-print" style="margin-bottom:16px"><button onclick="window.print()">Cetak</button> <a href="prestasi.php">Kembali</a></div>
<h3>REKAPITULASI PRESTASI SISWA</h3><div class="sub">Sistem Informasi Pelanggaran dan Prestasi Siswa</div>
<table><thead><tr><th>No</th><th>Tanggal</th><th>Nama Siswa</th><th>Prestasi / Kegiatan</th><th>Tingkat</th><th>Poin</th><th>Keterangan</th></tr></thead><tbody>
<?php $no=1; if ($result->num_rows): while($r=$result->fetch_assoc()): ?>
<tr><td style="text-align:center"><?= $no++ ?></td><td><?= cp_h(!empty($r['tanggal']) ? date('d-m-Y',strtotime($r['tanggal'])) : '-') ?></td><td><?= cp_h($r['nama_siswa']) ?></td><td><?= cp_h($r['nama_prestasi']) ?></td><td><?= cp_h($r['tingkat']) ?></td><td style="text-align:center"><?= (int)$r['poin'] ?></td><td><?= cp_h($r['keterangan']) ?></td></tr>
<?php endwhile; else: ?><tr><td colspan="7" style="text-align:center">Belum ada data prestasi.</td></tr><?php endif; ?>
</tbody></table></body></html>
