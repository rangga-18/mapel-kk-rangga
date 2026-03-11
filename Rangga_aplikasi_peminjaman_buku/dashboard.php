<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- NAVBAR -->
<div class="bg-purple-800 text-white p-4 shadow-lg">
    <div class="max-w-6xl mx-auto flex justify-between">
        <h1 class="text-xl font-bold">Sistem Peminjaman Buku</h1>
        <a href="logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</a>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-8 space-y-8">

    <!-- PEMINJAMAN -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-bold mb-4">Peminjaman Buku</h2>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1">Nama Peminjam</label>
                <input type="text" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block mb-1">Judul Buku</label>
                <input type="text" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block mb-1">Tanggal Pinjam</label>
                <input type="date" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block mb-1">Tanggal Kembali</label>
                <input type="date" class="w-full border rounded p-2">
            </div>
        </div>

        <button class="mt-4 bg-purple-700 text-white px-6 py-2 rounded hover:bg-purple-800">
            Simpan Peminjaman
        </button>
    </div>


    <!-- PENGEMBALIAN -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-bold mb-4">Pengembalian Buku</h2>

        <div class="flex gap-4">
            <input type="text" placeholder="Cari nama / judul buku..."
                class="flex-1 border rounded p-2">
            <button class="bg-purple-700 text-white px-6 py-2 rounded hover:bg-purple-800">
                Cari
            </button>
        </div>
    </div>


    <!-- MANAJEMEN -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-bold mb-4">Manajemen & Laporan</h2>

        <div class="grid md:grid-cols-4 gap-4">
            <a href="data_buku.php" class="bg-purple-700 text-white p-3 text-center rounded hover:bg-purple-800">
                Daftar Buku
            </a>
            <a href="riwayat.php" class="bg-purple-700 text-white p-3 text-center rounded hover:bg-purple-800">
                Riwayat Transaksi
            </a>
            <a href="data_user.php" class="bg-purple-700 text-white p-3 text-center rounded hover:bg-purple-800">
                Data Peminjam
            </a>
            <a href="tambah_user.php" class="bg-purple-700 text-white p-3 text-center rounded hover:bg-purple-800">
                Laporan
            </a>
        </div>
    </div>

</div>
<footer class="bg-purple-800 text-white text-center p-3 fixed bottom-0 w-full">
    <p>© 2026 Sistem Peminjaman Buku Dibuat oleh @Rian Dika Rangga Raditai</p>
</footer>


</body>
</html>
