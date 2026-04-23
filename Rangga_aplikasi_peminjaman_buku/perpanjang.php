<?php
include "koneksi.php";

$id = $_GET['id'];

mysqli_query($conn,"UPDATE transaksi 
    SET tanggal_jatuh_tempo = DATE_ADD(tanggal_jatuh_tempo, INTERVAL 7 DAY)
    WHERE id='$id'");

echo "<script>alert('Berhasil diperpanjang 7 hari');window.location='transaksi_user.php';</script>";
?>