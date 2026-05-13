<?php
// includes/navbar_user.php
if (!isset($_SESSION['id']) || $_SESSION['level'] != 'user') {
    header("Location: ../index.php"); exit;
}

// Ambil data anggota
include_once "../koneksi.php";
$user_id = (int)$_SESSION['id'];
$anggota_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE user_id=$user_id"));
$anggota_id   = $anggota_data['id'] ?? 0;

$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0f1117;--sidebar:#13161f;--card:#1a1d27;--border:#2a2d3a;--gold:#c9a96e;--gold-light:#e8c88a;--text:#e8e8f0;--muted:#8b8fa8;--success:#4dbd74;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
        .sidebar{width:240px;background:var(--sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;}
        .sidebar-logo{padding:28px 20px;border-bottom:1px solid var(--border);}
        .logo-text{font-family:'Playfair Display',serif;font-size:1.1rem;color:var(--gold);line-height:1.3;}
        .logo-sub{font-size:.73rem;color:var(--muted);margin-top:2px;}
        .sidebar-nav{flex:1;padding:16px 0;}
        .nav-section{padding:8px 20px 4px;font-size:.68rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:11px 20px;color:var(--muted);text-decoration:none;font-size:.86rem;font-weight:500;transition:all .15s;border-left:3px solid transparent;}
        .nav-item:hover{color:var(--text);background:rgba(255,255,255,.04);}
        .nav-item.active{color:var(--gold);background:rgba(201,169,110,.08);border-left-color:var(--gold);}
        .icon{font-size:1rem;width:18px;text-align:center;}
        .sidebar-footer{padding:16px 20px;border-top:1px solid var(--border);}
        .user-avatar{width:34px;height:34px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.88rem;color:#1a1400;flex-shrink:0;}
        .user-info{display:flex;align-items:center;gap:10px;}
        .user-name{font-size:.83rem;font-weight:600;}
        .user-level{font-size:.7rem;color:var(--muted);}
        .btn-logout{display:block;margin-top:12px;text-align:center;background:rgba(224,85,85,.1);border:1px solid rgba(224,85,85,.25);color:#ff7070;padding:8px;border-radius:8px;text-decoration:none;font-size:.8rem;font-weight:600;transition:all .2s;}
        .btn-logout:hover{background:rgba(224,85,85,.2);}
        .main{margin-left:240px;flex:1;display:flex;flex-direction:column;}
        .topbar{padding:20px 28px;border-bottom:1px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:space-between;}
        .topbar h1{font-size:1.25rem;font-weight:600;}
        .topbar .breadcrumb{font-size:.8rem;color:var(--muted);}
        .content{padding:28px;flex:1;}
        .card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px;margin-bottom:22px;}
        .stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px 20px;}
        .stat-label{font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
        .stat-value{font-size:1.8rem;font-weight:700;}
        table{width:100%;border-collapse:collapse;}
        thead th{text-align:left;padding:11px 14px;font-size:.76rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--border);}
        tbody td{padding:13px 14px;font-size:.87rem;border-bottom:1px solid rgba(255,255,255,.04);}
        tbody tr:hover{background:rgba(255,255,255,.02);}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;}
        .badge-gold{background:rgba(201,169,110,.15);color:var(--gold);border:1px solid rgba(201,169,110,.3);}
        .badge-green{background:rgba(77,189,116,.15);color:#4dbd74;border:1px solid rgba(77,189,116,.3);}
        .badge-red{background:rgba(224,85,85,.15);color:#e05555;border:1px solid rgba(224,85,85,.3);}
        .badge-blue{background:rgba(100,160,255,.15);color:#64a0ff;border:1px solid rgba(100,160,255,.3);}
        .btn{display:inline-block;padding:8px 16px;border-radius:9px;font-size:.83rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;}
        .btn-primary{background:linear-gradient(135deg,var(--gold),var(--gold-light));color:#1a1400;}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(201,169,110,.3);}
        .btn-sm{padding:5px 11px;font-size:.77rem;}
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;}
        .form-group select,.form-group input{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:10px 13px;color:var(--text);font-size:.9rem;font-family:'DM Sans',sans-serif;outline:none;transition:all .2s;}
        .form-group select:focus,.form-group input:focus{border-color:var(--gold);}
        .form-group select option{background:var(--card);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .alert{padding:11px 15px;border-radius:10px;font-size:.87rem;margin-bottom:18px;}
        .alert-err{background:rgba(220,60,60,.12);border:1px solid rgba(220,60,60,.3);color:#ff7070;}
        .alert-ok{background:rgba(60,200,100,.12);border:1px solid rgba(60,200,100,.3);color:#5fdf90;}
        .search-box{display:flex;gap:10px;margin-bottom:18px;}
        .search-box input{flex:1;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:9px 13px;color:var(--text);font-size:.88rem;font-family:'DM Sans',sans-serif;outline:none;}
        .search-box input:focus{border-color:var(--gold);}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-text">📚 Perpustakaan</div>
        <div class="logo-sub">Portal Siswa</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>
        <a href="dashboard.php" class="nav-item <?= $current=='dashboard.php'?'active':'' ?>">
            <span class="icon">🏠</span> Beranda
        </a>
        <a href="katalog.php" class="nav-item <?= $current=='katalog.php'?'active':'' ?>">
            <span class="icon">📖</span> Katalog Buku
        </a>
        <a href="pinjam.php" class="nav-item <?= $current=='pinjam.php'?'active':'' ?>">
            <span class="icon">📋</span> Pinjam Buku
        </a>
        <a href="riwayat.php" class="nav-item <?= $current=='riwayat.php'?'active':'' ?>">
            <span class="icon">📜</span> Riwayat Saya
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                <div class="user-level"><?= $anggota_data['nis'] ?? 'Siswa' ?></div>
            </div>
        </div>
        <a href="../logout.php" class="btn-logout">🚪 Keluar</a>
    </div>
</div>
<div class="main">
