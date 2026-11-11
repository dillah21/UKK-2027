<?php
require_once 'auth.php';
require_once 'koneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role_login = strtolower(trim($_SESSION['role'] ?? ''));
$is_guru = ($role_login === 'guru');
$filter_kelas_guru = " AND UPPER(TRIM(k.nama)) IN ('12 RPL 1', '12 RPL 2')";
$error = '';
$success = '';
$guru_id_login = 0;
$tahun = $koneksi->query("SELECT id, nama FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC, id DESC LIMIT 1")->fetch_assoc();
$tahun_ajaran_id = (int)($tahun['id'] ?? 0);

if ($is_guru) {
    $uid = (int)($_SESSION['user_id'] ?? 0);
    $q = $koneksi->prepare("SELECT id FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1");
    $q->bind_param('i', $uid); $q->execute();
    $guru_login = $q->get_result()->fetch_assoc(); $q->close();
    $guru_id_login = (int)($guru_login['id'] ?? 0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $siswa_id = (int)($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);
        $guru_id = $is_guru ? $guru_id_login : (int)($_POST['guru_id'] ?? 0);
        $tanggal = trim($_POST['tanggal'] ?? '');
        $keterangan = $is_guru ? '' : trim($_POST['keterangan'] ?? '');

        if ($tahun_ajaran_id <= 0) throw new Exception('Belum ada tahun ajaran aktif.');
        if ($is_guru && $guru_id <= 0) throw new Exception('Akun Guru belum terhubung dengan data Guru aktif. Hubungi administrator.');
        if ($siswa_id <= 0 || $pelanggaran_id <= 0 || $guru_id <= 0 ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
            (!$is_guru && $keterangan === '')) {
            throw new Exception('Mohon isi semua data dengan benar.');
        }

        $q = $koneksi->prepare("SELECT nama FROM t_siswa WHERE id = ? AND status_aktif = 1 LIMIT 1");
        $q->bind_param('i', $siswa_id); $q->execute();
        $siswa = $q->get_result()->fetch_assoc(); $q->close();
        if (!$siswa) throw new Exception('Siswa tidak ditemukan atau tidak aktif.');

        // Kelas Guru diambil dari penempatan siswa pada tahun ajaran aktif, bukan input browser.
        if ($is_guru) {
            $sql_kelas = "SELECT k.id, k.nama FROM t_kelas_siswa ks
                INNER JOIN t_kelas k ON k.id = ks.kelas_id
                WHERE ks.siswa_id = ? AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
                  AND k.status_aktif = 1" . $filter_kelas_guru . "
                ORDER BY ks.id DESC LIMIT 1";
            $q = $koneksi->prepare($sql_kelas);
            $q->bind_param('ii', $siswa_id, $tahun_ajaran_id);
        } else {
            $kelas_id_post = (int)($_POST['kelas_id'] ?? 0);
            $q = $koneksi->prepare("SELECT id, nama FROM t_kelas WHERE id = ? AND status_aktif = 1 AND UPPER(TRIM(nama)) IN ('12 RPL 1', '12 RPL 2') LIMIT 1");
            $q->bind_param('i', $kelas_id_post);
        }
        $q->execute(); $kelas = $q->get_result()->fetch_assoc(); $q->close();
        if (!$kelas) throw new Exception($is_guru ? 'Siswa belum ditempatkan di kelas 12 RPL 1 atau 12 RPL 2 pada tahun ajaran aktif.' : 'Kelas tidak ditemukan atau tidak aktif.');
        $kelas_id = (int)$kelas['id'];

        $q = $koneksi->prepare("SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = ? AND status_aktif = 1 LIMIT 1");
        $q->bind_param('i', $pelanggaran_id); $q->execute();
        $pelanggaran = $q->get_result()->fetch_assoc(); $q->close();
        if (!$pelanggaran) throw new Exception('Jenis pelanggaran tidak ditemukan atau tidak aktif.');

        $q = $koneksi->prepare("SELECT nama FROM t_guru WHERE id = ? AND status_aktif = 1 LIMIT 1");
        $q->bind_param('i', $guru_id); $q->execute();
        $guru = $q->get_result()->fetch_assoc(); $q->close();
        if (!$guru) throw new Exception('Guru tidak ditemukan atau tidak aktif.');

        $stmt = $koneksi->prepare("INSERT INTO t_pelanggaran_siswa
            (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id,
             nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal,
             keterangan, poin, tindakan, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', 'Proses')");
        $poin = (int)$pelanggaran['poin'];
        $kategori_id = (int)$pelanggaran['pelanggaran_kategori_id'];
        $nama_siswa = $siswa['nama']; $nama_kelas = $kelas['nama'];
        $nama_pelanggaran = $pelanggaran['nama']; $nama_guru = $guru['nama'];
        $stmt->bind_param('iisisisiisssi', $tahun_ajaran_id, $siswa_id, $nama_siswa,
            $kelas_id, $nama_kelas, $pelanggaran_id, $nama_pelanggaran, $kategori_id,
            $guru_id, $nama_guru, $tanggal, $keterangan, $poin);
        $stmt->execute(); $stmt->close();
        $success = 'Data pelanggaran berhasil disimpan. Tindakan pembinaan dapat diisi melalui menu Tindakan.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

function tp_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
if ($is_guru) {
    $sql_siswa = "SELECT s.id, s.nis, s.nama, k.id AS kelas_id, k.nama AS nama_kelas
        FROM t_siswa s
        INNER JOIN t_kelas_siswa ks ON ks.id = (SELECT MAX(ks2.id) FROM t_kelas_siswa ks2
            WHERE ks2.siswa_id = s.id AND ks2.tahun_ajaran_id = ? AND ks2.status_aktif = 1)
        INNER JOIN t_kelas k ON k.id = ks.kelas_id
        WHERE s.status_aktif = 1 AND k.status_aktif = 1" . $filter_kelas_guru . " ORDER BY s.nama";
    $siswa_stmt = $koneksi->prepare($sql_siswa);
    $siswa_stmt->bind_param('i', $tahun_ajaran_id); $siswa_stmt->execute();
    $siswa_list = $siswa_stmt->get_result();
} else {
    $siswa_list = $koneksi->query("SELECT id, nis, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama");
}
$kelas_list = $koneksi->query("SELECT id, nama FROM t_kelas WHERE status_aktif = 1 AND UPPER(TRIM(nama)) IN ('12 RPL 1', '12 RPL 2') ORDER BY nama");
$pelanggaran_list = $koneksi->query("SELECT id, kode, nama, poin FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY nama");
$guru_list = $koneksi->query("SELECT id, nama FROM t_guru WHERE status_aktif = 1 ORDER BY nama");
include 'header.php';
?>
<div class="container-fluid py-3">
    <h3>Form Input Pelanggaran Siswa</h3>
    <p><strong>Tahun Ajaran Aktif:</strong> <?= tp_h($tahun['nama'] ?? 'Belum ditentukan') ?></p>
    <?php if ($error): ?><div class="alert alert-danger"><?= tp_h($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= tp_h($success) ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST" action="tambah_pelanggaran.php">
            <div class="form-row">
                <div class="form-group col-md-4"><label>Tanggal Kejadian</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                <div class="form-group col-md-4">
                    <label>Siswa</label>
                    <?php if ($is_guru): ?>
                    <div class="dropdown-siswa-pelanggaran" style="position:relative;">
                        <input type="search" id="cariSiswaPelanggaran" class="form-control" placeholder="-- Pilih atau cari siswa --" autocomplete="off" aria-label="Cari dan pilih siswa" aria-controls="daftarSiswaPelanggaran" aria-expanded="false" required>
                        <div id="daftarSiswaPelanggaran" class="list-group shadow-sm" role="listbox" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1050;max-height:240px;overflow-y:auto;background:#fff;"></div>
                        <select name="siswa_id" id="pilihSiswaPelanggaran" class="d-none" aria-hidden="true" tabindex="-1"><option value="">-- Pilih Siswa --</option>
                            <?php while($r=$siswa_list->fetch_assoc()): ?>
                            <option value="<?= (int)$r['id'] ?>" data-kelas-id="<?= (int)$r['kelas_id'] ?>" data-kelas-nama="<?= tp_h($r['nama_kelas']) ?>"><?= tp_h($r['nis'].' - '.$r['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small id="statusPilihSiswaPelanggaran" class="form-text text-muted">Belum ada siswa yang dipilih.</small>
                    </div>
                    <?php else: ?>
                    <select name="siswa_id" id="pilihSiswaPelanggaran" class="form-control" required><option value="">-- Pilih Siswa --</option>
                        <?php while($r=$siswa_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nis'].' - '.$r['nama']) ?></option><?php endwhile; ?>
                    </select>
                    <?php endif; ?>
                </div>
                <div class="form-group col-md-4">
                    <label>Kelas</label>
                    <?php if ($is_guru): ?>
                    <input type="text" id="kelasOtomatisGuru" class="form-control" value="" placeholder="Terisi otomatis setelah memilih siswa" readonly>
                    <input type="hidden" name="kelas_id" id="kelasIdOtomatisGuru" value="">
                    <?php else: ?>
                    <select name="kelas_id" class="form-control" required><option value="">-- Pilih Kelas --</option><?php while($r=$kelas_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nama']) ?></option><?php endwhile; ?></select>
                    <?php endif; ?>
                </div>
                <div class="form-group col-md-6">
                    <label>Jenis Pelanggaran</label>
                    <?php if ($is_guru): ?>
                    <div class="dropdown-pelanggaran-guru" style="position:relative;">
                        <input type="search" id="cariJenisPelanggaranGuru" class="form-control" placeholder="-- Pilih atau cari pelanggaran --" autocomplete="off" aria-label="Cari dan pilih jenis pelanggaran" aria-controls="daftarJenisPelanggaranGuru" aria-expanded="false" required>
                        <div id="daftarJenisPelanggaranGuru" class="list-group shadow-sm" role="listbox" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1050;max-height:240px;overflow-y:auto;background:#fff;"></div>
                        <select name="pelanggaran_id" id="pilihJenisPelanggaranGuru" class="d-none" aria-hidden="true" tabindex="-1"><option value="">-- Pilih Pelanggaran --</option>
                            <?php while($r=$pelanggaran_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['kode'].' - '.$r['nama'].' ('.$r['poin'].' poin)') ?></option><?php endwhile; ?>
                        </select>
                        <small id="statusPilihJenisPelanggaranGuru" class="form-text text-muted">Belum ada jenis pelanggaran yang dipilih.</small>
                    </div>
                    <?php else: ?>
                    <select name="pelanggaran_id" class="form-control" required><option value="">-- Pilih Pelanggaran --</option><?php while($r=$pelanggaran_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['kode'].' - '.$r['nama'].' ('.$r['poin'].' poin)') ?></option><?php endwhile; ?></select>
                    <?php endif; ?>
                </div>
                <?php if (!$is_guru): ?>
                <div class="form-group col-md-6"><label>Guru Pelapor</label><select name="guru_id" class="form-control" required><option value="">-- Pilih Guru --</option><?php while($r=$guru_list->fetch_assoc()): ?><option value="<?= (int)$r['id'] ?>"><?= tp_h($r['nama']) ?></option><?php endwhile; ?></select></div>
                <div class="form-group col-12"><label>Keterangan / Kronologi</label><textarea name="keterangan" class="form-control" rows="3" required></textarea></div>
                <?php endif; ?>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Data Pelanggaran</button>
            <a href="pelanggaran.php" class="btn btn-secondary">Batal</a>
        </form>
    </div></div>
</div>
<?php if ($is_guru): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function buatDropdownCari(inputId, selectId, listId, statusId, onSelect, labelKosong) {
        const input = document.getElementById(inputId), select = document.getElementById(selectId), list = document.getElementById(listId), status = document.getElementById(statusId);
        if (!input || !select || !list) return;
        const items = Array.from(select.options).slice(1).map(o => ({value:o.value,label:o.textContent.trim(),option:o}));
        function close(){ list.style.display='none'; input.setAttribute('aria-expanded','false'); }
        function render(q){
            const term=(q||'').trim().toLocaleLowerCase('id');
            const found=items.filter(x=>x.label.toLocaleLowerCase('id').includes(term));
            list.innerHTML='';
            if(!found.length){ const d=document.createElement('div'); d.className='list-group-item text-muted'; d.textContent=labelKosong; list.appendChild(d); }
            found.slice(0,100).forEach(x=>{ const b=document.createElement('button'); b.type='button'; b.className='list-group-item list-group-item-action'; b.textContent=x.label; b.addEventListener('click',()=>{ select.value=x.value; input.value=x.label; input.setCustomValidity(''); if(status){status.textContent='Dipilih: '+x.label;status.className='form-text text-success';} if(onSelect) onSelect(x.option); close(); }); list.appendChild(b); });
            list.style.display='block'; input.setAttribute('aria-expanded','true');
        }
        input.addEventListener('focus',()=>{ if(select.value) input.select(); render(''); });
        input.addEventListener('input',()=>{ select.value=''; input.setCustomValidity(''); if(status){status.textContent='Pilih salah satu dari daftar.';status.className='form-text text-muted';} if(onSelect) onSelect(null); render(input.value); });
        input.addEventListener('keydown',e=>{ if(e.key==='Escape') close(); if(e.key==='Enter' && list.style.display==='block'){const b=list.querySelector('button');if(b){e.preventDefault();b.click();}} });
        input.addEventListener('invalid',()=>{if(!select.value)input.setCustomValidity('Silakan pilih data dari daftar.');});
        document.addEventListener('click',e=>{if(!e.target.closest('#'+listId)&&!e.target.closest('#'+inputId))close();});
    }
    buatDropdownCari('cariSiswaPelanggaran','pilihSiswaPelanggaran','daftarSiswaPelanggaran','statusPilihSiswaPelanggaran',function(option){
        const kelas = document.getElementById('kelasOtomatisGuru'), kelasId = document.getElementById('kelasIdOtomatisGuru');
        if(kelas && kelasId){ kelas.value=option ? (option.dataset.kelasNama||'') : ''; kelasId.value=option ? (option.dataset.kelasId||'') : ''; }
    },'Siswa tidak ditemukan');
    buatDropdownCari('cariJenisPelanggaranGuru','pilihJenisPelanggaranGuru','daftarJenisPelanggaranGuru','statusPilihJenisPelanggaranGuru',null,'Jenis pelanggaran tidak ditemukan');
});
</script>
<?php endif; ?>
<?php include 'footer.php'; ?>
