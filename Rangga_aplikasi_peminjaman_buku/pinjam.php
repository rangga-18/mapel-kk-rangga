<?php
include "cek_login.php";
include "koneksi.php";

if(isset($_POST['pinjam'])){
    $user_id = $_SESSION['id'];
    $buku_id = $_POST['buku'];

    $tanggal_pinjam = date('Y-m-d');
    $jatuh_tempo = date('Y-m-d', strtotime('+7 days')); // batas 7 hari

    mysqli_query($conn,"INSERT INTO transaksi 
        (user_id,buku_id,tanggal_pinjam,tanggal_jatuh_tempo,status)
        VALUES ('$user_id','$buku_id','$tanggal_pinjam','$jatuh_tempo','dipinjam')");

    mysqli_query($conn,"UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");

    echo "<script>alert('Buku berhasil dipinjam');window.location='transaksi_user.php';</script>";
}
?>