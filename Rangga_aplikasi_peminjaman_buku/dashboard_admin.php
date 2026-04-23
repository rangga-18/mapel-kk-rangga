<?php
include "cek_login.php";

if($_SESSION['level'] != 'admin'){
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 flex flex-col min-h-screen">

<!-- HEADER -->
<div class="bg-red-600 text-white flex justify-between items-center px-6 py-3">
    <h1 class="text-2xl font-bold">Aplikasi Peminjaman Buku</h1>
    <a href="logout.php" class="bg-gray-700 px-3 py-1 rounded">Logout</a>
</div>

<div class="flex flex-1">

    <!-- SIDEBAR -->
    <div class="w-64 bg-gray-900 text-white p-4">
        <p class="mb-4">Hai, <?= $_SESSION['username']; ?></p>

        <ul class="space-y-3">
            <li><a href="#" class="block hover:bg-gray-700 p-2 rounded">Dashboard</a></li>
            <li><a href="anggota.php" class="block hover:bg-gray-700 p-2 rounded">Data Anggota</a></li>
            <li><a href="buku.php" class="block hover:bg-gray-700 p-2 rounded">Data Buku</a></li>
            <li><a href="user.php" class="block hover:bg-gray-700 p-2 rounded">Pengguna</a></li>
            <li><a href="transaksi.php" class="block hover:bg-gray-700 p-2 rounded">Transaksi</a></li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-6">
        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-lg mb-2">Selamat Datang Admin</h2>
            <p>Silakan pilih menu di samping</p>
        </div>
    </div>

</div>

<footer class="bg-gray-900 text-white text-center py-4">
    © 2026 Aplikasi Peminjaman Buku
</footer>

</body>
</html>