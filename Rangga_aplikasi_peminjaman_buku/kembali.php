<?php
include "koneksi.php";

$id = $_GET['id'];
$denda_per_hari = 1000;

$data = mysqli_query($conn,"SELECT * FROM transaksi WHERE id='$id'");
$d = mysqli_fetch_array($data);

$hari_ini = date('Y-m-d');
$terlambat = 0;
$denda = 0;

if($hari_ini > $d['tanggal_jatuh_tempo']){
    $selisih = (strtotime($hari_ini) - strtotime($d['tanggal_jatuh_tempo'])) / 86400;
    $terlambat = $selisih;
    $denda = $terlambat * $denda_per_hari;
}

mysqli_query($conn,"UPDATE transaksi SET 
    status='kembali',
    tanggal_kembali='$hari_ini',
    denda='$denda'
    WHERE id='$id'");

mysqli_query($conn,"UPDATE buku SET stok = stok + 1 WHERE id='$d[buku_id]'");

echo "<script>alert('Buku dikembalikan. Denda: Rp $denda');window.location='transaksi_user.php';</script>";
?>