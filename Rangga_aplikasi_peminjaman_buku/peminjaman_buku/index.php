<?php
session_start();
include "koneksi.php";

// Redirect jika sudah login
if (isset($_SESSION['id'])) {
    if ($_SESSION['level'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    $data = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($data) > 0) {
        $row = mysqli_fetch_assoc($data);

        $_SESSION['id']       = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama']     = $row['nama'];
        $_SESSION['level']    = $row['level'];

        if ($row['level'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1117;
            --card: #1a1d27;
            --border: #2a2d3a;
            --gold: #c9a96e;
            --gold-light: #e8c88a;
            --text: #e8e8f0;
            --muted: #8b8fa8;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(201,169,110,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(201,169,110,0.05) 0%, transparent 50%);
        }
        .container {
            display: flex;
            width: 900px;
            max-width: 95vw;
            min-height: 520px;
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        }
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #1a1400, #2a2000);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -50px; left: -50px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,169,110,0.15), transparent);
        }
        .left-panel::after {
            content: '📚';
            font-size: 120px;
            position: absolute;
            bottom: -20px;
            right: -20px;
            opacity: 0.08;
        }
        .left-panel h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: var(--gold);
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .left-panel p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .badge {
            display: inline-block;
            background: rgba(201,169,110,0.15);
            border: 1px solid rgba(201,169,110,0.3);
            color: var(--gold);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
            text-transform: uppercase;
        }
        .right-panel {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: var(--text);
        }
        .right-panel .subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 36px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-group input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            color: var(--text);
            font-size: 0.95rem;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            outline: none;
        }
        .form-group input:focus {
            border-color: var(--gold);
            background: rgba(201,169,110,0.05);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #1a1400;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(201,169,110,0.3);
        }
        .error-msg {
            background: rgba(220,60,60,0.12);
            border: 1px solid rgba(220,60,60,0.3);
            color: #ff7070;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: var(--muted);
        }
        .register-link a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover { text-decoration: underline; }
        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 0.75rem;
            color: var(--muted);
            opacity: 0.6;
        }
        @media (max-width: 640px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 30px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left-panel">
        <span class="badge">Perpustakaan Digital</span>
        <h1>Sistem Peminjaman Buku Sekolah</h1>
        <p>Platform digital untuk memudahkan siswa dan admin perpustakaan dalam peminjaman dan pendataan buku secara efisien.</p>
    </div>
    <div class="right-panel">
        <h2>Selamat Datang</h2>
        <p class="subtitle">Masuk dengan akun perpustakaan Anda</p>

        <?php if ($error): ?>
            <div class="error-msg">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="btn-login">Masuk →</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>

        <div class="footer-note">
            © 2026 Sistem Peminjaman Buku · Dibuat oleh @Rian Dika Rangga Raditai
        </div>
    </div>
</div>
</body>
</html>
