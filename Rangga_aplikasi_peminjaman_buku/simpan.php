<?php
// koneksi database
$koneksi = mysqli_connect("localhost", "root", "", "aplikasi_peminjaman_buku");

// cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ambil data dari form
$nama = $_POST['nama'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_kembali = $_POST['tanggal_kembali'];

// query simpan
$query = "INSERT INTO peminjaman (nama, tanggal_pinjam, tanggal_kembali)
          VALUES ('$nama', '$tanggal_pinjam', '$tanggal_kembali')";

if (mysqli_query($koneksi, $query)) {
    echo "Data berhasil disimpan!";
} else {
    echo "Error: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>