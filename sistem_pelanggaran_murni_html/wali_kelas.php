<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function namaKelas($tingkat, $jurusan, $nama) {
    $tingkat = trim((string)$tingkat);
    $jurusan = strtoupper(trim((string)$jurusan));
    $nama = trim((string)$nama);

    $valid = ['RPL', 'TKJ', 'BDPM', 'TSM', 'TKR'];

    if ($tingkat !== '' && in_array($jurusan, $valid, true)) {
        $rombel = '';

        if (preg_match('/(\d+)\s*$/', $nama, $m)) {
            $rombel = $m[1];
        }

        return trim($tingkat . ' ' . $jurusan . ' ' . $rombel);
    }

    return $nama !== '' ? $nama : '-';
}

function labelTahun($row) {
    foreach (['nama_tahun_ajaran', 'tahun_ajaran', 'nama', 'tahun', 'semester'] as $field) {
        if (!empty($row[$field])) {
            return (string)$row[$field];
        }
    }

    return 'Tahun ajaran #' . ($row['id'] ?? '?');
}

$pesan_sukses = '';
$pesan_error = '';

$tahun_list = [];
$kelas_list = [];
$guru_list = [];
$wali_list = [];

// Sinkronkan status tahun ajaran: tahun awal 2021-2026 aktif,
// sedangkan tahun setelah 2026 tidak aktif.
try {
    $koneksi->query("UPDATE t_tahun_ajaran
        SET status_aktif = CASE
            WHEN CAST(SUBSTRING_INDEX(nama, '/', 1) AS UNSIGNED) <= 2026 THEN 1
            ELSE 0
        END
        WHERE nama REGEXP '^[0-9]{4}/[0-9]{4}$'");
    // Pastikan pilihan 2021/2022 sampai 2026/2027 tersedia tanpa
    // mengubah ID tahun ajaran lama yang sudah dipakai relasi wali kelas.
    for ($tahun = 2021; $tahun <= 2026; $tahun++) {
        $nama_tahun = $tahun . '/' . ($tahun + 1);
        $mulai_tahun = $tahun . '-07-01';
        $selesai_tahun = ($tahun + 1) . '-06-30';
        $aktif_tahun = 1;
        $cek_tahun = $koneksi->prepare("SELECT id FROM t_tahun_ajaran WHERE nama = ? ORDER BY id ASC LIMIT 1");
        $cek_tahun->bind_param('s', $nama_tahun);
        $cek_tahun->execute();
        $tahun_ada = $cek_tahun->get_result()->fetch_assoc();
        $cek_tahun->close();

        if ($tahun_ada) {
            $id_tahun_ada = (int)$tahun_ada['id'];
            $update_tahun = $koneksi->prepare("UPDATE t_tahun_ajaran SET tanggal_mulai = ?, tanggal_selesai = ?, status_aktif = ? WHERE id = ?");
            $update_tahun->bind_param('ssii', $mulai_tahun, $selesai_tahun, $aktif_tahun, $id_tahun_ada);
            $update_tahun->execute();
            $update_tahun->close();
        } else {
            $insert_tahun = $koneksi->prepare("INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif) VALUES (?, ?, ?, ?)");
            $insert_tahun->bind_param('sssi', $nama_tahun, $mulai_tahun, $selesai_tahun, $aktif_tahun);
            $insert_tahun->execute();
            $insert_tahun->close();
        }
    }
} catch (Throwable $e) {
    $pesan_error = 'Gagal menyinkronkan status tahun ajaran: ' . $e->getMessage();
}

// Data tahun ajaran aktif untuk formulir
try {
    $res = $koneksi->query("SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai ASC, id ASC");

    while ($row = $res->fetch_assoc()) {
        $tahun_list[] = $row;
    }
} catch (Throwable $e) {
    $pesan_error = 'Gagal membaca tahun ajaran: ' . $e->getMessage();
}

// Tahun ajaran default
$tahun_default = 0;

foreach ($tahun_list as $tahun) {
    if (
        isset($tahun['status_aktif']) &&
        (int)$tahun['status_aktif'] === 1
    ) {
        $tahun_default = (int)$tahun['id'];
        break;
    }
}

if ($tahun_default === 0 && !empty($tahun_list)) {
    $tahun_default = (int)$tahun_list[0]['id'];
}

// Data kelas, hanya lima jurusan
try {
    $res = $koneksi->query("
        SELECT id, nama, tingkat, jurusan
        FROM t_kelas
        WHERE status_aktif = 1
        ORDER BY tingkat, jurusan, nama
    ");

    while ($row = $res->fetch_assoc()) {
        $kelas_list[] = $row;
    }
} catch (Throwable $e) {
    $pesan_error = 'Gagal membaca kelas: ' . $e->getMessage();
}

// Data guru
try {
    $res = $koneksi->query("
        SELECT id, nama, nip
        FROM t_guru
        WHERE status_aktif = 1
        ORDER BY nama
    ");

    while ($row = $res->fetch_assoc()) {
        $guru_list[] = $row;
    }
} catch (Throwable $e) {
    $pesan_error = 'Gagal membaca guru: ' . $e->getMessage();
}

// Proses tambah, edit, hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['simpan_wali']) || isset($_POST['edit_wali'])) {
            $is_edit = isset($_POST['edit_wali']);

            $id = (int)($_POST['id'] ?? 0);
            $tahun_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
            $kelas_id = (int)($_POST['kelas_id'] ?? 0);
            $guru_id = (int)($_POST['guru_id'] ?? 0);
            $mulai = trim($_POST['tanggal_mulai'] ?? '');
            $selesai = trim($_POST['tanggal_selesai'] ?? '');
            $status = isset($_POST['status_aktif']) ? 1 : 0;

            if (!$tahun_id || !$kelas_id || !$guru_id || !$mulai || !$selesai) {
                throw new Exception('Semua kolom wajib diisi.');
            }

            if ($selesai < $mulai) {
                throw new Exception('Tanggal selesai tidak boleh sebelum tanggal mulai.');
            }

            $valid_tahun = false;
            foreach ($tahun_list as $th) {
                if ((int)$th['id'] === $tahun_id) {
                    $valid_tahun = true;
                    break;
                }
            }

            if (!$valid_tahun) {
                throw new Exception('Tahun ajaran tidak ditemukan.');
            }

            $valid_kelas = false;
            foreach ($kelas_list as $k) {
                if ((int)$k['id'] === $kelas_id) {
                    $valid_kelas = true;
                    break;
                }
            }

            if (!$valid_kelas) {
                throw new Exception('Kelas tidak aktif atau jurusannya tidak diizinkan.');
            }

            $valid_guru = false;
            foreach ($guru_list as $g) {
                if ((int)$g['id'] === $guru_id) {
                    $valid_guru = true;
                    break;
                }
            }

            if (!$valid_guru) {
                throw new Exception('Guru tidak aktif atau tidak ditemukan.');
            }


            // Cegah kelas memiliki lebih dari satu wali kelas aktif pada tahun ajaran yang sama
            if ($status === 1) {
                $cek = $koneksi->prepare("
                    SELECT id
                    FROM t_wali_kelas
                    WHERE kelas_id = ?
                      AND tahun_ajaran_id = ?
                      AND status_aktif = 1
                      AND id <> ?
                    LIMIT 1
                ");

                $cek->bind_param('iii', $kelas_id, $tahun_id, $id);
                $cek->execute();
                $ada = $cek->get_result()->num_rows > 0;
                $cek->close();

                if ($ada) {
                    throw new Exception('Kelas sudah mempunyai wali kelas aktif pada tahun ajaran tersebut.');
                }
            }

            if ($is_edit) {
                if ($id <= 0) {
                    throw new Exception('ID penugasan tidak valid.');
                }

                $stmt = $koneksi->prepare("
                    UPDATE t_wali_kelas
                    SET tahun_ajaran_id = ?,
                        kelas_id = ?,
                        guru_id = ?,
                        tanggal_mulai = ?,
                        tanggal_selesai = ?,
                        status_aktif = ?
                    WHERE id = ?
                ");

                $stmt->bind_param(
                    'iiissii',
                    $tahun_id,
                    $kelas_id,
                    $guru_id,
                    $mulai,
                    $selesai,
                    $status,
                    $id
                );

                $stmt->execute();
                $stmt->close();

                $pesan_sukses = 'Data wali kelas berhasil diperbarui.';
            } else {
                $stmt = $koneksi->prepare("
                    INSERT INTO t_wali_kelas
                    (
                        tahun_ajaran_id,
                        kelas_id,
                        guru_id,
                        tanggal_mulai,
                        tanggal_selesai,
                        status_aktif
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    'iiissi',
                    $tahun_id,
                    $kelas_id,
                    $guru_id,
                    $mulai,
                    $selesai,
                    $status
                );

                $stmt->execute();
                $stmt->close();

                $pesan_sukses = 'Wali kelas berhasil ditambahkan.';
            }
        } elseif (isset($_POST['hapus_wali'])) {
            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID penugasan tidak valid.');
            }

            $stmt = $koneksi->prepare("DELETE FROM t_wali_kelas WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $pesan_sukses = $stmt->affected_rows > 0
                ? 'Penugasan wali kelas berhasil dihapus.'
                : 'Data penugasan tidak ditemukan.';

            $stmt->close();
        }
    } catch (Throwable $e) {
        $pesan_error = $e->getMessage();
    }
}

// Ambil data wali kelas
try {
    $res = $koneksi->query("
        SELECT
            wk.id,
            wk.tahun_ajaran_id,
            wk.kelas_id,
            wk.guru_id,
            wk.tanggal_mulai,
            wk.tanggal_selesai,
            wk.status_aktif,
            ta.nama AS nama_tahun_ajaran,
            ta.tanggal_mulai AS periode_mulai,
            ta.tanggal_selesai AS periode_selesai,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            g.nama AS nama_guru,
            g.nip AS nip_guru
        FROM t_wali_kelas wk
        LEFT JOIN t_tahun_ajaran ta ON ta.id = wk.tahun_ajaran_id
        LEFT JOIN t_kelas k ON k.id = wk.kelas_id
        LEFT JOIN t_guru g ON g.id = wk.guru_id
        WHERE wk.tahun_ajaran_id = 1
          AND wk.guru_id IN (2, 5)
          AND wk.kelas_id IN (6, 51)
        ORDER BY CASE wk.guru_id WHEN 5 THEN 1 WHEN 2 THEN 2 ELSE 3 END
    ");

    while ($row = $res->fetch_assoc()) {
        $wali_list[] = $row;
    }
} catch (Throwable $e) {
    $pesan_error = 'Gagal mengambil data wali kelas: ' . $e->getMessage();
}

include 'header.php';
?>

<!-- Tambahkan CSS DataTables agar tampilannya rapi -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Wali Kelas</h2>

        <?php if (count($wali_list) < 2): ?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            + Tambah Wali Kelas
        </button>
        <?php else: ?>
        <span class="badge badge-success">Maksimal 2 Wali Kelas</span>
        <?php endif; ?>
    </div>

    <?php if ($pesan_error !== ''): ?>
        <div class="alert alert-danger">
            <?= h($pesan_error) ?>
        </div>
    <?php endif; ?>

    <?php if ($pesan_sukses !== ''): ?>
        <div class="alert alert-success">
            <?= h($pesan_sukses) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Daftar Wali Kelas</strong>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelWali">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Kelas Binaan</th>
                            <th>Tahun Ajaran</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($wali_list as $i => $r): ?>
                        <?php
                        $id = (int)$r['id'];
                        $nama_kelas = namaKelas(
                            $r['tingkat'],
                            $r['jurusan'],
                            $r['nama_kelas']
                        );

                        $label_tahun = !empty($r['nama_tahun_ajaran'])
                            ? $r['nama_tahun_ajaran']
                            : 'Tahun ajaran tidak ditemukan';
                        $periode_mulai = $r['periode_mulai'] ?? $r['tanggal_mulai'];
                        $periode_selesai = $r['periode_selesai'] ?? $r['tanggal_selesai'];
                        ?>

                        <tr class="baris-wali">
                            <td><?= $i + 1 ?></td>
                            <td><?= h($r['nama_guru']) ?></td>
                            <td><?= h($r['nip_guru']) ?></td>
                            <td><?= h($nama_kelas) ?></td>
                            <td><?= h($label_tahun) ?></td>
                            <td>
                                <?= h($periode_mulai) ?>
                                s.d.
                                <?= h($periode_selesai) ?>
                            </td>
                            <td>
                                <?php if ((int)$r['status_aktif'] === 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <button type="button"
                                        class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#modalEdit<?= $id ?>">
                                    Edit
                                </button>

                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#modalHapus<?= $id ?>">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog"
     aria-labelledby="judulTambah" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="wali_kelas.php" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="judulTambah">Tambah Wali Kelas</h5>
                <button type="button" class="close text-white"
                        data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-control" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach ($tahun_list as $th): ?>
                            <option value="<?= (int)$th['id'] ?>"
                                <?= (int)$th['id'] === $tahun_default ? 'selected' : '' ?>>
                                <?= h(labelTahun($th)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Guru</label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($guru_list as $g): ?>
                            <option value="<?= (int)$g['id'] ?>">
                                <?= h($g['nama']) ?> - <?= h($g['nip']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelas Binaan</label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= (int)$k['id'] ?>">
                                <?= h(namaKelas($k['tingkat'], $k['jurusan'], $k['nama'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input"
                           name="status_aktif" id="statusTambah" checked>
                    <label class="form-check-label" for="statusTambah">
                        Penugasan aktif
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="submit" name="simpan_wali" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DAN HAPUS -->
<?php foreach ($wali_list as $r): ?>
    <?php
    $id = (int)$r['id'];
    $nama_kelas = namaKelas($r['tingkat'], $r['jurusan'], $r['nama_kelas']);
    ?>

    <div class="modal fade" id="modalEdit<?= $id ?>" tabindex="-1"
         role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="wali_kelas.php" class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Wali Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Tutup">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-control" required>
                            <?php foreach ($tahun_list as $th): ?>
                                <option value="<?= (int)$th['id'] ?>"
                                    <?= (int)$th['id'] === (int)$r['tahun_ajaran_id'] ? 'selected' : '' ?>>
                                    <?= h(labelTahun($th)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control" required>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= (int)$g['id'] ?>"
                                    <?= (int)$g['id'] === (int)$r['guru_id'] ? 'selected' : '' ?>>
                                    <?= h($g['nama']) ?> - <?= h($g['nip']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Kelas Binaan</label>
                        <select name="kelas_id" class="form-control" required>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= (int)$k['id'] ?>"
                                    <?= (int)$k['id'] === (int)$r['kelas_id'] ? 'selected' : '' ?>>
                                    <?= h(namaKelas($k['tingkat'], $k['jurusan'], $k['nama'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control"
                               value="<?= h($r['tanggal_mulai']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control"
                               value="<?= h($r['tanggal_selesai']) ?>" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input"
                               name="status_aktif" id="statusEdit<?= $id ?>"
                               <?= (int)$r['status_aktif'] === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="statusEdit<?= $id ?>">
                            Penugasan aktif
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" name="edit_wali" class="btn btn-warning">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalHapus<?= $id ?>" tabindex="-1"
         role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="wali_kelas.php" class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"
                            aria-label="Tutup">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Yakin ingin menghapus penugasan
                    <strong><?= h($r['nama_guru']) ?></strong>
                    untuk kelas <strong><?= h($nama_kelas) ?></strong>?

                    <input type="hidden" name="id" value="<?= $id ?>">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" name="hapus_wali" class="btn btn-danger">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<!-- Script JavaScript DataTables -->
<script>
$(document).ready(function() {
    $('#tabelWali').DataTable({
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

<?php
include 'footer.php';
?>