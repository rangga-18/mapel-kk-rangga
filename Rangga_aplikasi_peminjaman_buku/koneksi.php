<?php
$conn = mysqli_connect("localhost","root","","bd_aplikasi_peminjaman_buku");
if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
