<?php
session_start();
include '../koneksi.php';
if (!isset($_GET['id'])) {
    header('Location: ../admin/d-admin.php');
    exit;
}
$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT lokasi_file FROM foto WHERE id_foto = $id");
$data = mysqli_fetch_assoc($q);
if ($data) {
    $file_path = $data['lokasi_file'];
    if (!empty($file_path) && file_exists($file_path)) {
        @unlink($file_path);
    }
    $del = mysqli_query($conn, "DELETE FROM foto WHERE id_foto = $id");
    if ($del) {
        echo "<script>alert('Foto berhasil dihapus!'); window.location='../admin/d-admin.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data dari database.'); window.location='../admin/d-admin.php';</script>";
    }
} else {
    echo "<script>alert('Foto tidak ditemukan di database.'); window.location='../admin/d-admin.php';</script>";
}
?>