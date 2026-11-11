<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';


// =====================================================
// HELPER
// =====================================================

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}


// =====================================================
// 1. TAMBAH GURU
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_guru'])) {

    $nip_raw = trim($_POST['nip'] ?? '');
    $nip = preg_replace('/\D+/', '', $nip_raw);
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_input = (string)($_POST['password'] ?? '');

    if (
        $nama === '' ||
        $password_input === '' || strlen($password_input) < 6 ||
        strlen($nip_raw) > 15 ||
        ($nip_raw !== '' && strlen($nip) !== strlen($nip_raw))
    ) {

        $pesan_error = strlen($nip_raw) > 15
            ? 'NIP maksimal 15 digit!'
            : ($password_input === '' ? 'Password wajib diisi.' : (strlen($password_input) < 6 ? 'Password minimal 6 karakter.' : 'Nama wajib diisi dan NIP hanya boleh berisi angka.'));

    } else {

        try {

            $koneksi->begin_transaction();

            if ($email === '') {
                $suffix = bin2hex(random_bytes(4));
                $email = 'guru.' . $suffix . '@sekolah.sch.id';
            }

            $hash = password_hash($password_input, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(20));


            // BUAT AKUN PENGGUNA
            $user = $koneksi->prepare("
                INSERT INTO t_users
                (name, email, password, remember_token, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'guru', NOW(), NOW())
            ");

            $user->bind_param('ssss', $nama, $email, $hash, $token);
            $user->execute();
            $user_id_baru = $koneksi->insert_id;
            $user->close();


            // SIMPAN DATA GURU
            $status_aktif = 1;

            $guru = $koneksi->prepare("
                INSERT INTO t_guru
                (nip, nama, email, status_aktif, user_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $guru->bind_param('sssii', $nip, $nama, $email, $status_aktif, $user_id_baru);
            $guru->execute();
            $guru->close();


            $koneksi->commit();

            $pesan_sukses = 'Guru berhasil ditambahkan. Email: ' . $email;

        } catch (Throwable $e) {

            $koneksi->rollback();
            error_log('Error tambah guru: ' . $e->getMessage());
            $pesan_error = 'Gagal menambahkan guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 2. EDIT GURU
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_guru'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nip_raw = trim($_POST['nip'] ?? '');
    $nip = preg_replace('/\D+/', '', $nip_raw);
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (
        $id <= 0 ||
        $nama === '' ||
        strlen($nip_raw) > 15 ||
        ($nip_raw !== '' && strlen($nip) !== strlen($nip_raw))
    ) {

        $pesan_error = strlen($nip_raw) > 15
            ? 'NIP maksimal 15 digit!'
            : 'Data guru tidak valid. NIP hanya boleh berisi angka.';

    } else {

        try {

            $koneksi->begin_transaction();

            // CEK DATA GURU
            $cek_guru = $koneksi->prepare("SELECT id, user_id FROM t_guru WHERE id = ?");
            $cek_guru->bind_param('i', $id);
            $cek_guru->execute();
            $data_guru = $cek_guru->get_result()->fetch_assoc();
            $cek_guru->close();

            if (!$data_guru) {
                throw new Exception('Data guru tidak ditemukan.');
            }

            // UPDATE DATA GURU
            $stmt = $koneksi->prepare("
                UPDATE t_guru
                SET nip = ?,
                    nama = ?,
                    email = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->bind_param('sssi', $nip, $nama, $email, $id);
            $stmt->execute();
            $stmt->close();


            // Sinkronkan akun hanya jika user_id benar-benar menunjuk akun ber-role guru.
            // Data guru lama pada database dapat terhubung ke akun admin/siswa/wali kelas.
            // Akun selain guru tidak boleh ikut berubah saat profil guru diedit.
            if (!empty($data_guru['user_id'])) {
                $user = $koneksi->prepare("
                    UPDATE t_users
                    SET name = ?,
                        email = ?,
                        updated_at = NOW()
                    WHERE id = ?
                      AND LOWER(TRIM(role)) = 'guru'
                ");
                $user->bind_param('ssi', $nama, $email, $data_guru['user_id']);
                $user->execute();
                $user->close();
            }

            $koneksi->commit();
            $pesan_sukses = 'Data guru berhasil diperbarui.';

        } catch (Throwable $e) {

            $koneksi->rollback();
            error_log('Error edit guru: ' . $e->getMessage());
            $pesan_error = 'Gagal memperbarui guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 3. HAPUS GURU
// =====================================================

if (
    isset($_GET['action'], $_GET['id']) &&
    $_GET['action'] === 'hapus'
) {

    $id_hapus = (int)$_GET['id'];

    if ($id_hapus <= 0) {

        $pesan_error = 'ID guru tidak valid.';

    } else {

        try {

            $koneksi->begin_transaction();

            // CEK APAKAH GURU MASIH MENJADI WALI KELAS
            $cek = $koneksi->prepare("
                SELECT COUNT(*) AS total
                FROM t_wali_kelas
                WHERE guru_id = ?
            ");
            $cek->bind_param('i', $id_hapus);
            $cek->execute();
            $hasil = $cek->get_result()->fetch_assoc();
            $cek->close();

            if ((int)($hasil['total'] ?? 0) > 0) {
                $koneksi->rollback();
                $pesan_error = 'Guru tidak dapat dihapus karena masih terdaftar sebagai wali kelas.';
            } else {

                // AMBIL USER_ID
                $get_user = $koneksi->prepare("SELECT user_id FROM t_guru WHERE id = ?");
                $get_user->bind_param('i', $id_hapus);
                $get_user->execute();
                $res_user = $get_user->get_result()->fetch_assoc();
                $get_user->close();

                // HAPUS DATA GURU
                $hapus = $koneksi->prepare("DELETE FROM t_guru WHERE id = ?");
                $hapus->bind_param('i', $id_hapus);
                $hapus->execute();
                $hapus->close();

                // Hapus akun hanya jika akun terkait memang ber-role guru.
                // Jangan sampai akun admin atau role lain terhapus akibat relasi data lama.
                if (!empty($res_user['user_id'])) {
                    $hapus_user = $koneksi->prepare("
                        DELETE FROM t_users
                        WHERE id = ?
                          AND LOWER(TRIM(role)) = 'guru'
                    ");
                    $hapus_user->bind_param('i', $res_user['user_id']);
                    $hapus_user->execute();
                    $hapus_user->close();
                }

                $koneksi->commit();
                $pesan_sukses = 'Data guru berhasil dihapus!';
            }

        } catch (Throwable $e) {

            $koneksi->rollback();
            error_log('Error hapus guru: ' . $e->getMessage());
            $pesan_error = 'Gagal menghapus data guru: ' . $e->getMessage();
        }
    }
}


// =====================================================
// 4. AMBIL DATA GURU
// =====================================================

$result_guru = $koneksi->query("
    SELECT g.*,
           CASE
               WHEN u.id IS NOT NULL AND u.password IS NOT NULL AND u.password <> '' THEN 1
               ELSE 0
           END AS password_tersedia
    FROM t_guru g
    LEFT JOIN t_users u ON u.id = g.user_id
    ORDER BY g.id ASC
");


// =====================================================
// 5. HEADER DASHBOARD
// =====================================================

include 'header.php';

?>


<!-- =====================================================
     JUDUL HALAMAN
===================================================== -->

<div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Data Guru</h1>
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalGuru">
        <i class="fas fa-plus mr-1"></i> Tambah Guru Baru
    </button>
</div>


<!-- =====================================================
     NOTIFIKASI
===================================================== -->

<?php if ($pesan_sukses !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= e($pesan_sukses) ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= e($pesan_error) ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>


<!-- =====================================================
     TABEL DATA GURU
===================================================== -->

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelGuru" class="table table-striped table-bordered mb-0" style="width:100%;">
                <thead class="thead-dark text-center">
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:20%;">NIP / NIK</th>
                        <th>Nama Lengkap Guru</th>
                        <th>Email</th>
                        <th style="width:15%;">Password</th>
                        <th style="width:15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_guru && $result_guru->num_rows > 0): ?>
                    <?php
                    $no = 1;
                    while ($r = $result_guru->fetch_assoc()):
                        $id = (int)$r['id'];
                        $nip_asli = $r['nip'] ?? $r['nik'] ?? '';
                        $nama_asli = $r['nama'] ?? $r['nama_guru'] ?? '';
                        $email_asli = $r['email'] ?? '';
                        $password_tersedia = (int)($r['password_tersedia'] ?? 0) === 1;
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= $nip_asli !== '' ? e($nip_asli) : '-' ?></td>
                            <td><?= e($nama_asli) ?></td>
                            <td><?= e($email_asli) ?></td>
                            <td class="text-center">
                                <?php if ($password_tersedia): ?>
                                    <span class="text-success" title="Password tersimpan di database">
                                        <i class="fas fa-lock mr-1"></i>••••••••
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada akun</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <!-- TOMBOL EDIT -->
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm btn-edit-guru"
                                    data-id="<?= $id ?>"
                                    data-nip="<?= e($nip_asli) ?>"
                                    data-nama="<?= e($nama_asli) ?>"
                                    data-email="<?= e($email_asli) ?>"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- TOMBOL HAPUS -->
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-hapus-guru"
                                    data-id="<?= $id ?>"
                                    data-nama="<?= e($nama_asli) ?>"
                                    title="Hapus"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- =====================================================
     MODAL EDIT GURU
===================================================== -->

<div class="modal fade" id="modalEditGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="guru.php" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Data Guru
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIP / NIK</label>
                    <input type="text" name="nip" id="edit_nip" maxlength="15" inputmode="numeric" pattern="[0-9]{0,15}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Nama Guru <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" name="edit_guru" class="btn btn-warning btn-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


<!-- =====================================================
     MODAL HAPUS GURU
===================================================== -->

<div class="modal fade" id="modalHapusGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Hapus Data Guru</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus guru <strong id="hapus_nama_guru"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <a href="#" id="tombolKonfirmasiHapusGuru" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash mr-1"></i> Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="mt-5 py-3 text-center text-muted border-top small">
    &copy; 2026 - Sistem Informasi Pelanggaran Siswa &bull; Developed by <strong>Azril Akbar Fawji Abdillah</strong>
</footer>

</main>
</div>
</div>


<!-- =====================================================
     MODAL TAMBAH GURU
===================================================== -->

<div class="modal fade" id="modalGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="guru.php" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-chalkboard-teacher mr-2"></i> Tambah Guru Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIP / NIK</label>
                    <input type="text" name="nip" maxlength="15" inputmode="numeric" pattern="[0-9]{0,15}" class="form-control" placeholder="Nomor NIP / NIK">
                </div>
                <div class="form-group">
                    <label>Nama Guru <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap beserta gelar">
                </div>
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="email@sekolah.sch.id" required>
                </div>
                <div class="form-group">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password akun guru" minlength="6" required autocomplete="new-password">
                    <small class="form-text text-muted">Password minimal 6 karakter.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_guru" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelGuru').DataTable({
        order: [],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data per halaman',
            zeroRecords: 'Tidak ada data yang ditemukan',
            info: 'Menampilkan halaman _PAGE_ dari _PAGES_',
            infoEmpty: 'Tidak ada data tersedia',
            infoFiltered: '(disaring dari _MAX_ total data)',
            paginate: {
                first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya'
            }
        }
    });

    $(document).on('click', '.btn-edit-guru', function () {
        var id = $(this).attr('data-id');
        var nip = $(this).attr('data-nip');
        var nama = $(this).attr('data-nama');
        var email = $(this).attr('data-email');

        $('#edit_id').val(id);
        $('#edit_nip').val(nip);
        $('#edit_nama').val(nama);
        $('#edit_email').val(email);

        $('#modalEditGuru').modal('show');
    });

    $(document).on('click', '.btn-hapus-guru', function () {
        var id = $(this).attr('data-id');
        var nama = $(this).attr('data-nama');

        $('#hapus_nama_guru').text(nama);
        $('#tombolKonfirmasiHapusGuru').attr('href', 'guru.php?action=hapus&id=' + encodeURIComponent(id));
        $('#modalHapusGuru').modal('show');
    });
});
</script>

</body>
</html>