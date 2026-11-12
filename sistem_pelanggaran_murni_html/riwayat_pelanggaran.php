
<?php
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistem_pelanggaran";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Query riwayat pelanggaran tanpa mengambil data guru pelapor
$query = "SELECT
            p.*,
            s.nis,
            s.nama_siswa,
            k.nama_kelas,
            dp.nama_pelanggaran,
            cat.nama_kategori
          FROM pelanggaran p
          JOIN siswa s ON p.id_siswa = s.id_siswa
          JOIN kelas k ON s.id_kelas = k.id_kelas
          JOIN detail_pelanggaran dp ON p.id_detail = dp.id_detail
          JOIN kategori cat ON dp.id_kategori = cat.id_kategori
          WHERE p.status = 'Selesai'
          ORDER BY p.tanggal DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pelanggaran - Sistem Pelanggaran</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid py-4">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Riwayat Pelanggaran Siswa</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table
                    id="tabelRiwayat"
                    class="table table-striped table-bordered align-middle"
                    style="width:100%;">

                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">NO</th>
                            <th>TANGGAL</th>
                            <th>NIS</th>
                            <th>NAMA SISWA</th>
                            <th>KELAS</th>
                            <th>KATEGORI</th>
                            <th>JENIS PELANGGARAN</th>
                            <th>POIN</th>
                            <th>TINDAKAN / PEMBINAAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    if ($result && mysqli_num_rows($result) > 0):

                        while ($row = mysqli_fetch_assoc($result)):
                    ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['nis'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-danger">
                                    <?= htmlspecialchars($row['poin'] ?? '0'); ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success">
                                    Selesai
                                </span>
                            </td>

                        </tr>

                    <?php
                        endwhile;

                    else:
                    ?>

                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                Belum ada data riwayat pelanggaran.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function() {

    $('#tabelRiwayat').DataTable({

        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },

        responsive: true,
        ordering: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100]

    });

});
</script>

</body>
</html>
