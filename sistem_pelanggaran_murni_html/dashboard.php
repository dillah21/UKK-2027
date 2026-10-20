
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

/*
|--------------------------------------------------------------------------
| Helper database
|--------------------------------------------------------------------------
*/

function dashboard_count($koneksi, $table) {
    $allowed = [
        't_siswa',
        't_guru',
        't_pelanggaran_kategori',
        't_pelanggaran_siswa',
        't_prestasi'
    ];

    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    try {
        $result = $koneksi->query(
            "SELECT COUNT(*) AS total FROM `$table`"
        );

        return (int) $result->fetch_assoc()['total'];
    } catch (Throwable $e) {
        error_log('Dashboard count: ' . $e->getMessage());
        return 0;
    }
}

function dashboard_rows($koneksi, $sql) {
    try {
        $result = $koneksi->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        error_log('Dashboard query: ' . $e->getMessage());
        return [];
    }
}

function h($value) {
    return htmlspecialchars(
        (string) ($value ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

$total_siswa = dashboard_count($koneksi, 't_siswa');
$total_guru = dashboard_count($koneksi, 't_guru');
$total_kategori = dashboard_count(
    $koneksi,
    't_pelanggaran_kategori'
);
$total_kasus = dashboard_count(
    $koneksi,
    't_pelanggaran_siswa'
);
$total_prestasi = dashboard_count($koneksi, 't_prestasi');

/*
|--------------------------------------------------------------------------
| Data terbaru
|--------------------------------------------------------------------------
| Query menggunakan nama kolom yang dipakai dashboard asli.
| Jika skema berbeda, periksa log PHP dan sesuaikan nama kolom.
|--------------------------------------------------------------------------
*/

$pelanggaran = dashboard_rows($koneksi, "
    SELECT
        p.id_pelanggaran,
        p.tanggal,
        p.poin,
        s.nama_siswa,
        k.nama_kelas,
        kat.nama_kategori
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_siswa s
        ON p.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k
        ON s.id_kelas = k.id_kelas
    LEFT JOIN t_pelanggaran_kategori kat
        ON p.id_kategori = kat.id_kategori
    ORDER BY p.tanggal DESC, p.id_pelanggaran DESC
    LIMIT 5
");

$prestasi = dashboard_rows($koneksi, "
    SELECT
        pr.id_prestasi,
        pr.tanggal,
        pr.nama_prestasi,
        pr.poin,
        s.nama_siswa,
        k.nama_kelas
    FROM t_prestasi pr
    LEFT JOIN t_siswa s
        ON pr.id_siswa = s.id_siswa
    LEFT JOIN t_kelas k
        ON s.id_kelas = k.id_kelas
    ORDER BY pr.tanggal DESC
    LIMIT 5
");

include 'header.php';
?>

<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between
                align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold">Dashboard</h2>
            <p class="text-muted mb-0">
                Sistem Informasi Pelanggaran Siswa
            </p>
        </div>

        <span class="badge badge-primary p-2">
            <?= h(ucfirst($_SESSION['role'] ?? 'Pengguna')) ?>
        </span>
    </div>

    <!-- STATISTIK -->
    <div class="row">

        <?php
        $cards = [
            [
                'label' => 'Total Siswa',
                'value' => $total_siswa,
                'color' => 'primary',
                'icon' => 'fa-user-graduate'
            ],
            [
                'label' => 'Total Guru',
                'value' => $total_guru,
                'color' => 'success',
                'icon' => 'fa-chalkboard-teacher'
            ],
            [
                'label' => 'Kategori Pelanggaran',
                'value' => $total_kategori,
                'color' => 'info',
                'icon' => 'fa-tags'
            ],
            [
                'label' => 'Total Kasus',
                'value' => $total_kasus,
                'color' => 'danger',
                'icon' => 'fa-exclamation-triangle'
            ],
            [
                'label' => 'Total Prestasi',
                'value' => $total_prestasi,
                'color' => 'warning',
                'icon' => 'fa-trophy'
            ]
        ];
        ?>

        <?php foreach ($cards as $card): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100"
                     style="border-left:4px solid
                     var(--<?= h($card['color']) ?>)!important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between
                                    align-items-center">
                            <div>
                                <div class="small text-muted
                                            text-uppercase mb-2">
                                    <?= h($card['label']) ?>
                                </div>
                                <h3 class="font-weight-bold mb-0">
                                    <?= (int) $card['value'] ?>
                                </h3>
                            </div>

                            <i class="fas <?= h($card['icon']) ?>
                               fa-2x text-<?= h($card['color']) ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- SAMBUTAN -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="font-weight-bold text-primary">
                Halo, <?= h(
                    $_SESSION['nama']
                    ?? $_SESSION['username']
                    ?? 'Pengguna'
                ) ?>!
            </h5>

            <p class="text-muted">
                Selamat datang di Sistem Informasi Pelanggaran
                Siswa. Gunakan menu untuk mengelola data siswa,
                guru, prestasi, pelanggaran, dan laporan.
            </p>

            <div class="d-flex flex-wrap">
                <a href="siswa.php"
                   class="btn btn-outline-primary btn-sm mr-2 mb-2">
                    Data Siswa
                </a>

                <a href="guru.php"
                   class="btn btn-outline-success btn-sm mr-2 mb-2">
                    Data Guru
                </a>

                <a href="pelanggaran.php"
                   class="btn btn-outline-danger btn-sm mr-2 mb-2">
                    Data Pelanggaran
                </a>

                <a href="prestasi.php"
                   class="btn btn-outline-warning btn-sm mr-2 mb-2">
                    Data Prestasi
                </a>

                <a href="laporan.php"
                   class="btn btn-outline-info btn-sm mb-2">
                    Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- TABEL PELANGGARAN -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="font-weight-bold text-danger mb-0">
                <i class="fas fa-exclamation-circle mr-2"></i>
                5 Pelanggaran Terbaru
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Poin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$pelanggaran): ?>
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted">
                                Belum ada data pelanggaran,
                                atau struktur tabel perlu disesuaikan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pelanggaran as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= h($row['tanggal']) ?></td>
                                <td><?= h($row['nama_siswa']) ?></td>
                                <td><?= h($row['nama_kelas']) ?></td>
                                <td><?= h($row['nama_kategori']) ?></td>
                                <td>
                                    <span class="badge badge-danger">
                                        <?= (int) ($row['poin'] ?? 0) ?>
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

    <!-- TABEL PRESTASI -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="font-weight-bold text-success mb-0">
                <i class="fas fa-trophy mr-2"></i>
                5 Prestasi Terbaru
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Prestasi</th>
                            <th>Poin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$prestasi): ?>
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted">
                                Belum ada data prestasi,
                                atau struktur tabel perlu disesuaikan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prestasi as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= h($row['tanggal']) ?></td>
                                <td><?= h($row['nama_siswa']) ?></td>
                                <td><?= h($row['nama_kelas']) ?></td>
                                <td><?= h($row['nama_prestasi']) ?></td>
                                <td>
                                    <span class="badge badge-success">
                                        +<?= (int) ($row['poin'] ?? 0) ?>
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

<?php include 'footer.php'; ?>
