<?php
include "cek_login.php";
include "koneksi.php";

if(isset($_POST['simpan'])){

    $user_id = $_POST['user'];
    $buku_id = $_POST['buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $jatuh_tempo = $_POST['jatuh_tempo'];

    mysqli_query($conn,"INSERT INTO transaksi 
        (user_id,buku_id,tanggal_pinjam,tanggal_jatuh_tempo,status)
        VALUES ('$user_id','$buku_id','$tanggal_pinjam','$jatuh_tempo','dipinjam')");

    mysqli_query($conn,"UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");

    echo "<script>alert('Data berhasil ditambahkan');window.location='transaksi.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Transaksi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

<div class="bg-white p-6 rounded shadow w-96 mx-auto">

<h2 class="text-lg font-bold mb-4">Tambah Transaksi</h2>

<form method="POST">

    <!-- USER -->
    <label class="block mb-1">Nama User</label>
    <select name="user" class="w-full border p-2 mb-3" required>
        <option value="">-- Pilih User --</option>
        <?php
        $u = mysqli_query($conn,"SELECT * FROM users WHERE level='user'");
        while($user = mysqli_fetch_array($u)){
            echo "<option value='$user[id]'>$user[username]</option>";
        }
        ?>
    </select>

    <!-- BUKU -->
    <label class="block mb-1">Buku</label>
    <select name="buku" class="w-full border p-2 mb-3" required>
        <option value="">-- Pilih Buku --</option>
        <?php
        $b = mysqli_query($conn,"SELECT * FROM buku WHERE stok > 0");
        while($buku = mysqli_fetch_array($b)){
            echo "<option value='$buku[id]'>$buku[judul] (stok: $buku[stok])</option>";
        }
        ?>
    </select>

    <!-- TANGGAL -->
    <label class="block mb-1">Tanggal Pinjam</label>
    <input type="date" name="tanggal_pinjam" 
           class="w-full border p-2 mb-3" required>

    <label class="block mb-1">Tanggal Jatuh Tempo</label>
    <input type="date" name="jatuh_tempo" 
           class="w-full border p-2 mb-3" required>

    <button name="simpan" 
        class="bg-green-500 text-white px-4 py-2 rounded w-full">
        Simpan
    </button>

</form>

</div>

</body>
</html>