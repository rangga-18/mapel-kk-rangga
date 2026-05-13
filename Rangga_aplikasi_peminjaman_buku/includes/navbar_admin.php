<?php
// includes/navbar_admin.php
// Pastikan dipanggil setelah session_start() dan cek login
if (!isset($_SESSION['id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php"); exit;
}
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Admin Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0f1117;--sidebar:#13161f;--card:#1a1d27;--border:#2a2d3a;--gold:#c9a96e;--gold-light:#e8c88a;--text:#e8e8f0;--muted:#8b8fa8;--danger:#e05555;--success:#4dbd74;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
        /* Sidebar */
        .sidebar{width:260px;background:var(--sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;}
        .sidebar-logo{padding:28px 24px;border-bottom:1px solid var(--border);}
        .sidebar-logo .logo-text{font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--gold);line-height:1.3;}
        .sidebar-logo .logo-sub{font-size:.75rem;color:var(--muted);margin-top:2px;}
        .sidebar-nav{flex:1;padding:20px 0;overflow-y:auto;}
        .nav-section{padding:8px 20px 4px;font-size:.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 24px;color:var(--muted);text-decoration:none;font-size:.88rem;font-weight:500;transition:all .15s;border-left:3px solid transparent;}
        .nav-item:hover{color:var(--text);background:rgba(255,255,255,.04);}
        .nav-item.active{color:var(--gold);background:rgba(201,169,110,.08);border-left-color:var(--gold);}
        .nav-item .icon{font-size:1.1rem;width:20px;text-align:center;}
        .sidebar-footer{padding:20px 24px;border-top:1px solid var(--border);}
        .user-info{display:flex;align-items:center;gap:10px;}
        .user-avatar{width:36px;height:36px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;color:#1a1400;}
        .user-name{font-size:.85rem;font-weight:600;color:var(--text);}
        .user-level{font-size:.72rem;color:var(--muted);}
        .btn-logout{display:block;margin-top:12px;text-align:center;background:rgba(224,85,85,.12);border:1px solid rgba(224,85,85,.3);color:#ff7070;padding:8px;border-radius:8px;text-decoration:none;font-size:.82rem;font-weight:600;transition:all .2s;}
        .btn-logout:hover{background:rgba(224,85,85,.2);}
        /* Main Content */
        .main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
        .topbar{padding:20px 32px;border-bottom:1px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:space-between;}
        .topbar h1{font-size:1.3rem;font-weight:600;}
        .topbar .breadcrumb{font-size:.82rem;color:var(--muted);}
        .content{padding:32px;flex:1;}
        /* Cards & Components */
        .card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:24px;}
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px 24px;}
        .stat-label{font-size:.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;}
        .stat-value{font-size:2rem;font-weight:700;color:var(--text);}
        .stat-icon{font-size:1.8rem;float:right;opacity:.6;}
        table{width:100%;border-collapse:collapse;}
        thead th{text-align:left;padding:12px 16px;font-size:.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--border);}
        tbody td{padding:14px 16px;font-size:.88rem;border-bottom:1px solid rgba(255,255,255,.04);}
        tbody tr:hover{background:rgba(255,255,255,.02);}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.74rem;font-weight:600;}
        .badge-gold{background:rgba(201,169,110,.15);color:var(--gold);border:1px solid rgba(201,169,110,.3);}
        .badge-green{background:rgba(77,189,116,.15);color:#4dbd74;border:1px solid rgba(77,189,116,.3);}
        .badge-red{background:rgba(224,85,85,.15);color:#e05555;border:1px solid rgba(224,85,85,.3);}
        .badge-blue{background:rgba(100,160,255,.15);color:#64a0ff;border:1px solid rgba(100,160,255,.3);}
        .btn{display:inline-block;padding:9px 18px;border-radius:9px;font-size:.85rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;}
        .btn-primary{background:linear-gradient(135deg,var(--gold),var(--gold-light));color:#1a1400;}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(201,169,110,.3);}
        .btn-danger{background:rgba(224,85,85,.15);border:1px solid rgba(224,85,85,.3);color:#ff7070;}
        .btn-danger:hover{background:rgba(224,85,85,.25);}
        .btn-sm{padding:6px 12px;font-size:.78rem;}
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:.82rem;font-weight:600;color:var(--muted);margin-bottom:7px;text-transform:uppercase;letter-spacing:.05em;}
        .form-group input,.form-group select,.form-group textarea{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);font-size:.92rem;font-family:'DM Sans',sans-serif;transition:all .2s;outline:none;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--gold);background:rgba(201,169,110,.05);}
        .form-group select option{background:var(--card);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .alert{padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:20px;}
        .alert-err{background:rgba(220,60,60,.12);border:1px solid rgba(220,60,60,.3);color:#ff7070;}
        .alert-ok{background:rgba(60,200,100,.12);border:1px solid rgba(60,200,100,.3);color:#5fdf90;}
        .search-box{display:flex;gap:12px;margin-bottom:20px;}
        .search-box input{flex:1;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-size:.9rem;font-family:'DM Sans',sans-serif;outline:none;}
        .search-box input:focus{border-color:var(--gold);}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-text">📚 Perpustakaan</div>
        <div class="logo-sub">Panel Administrator</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Utama</div>
        <a href="dashboard.php" class="nav-item <?= $current=='dashboard.php'?'active':'' ?>">
            <span class="icon">🏠</span> Dashboard
        </a>
        <div class="nav-section">Data Master</div>
        <a href="buku.php" class="nav-item <?= $current=='buku.php'?'active':'' ?>">
            <span class="icon">📖</span> Data Buku
        </a>
        <a href="anggota.php" class="nav-item <?= $current=='anggota.php'?'active':'' ?>">
            <span class="icon">👥</span> Kelola Anggota
        </a>
        <div class="nav-section">Transaksi</div>
        <a href="peminjaman.php" class="nav-item <?= $current=='peminjaman.php'?'active':'' ?>">
            <span class="icon">📋</span> Peminjaman
        </a>
        <a href="pengembalian.php" class="nav-item <?= $current=='pengembalian.php'?'active':'' ?>">
            <span class="icon">↩️</span> Pengembalian
        </a>
        <a href="laporan.php" class="nav-item <?= $current=='laporan.php'?'active':'' ?>">
            <span class="icon">📊</span> Laporan
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                <div class="user-level">Administrator</div>
            </div>
        </div>
        <a href="../logout.php" class="btn-logout">🚪 Keluar</a>
    </div>
</div>
<div class="main">
