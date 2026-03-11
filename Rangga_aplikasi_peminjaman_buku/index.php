<?php
session_start();
include "login.php";

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = mysqli_query($conn,"SELECT * FROM users 
                                WHERE username='$username' 
                                AND password='$password'");

    $cek = mysqli_num_rows($data);

    if($cek > 0){
        $d = mysqli_fetch_array($data);

        $_SESSION['id'] = $d['id'];
        $_SESSION['username'] = $d['username'];
        $_SESSION['level'] = $d['level'];

        // ARAHKAN SESUAI LEVEL
        if($d['level'] == "admin"){
            header("Location: dashboard_admin.php");
        }
        elseif($d['level'] == "petugas"){
            header("Location: dashboard_petugas.php");
        }
        elseif($d['level'] == "peminjam"){
            header("Location: dashboard_peminjam.php");
        }

        exit;
    }else{
        $error = "Username atau Password salah!";
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
        📚 Login Sistem
    </h2>

    <?php if($error != ""){ ?>
        <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-center">
            <?php echo $error; ?>
        </div>
    <?php } ?>

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

</div>

</body>
</html>
