<?php
session_start();
include "koneksi.php";

/* ==============================
   CEK SESSION (AGAR TIDAK ERROR)
============================== */
if(!isset($_SESSION['level'])){
    header("Location: dashboard.php");
    exit;
}

/* ==============================
   AMBIL DATA DARI DATABASE
============================== */
$data = mysqli_query($conn,"
SELECT peminjaman.*, users_charset.username, buku.judul
FROM peminjaman
JOIN users ON peminjaman.user_id = users.id
JOIN buku ON peminjaman.buku_id = buku.id
ORDER BY peminjaman.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Peminjaman</title>
</head>
<body>

<h2>Data Peminjaman Buku</h2>

<a href="dashboard.php">← Kembali ke Dashboard</a>
<br><br>

<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>No</th>
    <th>Nama User</th>
    <th>Judul Buku</th>
    <th>Tanggal Pinjam</th>
    <th>Tanggal Kembali</th>
    <th>Status</th>
</tr>

<?php
$no = 1;
while($d = mysqli_fetch_array($data)){
?>
<tr>
    <td><?php echo $no++; ?></td>
    <td><?php echo $d['username']; ?></td>
    <td><?php echo $d['judul']; ?></td>
    <td><?php echo $d['tanggal_pinjam']; ?></td>
    <td><?php echo $d['tanggal_kembali']; ?></td>
    <td><?php echo $d['status']; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>
