<?php
session_start();
include './koneksi.php';

if (isset($_POST['tambah'])) {
    $judul_foto   = mysqli_real_escape_string($conn, $_POST['judul_foto']);
    $lokasi_foto  = mysqli_real_escape_string($conn, $_POST['lokasi_foto']);
    $deskripsi_foto = mysqli_real_escape_string($conn, $_POST['deskripsi_foto']);
    $tanggal_upload = date('Y-m-d');

    $id_user = $_SESSION['id_user'] ?? ''; // default jika belum login

    // Data file
    $nama_file = $_FILES['lokasi_file']['name'];
    $tmp_file  = $_FILES['lokasi_file']['tmp_name'];
    $size      = $_FILES['lokasi_file']['size'];
    $error     = $_FILES['lokasi_file']['error'];
    $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    // Format yang diizinkan
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        echo "<script>alert('Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.'); history.back();</script>";
        exit;
    }

    // Batas ukuran file = 10 MB
    if ($size > 10 * 1024 * 1024) { // 10 MB dalam byte
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 10MB.'); history.back();</script>";
        exit;
    }

    if ($error) {
        echo "<script>alert('Terjadi kesalahan saat upload (error code: $error).'); history.back();</script>";
        exit;
    }

    // Pastikan folder img ada
    if (!is_dir('img')) {
        mkdir('img', 0777, true);
    }

    // Buat nama file baru
    $nama_file_baru = preg_replace('/[^A-Za-z0-9.]/', '_', $nama_file);

    // Pindah file ke folder img
    if (move_uploaded_file($tmp_file, "img/" . $nama_file_baru)) {

        $sql = "INSERT INTO tb_foto (judul_foto, deskripsi_foto, lokasi_foto, lokasi_file, tanggal_upload, id_user)
                VALUES ('$judul_foto', '$deskripsi_foto', '$lokasi_foto', '$nama_file_baru', '$tanggal_upload', '$id_user')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Foto berhasil ditambahkan!'); window.location='admin/?p=admin.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data ke database: ". mysqli_error($conn) ."'); history.back();</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload file ke folder img! Pastikan folder dapat ditulis.'); history.back();</script>";
    }
}
?>