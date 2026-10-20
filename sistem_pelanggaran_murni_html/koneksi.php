
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ukk_2026";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($host, $user, $pass, $db);
    $koneksi->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection error: " . $e->getMessage());

    http_response_code(500);
    exit(
        "Koneksi database gagal. Pastikan MySQL aktif " .
        "dan nama database di koneksi.php sudah benar."
    );
}
?>
