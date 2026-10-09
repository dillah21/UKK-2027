<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    exit('Akses ditolak.');
}

$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
$kelas_id = intval($_GET['kelas_id'] ?? 0);

$filename = "Laporan_Pelanggaran_Siswa_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['No', 'Tanggal', 'NISN', 'Nama Siswa', 'Kelas', 'Nama Pelanggaran', 'Poin']);

$sql = "SELECT r.id, r.tanggal, s.nisn, s.nama AS nama_siswa, k.nama AS nama_kelas, 
               p.nama AS nama_pelanggaran, p.poin
        FROM t_riwayat_pelanggaran r
        JOIN t_siswa s ON r.siswa_id = s.id
        LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        JOIN t_pelanggaran p ON r.pelanggaran_id = p.id
        WHERE 1=1";

if (!empty($tgl_mulai)) {
    $sql .= " AND r.tanggal >= '" . $koneksi->real_escape_string($tgl_mulai) . "'";
}

if (!empty($tgl_selesai)) {
    $sql .= " AND r.tanggal <= '" . $koneksi->real_escape_string($tgl_selesai) . "'";
}

if ($kelas_id > 0) {
    $sql .= " AND k.id = " . $kelas_id;
}

$sql .= " ORDER BY r.tanggal DESC, r.id DESC";
$result = $koneksi->query($sql);

$no = 1;
$total_poin = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_poin += $row['poin'];
        fputcsv($output, [
            $no++,
            date('d-m-Y', strtotime($row['tanggal'])),
            "'" . $row['nisn'],
            $row['nama_siswa'],
            $row['nama_kelas'] ?? '-',
            $row['nama_pelanggaran'],
            $row['poin']
        ]);
    }
    fputcsv($output, ['', '', '', '', '', 'Total Akumulasi Poin:', $total_poin]);
} else {
    fputcsv($output, ['-', '-', '-', 'Tidak ada data pelanggaran yang sesuai filter.', '-', '-', '-']);
}

fclose($output);
exit;