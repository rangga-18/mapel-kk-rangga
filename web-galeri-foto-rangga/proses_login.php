```php
<?php
session_start();
include("koneksi.php");

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Query cek user
$sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

// Cek error query
if (!$result) {
    die("Query error: " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Simpan session
    $_SESSION['username']   = $row['username'];
    $_SESSION['level_user'] = $row['level_user'];

    // Arahkan sesuai level_user
    if ($row['level_user'] == "admin") {
        header("Location: admin.php");
        exit;
    } elseif ($row['level_user'] == "operator") {
        header("Location: operator.php");
        exit;
    } elseif ($row['level_user'] == "guest") {
        header("Location: guest.php");
        exit;
    } else {
        // fallback kalau level_user tidak dikenal
        header("Location: welcome.php");
        exit;
    }

} else {
    echo "Login gagal. <a href='index.php'>Coba lagi</a>";
}

$conn->close();
?>
```
