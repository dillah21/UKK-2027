<?php
require_once 'auth.php';
require_once 'koneksi.php';

// Filter server-side tetap tersedia, sedangkan pencarian, pengurutan,
// dan pagination interaktif disediakan DataTables untuk role Guru.
$search = trim($_GET['search'] ?? '');
$filter_siswa_id = (int)($_GET['siswa_id'] ?? 0);

$sql = "SELECT ps.*, s.nis, s.nama AS nama_siswa,
               k.nama AS nama_kelas, g.nama AS nama_guru,
               pk.nama AS nama_kategori
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k ON ps.kelas_id = k.id
        LEFT JOIN t_guru g ON ps.guru_id = g.id
        LEFT JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_kategori_id = pk.id";

$role_halaman = strtolower(trim($_SESSION['role'] ?? ''));
$kondisi = ["UPPER(TRIM(COALESCE(k.nama, ps.nama_kelas))) IN ('12 RPL 1', '12 RPL 2')"];
if ($role_halaman === 'guru') {
    $user_id_riwayat = (int)($_SESSION['user_id'] ?? 0);
    $qguru_riwayat = $koneksi->prepare("SELECT id FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1");
    $qguru_riwayat->bind_param('i', $user_id_riwayat);
    $qguru_riwayat->execute();
    $guru_riwayat = $qguru_riwayat->get_result()->fetch_assoc();
    $qguru_riwayat->close();
    $guru_id_riwayat = (int)($guru_riwayat['id'] ?? 0);
    $kondisi[] = "ps.guru_id = " . $guru_id_riwayat;
}
if ($filter_siswa_id > 0) $kondisi[] = "ps.siswa_id = " . $filter_siswa_id;
if ($search !== '') {
    $kata = $koneksi->real_escape_string($search);
    $kondisi[] = "(s.nama LIKE '%{$kata}%' OR s.nis LIKE '%{$kata}%' OR ps.nama_pelanggaran LIKE '%{$kata}%')";
}
if ($kondisi) $sql .= " WHERE " . implode(' AND ', $kondisi);
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";
$result = $koneksi->query($sql);

function riwayat_h($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$gunakan_datatables = ($role_halaman === 'guru');

include 'header.php';
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <h2 class="mb-1"><i class="fas fa-history text-primary mr-2"></i>Riwayat Pelanggaran Siswa</h2>
            <p class="text-muted mb-0">Daftar catatan pelanggaran siswa yang telah dicatat.</p>
        </div>
        <span class="badge badge-primary px-3 py-2 mt-2 mt-md-0">
            <?= $result ? (int)$result->num_rows : 0; ?> data
        </span>
    </div>

    <?php if ($result === false): ?>
        <div class="alert alert-danger" role="alert">
            Gagal mengambil data riwayat: <?= riwayat_h($koneksi->error); ?>
        </div>
    <?php else: ?>
        <?php if ($search !== '' || $filter_siswa_id > 0): ?>
            <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap">
                <span>Filter riwayat sedang diterapkan.</span>
                <a class="btn btn-sm btn-outline-primary mt-2 mt-md-0" href="riwayat_pelanggaran.php">
                    <i class="fas fa-undo mr-1"></i>Reset Filter
                </a>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelRiwayat" class="table table-striped table-bordered table-hover mb-0" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Kategori</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Tindakan / Pembinaan</th>
                                <th>Status</th>
                                <th>Guru Pelapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td data-order="<?= riwayat_h($row['tanggal']); ?>"><?= riwayat_h($row['tanggal']); ?></td>
                                    <td><?= riwayat_h($row['nis']); ?></td>
                                    <td><?= riwayat_h($row['nama_siswa']); ?></td>
                                    <td><?= riwayat_h($row['nama_kelas']); ?></td>
                                    <td><?= riwayat_h($row['nama_kategori']); ?></td>
                                    <td><?= riwayat_h($row['nama_pelanggaran']); ?></td>
                                    <td class="text-center"><span class="badge badge-danger"><?= (int)($row['poin'] ?? 0); ?></span></td>
                                    <td><?= riwayat_h($row['tindakan']); ?></td>
                                    <td>
                                        <?php $status = (string)($row['status'] ?? ''); ?>
                                        <span class="badge <?= $status === 'Selesai' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?= riwayat_h($status); ?>
                                        </span>
                                    </td>
                                    <td><?= riwayat_h($row['nama_guru']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($gunakan_datatables): ?>
<script>
(function () {
    function initTabelRiwayat() {
        if (!window.jQuery || !jQuery.fn.DataTable || jQuery.fn.dataTable.isDataTable('#tabelRiwayat')) {
            return;
        }

        jQuery('#tabelRiwayat').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
            order: [[1, 'desc']],
            autoWidth: false,
            scrollX: true,
            language: {
                search: 'Cari riwayat:',
                searchPlaceholder: 'Nama, NIS, pelanggaran, atau guru...',
                lengthMenu: 'Tampilkan _MENU_ data',
                zeroRecords: 'Data riwayat tidak ditemukan',
                emptyTable: 'Belum ada catatan riwayat pelanggaran',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data tersedia',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTabelRiwayat);
    } else {
        initTabelRiwayat();
    }
})();
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
