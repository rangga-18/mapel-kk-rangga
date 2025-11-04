<?php
session_start();
include("koneksi.php");
$username = $_POST['username'];
$password = $_POST['password'];
$sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);
if (!$result) {
    die("Query error: " . $conn->error);
}
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username']   = $row['username'];
    $_SESSION['level_user'] = $row['level_user'];
    if ($row['level_user'] == "admin") {
        header("Location: admin/admin.php");
        exit;
    } elseif ($row['level_user'] == "operator") {
        header("Location: operator.php");
        exit;
    } elseif ($row['level_user'] == "guest") {
        header("Location: guest.php");
        exit;
    } else {
        header("Location: welcome.php");
        exit;
    }
} else {
    echo "Login gagal. <a href='index.php'>Coba lagi</a>";
}
$conn->close();
?>