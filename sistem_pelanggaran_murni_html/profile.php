<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

// Panggil header dinamis
include 'header.php';

$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$email_user = $_SESSION['email'] ?? '-';
$role_user = ucfirst(strtolower($_SESSION['role'] ?? 'user'));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-cog mr-2"></i> Pengaturan Akun & Profil</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white font-weight-bold">
                <i class="fas fa-id-card mr-2"></i> Informasi Akun Anda
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Email Akun (Username)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($email_user); ?>" readonly disabled>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nama_user); ?>" readonly disabled>
                    </div>
                    <div class="form-group">
                        <label>Hak Akses / Role</label>
                        <div>
                            <span class="badge badge-info p-2" style="font-size: 14px;"><?= htmlspecialchars($role_user); ?></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>