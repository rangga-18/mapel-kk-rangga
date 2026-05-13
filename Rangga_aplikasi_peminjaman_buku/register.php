<?php
session_start();
include "koneksi.php";

if (isset($_SESSION['id'])) {
    header("Location: " . ($_SESSION['level'] == 'admin' ? "admin/dashboard.php" : "user/dashboard.php"));
    exit;
}

$error = "";
$success = "";

if (isset($_POST['daftar'])) {
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nis      = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $kelas    = mysqli_real_escape_string($conn, trim($_POST['kelas']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $konfirm  = trim($_POST['konfirm']);

    if ($password !== $konfirm) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek username sudah ada
        $cek = mysqli_query($conn, "SELECT id FROM user WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah digunakan, pilih yang lain!";
        } else {
            // Simpan user
            $sql_user = "INSERT INTO user (nama, username, password, level) VALUES ('$nama', '$username', '$password', 'user')";
            if (mysqli_query($conn, $sql_user)) {
                $user_id = mysqli_insert_id($conn);
                // Simpan anggota
                $sql_anggota = "INSERT INTO anggota (nis, nama, kelas, user_id) VALUES ('$nis', '$nama', '$kelas', $user_id)";
                mysqli_query($conn, $sql_anggota);
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0f1117;--card:#1a1d27;--border:#2a2d3a;--gold:#c9a96e;--gold-light:#e8c88a;--text:#e8e8f0;--muted:#8b8fa8; }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;color:var(--text);background-image:radial-gradient(ellipse at 20% 50%,rgba(201,169,110,.08) 0%,transparent 50%);}
        .card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:50px;width:500px;max-width:95vw;box-shadow:0 40px 80px rgba(0,0,0,.5);}
        h2{font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--gold);margin-bottom:6px;}
        .subtitle{color:var(--muted);font-size:.9rem;margin-bottom:30px;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:.82rem;font-weight:600;color:var(--muted);margin-bottom:7px;text-transform:uppercase;letter-spacing:.05em;}
        .form-group input{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:12px 14px;color:var(--text);font-size:.92rem;font-family:'DM Sans',sans-serif;transition:all .2s;outline:none;}
        .form-group input:focus{border-color:var(--gold);background:rgba(201,169,110,.05);}
        .btn{width:100%;padding:14px;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:#1a1400;font-weight:700;font-size:1rem;border:none;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;margin-top:4px;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(201,169,110,.3);}
        .alert{padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:18px;}
        .alert-err{background:rgba(220,60,60,.12);border:1px solid rgba(220,60,60,.3);color:#ff7070;}
        .alert-ok{background:rgba(60,200,100,.12);border:1px solid rgba(60,200,100,.3);color:#5fdf90;}
        .back-link{text-align:center;margin-top:20px;font-size:.88rem;color:var(--muted);}
        .back-link a{color:var(--gold);text-decoration:none;font-weight:600;}
    </style>
</head>
<body>
<div class="card">
    <h2>Daftar Anggota</h2>
    <p class="subtitle">Buat akun baru untuk mengakses perpustakaan</p>

    <?php if($error): ?><div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Nama lengkap" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="nis" placeholder="Nomor Induk Siswa" required>
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: XII RPL 1" required>
            </div>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Buat username unik" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi</label>
                <input type="password" name="konfirm" placeholder="Ulangi password" required>
            </div>
        </div>
        <button type="submit" name="daftar" class="btn">Daftar Sekarang →</button>
    </form>
    <div class="back-link">Sudah punya akun? <a href="index.php">Login di sini</a></div>
</div>
</body>
</html>
