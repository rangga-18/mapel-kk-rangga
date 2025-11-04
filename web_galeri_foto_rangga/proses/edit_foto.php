<?php
session_start();
include './koneksi.php';

if (isset($_POST['update'])) {

    // Ambil data dari form
    $id_foto = $_POST['id_foto'];
    $judul_foto = mysqli_real_escape_string($conn, $_POST['judul_foto']);
    $lokasi_foto = mysqli_real_escape_string($conn, $_POST['lokasi_foto']);
    $deskripsi_foto = mysqli_real_escape_string($conn, $_POST['deskripsi_foto']);
    $tanggal_update = date('Y-m-d');

    // Ambil data lama dari database
    $query_lama = mysqli_query($conn,"SELECT lokasi_file FROM tb_foto WHERE id_foto = $id_foto");
    $data_lama = mysqli_fetch_assoc($query_lama);
    $file_lama = $data_lama['lokasi_file'];

    // Jika user mengganti foto baru
    if (!empty($_FILES['lokasi_file']['name'])) {
        $nama_file = $_FILES['lokasi_file']['name'];
        $tmp_file  = $_FILES['lokasi_file']['tmp_name'];
        $size      = $_FILES['lokasi_file']['size'];
        $error     = $_FILES['lokasi_file']['error'];
        $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        // Validasi format dan ukuran
        $allowed_ext = ['jpg','jpeg','png'];
        $max_size = 10 * 1024 * 1024; // 10 MB

        if (!in_array($ext, $allowed_ext)) {
            echo "<script>alert('Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.'); history.back();</script>";
            exit;
        }

        if ($size > $max_size) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 10mb.'); history.back();</script>";
            exit;
        }

        // Buat nama unik dan simpan file baru
        $nama_baru = preg_replace('/[^A-Za-z0-9.]/', '_', $nama_file);

        if (move_uploaded_file($tmp_file, 'img/'.$nama_baru)) {

            // Hapus file lama
            $path_lama = 'img/' . $file_lama;
            if (file_exists($path_lama)) {
                unlink($path_lama);
            }

            // Update semua data termasuk foto baru
            $update = mysqli_query($conn, "UPDATE tb_foto SET
                        judul_foto = '$judul_foto',
                        lokasi_foto = '$lokasi_foto',
                        deskripsi_foto = '$deskripsi_foto',
                        lokasi_file = '$nama_baru',
                        tanggal_upload = '$tanggal_update'
                        WHERE id_foto = '$id_foto' ");

            if ($update) {
                echo "<script>alert('Foto berhasil diperbarui dengan foto baru!'); window.location='../admin/?p=admin.php';</script>";
            } else {
                echo "<script>alert('Gagal memperbarui data!'); history.back();</script>";
            }

        } else {
            echo "<script>alert('Gagal mengunggah file baru!!'); history.back();</script>";
        }

    } else {
        // Jika tidak mengganti foto, hanya update data teks
        $update = mysqli_query($conn,"UPDATE tb_foto SET
                        judul_foto = '$judul_foto',
                        lokasi_foto = '$lokasi_foto',
                        deskripsi_foto = '$deskripsi_foto',
                        tanggal_upload = '$tanggal_update'
                        WHERE id_foto = '$id_foto' ");

        if ($update) {
            echo "<script>alert('Data foto berhasil diperbarui tanpa mengganti foto.'); window.location='../admin/?p=admin.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data.'); history.back();</script>";
        }
    }

} else {
    header('Location: ../admin/?p=admin.php');
    exit;
}
?>