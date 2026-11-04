<?php
require_once 'auth.php';
require_once 'koneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$pesan_sukses = '';
$pesan_error = '';
function pel_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['simpan_pelanggaran']) || isset($_POST['edit_pelanggaran'])) {
            $id = (int)($_POST['id'] ?? 0);
            $kode = strtoupper(trim($_POST['kode'] ?? ''));
            $nama = trim($_POST['nama'] ?? '');
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $poin = (int)($_POST['poin'] ?? 0);
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

            if ($kode === '' || $nama === '' || $kategori_id <= 0 || $poin <= 0 || $deskripsi === '') {
                throw new Exception('Kode, nama, kategori, poin, dan deskripsi wajib diisi.');
            }

            $cek = $koneksi->prepare("SELECT id FROM t_pelanggaran_kategori WHERE id = ? LIMIT 1");
            $cek->bind_param('i', $kategori_id);
            $cek->execute();
            $valid_kategori = $cek->get_result()->num_rows > 0;
            $cek->close();
            if (!$valid_kategori) throw new Exception('Kategori pelanggaran tidak ditemukan.');

            if (isset($_POST['edit_pelanggaran'])) {
                if ($id <= 0) throw new Exception('ID pelanggaran tidak valid.');
                $stmt = $koneksi->prepare("UPDATE t_pelanggaran SET kode=?, nama=?, poin=?, deskripsi=?, pelanggaran_kategori_id=?, status_aktif=?, updated_at=NOW() WHERE id=?");
                $stmt->bind_param('ssisiii', $kode, $nama, $poin, $deskripsi, $kategori_id, $status_aktif, $id);
                $stmt->execute(); $stmt->close();
                $pesan_sukses = 'Data pelanggaran berhasil diperbarui.';
            } else {
                $stmt = $koneksi->prepare("INSERT INTO t_pelanggaran (kode, nama, poin, deskripsi, pelanggaran_kategori_id, status_aktif, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param('ssisii', $kode, $nama, $poin, $deskripsi, $kategori_id, $status_aktif);
                $stmt->execute(); $stmt->close();
                $pesan_sukses = 'Jenis pelanggaran berhasil ditambahkan.';
            }
        } elseif (isset($_POST['hapus_pelanggaran'])) {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('ID pelanggaran tidak valid.');
            $stmt = $koneksi->prepare("DELETE FROM t_pelanggaran WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $pesan_sukses = $stmt->affected_rows ? 'Jenis pelanggaran berhasil dihapus.' : 'Data tidak ditemukan.';
            $stmt->close();
        }
    } catch (Throwable $e) {
        $pesan_error = 'Proses gagal: ' . $e->getMessage();
    }
}

