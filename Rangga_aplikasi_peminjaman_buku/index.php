<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = mysqli_query($conn,"SELECT * FROM users 
        WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($data) > 0){
        $row = mysqli_fetch_assoc($data);

        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['level'] = $row['level'];

        // 🔥 BEDA DASHBOARD
        if($row['level'] == 'admin'){
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_user.php");
        }

    } else {
        echo "<script>alert('Login Gagal');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Peminjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded-2xl shadow-2xl w-96">

    <h2 class="text-2xl font-bold text-center text-purple-700 mb-6">
        Aplikasi Peminjaman Buku
    </h2>



    <form method="POST">

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Username</label>
            <input type="text" name="username"
                class="w-full border rounded-lg p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Password</label>
            <input type="password" name="password"
                class="w-full border rounded-lg p-2" required>
        </div>

        <button type="submit" name="login"
            class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
            Login
        </button>

    </form>
  <p>© 2026 Aplikasi Peminjaman Buku Dibuat oleh @Rian Dika Rangga Raditai</p>
</div>

</body>
</html>
