<?php
require_once 'koneksi.php';

$email = 'user2@sekolah.sch.id';
$password_input = 'password2';

$stmt = $koneksi->prepare("SELECT * FROM t_users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<b>Data Ditemukan di Database:</b><br>";
    echo "ID: " . $row['id'] . "<br>";
    echo "Nama: " . $row['name'] . "<br>";
    echo "Email: " . $row['email'] . "<br>";
    echo "Password di Database: " . $row['password'] . "<br>";
    echo "Role di Database: [" . $row['role'] . "]<br><hr>";

    if ($password_input === $row['password']) {
        echo "<span style='color:green;'><b>STATUS: Password Cocok!</b></span><br>";
    } else {
        echo "<span style='color:red;'><b>STATUS: Password TIDAK Cocok!</b> (Input: $password_input vs DB: {$row['password']})</span><br>";
    }

    $role_bersih = strtolower(trim($row['role']));
    if ($role_bersih === 'guru' || $role_bersih === 'siswa') {
        echo "<span style='color:green;'><b>ROLE: Valid (Guru/Siswa)</b></span>";
    } else {
        echo "<span style='color:red;'><b>ROLE: Tidak diizinkan ('$role_bersih')</b></span>";
    }
} else {
    echo "<span style='color:red;'><b>Email tidak ditemukan sama sekali di tabel t_users!</b></span>";
}
?>