$list_kategori = $koneksi->query("SELECT id, nama FROM t_pelanggaran_kategori WHERE status_aktif=1 ORDER BY nama");
$result_pelanggaran = $koneksi->query("
    SELECT p.*, k.nama AS nama_kategori
    FROM t_pelanggaran p
    LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id
    ORDER BY p.id DESC
");
$kategori_rows = $list_kategori->fetch_all(MYSQLI_ASSOC);
$pelanggaran_rows = $result_pelanggaran->fetch_all(MYSQLI_ASSOC);

include 'header.php';
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap border-bottom pb-3 mb-3">
        <h1 class="h3">Jenis Pelanggaran</h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah"><i class="fas fa-plus mr-1"></i> Tambah Pelanggaran</button>
    </div>
    <?php if ($pesan_sukses): ?><div class="alert alert-success"><?= pel_h($pesan_sukses) ?></div><?php endif; ?>
    <?php if ($pesan_error): ?><div class="alert alert-danger"><?= pel_h($pesan_error) ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body table-responsive">
        <table id="tabelPelanggaran" class="table table-bordered table-striped table-hover">
            <thead class="thead-dark"><tr><th>No</th><th>Kode</th><th>Nama Pelanggaran</th><th>Kategori</th><th>Poin</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($pelanggaran_rows as $i => $r): ?>
                <tr>
                    <td><?= $i+1 ?></td><td><?= pel_h($r['kode']) ?></td><td><?= pel_h($r['nama']) ?></td>
                    <td><?= pel_h($r['nama_kategori'] ?? '-') ?></td><td><?= (int)$r['poin'] ?></td>
                    <td><?= (int)$r['status_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Tidak Aktif</span>' ?></td>
                    <td class="text-nowrap">
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPel<?= (int)$r['id'] ?>">Edit</button>
                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusPel<?= (int)$r['id'] ?>">Hapus</button>
                    </td>
                </tr>
                <div class="modal fade" id="editPel<?= (int)$r['id'] ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><form method="POST" class="modal-content">
                    <div class="modal-header bg-warning"><h5 class="modal-title">Edit Pelanggaran</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <div class="form-group"><label>Kode</label><input name="kode" class="form-control" maxlength="30" value="<?= pel_h($r['kode']) ?>" required></div>
                        <div class="form-group"><label>Nama Pelanggaran</label><input name="nama" class="form-control" maxlength="150" value="<?= pel_h($r['nama']) ?>" required></div>
                        <div class="form-group"><label>Kategori</label><select name="kategori_id" class="form-control" required><?php foreach($kategori_rows as $k): ?><option value="<?= (int)$k['id'] ?>" <?= (int)$r['pelanggaran_kategori_id']===(int)$k['id']?'selected':'' ?>><?= pel_h($k['nama']) ?></option><?php endforeach; ?></select></div>
                        <div class="form-group"><label>Poin</label><input type="number" name="poin" min="1" class="form-control" value="<?= (int)$r['poin'] ?>" required></div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" required><?= pel_h($r['deskripsi']) ?></textarea></div>
                        <div class="form-check"><input type="checkbox" class="form-check-input" name="status_aktif" id="aktif<?= (int)$r['id'] ?>" <?= (int)$r['status_aktif']===1?'checked':'' ?>><label class="form-check-label" for="aktif<?= (int)$r['id'] ?>">Aktif</label></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button name="edit_pelanggaran" class="btn btn-warning">Simpan</button></div>
                </form></div></div>
                <div class="modal fade" id="hapusPel<?= (int)$r['id'] ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><form method="POST" class="modal-content">
                    <div class="modal-header bg-danger text-white"><h5 class="modal-title">Hapus Pelanggaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                    <div class="modal-body">Yakin menghapus <strong><?= pel_h($r['nama']) ?></strong>?<input type="hidden" name="id" value="<?= (int)$r['id'] ?>"></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button name="hapus_pelanggaran" class="btn btn-danger">Ya, Hapus</button></div>
                </form></div></div>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div></div>
</div>
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><form method="POST" class="modal-content">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title">Tambah Pelanggaran</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
        <div class="form-group"><label>Kode</label><input name="kode" class="form-control" maxlength="30" required></div>
        <div class="form-group"><label>Nama Pelanggaran</label><input name="nama" class="form-control" maxlength="150" required></div>
        <div class="form-group"><label>Kategori</label><select name="kategori_id" class="form-control" required><option value="">-- Pilih Kategori --</option><?php foreach($kategori_rows as $k): ?><option value="<?= (int)$k['id'] ?>"><?= pel_h($k['nama']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Poin</label><input type="number" name="poin" min="1" class="form-control" required></div>
        <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" required></textarea></div>
        <div class="form-check"><input type="checkbox" class="form-check-input" name="status_aktif" id="aktifTambah" checked><label class="form-check-label" for="aktifTambah">Aktif</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button name="simpan_pelanggaran" class="btn btn-primary">Simpan</button></div>
</form></div></div>
<script>
if (window.jQuery && window.jQuery.fn.DataTable && !window.jQuery.fn.dataTable.isDataTable('#tabelPelanggaran')) {
    window.jQuery('#tabelPelanggaran').DataTable({pageLength:10,language:{search:'Cari:',lengthMenu:'Tampilkan _MENU_ data per halaman',zeroRecords:'Data tidak ditemukan',info:'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',infoEmpty:'Tidak ada data',paginate:{next:'Selanjutnya',previous:'Sebelumnya'}}});
}
</script>
<?php include 'footer.php'; ?>
