<?php
include "cek_login.php";

if($_SESSION['level'] != 'user'){
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="bg-red-600 text-white flex justify-between items-center px-6 py-3">
    <h1 class="text-2xl font-bold">Aplikasi Peminjaman Buku</h1>
    <a href="logout.php" class="bg-gray-700 px-3 py-1 rounded">Logout</a>
</div>

<div class="p-6 text-center">
    <h2>Selamat Datang <?= $_SESSION['username']; ?></h2>
<div class="grid grid-cols-2 gap-4 mt-6">

   <a href="transaksi_user.php" class="bg-white p-4 shadow rounded">
    📊 Data Transaksi
</a>

    <a href="kembali.php" class="bg-white p-4 shadow rounded">
        🔄 Pengembalian Buku
    </a>

</div>
</div>

</body>
</html>