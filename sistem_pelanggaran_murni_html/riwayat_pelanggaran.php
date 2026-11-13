<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Kolom Guru Pelapor hanya ditampilkan untuk Ari Sandriana dan role selain guru.
$role_login = strtolower(trim($_SESSION['role'] ?? ''));
$is_guru = ($role_login === 'guru');
$show_guru_pelapor = !$is_guru;
if ($is_guru) {
    $uid = (int)($_SESSION['user_id'] ?? 0);
    $qg = $koneksi->prepare("SELECT nama FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1");
    $qg->bind_param('i', $uid);
    $qg->execute();
    $guru_login = $qg->get_result()->fetch_assoc();
    $qg->close();
    $nama_guru_login = trim((string)($guru_login['nama'] ?? ''));
    $show_guru_pelapor = (strcasecmp($nama_guru_login, 'Ari Sandriana, S.Kom') === 0
        || strcasecmp($nama_guru_login, 'Ari Sandriana') === 0);
}

// Ambil riwayat yang sudah selesai dari tabel transaksi pelanggaran.
$sql = "
    SELECT
        ps.id,
        ps.tanggal,
        COALESCE(s.nis, '-') AS nis,
        ps.nama_siswa,
        ps.nama_kelas,
        COALESCE(pk.nama, '-') AS nama_kategori,
        ps.nama_pelanggaran,
        COALESCE(NULLIF(TRIM(ps.nama_guru), ''), '-') AS guru_pelapor,
        ps.poin,
        ps.tindakan,
        ps.status,
        ps.siswa_id
    FROM t_pelanggaran_siswa AS ps
    LEFT JOIN t_siswa AS s ON s.id = ps.siswa_id
    LEFT JOIN t_pelanggaran_kategori AS pk ON pk.id = ps.pelanggaran_kategori_id
    WHERE LOWER(TRIM(ps.status)) = 'selesai'
    ORDER BY ps.tanggal DESC, ps.id DESC
";
$result = $koneksi->query($sql);

function riwayat_h($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

include __DIR__ . '/header.php';
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap border-bottom pb-3 mb-3">
        <div>
            <h1 class="h3 mb-1">Riwayat Pelanggaran Siswa</h1>
            <p class="text-muted mb-0">Daftar pelanggaran siswa yang telah selesai ditindaklanjuti.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <span><i class="fas fa-history mr-2"></i>Data Riwayat Pelanggaran</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelRiwayat" class="table table-hover table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Jenis Pelanggaran</th>
                            <?php if ($show_guru_pelapor): ?><th>Guru Pelapor</th><?php endif; ?>
                            <th>Poin</th>
                            <th>Tindakan / Pembinaan</th>
                            <th>Status</th>
                            <th>Cetak Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-nowrap"><?= riwayat_h($row['tanggal']); ?></td>
                            <td><?= riwayat_h($row['nis']); ?></td>
                            <td><?= riwayat_h($row['nama_siswa']); ?></td>
                            <td class="text-nowrap"><?= riwayat_h($row['nama_kelas']); ?></td>
                            <td><?= riwayat_h($row['nama_kategori']); ?></td>
                            <td><?= riwayat_h($row['nama_pelanggaran']); ?></td>
                            <?php if ($show_guru_pelapor): ?><td><?= riwayat_h($row['guru_pelapor']); ?></td><?php endif; ?>
                            <td class="text-center"><span class="badge badge-danger"><?= (int)$row['poin']; ?></span></td>
                            <td><?= riwayat_h($row['tindakan']); ?></td>
                            <td class="text-center"><span class="badge badge-success"><?= riwayat_h($row['status']); ?></span></td>
                            <td class="text-center text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="cetak_sp.php?siswa_id=<?= (int)$row['siswa_id']; ?>" title="Cetak surat peringatan siswa ini">
                                    <i class="fas fa-print mr-1"></i> Cetak SP
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($result->num_rows === 0): ?>
                <div class="text-center text-muted py-3">Belum ada riwayat pelanggaran yang berstatus selesai.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(function () {
    $('#tabelRiwayat').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        responsive: true,
        ordering: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [{ orderable: false, targets: [0, <?= $show_guru_pelapor ? 11 : 10 ?>] }]
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
