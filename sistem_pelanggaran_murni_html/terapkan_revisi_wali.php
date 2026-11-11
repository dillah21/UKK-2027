<?php
// Jalankan satu kali oleh akun Admin untuk mengganti dua data wali kelas.
// Setelah berhasil, hapus file ini dari server.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'koneksi.php';

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Hanya Admin yang dapat menjalankan revisi ini.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function pastikanKelasRPL(mysqli $db, string $nama, int $rombel): int {
    $stmt = $db->prepare("
        SELECT id FROM t_kelas
        WHERE tingkat = 12 AND UPPER(TRIM(jurusan)) = 'RPL'
          AND (TRIM(nama) = ? OR TRIM(nama) LIKE ?)
        ORDER BY id ASC LIMIT 1
    ");
    $like = '%RPL ' . $rombel;
    $stmt->bind_param('ss', $nama, $like);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $id = (int)$row['id'];
        $upd = $db->prepare("
            UPDATE t_kelas SET nama = ?, tingkat = 12, jurusan = 'RPL',
                status_aktif = 1, updated_at = NOW()
            WHERE id = ?
        ");
        $upd->bind_param('si', $nama, $id);
        $upd->execute();
        $upd->close();
        return $id;
    }

    $ins = $db->prepare("
        INSERT INTO t_kelas
            (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
        VALUES (?, 12, 'RPL', 1, NOW(), NOW())
    ");
    $ins->bind_param('s', $nama);
    $ins->execute();
    $id = (int)$db->insert_id;
    $ins->close();
    return $id;
}

try {
    $db->begin_transaction();

    $res = $db->query("
        SELECT id, guru_id
        FROM t_wali_kelas
        ORDER BY id ASC
        LIMIT 2
        FOR UPDATE
    ");
    $penugasan = $res->fetch_all(MYSQLI_ASSOC);

    if (count($penugasan) < 2) {
        throw new Exception(
            'Database belum memiliki dua penugasan wali kelas. Tambahkan dua data terlebih dahulu, lalu jalankan revisi ini.'
        );
    }

    $guruTarget = [
        ['nama' => 'Ari Sandriana, S.Kom', 'nip' => '1980050005', 'guru_lama' => (int)$penugasan[0]['guru_id']],
        ['nama' => 'Aldi Maulana', 'nip' => '1980020002', 'guru_lama' => (int)$penugasan[1]['guru_id']]
    ];

    $guruIds = [];
    foreach ($guruTarget as $g) {
        $cek = $db->prepare("SELECT id FROM t_guru WHERE nip = ? ORDER BY id ASC LIMIT 1");
        $cek->bind_param('s', $g['nip']);
        $cek->execute();
        $ada = $cek->get_result()->fetch_assoc();
        $cek->close();

        $guruId = $ada ? (int)$ada['id'] : $g['guru_lama'];
        if ($guruId <= 0) {
            throw new Exception('ID guru lama tidak valid untuk ' . $g['nama']);
        }

        $upd = $db->prepare("
            UPDATE t_guru
            SET nama = ?, nip = ?, status_aktif = 1, updated_at = NOW()
            WHERE id = ?
        ");
        $upd->bind_param('ssi', $g['nama'], $g['nip'], $guruId);
        $upd->execute();
        if ($upd->affected_rows === 0 && $upd->errno) {
            throw new Exception('Gagal memperbarui data guru ' . $g['nama']);
        }
        $upd->close();
        $guruIds[] = $guruId;
    }

    if ($guruIds[0] === $guruIds[1]) {
        throw new Exception('Kedua data wali kelas mengarah ke guru yang sama. Periksa data guru terlebih dahulu.');
    }

    $kelas1 = pastikanKelasRPL($db, '12 RPL 1', 1);
    $kelas2 = pastikanKelasRPL($db, '12 RPL 2', 2);

    $update = $db->prepare("
        UPDATE t_wali_kelas
        SET guru_id = ?, kelas_id = ?, status_aktif = 1
        WHERE id = ?
    ");
    $id1 = (int)$penugasan[0]['id'];
    $update->bind_param('iii', $guruIds[0], $kelas1, $id1);
    $update->execute();

    $id2 = (int)$penugasan[1]['id'];
    $update->bind_param('iii', $guruIds[1], $kelas2, $id2);
    $update->execute();
    $update->close();

    // Pertahankan dua penugasan pertama beserta tahun ajaran dan tanggal periodenya.
    $hapus = $db->prepare("DELETE FROM t_wali_kelas WHERE id NOT IN (?, ?)");
    $hapus->bind_param('ii', $id1, $id2);
    $hapus->execute();
    $jumlahDihapus = $hapus->affected_rows;
    $hapus->close();

    $db->commit();

    echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Revisi Wali Kelas</title>';
    echo '<body style="font:16px Arial,sans-serif;padding:32px;color:#17345c">';
    echo '<h2>Revisi wali kelas berhasil diterapkan.</h2><ol>';
    echo '<li>Ari Sandriana, S.Kom — NIP 1980050005 — 12 RPL 1</li>';
    echo '<li>Aldi Maulana — NIP 1980020002 — 12 RPL 2</li></ol>';
    echo '<p>Kedua penugasan tetap menggunakan tahun ajaran dan periode tanggal sebelumnya.</p>';
    echo '<p>Data penugasan tambahan yang tersisa dihapus: ' . (int)$jumlahDihapus . '.</p>';
    echo '<p><strong>Keamanan:</strong> hapus file <code>terapkan_revisi_wali.php</code> dari folder aplikasi setelah selesai.</p>';
    echo '<p><a href="wali_kelas.php">Buka halaman Wali Kelas</a></p></body></html>';
} catch (Throwable $e) {
    $db->rollback();
    http_response_code(500);
    echo 'Revisi gagal: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
