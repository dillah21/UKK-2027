<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat      = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan      = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $status_aktif = mysqli_real_escape_string($koneksi, $_POST['status_aktif']);

    $query = "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif, created_at, updated_at) 
              VALUES ('$nama', '$tingkat', '$jurusan', '$status_aktif', NOW(), NOW())";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Data kelas berhasil ditambahkan!');
                window.location.href='kelas.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan data kelas: " . mysqli_error($koneksi) . "');
                window.location.href='kelas.php';
              </script>";
    }
} else {
    header("Location: kelas.php");
    exit();
}
?>