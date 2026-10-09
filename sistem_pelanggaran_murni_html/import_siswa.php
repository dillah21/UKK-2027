<?php
require_once 'auth.php';
require_once 'koneksi.php';

$message = '';

if (isset($_POST['import'])) {
    if (isset($_FILES['file_csv']) && $_FILES['file_csv']['error'] === UPLOAD_ERR_OK) {
        $filename = $_FILES['file_csv']['tmp_name'];
        $handle = fopen($filename, "r");
        
        $inserted = 0;
        // Skip baris pertama (Header CSV)
        fgetcsv($handle, 1000, ",");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Urutan CSV: NISN, Nama, Jenis Kelamin (L/P)
            $nisn = trim($data[0] ?? '');
            $nama = trim($data[1] ?? '');
            $jk = trim($data[2] ?? 'L');

            if (!empty($nisn) && !empty($nama)) {
                $stmt = $koneksi->prepare("INSERT INTO t_siswa (nisn, nama, jk) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nama=VALUES(nama), jk=VALUES(jk)");
                $stmt->bind_param("sss", $nisn, $nama, $jk);
                if ($stmt->execute()) {
                    $inserted++;
                }
                $stmt->close();
            }
        }
        fclose($handle);
        $message = "Berhasil mengimpor $inserted data siswa!";
    } else {
        $message = "Pilih file CSV yang valid!";
    }
}

include 'header.php';
?>

<h2>Impor Data Siswa via CSV</h2>
<p>Upload file <b>.csv</b> untuk menambahkan atau memperbarui data banyak siswa secara massal.</p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message); ?></b></p>
    <hr>
<?php endif; ?>

<form method="POST" action="import_siswa.php" enctype="multipart/form-data">
    <table border="0" cellpadding="5">
        <tr>
            <td><label for="file_csv">Pilih File CSV</label></td>
            <td>: <input type="file" id="file_csv" name="file_csv" accept=".csv" required></td>
        </tr>
        <tr>
            <td>Format CSV Contoh</td>
            <td>: <code>NISN,Nama,JK</code> (Contoh: <i>0012345,Ahmad,L</i>)</td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" name="import">Upload & Impor Data</button></td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>