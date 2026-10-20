
<?php
require_once 'auth.php';
require_once 'koneksi.php';

// Fungsi rekomendasi sanksi berdasarkan kode abang
function getStatusSanksi($poin)
{
    if ($poin >= 100) {
        return "Dikembalikan ke Orang Tua / DO";
    } elseif ($poin >= 50) {
        return "Surat Peringatan III (Skorsing)";
    } elseif ($poin >= 30) {
        return "Surat Peringatan II";
    } elseif ($poin >= 15) {
        return "Surat Peringatan I";
    } elseif ($poin > 0) {
        return "Teguran Lisan";
    }

    return "Bersih (Tidak Ada Poin)";
}

function e($nilai)
{
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}

// Hitung poin per siswa terlebih dahulu agar poin tidak terduplikasi
$sql = "
    SELECT
        s.id,
        s.nisn,
        s.nama AS nama_siswa,
        COALESCE(k.nama, 'Belum Ada Kelas') AS nama_kelas,
        COALESCE(rp.total_poin, 0) AS total_poin
    FROM t_siswa s

    LEFT JOIN (
        SELECT
            r.siswa_id,
            SUM(p.poin) AS total_poin
        FROM t_riwayat_pelanggaran r
        INNER JOIN t_pelanggaran p
            ON r.pelanggaran_id = p.id
        GROUP BY r.siswa_id
    ) rp ON s.id = rp.siswa_id

    LEFT JOIN (
        SELECT siswa_id, MAX(kelas_id) AS kelas_id
        FROM t_kelas_siswa
        GROUP BY siswa_id
    ) ks ON s.id = ks.siswa_id

    LEFT JOIN t_kelas k ON ks.kelas_id = k.id

    ORDER BY total_poin DESC, s.nama ASC
";

$result = $koneksi->query($sql);

include 'header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center
                flex-wrap mb-3">
        <div>
            <h2>Rekapitulasi Poin Siswa</h2>
            <p class="text-muted">
                Rekap akumulasi poin pelanggaran dan rekomendasi sanksi.
            </p>
        </div>

        <button class="btn btn-primary mb-2"
                onclick="window.print()">
            Cetak Rekap
        </button>
    </div>

    <?php if (!$result): ?>
        <div class="alert alert-danger">
            Gagal mengambil data rekap:
            <?= e($koneksi->error); ?>
        </div>
    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Total Poin</th>
                        <th>Rekomendasi Sanksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $poin = (int)$row['total_poin'];
                                $status = getStatusSanksi($poin);
                            ?>

                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nama_siswa']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>

                                <td class="text-center font-weight-bold">
                                    <?= $poin; ?> poin
                                </td>

                                <td>
                                    <?= e($status); ?>
                                </td>

                                <td class="text-center">
                                    <?php if ($poin >= 15): ?>
                                        <a
                                            class="btn btn-sm btn-warning"
                                            href="cetak_sp.php?siswa_id=<?= (int)$row['id']; ?>"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            Cetak Surat SP
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

<style>
@media print {
    button,
    .btn,
    nav,
    .sidebar,
    footer {
        display: none !important;
    }

    .container-fluid {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000 !important;
        padding: 6px !important;
    }
}
</style>

<?php include 'footer.php'; ?>
