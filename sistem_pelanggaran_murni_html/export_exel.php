<?php
require_once 'auth.php';
require_once 'koneksi.php';

$tgl_mulai = trim($_GET['tgl_mulai'] ?? '');
$tgl_selesai = trim($_GET['tgl_selesai'] ?? '');
$kelas_id = (int)($_GET['kelas_id'] ?? 0);

if ($tgl_mulai !== '' && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tgl_mulai)) {
    exit('Format tanggal mulai tidak valid.');
}
if ($tgl_selesai !== '' && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tgl_selesai)) {
    exit('Format tanggal selesai tidak valid.');
}
if ($tgl_mulai !== '' && $tgl_selesai !== '' && $tgl_selesai < $tgl_mulai) {
    exit('Tanggal selesai tidak boleh sebelum tanggal mulai.');
}

$sql = "SELECT id, tanggal, nama_siswa, nama_kelas, nama_pelanggaran, poin
        FROM t_pelanggaran_siswa WHERE 1=1";
$types = '';
$params = [];

if ($tgl_mulai !== '') {
    $sql .= " AND tanggal >= ?";
    $types .= 's';
    $params[] = $tgl_mulai;
}
if ($tgl_selesai !== '') {
    $sql .= " AND tanggal <= ?";
    $types .= 's';
    $params[] = $tgl_selesai;
}
if ($kelas_id > 0) {
    $sql .= " AND kelas_id = ?";
    $types .= 'i';
    $params[] = $kelas_id;
}
$sql .= " ORDER BY tanggal DESC, id DESC";

$stmt = $koneksi->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$filename = "Laporan_Pelanggaran_Siswa_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
fputcsv($output, ['No', 'Tanggal', 'Nama Siswa', 'Kelas', 'Nama Pelanggaran', 'Poin']);

$no = 1;
$total_poin = 0;
while ($row = $result->fetch_assoc()) {
    $total_poin += (int)$row['poin'];
    fputcsv($output, [
        $no++,
        date('d-m-Y', strtotime($row['tanggal'])),
        $row['nama_siswa'],
        $row['nama_kelas'],
        $row['nama_pelanggaran'],
        (int)$row['poin']
    ]);
}
fputcsv($output, ['', '', '', '', 'Total Akumulasi Poin:', $total_poin]);
fclose($output);
$stmt->close();
exit;
