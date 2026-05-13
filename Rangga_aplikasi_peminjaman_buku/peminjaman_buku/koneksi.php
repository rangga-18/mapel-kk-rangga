<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_perpustakaan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<div style='font-family:sans-serif;color:red;padding:20px;'>
        <h3>Koneksi Database Gagal!</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Pastikan XAMPP/Laragon berjalan dan database sudah diimport.</p>
    </div>");
}

mysqli_set_charset($conn, "utf8");
?>
