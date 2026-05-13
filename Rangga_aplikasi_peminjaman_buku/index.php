<?php
// ============================================================
// APLIKASI PEMINJAMAN BUKU - SINGLE FILE
// UKK RPL 2025/2026 - Paket 4
// Dibuat oleh Rian Dika Rangga Raditia
// ============================================================

session_start();

// ---- KONEKSI DATABASE ----
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_perpustakaan";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("<div style='font-family:sans-serif;color:red;padding:20px;'><h3>Koneksi Database Gagal!</h3><p>" . mysqli_connect_error() . "</p><p>Pastikan XAMPP/Laragon berjalan dan database sudah diimport.</p></div>");
}
mysqli_set_charset($conn, "utf8");

// ---- ROUTING ----
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

function url($page, $extra = '') {
    return "index.php?page=$page" . ($extra ? "&$extra" : '');
}

// ---- LOGOUT ----
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// ---- AUTO REDIRECT ----
if (isset($_SESSION['id'])) {

    if (in_array($page, ['login', 'register'])) {
        header("Location: index.php?page=" . ($_SESSION['level'] == 'admin'
            ? 'admin_dashboard'
            : 'user_dashboard'));
        exit;
    }

    // cek halaman admin
    if (strpos($page, 'admin_') === 0 && $_SESSION['level'] != 'admin') {
        header("Location: index.php?page=user_dashboard");
        exit;
    }

    // cek halaman user
    if (strpos($page, 'user_') === 0 && $_SESSION['level'] != 'user') {
        header("Location: index.php?page=admin_dashboard");
        exit;
    }

} else {

    if (!in_array($page, ['login', 'register'])) {
        header("Location: index.php?page=login");
        exit;
    }

}

// ---- DATA ANGGOTA ----
$anggota_id   = 0;
$anggota_data = [];
if (isset($_SESSION['id']) && $_SESSION['level'] == 'user') {
    $uid = (int)$_SESSION['id'];
    $res = mysqli_query($conn, "SELECT * FROM anggota WHERE user_id=$uid");
    if ($res && mysqli_num_rows($res) > 0) {
        $anggota_data = mysqli_fetch_assoc($res);
        $anggota_id   = (int)$anggota_data['id'];
    }
}

// ============================================================
// CSS GLOBAL
// ============================================================
$css = <<<CSS
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #0d1117;
    --sidebar: #111827;
    --card: #161b26;
    --card2: #1c2333;
    --border: #21293a;
    --border2: #2d3748;
    --gold: #c9a96e;
    --gold-light: #e2c27d;
    --gold-dim: rgba(201,169,110,.12);
    --text: #e2e8f0;
    --text2: #94a3b8;
    --text3: #64748b;
    --green: #4ade80;
    --red: #f87171;
    --blue: #60a5fa;
    --radius: 12px;
    --radius-sm: 8px;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;}
a{text-decoration:none;color:inherit;}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:5px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:var(--border2);border-radius:99px;}

/* ── LAYOUT ── */
.app{display:flex;min-height:100vh;}

/* ── SIDEBAR ── */
.sidebar{
    width:240px;background:var(--sidebar);
    border-right:1px solid var(--border);
    display:flex;flex-direction:column;
    position:fixed;top:0;left:0;height:100vh;z-index:100;
    transition:width .2s;
}
.sidebar-logo{
    padding:22px 20px 18px;
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;gap:11px;
}
.logo-icon{
    width:36px;height:36px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    border-radius:10px;display:flex;align-items:center;
    justify-content:center;font-size:1.1rem;flex-shrink:0;
    box-shadow:0 4px 12px rgba(201,169,110,.25);
}
.logo-text{font-family:'Playfair Display',serif;font-size:1rem;color:var(--gold);line-height:1.2;}
.logo-sub{font-size:.68rem;color:var(--text3);margin-top:1px;letter-spacing:.03em;}
.sidebar-nav{flex:1;padding:12px 0;overflow-y:auto;}
.nav-section{
    padding:12px 18px 4px;font-size:.65rem;font-weight:700;
    color:var(--text3);text-transform:uppercase;letter-spacing:.1em;
}
.nav-item{
    display:flex;align-items:center;gap:10px;
    padding:10px 18px;color:var(--text2);font-size:.84rem;
    font-weight:500;transition:all .15s;
    border-left:2px solid transparent;margin:1px 8px;
    border-radius:var(--radius-sm);
}
.nav-item:hover{color:var(--text);background:rgba(255,255,255,.05);}
.nav-item.active{
    color:var(--gold);background:var(--gold-dim);
    border-left-color:var(--gold);
    margin-left:6px;padding-left:20px;
}
.nav-icon{width:17px;text-align:center;font-size:.95rem;flex-shrink:0;}
.nav-badge{
    margin-left:auto;background:rgba(248,113,113,.15);
    color:var(--red);border-radius:99px;
    padding:1px 7px;font-size:.68rem;font-weight:700;
}
.sidebar-footer{
    padding:14px 16px;border-top:1px solid var(--border);
}
.user-card{
    display:flex;align-items:center;gap:10px;
    padding:10px 12px;background:var(--card2);
    border-radius:var(--radius-sm);border:1px solid var(--border);
}
.user-avatar{
    width:34px;height:34px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    font-weight:700;font-size:.88rem;color:#1a1000;flex-shrink:0;
}
.user-name{font-size:.82rem;font-weight:600;line-height:1.3;}
.user-role{font-size:.68rem;color:var(--text3);}
.btn-logout{
    display:flex;align-items:center;justify-content:center;gap:6px;
    margin-top:10px;background:rgba(248,113,113,.08);
    border:1px solid rgba(248,113,113,.2);color:var(--red);
    padding:8px;border-radius:var(--radius-sm);font-size:.8rem;
    font-weight:600;transition:all .2s;width:100%;cursor:pointer;
}
.btn-logout:hover{background:rgba(248,113,113,.15);}

/* ── MAIN ── */
.main{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;overflow-x:hidden;}

/* ── TOPBAR ── */
.topbar{
    padding:18px 28px;border-bottom:1px solid var(--border);
    background:rgba(13,17,23,.9);backdrop-filter:blur(12px);
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:50;
}
.topbar-left h1{font-size:1.15rem;font-weight:700;letter-spacing:-.01em;}
.topbar-left .sub{font-size:.78rem;color:var(--text3);margin-top:2px;}
.topbar-right{display:flex;align-items:center;gap:12px;}
.date-chip{
    font-size:.76rem;color:var(--text2);
    background:var(--card2);border:1px solid var(--border);
    padding:6px 12px;border-radius:var(--radius-sm);
}

/* ── CONTENT ── */
.content{padding:24px 28px;flex:1;}

/* ── FOOTER ── */
.footer{
    margin-left:0;
    padding:16px 28px;
    border-top:1px solid var(--border);
    display:flex;align-items:center;justify-content:center;
    flex-direction:column;gap:4px;
    background:var(--sidebar);
    text-align:center;
}
.footer-text{font-size:.75rem;color:var(--text3);}
.footer-text span{color:var(--gold);font-weight:600;}
.footer-right{font-size:.72rem;color:var(--text3);}

/* ── CARDS ── */
.card{
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);padding:20px;margin-bottom:20px;
}
.card-header{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:18px;
}
.card-title{font-size:.9rem;font-weight:700;display:flex;align-items:center;gap:8px;}
.card-title .ct-icon{
    width:28px;height:28px;background:var(--gold-dim);
    border-radius:7px;display:flex;align-items:center;
    justify-content:center;font-size:.85rem;
}

/* ── STAT CARDS ── */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;}
.stat-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px;}
.stat-card{
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);padding:18px 20px;
    position:relative;overflow:hidden;
    transition:border-color .2s,transform .2s;
}
.stat-card:hover{border-color:var(--border2);transform:translateY(-1px);}
.stat-card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,transparent,var(--gold),transparent);
    opacity:0;transition:opacity .2s;
}
.stat-card:hover::before{opacity:1;}
.stat-label{font-size:.72rem;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;}
.stat-value{font-size:2rem;font-weight:800;letter-spacing:-.02em;line-height:1;}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:6px;}
.stat-icon-wrap{
    position:absolute;top:16px;right:16px;
    width:36px;height:36px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;
    background:rgba(255,255,255,.04);
}

/* ── TABLE ── */
.table-wrap{overflow-x:auto;border-radius:var(--radius-sm);border:1px solid var(--border);}
table{width:100%;border-collapse:collapse;}
thead tr{background:rgba(255,255,255,.02);}
thead th{
    text-align:left;padding:11px 14px;
    font-size:.7rem;color:var(--text3);
    text-transform:uppercase;letter-spacing:.07em;font-weight:700;
    border-bottom:1px solid var(--border);white-space:nowrap;
}
tbody td{padding:13px 14px;font-size:.85rem;border-bottom:1px solid rgba(255,255,255,.03);}
tbody tr:last-child td{border-bottom:none;}
tbody tr{transition:background .1s;}
tbody tr:hover{background:rgba(255,255,255,.025);}

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:.7rem;font-weight:700;white-space:nowrap;}
.badge-gold{background:rgba(201,169,110,.12);color:var(--gold);border:1px solid rgba(201,169,110,.25);}
.badge-green{background:rgba(74,222,128,.1);color:var(--green);border:1px solid rgba(74,222,128,.2);}
.badge-red{background:rgba(248,113,113,.1);color:var(--red);border:1px solid rgba(248,113,113,.2);}
.badge-blue{background:rgba(96,165,250,.1);color:var(--blue);border:1px solid rgba(96,165,250,.2);}
.badge-gray{background:rgba(255,255,255,.06);color:var(--text2);border:1px solid var(--border);}

/* ── BUTTONS ── */
.btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:9px 16px;border-radius:var(--radius-sm);
    font-size:.82rem;font-weight:600;border:none;
    cursor:pointer;font-family:'Inter',sans-serif;transition:all .2s;
    white-space:nowrap;
}
.btn-primary{
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    color:#1a1000;box-shadow:0 2px 8px rgba(201,169,110,.2);
}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(201,169,110,.35);}
.btn-danger{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:var(--red);}
.btn-danger:hover{background:rgba(248,113,113,.2);}
.btn-ghost{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--text2);}
.btn-ghost:hover{background:rgba(255,255,255,.08);color:var(--text);}
.btn-edit{background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.25);color:var(--gold);}
.btn-edit:hover{background:rgba(201,169,110,.2);}
.btn-return{background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.2);color:var(--green);}
.btn-return:hover{background:rgba(74,222,128,.18);}
.btn-sm{padding:5px 11px;font-size:.75rem;}
.acts{display:flex;gap:6px;flex-wrap:wrap;}

/* ── FORMS ── */
.form-group{margin-bottom:16px;}
.form-group label{
    display:block;font-size:.74rem;font-weight:700;
    color:var(--text3);margin-bottom:7px;
    text-transform:uppercase;letter-spacing:.06em;
}
.form-group input,
.form-group select,
.form-group textarea{
    width:100%;background:var(--card2);
    border:1px solid var(--border);border-radius:var(--radius-sm);
    padding:10px 13px;color:var(--text);
    font-size:.88rem;font-family:'Inter',sans-serif;
    outline:none;transition:border-color .2s,box-shadow .2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus{
    border-color:var(--gold);
    box-shadow:0 0 0 3px rgba(201,169,110,.1);
}
.form-group select option{background:#1c2333;}
.form-group input[disabled]{background:rgba(255,255,255,.03);color:var(--text3);cursor:not-allowed;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}

/* ── ALERTS ── */
.alert{
    display:flex;align-items:flex-start;gap:10px;
    padding:12px 15px;border-radius:var(--radius-sm);
    font-size:.85rem;margin-bottom:16px;
}
.alert-ok{background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);color:#86efac;}
.alert-err{background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:#fca5a5;}

/* ── SEARCH ── */
.search-row{display:flex;gap:10px;margin-bottom:16px;}
.search-row input{
    flex:1;background:var(--card2);border:1px solid var(--border);
    border-radius:var(--radius-sm);padding:9px 13px;
    color:var(--text);font-size:.86rem;font-family:'Inter',sans-serif;outline:none;
    transition:border-color .2s;
}
.search-row input:focus{border-color:var(--gold);}

/* ── DIVIDER ── */
.divider{height:1px;background:var(--border);margin:18px 0;}

/* ── CODE ── */
code{
    color:var(--gold);font-size:.78rem;
    background:var(--gold-dim);padding:2px 7px;
    border-radius:4px;font-family:monospace;
}

/* ── EMPTY STATE ── */
.empty-state{text-align:center;padding:48px 24px;color:var(--text3);}
.empty-state .es-icon{font-size:2.8rem;margin-bottom:12px;opacity:.5;}
.empty-state p{font-size:.88rem;}

/* ── INFO BOX ── */
.info-box{
    background:var(--gold-dim);border:1px solid rgba(201,169,110,.18);
    border-radius:var(--radius-sm);padding:12px 15px;
    font-size:.82rem;color:var(--text2);margin-bottom:16px;
    display:flex;gap:8px;align-items:flex-start;
}

/* ── BOOK CARD ── */
.book-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;}
.book-card{
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);padding:18px;
    transition:border-color .2s,transform .2s,box-shadow .2s;
    display:flex;flex-direction:column;
}
.book-card:hover{
    border-color:rgba(201,169,110,.4);
    transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(0,0,0,.3);
}
.book-spine{
    width:44px;height:56px;
    background:linear-gradient(135deg,var(--gold-dim),rgba(201,169,110,.06));
    border:1px solid rgba(201,169,110,.2);
    border-radius:6px;display:flex;align-items:center;
    justify-content:center;font-size:1.4rem;margin-bottom:14px;
    flex-shrink:0;
}
.book-title{font-weight:700;font-size:.88rem;line-height:1.45;margin-bottom:5px;flex:1;}
.book-author{font-size:.75rem;color:var(--text3);margin-bottom:12px;}
.book-meta{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;}
.book-code{font-size:.7rem;color:var(--text3);margin-bottom:14px;}

/* ── CONFIRM CARD ── */
.confirm-card{
    background:var(--card2);border:1px solid rgba(201,169,110,.2);
    border-radius:var(--radius);padding:22px;margin-bottom:14px;
}
.confirm-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.cg-label{font-size:.7rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.cg-value{font-size:.9rem;font-weight:600;}
</style>
CSS;

// ============================================================
// HELPERS
// ============================================================
function sidebar_admin($page) {
    $nav = [
        'admin_dashboard'    => ['🏠', 'Dashboard',      'MENU UTAMA', false],
        'admin_buku'         => ['📖', 'Data Buku',       'DATA MASTER', false],
        'admin_anggota'      => ['👥', 'Kelola Anggota',  '', false],
        'admin_peminjaman'   => ['📋', 'Peminjaman',      'TRANSAKSI', false],
        'admin_pengembalian' => ['↩️', 'Pengembalian',    '', false],
        'admin_laporan'      => ['📊', 'Laporan',         '', false],
    ];
    echo '<div class="sidebar">';
    echo '<div class="sidebar-logo"><div class="logo-icon">📚</div><div><div class="logo-text">Aplikasi Peminjaman Buku</div><div class="logo-sub">Panel Administrator</div></div></div>';
    echo '<nav class="sidebar-nav">';
    $last = '';
    foreach ($nav as $key => [$icon, $label, $section, $badge]) {
        if ($section && $section !== $last) {
            echo "<div class='nav-section'>$section</div>";
            $last = $section;
        }
        $active = $page === $key ? 'active' : '';
        echo "<a href='".url($key)."' class='nav-item $active'><span class='nav-icon'>$icon</span>$label</a>";
    }
    echo '</nav>';
    echo '<div class="sidebar-footer">';
    echo '<div class="user-card"><div class="user-avatar">'.strtoupper(substr($_SESSION['nama'],0,1)).'</div>';
    echo '<div><div class="user-name">'.htmlspecialchars($_SESSION['nama']).'</div><div class="user-role">Administrator</div></div></div>';
    echo '<a href="'.url('logout').'" class="btn-logout">🚪 Keluar</a>';
    echo '</div></div>';
}

function sidebar_user($page, $anggota_data) {
    $nav = [
        'user_dashboard' => ['🏠', 'Beranda'],
        'user_katalog'   => ['📖', 'Katalog Buku'],
        'user_pinjam'    => ['📋', 'Pinjam Buku'],
        'user_riwayat'   => ['📜', 'Riwayat Saya'],
    ];
    echo '<div class="sidebar">';
    echo '<div class="sidebar-logo"><div class="logo-icon">📚</div><div><div class="logo-text">Aplikasi Peminjaman Buku</div><div class="logo-sub">Portal Siswa</div></div></div>';
    echo '<nav class="sidebar-nav"><div class="nav-section">MENU</div>';
    foreach ($nav as $key => [$icon, $label]) {
        $active = $page === $key ? 'active' : '';
        echo "<a href='".url($key)."' class='nav-item $active'><span class='nav-icon'>$icon</span>$label</a>";
    }
    echo '</nav>';
    echo '<div class="sidebar-footer">';
    echo '<div class="user-card"><div class="user-avatar">'.strtoupper(substr($_SESSION['nama'],0,1)).'</div>';
    echo '<div><div class="user-name">'.htmlspecialchars($_SESSION['nama']).'</div><div class="user-role">'.htmlspecialchars($anggota_data['nis'] ?? 'Siswa').'</div></div></div>';
    echo '<a href="'.url('logout').'" class="btn-logout">🚪 Keluar</a>';
    echo '</div></div>';
}

function topbar($title, $sub = '') {
    echo "<div class='topbar'><div class='topbar-left'><h1>$title</h1>".($sub ? "<div class='sub'>$sub</div>" : '')."</div><div class='topbar-right'><div class='date-chip'>📅 ".date('d F Y')."</div></div></div>";
}

function footer_bar() {
    echo "<footer class='footer'>"
        ."<div class='footer-text'>© 2026 <span>Aplikasi Peminjaman Buku</span> — Hak Cipta Dilindungi</div>"
        ."<div class='footer-right'>Dibuat oleh <strong style='color:var(--gold)'>Rian Dika Rangga Raditia</strong> &nbsp;·&nbsp; UKK RPL 2025/2026</div>"
        ."</footer>";
}

function alert_msg($msg) {
    if (!$msg) return;
    [$type, $text] = explode('|', $msg, 2);
    $icon = $type === 'success' ? '✅' : '⚠️';
    $cls  = $type === 'success' ? 'alert-ok' : 'alert-err';
    echo "<div class='alert $cls'><span>$icon</span><span>".htmlspecialchars($text)."</span></div>";
}

function status_badge($status) {
    switch ($status) {
        case 'dikembalikan':
            return "<span class='badge badge-green'>● Dikembalikan</span>";
        case 'terlambat':
            return "<span class='badge badge-red'>● Terlambat</span>";
        default:
            return "<span class='badge badge-gold'>● Dipinjam</span>";
    }
}

function card_open($title, $icon = '', $action = '') {
    $ico = $icon ? "<div class='ct-icon'>$icon</div>" : '';
    $act = $action ?: '';
    echo "<div class='card'><div class='card-header'><div class='card-title'>$ico $title</div>$act</div>";
}

function card_close() { echo "</div>"; }

// ============================================================
// LOGIN PAGE
// ============================================================
if ($page === 'login') {
    $error = '';
    if (isset($_POST['login'])) {
        $uname = mysqli_real_escape_string($conn, trim($_POST['username']));
        $pass  = trim($_POST['password']);
        $res   = mysqli_query($conn, "SELECT * FROM user WHERE username='$uname' AND password='$pass'");
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $_SESSION['id']       = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama'];
            $_SESSION['level']    = $row['level'];
            header("Location: index.php?page=" . ($row['level'] === 'admin' ? 'admin_dashboard' : 'user_dashboard'));
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login — Aplikasi Peminjaman Buku</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --gold:#c9a96e;
    --gold-light:#e2c27d;
    --gold-dim:rgba(201,169,110,.15);
    --dark:#1a1200;
    --dark2:#0f0b00;
    --text:#f5eed8;
    --text2:#c4b48a;
    --text3:#8a7a5a;
    --radius:14px;
    --radius-sm:9px;
}
body{
    font-family:'Inter',sans-serif;
    min-height:100vh;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    background:#1a1200;
    position:relative;
    overflow:hidden;
}
/* BG image full cover */
.bg-photo{
    position:fixed;inset:0;
    background-image:url('istockphoto-542582100-170667a.jpg');
    background-size:cover;
    background-position:center;
    filter:brightness(.35) saturate(.8);
    z-index:0;
}
.bg-overlay{
    position:fixed;inset:0;
    background:linear-gradient(
        160deg,
        rgba(10,7,0,.82) 0%,
        rgba(20,13,0,.6) 40%,
        rgba(5,3,0,.75) 100%
    );
    z-index:1;
}
/* Decorative gold glow */
.bg-glow{
    position:fixed;
    top:20%;left:50%;transform:translateX(-50%);
    width:600px;height:300px;
    background:radial-gradient(ellipse,rgba(201,169,110,.08) 0%,transparent 70%);
    z-index:2;pointer-events:none;
}

/* ── WRAPPER ── */
.login-wrap{
    position:relative;z-index:10;
    display:flex;flex-direction:column;
    align-items:center;
    width:100%;
    padding:24px 16px 32px;
}

/* ── HEADER LOGO ── */
.login-logo{
    display:flex;align-items:center;gap:12px;
    margin-bottom:28px;
    animation:fadeDown .6s ease both;
}
.login-logo-icon{
    width:48px;height:48px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    border-radius:14px;display:flex;align-items:center;justify-content:center;
    font-size:1.5rem;box-shadow:0 6px 20px rgba(201,169,110,.3);
}
.login-logo-text{
    font-family:'Playfair Display',serif;
    font-size:1.45rem;color:var(--gold-light);
    text-shadow:0 2px 12px rgba(0,0,0,.6);
    line-height:1.2;
}
.login-logo-sub{font-size:.72rem;color:var(--text3);margin-top:2px;letter-spacing:.04em;}

/* ── CARD ── */
.login-card{
    width:420px;max-width:95vw;
    background:rgba(15,10,0,.75);
    backdrop-filter:blur(20px);
    border:1px solid rgba(201,169,110,.2);
    border-radius:var(--radius);
    padding:36px 38px 32px;
    box-shadow:
        0 32px 64px rgba(0,0,0,.6),
        0 0 0 1px rgba(201,169,110,.07),
        inset 0 1px 0 rgba(201,169,110,.1);
    animation:fadeUp .6s ease .1s both;
}
.card-title{
    font-family:'Playfair Display',serif;
    font-size:1.5rem;color:var(--text);
    margin-bottom:4px;
    letter-spacing:-.01em;
}
.card-sub{
    font-size:.83rem;color:var(--text3);
    margin-bottom:26px;
    border-bottom:1px solid rgba(201,169,110,.12);
    padding-bottom:22px;
}

/* ── FORM ── */
.inp-group{margin-bottom:16px;}
.inp-label{
    display:block;font-size:.72rem;font-weight:700;
    color:var(--text3);text-transform:uppercase;letter-spacing:.07em;
    margin-bottom:7px;
}
.inp-field{
    width:100%;
    background:rgba(255,255,255,.04);
    border:1px solid rgba(201,169,110,.18);
    border-radius:var(--radius-sm);
    padding:11px 14px;
    color:var(--text);
    font-size:.9rem;font-family:'Inter',sans-serif;
    outline:none;
    transition:border-color .2s,box-shadow .2s,background .2s;
}
.inp-field:focus{
    border-color:var(--gold);
    background:rgba(201,169,110,.06);
    box-shadow:0 0 0 3px rgba(201,169,110,.12);
}
.inp-field::placeholder{color:var(--text3);opacity:.7;}

/* ── ERROR ── */
.err-box{
    display:flex;align-items:center;gap:8px;
    background:rgba(248,113,113,.1);
    border:1px solid rgba(248,113,113,.25);
    color:#fca5a5;
    padding:11px 14px;border-radius:var(--radius-sm);
    font-size:.84rem;margin-bottom:16px;
}

/* ── BTN ── */
.btn-login{
    width:100%;padding:12px;margin-top:6px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    color:#1a0f00;font-weight:700;font-size:.95rem;
    border:none;border-radius:var(--radius-sm);cursor:pointer;
    font-family:'Inter',sans-serif;transition:all .2s;
    box-shadow:0 4px 16px rgba(201,169,110,.3);
    letter-spacing:.01em;
}
.btn-login:hover{
    transform:translateY(-1px);
    box-shadow:0 8px 24px rgba(201,169,110,.4);
}
.btn-login:active{transform:translateY(0);}

/* ── LINK ── */
.card-link{
    text-align:center;margin-top:18px;
    font-size:.84rem;color:var(--text3);
}
.card-link a{color:var(--gold);font-weight:600;transition:color .15s;}
.card-link a:hover{color:var(--gold-light);}

/* ── DECORATIVE DIVIDER ── */
.or-divider{
    display:flex;align-items:center;gap:10px;
    margin:18px 0 14px;
    color:var(--text3);font-size:.72rem;letter-spacing:.06em;text-transform:uppercase;
}
.or-divider::before,.or-divider::after{
    content:'';flex:1;height:1px;
    background:linear-gradient(90deg,transparent,rgba(201,169,110,.2),transparent);
}

/* ── FOOTER ── */
.login-footer{
    position:relative;z-index:10;
    margin-top:22px;
    text-align:center;
    font-size:.72rem;color:var(--text3);
    display:flex;align-items:center;justify-content:center;gap:8px;
    animation:fadeUp .6s ease .2s both;
}
.login-footer span{color:var(--gold);font-weight:600;}
.lf-dot{opacity:.3;}

/* ── BOTTOM QUOTES ── */
.book-quote{
    position:relative;z-index:10;
    margin-top:14px;
    font-family:'Playfair Display',serif;
    font-style:italic;
    font-size:.82rem;
    color:rgba(201,169,110,.4);
    text-align:center;
    max-width:320px;
    animation:fadeUp .6s ease .3s both;
}

@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeDown{from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
    .login-card{padding:28px 22px 24px;}
    .card-title{font-size:1.3rem;}
}
</style>
</head>
<body>
<div class="bg-photo"></div>
<div class="bg-overlay"></div>
<div class="bg-glow"></div>

<div class="login-wrap">
    <!-- Logo / Brand -->
    <div class="login-logo">
        <div class="login-logo-icon">📚</div>
        <div>
            <div class="login-logo-text">Aplikasi Peminjaman Buku</div>
            <div class="login-logo-sub">Sistem Peminjaman Buku Sekolah</div>
        </div>
    </div>

    <!-- Card -->
    <div class="login-card">
        <div class="card-title">Selamat Datang 👋</div>
        <div class="card-sub">Masuk dengan akun Aplikasi Peminjaman Buku Anda</div>

        <?php if($error): ?>
        <div class="err-box">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="inp-group">
                <label class="inp-label">Username</label>
                <input class="inp-field" type="text" name="username"
                    placeholder="Masukkan username Anda" required
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="inp-group">
                <label class="inp-label">Password</label>
                <input class="inp-field" type="password" name="password"
                    placeholder="Masukkan password Anda" required>
            </div>
            <button type="submit" name="login" class="btn-login">
                Masuk ke Aplikasi Peminjaman Buku →
            </button>
        </form>

        <div class="or-divider">atau</div>

        <div class="card-link">
            Belum punya akun?
            <a href="<?= url('register') ?>">Daftar sebagai anggota</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <span>Aplikasi Peminjaman Buku</span>
        <span class="lf-dot">·</span>
        Rian Dika Rangga Raditia
        <span class="lf-dot">·</span>
        © 2026
    </div>

    <div class="book-quote">"Membaca adalah jendela dunia yang tak pernah tertutup."</div>
</div>
</body></html>
    <?php
    exit;
}

// ============================================================
// REGISTER PAGE
// ============================================================
if ($page === 'register') {
    $error = $success = '';
    if (isset($_POST['daftar'])) {
        $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
        $nis      = mysqli_real_escape_string($conn, trim($_POST['nis']));
        $kelas    = mysqli_real_escape_string($conn, trim($_POST['kelas']));
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $pw       = trim($_POST['password']);
        $kf       = trim($_POST['konfirm']);
        if ($pw !== $kf) {
            $error = "Password dan konfirmasi tidak cocok!";
        } else {
            $cek = mysqli_query($conn, "SELECT id FROM user WHERE username='$username'");
            if (mysqli_num_rows($cek) > 0) {
                $error = "Username sudah digunakan!";
            } else {
                mysqli_query($conn, "INSERT INTO user (nama,username,password,level) VALUES ('$nama','$username','$pw','user')");
                $uid = mysqli_insert_id($conn);
                mysqli_query($conn, "INSERT INTO anggota (nis,nama,kelas,user_id) VALUES ('$nis','$nama','$kelas',$uid)");
                $success = "Pendaftaran berhasil! Silakan login.";
            }
        }
    }
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Daftar Anggota — Aplikasi Peminjaman Buku</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --gold:#c9a96e;--gold-light:#e2c27d;
    --text:#f5eed8;--text2:#c4b48a;--text3:#8a7a5a;
    --radius:14px;--radius-sm:9px;
}
body{
    font-family:'Inter',sans-serif;
    min-height:100vh;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    background:#1a1200;position:relative;overflow:hidden;
    padding:24px 16px;
}
.bg-photo{
    position:fixed;inset:0;
    background-image:url('istockphoto-542582100-170667a.jpg');
    background-size:cover;background-position:center;
    filter:brightness(.3) saturate(.7);z-index:0;
}
.bg-overlay{
    position:fixed;inset:0;
    background:linear-gradient(160deg,rgba(10,7,0,.85) 0%,rgba(5,3,0,.75) 100%);
    z-index:1;
}
.reg-wrap{position:relative;z-index:10;width:100%;display:flex;flex-direction:column;align-items:center;}
.reg-logo{
    display:flex;align-items:center;gap:12px;margin-bottom:24px;
    animation:fadeDown .5s ease both;
}
.reg-logo-icon{
    width:42px;height:42px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    border-radius:12px;display:flex;align-items:center;justify-content:center;
    font-size:1.3rem;box-shadow:0 4px 16px rgba(201,169,110,.25);
}
.reg-logo-text{font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--gold-light);}
.reg-logo-sub{font-size:.7rem;color:var(--text3);margin-top:2px;}
.reg-card{
    width:520px;max-width:95vw;
    background:rgba(15,10,0,.78);backdrop-filter:blur(20px);
    border:1px solid rgba(201,169,110,.2);border-radius:var(--radius);
    padding:34px 38px 28px;
    box-shadow:0 32px 64px rgba(0,0,0,.55),0 0 0 1px rgba(201,169,110,.06),inset 0 1px 0 rgba(201,169,110,.1);
    animation:fadeUp .5s ease .1s both;
}
.reg-header{margin-bottom:24px;}
.reg-header h2{font-family:'Playfair Display',serif;font-size:1.45rem;color:var(--text);margin-bottom:4px;}
.reg-header p{font-size:.83rem;color:var(--text3);padding-bottom:18px;border-bottom:1px solid rgba(201,169,110,.12);}
.form-group{margin-bottom:14px;}
.form-group label{display:block;font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.form-group input{
    width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(201,169,110,.18);
    border-radius:var(--radius-sm);padding:10px 13px;color:var(--text);
    font-size:.88rem;font-family:'Inter',sans-serif;outline:none;
    transition:border-color .2s,box-shadow .2s;
}
.form-group input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,169,110,.12);background:rgba(201,169,110,.05);}
.form-group input::placeholder{color:var(--text3);opacity:.6;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.alert{display:flex;align-items:flex-start;gap:8px;padding:11px 14px;border-radius:var(--radius-sm);font-size:.84rem;margin-bottom:14px;}
.alert-ok{background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);color:#86efac;}
.alert-err{background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:#fca5a5;}
.btn-daftar{
    width:100%;padding:12px;
    background:linear-gradient(135deg,var(--gold),var(--gold-light));
    color:#1a0f00;font-weight:700;font-size:.95rem;
    border:none;border-radius:var(--radius-sm);cursor:pointer;
    font-family:'Inter',sans-serif;transition:all .2s;
    box-shadow:0 4px 16px rgba(201,169,110,.28);margin-top:4px;
}
.btn-daftar:hover{transform:translateY(-1px);box-shadow:0 8px 22px rgba(201,169,110,.38);}
.back-link{text-align:center;margin-top:16px;font-size:.84rem;color:var(--text3);}
.back-link a{color:var(--gold);font-weight:600;}
.reg-footer{
    position:relative;z-index:10;margin-top:20px;
    text-align:center;font-size:.72rem;color:var(--text3);
    display:flex;align-items:center;justify-content:center;gap:8px;
    animation:fadeUp .5s ease .2s both;
}
.reg-footer span{color:var(--gold);font-weight:600;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeDown{from{opacity:0;transform:translateY(-10px);}to{opacity:1;transform:translateY(0);}}
@media(max-width:480px){.reg-card{padding:24px 20px;}.form-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="bg-photo"></div>
<div class="bg-overlay"></div>
<div class="reg-wrap">
    <div class="reg-logo">
        <div class="reg-logo-icon">📚</div>
        <div>
            <div class="reg-logo-text">Aplikasi Peminjaman Buku</div>
            <div class="reg-logo-sub">Sistem Peminjaman Buku Sekolah</div>
        </div>
    </div>
    <div class="reg-card">
        <div class="reg-header">
            <h2>📝 Daftar Anggota</h2>
            <p>Buat akun baru untuk mengakses Aplikasi Peminjaman Buku</p>
        </div>
        <?php if($error): ?><div class="alert alert-err"><span>⚠️</span><span><?= htmlspecialchars($error) ?></span></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-ok"><span>✅</span><span><?= htmlspecialchars($success) ?></span></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" placeholder="Nama lengkap siswa" required></div>
            <div class="form-row">
                <div class="form-group"><label>NIS</label><input type="text" name="nis" placeholder="Nomor Induk Siswa" required></div>
                <div class="form-group"><label>Kelas</label><input type="text" name="kelas" placeholder="XII RPL 1" required></div>
            </div>
            <div class="form-group"><label>Username</label><input type="text" name="username" placeholder="Buat username unik" required></div>
            <div class="form-row">
                <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Password" required></div>
                <div class="form-group"><label>Konfirmasi</label><input type="password" name="konfirm" placeholder="Ulangi password" required></div>
            </div>
            <button type="submit" name="daftar" class="btn-daftar">Daftar Sekarang →</button>
        </form>
        <div class="back-link">Sudah punya akun? <a href="<?= url('login') ?>">Login di sini</a></div>
    </div>
    <div class="reg-footer">
        <span>Aplikasi Peminjaman Buku</span>
        <span style="opacity:.3">·</span>
        Rian Dika Rangga Raditia
        <span style="opacity:.3">·</span>
        © 2026
    </div>
</div>
</body></html>
    <?php
    exit;
}

// ============================================================
// RENDER LAYOUT (sidebar + main)
// ============================================================
$titles = [
    'admin_dashboard'    => 'Dashboard',
    'admin_buku'         => 'Data Buku',
    'admin_anggota'      => 'Kelola Anggota',
    'admin_peminjaman'   => 'Peminjaman',
    'admin_pengembalian' => 'Pengembalian',
    'admin_laporan'      => 'Laporan',
    'user_dashboard'     => 'Beranda',
    'user_katalog'       => 'Katalog Buku',
    'user_pinjam'        => 'Pinjam Buku',
    'user_riwayat'       => 'Riwayat Saya',
];
$title = $titles[$page] ?? 'Aplikasi Peminjaman Buku';

echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'>";
echo "<title>$title — Aplikasi Peminjaman Bukuu</title>$css</head><body><div class='app'>";

if ($_SESSION['level'] === 'admin') sidebar_admin($page);
else sidebar_user($page, $anggota_data);

echo "<div class='main'>";

// ==============================================================
// ██ ADMIN DASHBOARD
// ==============================================================
if ($page === 'admin_dashboard') {
    $tb  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM buku"))[0];
    $ta  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM anggota"))[0];
    $tp  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'"))[0];
    $tl  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE status='terlambat'"))[0];
    $rec = mysqli_query($conn,"SELECT p.*,a.nama AS nm,a.nis,b.judul FROM peminjaman p JOIN anggota a ON p.anggota_id=a.id JOIN buku b ON p.buku_id=b.id ORDER BY p.created_at DESC LIMIT 8");

    topbar("Dashboard", "Selamat datang, ".htmlspecialchars($_SESSION['nama']));
    echo "<div class='content'>";
    echo "<div class='stat-grid'>
        <div class='stat-card'><div class='stat-icon-wrap'>📚</div><div class='stat-label'>Total Buku</div><div class='stat-value'>$tb</div><div class='stat-sub'>Koleksi Aplikasi Peminjaman Buku</div></div>
        <div class='stat-card'><div class='stat-icon-wrap'>👥</div><div class='stat-label'>Total Anggota</div><div class='stat-value'>$ta</div><div class='stat-sub'>Siswa terdaftar</div></div>
        <div class='stat-card'><div class='stat-icon-wrap'>📋</div><div class='stat-label'>Sedang Dipinjam</div><div class='stat-value' style='color:var(--gold)'>$tp</div><div class='stat-sub'>Buku aktif dipinjam</div></div>
        <div class='stat-card'><div class='stat-icon-wrap'>⚠️</div><div class='stat-label'>Terlambat</div><div class='stat-value' style='color:var(--red)'>$tl</div><div class='stat-sub'>Perlu tindak lanjut</div></div>
    </div>";
    card_open("Peminjaman Terbaru", "📋", "<a href='".url('admin_peminjaman')."' class='btn btn-primary btn-sm'>Lihat Semua</a>");
    echo "<div class='table-wrap'><table><thead><tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead><tbody>";
    while($r=mysqli_fetch_assoc($rec)){
        if($r['status']=='dipinjam'&&$r['tgl_kembali']<date('Y-m-d')){mysqli_query($conn,"UPDATE peminjaman SET status='terlambat' WHERE id=".$r['id']);$r['status']='terlambat';}
        echo "<tr><td><code>{$r['kode_pinjam']}</code></td><td><div style='font-weight:600;font-size:.88rem'>".htmlspecialchars($r['nm'])."</div><div style='font-size:.73rem;color:var(--text3)'>{$r['nis']}</div></td><td style='max-width:180px;font-size:.85rem'>".htmlspecialchars($r['judul'])."</td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".status_badge($r['status'])."</td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ ADMIN BUKU
// ==============================================================
elseif ($page === 'admin_buku') {
    $msg='';
    if(isset($_GET['hapus'])){$id=(int)$_GET['hapus'];mysqli_query($conn,"DELETE FROM buku WHERE id=$id");$msg='success|Buku berhasil dihapus.';}
    if(isset($_POST['simpan'])){
        $id=(int)$_POST['id'];$kode=mysqli_real_escape_string($conn,trim($_POST['kode_buku']));
        $judul=mysqli_real_escape_string($conn,trim($_POST['judul']));$peng=mysqli_real_escape_string($conn,trim($_POST['pengarang']));
        $penerbit=mysqli_real_escape_string($conn,trim($_POST['penerbit']));$tahun=(int)$_POST['tahun_terbit'];
        $kat=mysqli_real_escape_string($conn,trim($_POST['kategori']));$stok=(int)$_POST['stok'];
        if($id>0){mysqli_query($conn,"UPDATE buku SET kode_buku='$kode',judul='$judul',pengarang='$peng',penerbit='$penerbit',tahun_terbit=$tahun,kategori='$kat',stok=$stok WHERE id=$id");$msg='success|Data buku berhasil diperbarui.';}
        else{$cek=mysqli_query($conn,"SELECT id FROM buku WHERE kode_buku='$kode'");if(mysqli_num_rows($cek)>0){$msg='error|Kode buku sudah ada!';}else{mysqli_query($conn,"INSERT INTO buku (kode_buku,judul,pengarang,penerbit,tahun_terbit,kategori,stok) VALUES ('$kode','$judul','$peng','$penerbit',$tahun,'$kat',$stok)");$msg='success|Buku berhasil ditambahkan.';}}
    }
    $edit=null;if(isset($_GET['edit'])){$edit=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM buku WHERE id=".(int)$_GET['edit']));}
    $q=isset($_GET['q'])?mysqli_real_escape_string($conn,$_GET['q']):'';
    $where=$q?"WHERE judul LIKE '%$q%' OR kode_buku LIKE '%$q%' OR pengarang LIKE '%$q%'":'';
    $list=mysqli_query($conn,"SELECT * FROM buku $where ORDER BY id DESC");

    topbar("Data Buku","Kelola koleksi buku Aplikasi Peminjaman Buku");
    echo "<div class='content'>";
    alert_msg($msg);
    card_open($edit?'✏️ Edit Buku':'➕ Tambah Buku Baru','📖');
    echo "<form method='POST'><input type='hidden' name='id' value='".($edit['id']??0)."'>";
    echo "<div class='form-row'><div class='form-group'><label>Kode Buku</label><input type='text' name='kode_buku' value='".htmlspecialchars($edit['kode_buku']??'')."' placeholder='BK007' required></div><div class='form-group'><label>Kategori</label><input type='text' name='kategori' value='".htmlspecialchars($edit['kategori']??'')."' placeholder='Teknologi, Sains...'></div></div>";
    echo "<div class='form-group'><label>Judul Buku</label><input type='text' name='judul' value='".htmlspecialchars($edit['judul']??'')."' placeholder='Judul lengkap buku' required></div>";
    echo "<div class='form-row'><div class='form-group'><label>Pengarang</label><input type='text' name='pengarang' value='".htmlspecialchars($edit['pengarang']??'')."' placeholder='Nama pengarang'></div><div class='form-group'><label>Penerbit</label><input type='text' name='penerbit' value='".htmlspecialchars($edit['penerbit']??'')."' placeholder='Nama penerbit'></div></div>";
    echo "<div class='form-row'><div class='form-group'><label>Tahun Terbit</label><input type='number' name='tahun_terbit' value='".($edit['tahun_terbit']??date('Y'))."' min='1900' max='2026'></div><div class='form-group'><label>Stok</label><input type='number' name='stok' value='".($edit['stok']??1)."' min='0' required></div></div>";
    echo "<div style='display:flex;gap:10px'><button type='submit' name='simpan' class='btn btn-primary'>".($edit?'💾 Update Buku':'➕ Simpan Buku')."</button>".($edit?"<a href='".url('admin_buku')."' class='btn btn-ghost'>Batal</a>":'')."</div></form>";
    card_close();
    card_open("Daftar Buku","📚","<span style='font-size:.78rem;color:var(--text3)'>".mysqli_num_rows($list)." buku</span>");
    echo "<form method='GET' class='search-row'><input type='hidden' name='page' value='admin_buku'><input type='text' name='q' value='".htmlspecialchars($q)."' placeholder='🔍 Cari judul, kode, atau pengarang...'><button type='submit' class='btn btn-primary btn-sm'>Cari</button>".($q?"<a href='".url('admin_buku')."' class='btn btn-ghost btn-sm'>Reset</a>":'')."</form>";
    echo "<div class='table-wrap'><table><thead><tr><th>Kode</th><th>Judul</th><th>Pengarang</th><th>Kategori</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>";
    while($r=mysqli_fetch_assoc($list)){
        echo "<tr><td><code>{$r['kode_buku']}</code></td><td style='font-weight:600;max-width:200px'>".htmlspecialchars($r['judul'])."</td><td style='color:var(--text2);font-size:.83rem'>".htmlspecialchars($r['pengarang'])."</td><td><span class='badge badge-blue'>".htmlspecialchars($r['kategori'])."</span></td><td style='color:var(--text3)'>{$r['tahun_terbit']}</td><td><span class='badge ".($r['stok']>0?'badge-green':'badge-red')."'>{$r['stok']}</span></td><td><div class='acts'><a href='".url('admin_buku','edit='.$r['id'])."' class='btn btn-sm btn-edit'>Edit</a><a href='".url('admin_buku','hapus='.$r['id'])."' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus buku ini?\")'>Hapus</a></div></td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ ADMIN ANGGOTA
// ==============================================================
elseif ($page === 'admin_anggota') {
    $msg='';
    if(isset($_GET['hapus'])){$id=(int)$_GET['hapus'];mysqli_query($conn,"DELETE FROM anggota WHERE id=$id");$msg='success|Anggota berhasil dihapus.';}
    if(isset($_POST['simpan'])){
        $id=(int)$_POST['id'];$nis=mysqli_real_escape_string($conn,trim($_POST['nis']));
        $nama=mysqli_real_escape_string($conn,trim($_POST['nama']));$kelas=mysqli_real_escape_string($conn,trim($_POST['kelas']));
        $alamat=mysqli_real_escape_string($conn,trim($_POST['alamat']));$tel=mysqli_real_escape_string($conn,trim($_POST['telepon']));
        if($id>0){mysqli_query($conn,"UPDATE anggota SET nis='$nis',nama='$nama',kelas='$kelas',alamat='$alamat',telepon='$tel' WHERE id=$id");$msg='success|Data anggota diperbarui.';}
        else{mysqli_query($conn,"INSERT INTO anggota (nis,nama,kelas,alamat,telepon) VALUES ('$nis','$nama','$kelas','$alamat','$tel')");$msg='success|Anggota berhasil ditambahkan.';}
    }
    $edit=null;if(isset($_GET['edit'])){$edit=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM anggota WHERE id=".(int)$_GET['edit']));}
    $q=isset($_GET['q'])?mysqli_real_escape_string($conn,$_GET['q']):'';
    $where=$q?"WHERE nama LIKE '%$q%' OR nis LIKE '%$q%' OR kelas LIKE '%$q%'":'';
    $list=mysqli_query($conn,"SELECT * FROM anggota $where ORDER BY id DESC");

    topbar("Kelola Anggota","Manajemen data anggota Aplikasi Peminjaman Buku");
    echo "<div class='content'>";
    alert_msg($msg);
    card_open($edit?'✏️ Edit Anggota':'➕ Tambah Anggota','👥');
    echo "<form method='POST'><input type='hidden' name='id' value='".($edit['id']??0)."'>";
    echo "<div class='form-row'><div class='form-group'><label>NIS</label><input type='text' name='nis' value='".htmlspecialchars($edit['nis']??'')."' placeholder='Nomor Induk Siswa' required></div><div class='form-group'><label>Kelas</label><input type='text' name='kelas' value='".htmlspecialchars($edit['kelas']??'')."' placeholder='XII RPL 1' required></div></div>";
    echo "<div class='form-group'><label>Nama Lengkap</label><input type='text' name='nama' value='".htmlspecialchars($edit['nama']??'')."' placeholder='Nama lengkap anggota' required></div>";
    echo "<div class='form-row'><div class='form-group'><label>Telepon</label><input type='text' name='telepon' value='".htmlspecialchars($edit['telepon']??'')."' placeholder='Nomor telepon'></div><div class='form-group'><label>Alamat</label><input type='text' name='alamat' value='".htmlspecialchars($edit['alamat']??'')."' placeholder='Alamat lengkap'></div></div>";
    echo "<div style='display:flex;gap:10px'><button type='submit' name='simpan' class='btn btn-primary'>".($edit?'💾 Update Anggota':'➕ Simpan Anggota')."</button>".($edit?"<a href='".url('admin_anggota')."' class='btn btn-ghost'>Batal</a>":'')."</div></form>";
    card_close();
    card_open("Daftar Anggota","👥","<span style='font-size:.78rem;color:var(--text3)'>".mysqli_num_rows($list)." anggota</span>");
    echo "<form method='GET' class='search-row'><input type='hidden' name='page' value='admin_anggota'><input type='text' name='q' value='".htmlspecialchars($q)."' placeholder='🔍 Cari nama, NIS, atau kelas...'><button type='submit' class='btn btn-primary btn-sm'>Cari</button>".($q?"<a href='".url('admin_anggota')."' class='btn btn-ghost btn-sm'>Reset</a>":'')."</form>";
    echo "<div class='table-wrap'><table><thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr></thead><tbody>";
    while($r=mysqli_fetch_assoc($list)){
        echo "<tr><td><code>{$r['nis']}</code></td><td style='font-weight:600'>".htmlspecialchars($r['nama'])."</td><td><span class='badge badge-blue'>".htmlspecialchars($r['kelas'])."</span></td><td style='color:var(--text2);font-size:.83rem'>".($r['telepon']?:'-')."</td><td style='color:var(--text2);font-size:.83rem;max-width:160px'>".htmlspecialchars($r['alamat']?:'-')."</td><td><div class='acts'><a href='".url('admin_anggota','edit='.$r['id'])."' class='btn btn-sm btn-edit'>Edit</a><a href='".url('admin_anggota','hapus='.$r['id'])."' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a></div></td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ ADMIN PEMINJAMAN
// ==============================================================
elseif ($page === 'admin_peminjaman') {
    $msg='';
    if(isset($_GET['hapus'])){$id=(int)$_GET['hapus'];$p=mysqli_fetch_assoc(mysqli_query($conn,"SELECT buku_id FROM peminjaman WHERE id=$id"));if($p)mysqli_query($conn,"UPDATE buku SET stok=stok+1 WHERE id=".(int)$p['buku_id']);mysqli_query($conn,"DELETE FROM peminjaman WHERE id=$id");$msg='success|Data peminjaman dihapus.';}
    if(isset($_POST['simpan'])){
        $aid=(int)$_POST['anggota_id'];$bid=(int)$_POST['buku_id'];$tp=$_POST['tgl_pinjam'];$tk=$_POST['tgl_kembali'];
        $stok_row=mysqli_fetch_assoc(mysqli_query($conn,"SELECT stok FROM buku WHERE id=$bid"));
        if($stok_row['stok']<1){$msg='error|Stok buku habis!';}
        else{$kode='PJM-'.str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);mysqli_query($conn,"INSERT INTO peminjaman (kode_pinjam,anggota_id,buku_id,tgl_pinjam,tgl_kembali,status) VALUES ('$kode',$aid,$bid,'$tp','$tk','dipinjam')");mysqli_query($conn,"UPDATE buku SET stok=stok-1 WHERE id=$bid");$msg="success|Peminjaman berhasil! Kode: $kode";}
    }
    $q=isset($_GET['q'])?mysqli_real_escape_string($conn,$_GET['q']):'';
    $where=$q?"WHERE a.nama LIKE '%$q%' OR p.kode_pinjam LIKE '%$q%' OR b.judul LIKE '%$q%'":'';
    $list=mysqli_query($conn,"SELECT p.*,a.nama AS nm,a.nis,b.judul FROM peminjaman p JOIN anggota a ON p.anggota_id=a.id JOIN buku b ON p.buku_id=b.id $where ORDER BY p.id DESC");
    $alist=mysqli_query($conn,"SELECT id,nis,nama,kelas FROM anggota ORDER BY nama");
    $blist=mysqli_query($conn,"SELECT id,kode_buku,judul,stok FROM buku WHERE stok>0 ORDER BY judul");

    topbar("Peminjaman Buku","Kelola transaksi peminjaman");
    echo "<div class='content'>";
    alert_msg($msg);
    card_open("Tambah Peminjaman","📋");
    echo "<form method='POST'><div class='form-row'><div class='form-group'><label>Anggota</label><select name='anggota_id' required><option value=''>-- Pilih Anggota --</option>";
    while($a=mysqli_fetch_assoc($alist)) echo "<option value='{$a['id']}'>{$a['nis']} — ".htmlspecialchars($a['nama'])." ({$a['kelas']})</option>";
    echo "</select></div><div class='form-group'><label>Buku</label><select name='buku_id' required><option value=''>-- Pilih Buku --</option>";
    while($b=mysqli_fetch_assoc($blist)) echo "<option value='{$b['id']}'>{$b['kode_buku']} — ".htmlspecialchars($b['judul'])." (Stok: {$b['stok']})</option>";
    echo "</select></div></div><div class='form-row'><div class='form-group'><label>Tanggal Pinjam</label><input type='date' name='tgl_pinjam' value='".date('Y-m-d')."' required></div><div class='form-group'><label>Tanggal Kembali</label><input type='date' name='tgl_kembali' value='".date('Y-m-d',strtotime('+7 days'))."' required></div></div>";
    echo "<button type='submit' name='simpan' class='btn btn-primary'>📋 Simpan Peminjaman</button></form>";
    card_close();
    card_open("Riwayat Peminjaman","📜");
    echo "<form method='GET' class='search-row'><input type='hidden' name='page' value='admin_peminjaman'><input type='text' name='q' value='".htmlspecialchars($q)."' placeholder='🔍 Cari nama, kode pinjam, atau judul...'><button type='submit' class='btn btn-primary btn-sm'>Cari</button>".($q?"<a href='".url('admin_peminjaman')."' class='btn btn-ghost btn-sm'>Reset</a>":'')."</form>";
    echo "<div class='table-wrap'><table><thead><tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Aksi</th></tr></thead><tbody>";
    while($r=mysqli_fetch_assoc($list)){
        if($r['status']=='dipinjam'&&$r['tgl_kembali']<date('Y-m-d')){mysqli_query($conn,"UPDATE peminjaman SET status='terlambat' WHERE id=".$r['id']);$r['status']='terlambat';}
        echo "<tr><td><code>{$r['kode_pinjam']}</code></td><td><div style='font-weight:600;font-size:.87rem'>".htmlspecialchars($r['nm'])."</div><div style='font-size:.72rem;color:var(--text3)'>{$r['nis']}</div></td><td style='font-size:.84rem;max-width:170px'>".htmlspecialchars($r['judul'])."</td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".status_badge($r['status'])."</td><td><div class='acts'>";
        if($r['status']!='dikembalikan') echo "<a href='".url('admin_pengembalian','id='.$r['id'])."' class='btn btn-sm btn-return'>↩ Kembalikan</a>";
        echo "<a href='".url('admin_peminjaman','hapus='.$r['id'])."' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a></div></td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ ADMIN PENGEMBALIAN
// ==============================================================
elseif ($page === 'admin_pengembalian') {
    $msg='';$detail=null;
    if(isset($_POST['proses'])){
        $id=(int)$_POST['pinjam_id'];$tgl=date('Y-m-d');
        $data=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM peminjaman WHERE id=$id"));
        $selisih=max(0,(strtotime($tgl)-strtotime($data['tgl_kembali']))/86400);$denda=(int)$selisih*1000;
        mysqli_query($conn,"UPDATE peminjaman SET status='dikembalikan',tgl_pengembalian='$tgl',denda=$denda WHERE id=$id");
        mysqli_query($conn,"UPDATE buku SET stok=stok+1 WHERE id=".(int)$data['buku_id']);
        $msg="success|Pengembalian berhasil dicatat. Denda: Rp ".number_format($denda,0,',','.');
    }
    if(isset($_GET['id'])){$id=(int)$_GET['id'];$detail=mysqli_fetch_assoc(mysqli_query($conn,"SELECT p.*,a.nama AS nm,a.nis,a.kelas,b.judul,b.kode_buku FROM peminjaman p JOIN anggota a ON p.anggota_id=a.id JOIN buku b ON p.buku_id=b.id WHERE p.id=$id AND p.status!='dikembalikan'"));}
    $riwayat=mysqli_query($conn,"SELECT p.*,a.nama AS nm,a.nis,b.judul FROM peminjaman p JOIN anggota a ON p.anggota_id=a.id JOIN buku b ON p.buku_id=b.id WHERE p.status='dikembalikan' ORDER BY p.tgl_pengembalian DESC LIMIT 20");

    topbar("Pengembalian Buku","Proses pengembalian & perhitungan denda");
    echo "<div class='content'>";
    alert_msg($msg);
    if($detail){
        $sisa=(strtotime($detail['tgl_kembali'])-strtotime(date('Y-m-d')))/86400;
        echo "<div class='card' style='border-color:rgba(201,169,110,.3)'>";
        echo "<div class='card-header'><div class='card-title'><div class='ct-icon'>📦</div>Konfirmasi Pengembalian</div></div>";
        echo "<div class='confirm-card'><div class='confirm-grid'>";
        echo "<div><div class='cg-label'>Kode Pinjam</div><div class='cg-value' style='color:var(--gold)'><code style='font-size:.95rem'>{$detail['kode_pinjam']}</code></div></div>";
        echo "<div><div class='cg-label'>Anggota</div><div class='cg-value'>".htmlspecialchars($detail['nm'])."</div><div style='font-size:.76rem;color:var(--text3)'>{$detail['nis']} · {$detail['kelas']}</div></div>";
        echo "<div><div class='cg-label'>Buku</div><div class='cg-value'>".htmlspecialchars($detail['judul'])."</div></div>";
        echo "<div><div class='cg-label'>Batas Kembali</div><div class='cg-value'>".date('d/m/Y',strtotime($detail['tgl_kembali']))."</div>";
        if($sisa<0){echo "<div style='margin-top:5px'><span class='badge badge-red'>Terlambat ".abs((int)$sisa)." hari — Denda Rp ".number_format(abs((int)$sisa)*1000,0,',','.')."</span></div>";}
        else{echo "<div style='margin-top:5px'><span class='badge badge-green'>Sisa ".(int)$sisa." hari</span></div>";}
        echo "</div></div></div>";
        echo "<form method='POST'><input type='hidden' name='pinjam_id' value='{$detail['id']}'><div style='display:flex;gap:10px'><button type='submit' name='proses' class='btn btn-primary' onclick='return confirm(\"Konfirmasi pengembalian?\")'>✅ Konfirmasi Pengembalian</button><a href='".url('admin_peminjaman')."' class='btn btn-ghost'>← Kembali</a></div></form></div>";
    } else {
        echo "<div class='card' style='border-color:rgba(201,169,110,.15);background:rgba(201,169,110,.03)'><div style='display:flex;align-items:center;gap:10px;color:var(--text2);font-size:.88rem'>ℹ️ Pilih transaksi dari halaman <a href='".url('admin_peminjaman')."' style='color:var(--gold);font-weight:600'>Peminjaman</a> untuk memproses pengembalian.</div></div>";
    }
    card_open("Riwayat Pengembalian","📋");
    echo "<div class='table-wrap'><table><thead><tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Dikembalikan</th><th>Denda</th></tr></thead><tbody>";
    while($r=mysqli_fetch_assoc($riwayat)){
        echo "<tr><td><code>{$r['kode_pinjam']}</code></td><td>".htmlspecialchars($r['nm'])."</td><td style='max-width:160px;font-size:.84rem'>".htmlspecialchars($r['judul'])."</td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".($r['tgl_pengembalian']?date('d/m/Y',strtotime($r['tgl_pengembalian'])):'—')."</td><td>".($r['denda']>0?"<span class='badge badge-red'>Rp ".number_format($r['denda'],0,',','.')."</span>":"<span class='badge badge-green'>Rp 0</span>")."</td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ ADMIN LAPORAN
// ==============================================================
elseif ($page === 'admin_laporan') {
    $bulan=isset($_GET['bulan'])?$_GET['bulan']:date('Y-m');$bs=$bulan.'%';
    $tp=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE tgl_pinjam LIKE '$bs'"))[0];
    $tk=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE tgl_pengembalian LIKE '$bs'"))[0];
    $td=mysqli_fetch_row(mysqli_query($conn,"SELECT COALESCE(SUM(denda),0) FROM peminjaman WHERE tgl_pengembalian LIKE '$bs'"))[0];
    $list=mysqli_query($conn,"SELECT p.*,a.nama AS nm,a.nis,a.kelas,b.judul FROM peminjaman p JOIN anggota a ON p.anggota_id=a.id JOIN buku b ON p.buku_id=b.id WHERE p.tgl_pinjam LIKE '$bs' ORDER BY p.tgl_pinjam DESC");

    topbar("Laporan Peminjaman","Laporan bulanan transaksi Aplikasi Peminjaman Buku");
    echo "<div class='content'>";
    card_open("Filter Laporan","📅");
    echo "<form method='GET' style='display:flex;gap:12px;align-items:flex-end'><div class='form-group' style='margin:0'><label>Pilih Bulan</label><input type='month' name='bulan' value='$bulan'><input type='hidden' name='page' value='admin_laporan'></div><button type='submit' class='btn btn-primary'>Tampilkan</button><button type='button' onclick='window.print()' class='btn btn-ghost'>🖨️ Cetak Laporan</button></form>";
    card_close();
    echo "<div class='stat-grid-3'>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>📋</div><div class='stat-label'>Dipinjam</div><div class='stat-value' style='color:var(--gold)'>$tp</div><div class='stat-sub'>Transaksi bulan ini</div></div>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>↩️</div><div class='stat-label'>Dikembalikan</div><div class='stat-value' style='color:var(--green)'>$tk</div><div class='stat-sub'>Sudah kembali</div></div>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>💰</div><div class='stat-label'>Total Denda</div><div class='stat-value' style='color:var(--red);font-size:1.4rem'>Rp ".number_format($td,0,',','.')."</div><div class='stat-sub'>Akumulasi denda</div></div>";
    echo "</div>";
    card_open("Detail Transaksi — ".date('F Y',strtotime($bulan.'-01')),"📊");
    echo "<div class='table-wrap'><table><thead><tr><th>#</th><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Denda</th></tr></thead><tbody>";
    $no=1; while($r=mysqli_fetch_assoc($list)){
        echo "<tr><td style='color:var(--text3)'>$no</td><td><code>{$r['kode_pinjam']}</code></td><td><div style='font-weight:600;font-size:.87rem'>".htmlspecialchars($r['nm'])."</div><div style='font-size:.72rem;color:var(--text3)'>{$r['nis']}</div></td><td style='max-width:160px;font-size:.84rem'>".htmlspecialchars($r['judul'])."</td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".status_badge($r['status'])."</td><td>".($r['denda']>0?"<span class='badge badge-red'>Rp ".number_format($r['denda'],0,',','.')."</span>":"<span style='color:var(--text3)'>—</span>")."</td></tr>";
        $no++;
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ USER DASHBOARD
// ==============================================================
elseif ($page === 'user_dashboard') {
    $sp=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id AND status='dipinjam'"))[0];
    $tl=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id AND status='terlambat'"))[0];
    $tot=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id"))[0];
    $aktif=mysqli_query($conn,"SELECT p.*,b.judul,b.kode_buku FROM peminjaman p JOIN buku b ON p.buku_id=b.id WHERE p.anggota_id=$anggota_id AND p.status IN('dipinjam','terlambat') ORDER BY p.tgl_kembali ASC");

    topbar("Halo, ".htmlspecialchars($_SESSION['nama'])." 👋", htmlspecialchars($anggota_data['kelas']??'Portal Siswa'));
    echo "<div class='content'>";
    if($tl>0) echo "<div class='alert alert-err'><span>⚠️</span><span>Anda memiliki <strong>$tl</strong> buku yang terlambat dikembalikan. Segera kembalikan ke Aplikasi Peminjaman Buku!</span></div>";
    echo "<div class='stat-grid-3'>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>📋</div><div class='stat-label'>Sedang Dipinjam</div><div class='stat-value' style='color:var(--gold)'>$sp</div><div class='stat-sub'>Buku aktif</div></div>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>⚠️</div><div class='stat-label'>Terlambat</div><div class='stat-value' style='color:var(--red)'>$tl</div><div class='stat-sub'>Perlu dikembalikan</div></div>";
    echo "<div class='stat-card'><div class='stat-icon-wrap'>📜</div><div class='stat-label'>Total Pinjaman</div><div class='stat-value'>$tot</div><div class='stat-sub'>Sepanjang waktu</div></div>";
    echo "</div>";
    card_open("Buku Sedang Dipinjam","📋","<a href='".url('user_pinjam')."' class='btn btn-primary btn-sm'>+ Pinjam Buku</a>");
    if(mysqli_num_rows($aktif)==0){
        echo "<div class='empty-state'><div class='es-icon'>📭</div><p>Belum ada buku yang dipinjam</p><a href='".url('user_katalog')."' style='color:var(--gold);font-size:.84rem;display:inline-block;margin-top:8px'>Lihat katalog buku →</a></div>";
    } else {
        echo "<div class='table-wrap'><table><thead><tr><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead><tbody>";
        while($r=mysqli_fetch_assoc($aktif)){
            $sisa=(strtotime($r['tgl_kembali'])-time())/86400;
            echo "<tr><td><div style='font-weight:600;font-size:.88rem'>".htmlspecialchars($r['judul'])."</div><div style='font-size:.73rem;color:var(--text3)'>{$r['kode_pinjam']}</div></td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']));
            if($sisa<0) echo "<div style='font-size:.72rem;margin-top:3px'><span class='badge badge-red'>Telat ".abs((int)$sisa)." hari</span></div>";
            elseif($sisa<=2) echo "<div style='font-size:.72rem;margin-top:3px'><span class='badge badge-gold'>Sisa ".(int)$sisa." hari</span></div>";
            echo "</td><td>".status_badge($r['status'])."</td></tr>";
        }
        echo "</tbody></table></div>";
    }
    card_close();
    echo "</div>";
}

// ==============================================================
// ██ USER KATALOG
// ==============================================================
elseif ($page === 'user_katalog') {
    $q=isset($_GET['q'])?mysqli_real_escape_string($conn,$_GET['q']):'';
    $kat=isset($_GET['kat'])?mysqli_real_escape_string($conn,$_GET['kat']):'';
    $where="WHERE 1=1";
    if($q) $where.=" AND (judul LIKE '%$q%' OR pengarang LIKE '%$q%' OR kode_buku LIKE '%$q%')";
    if($kat) $where.=" AND kategori='$kat'";
    $list=mysqli_query($conn,"SELECT * FROM buku $where ORDER BY judul ASC");
    $katlist=mysqli_query($conn,"SELECT DISTINCT kategori FROM buku WHERE kategori!='' ORDER BY kategori");

    topbar("Katalog Buku","Temukan buku yang ingin kamu pinjam");
    echo "<div class='content'>";
    card_open("Cari Buku","🔍");
    echo "<form method='GET' style='display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end'><input type='hidden' name='page' value='user_katalog'>";
    echo "<input type='text' name='q' value='".htmlspecialchars($q)."' placeholder='Cari judul, pengarang, atau kode...' style='flex:1;min-width:200px;background:var(--card2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 13px;color:var(--text);font-family:Inter,sans-serif;font-size:.88rem;outline:none'>";
    echo "<select name='kat' style='background:var(--card2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 13px;color:var(--text);font-family:Inter,sans-serif;font-size:.88rem;outline:none'><option value=''>Semua Kategori</option>";
    while($k=mysqli_fetch_assoc($katlist)) echo "<option value='{$k['kategori']}' ".($kat==$k['kategori']?'selected':'').">".htmlspecialchars($k['kategori'])."</option>";
    echo "</select><button type='submit' class='btn btn-primary'>Cari</button>".($q||$kat?"<a href='".url('user_katalog')."' class='btn btn-ghost'>Reset</a>":'')."</form>";
    card_close();
    echo "<div class='book-grid'>";
    $count=0;
    while($r=mysqli_fetch_assoc($list)){
        $count++;
        echo "<div class='book-card'>";
        echo "<div class='book-spine'>📗</div>";
        echo "<div class='book-title'>".htmlspecialchars($r['judul'])."</div>";
        echo "<div class='book-author'>".htmlspecialchars($r['pengarang'])." · {$r['tahun_terbit']}</div>";
        echo "<div class='book-meta'><span class='badge badge-blue'>".htmlspecialchars($r['kategori'])."</span><span class='badge ".($r['stok']>0?'badge-green':'badge-red')."'>".($r['stok']>0?"● Tersedia ({$r['stok']})":"● Habis")."</span></div>";
        echo "<div class='book-code'>{$r['kode_buku']} · ".htmlspecialchars($r['penerbit'])."</div>";
        if($r['stok']>0) echo "<a href='".url('user_pinjam','buku_id='.$r['id'])."' class='btn btn-primary btn-sm' style='justify-content:center;width:100%'>Pinjam Sekarang</a>";
        else echo "<span class='btn btn-sm btn-ghost' style='justify-content:center;width:100%;cursor:not-allowed;opacity:.5'>Stok Habis</span>";
        echo "</div>";
    }
    if($count===0) echo "<div style='grid-column:1/-1'><div class='empty-state'><div class='es-icon'>📭</div><p>Tidak ada buku ditemukan</p></div></div>";
    echo "</div></div>";
}

// ==============================================================
// ██ USER PINJAM
// ==============================================================
elseif ($page === 'user_pinjam') {
    $msg='';
    if(isset($_POST['pinjam'])&&$anggota_id){
        $bid=(int)$_POST['buku_id'];
        $cek=mysqli_query($conn,"SELECT id FROM peminjaman WHERE anggota_id=$anggota_id AND buku_id=$bid AND status='dipinjam'");
        if(mysqli_num_rows($cek)>0){$msg='error|Anda sudah meminjam buku ini!';}
        else{
            $stok=mysqli_fetch_assoc(mysqli_query($conn,"SELECT stok FROM buku WHERE id=$bid"));
            if($stok['stok']<1){$msg='error|Stok buku habis!';}
            else{
                $kode='PJM-'.str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);
                $tp=date('Y-m-d');$tk=date('Y-m-d',strtotime('+7 days'));
                mysqli_query($conn,"INSERT INTO peminjaman (kode_pinjam,anggota_id,buku_id,tgl_pinjam,tgl_kembali,status) VALUES ('$kode',$anggota_id,$bid,'$tp','$tk','dipinjam')");
                mysqli_query($conn,"UPDATE buku SET stok=stok-1 WHERE id=$bid");
                $msg="success|Peminjaman berhasil! Kode: $kode. Kembalikan sebelum ".date('d/m/Y',strtotime($tk));
            }
        }
    }
    $preselect=isset($_GET['buku_id'])?(int)$_GET['buku_id']:0;
    $blist=mysqli_query($conn,"SELECT * FROM buku WHERE stok>0 ORDER BY judul");
    $aktif=mysqli_query($conn,"SELECT p.*,b.judul,b.kode_buku FROM peminjaman p JOIN buku b ON p.buku_id=b.id WHERE p.anggota_id=$anggota_id AND p.status IN('dipinjam','terlambat') ORDER BY p.tgl_kembali ASC");

    topbar("Pinjam Buku","Ajukan peminjaman buku di Aplikasi Peminjaman Buku");
    echo "<div class='content'>";
    alert_msg($msg);
    card_open("Form Peminjaman Buku","📋");
    echo "<div class='info-box'>ℹ️ Masa pinjam <strong>7 hari</strong>. Denda keterlambatan <strong>Rp 1.000 / hari</strong>. Maksimal 3 buku bersamaan.</div>";
    if($anggota_id){
        echo "<form method='POST'><div class='form-group'><label>Pilih Buku</label><select name='buku_id' id='bukuSel' required onchange='updateInfo(this)'><option value=''>-- Pilih buku yang ingin dipinjam --</option>";
        while($b=mysqli_fetch_assoc($blist)){$sel=$preselect==$b['id']?'selected':'';echo "<option value='{$b['id']}' $sel data-judul='".htmlspecialchars($b['judul'],ENT_QUOTES)."' data-peng='".htmlspecialchars($b['pengarang'],ENT_QUOTES)."' data-stok='{$b['stok']}'>{$b['kode_buku']} — ".htmlspecialchars($b['judul'])." (Stok: {$b['stok']})</option>";}
        echo "</select></div><div id='binfo' style='display:none;background:var(--card2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:13px;margin-bottom:14px'><div id='bjudul' style='font-weight:700;margin-bottom:3px'></div><div id='bpeng' style='font-size:.82rem;color:var(--text3)'></div></div>";
        echo "<div class='form-row'><div class='form-group'><label>Tanggal Pinjam</label><input type='text' value='".date('d/m/Y')."' disabled></div><div class='form-group'><label>Batas Kembali</label><input type='text' value='".date('d/m/Y',strtotime('+7 days'))."' disabled style='color:var(--gold)'></div></div>";
        echo "<button type='submit' name='pinjam' class='btn btn-primary'>📋 Ajukan Peminjaman</button></form>";
    } else {
        echo "<div class='alert alert-err'><span>⚠️</span><span>Akun Anda belum terdaftar sebagai anggota. Hubungi admin Aplikasi Peminjaman Buku.</span></div>";
    }
    card_close();
    card_open("Peminjaman Aktif Saya","📌","<a href='".url('user_riwayat')."' style='font-size:.8rem;color:var(--gold)'>Lihat semua riwayat →</a>");
    if(mysqli_num_rows($aktif)==0){echo "<p style='color:var(--text3);font-size:.86rem'>Tidak ada peminjaman aktif.</p>";}
    else{
        echo "<div class='table-wrap'><table><thead><tr><th>Buku</th><th>Batas Kembali</th><th>Status</th></tr></thead><tbody>";
        while($r=mysqli_fetch_assoc($aktif)){echo "<tr><td><div style='font-weight:600;font-size:.87rem'>".htmlspecialchars($r['judul'])."</div><div style='font-size:.72rem;color:var(--text3)'>{$r['kode_pinjam']}</div></td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".status_badge($r['status'])."</td></tr>";}
        echo "</tbody></table></div>";
    }
    card_close();
    echo "</div>";
    echo "<script>function updateInfo(s){var o=s.options[s.selectedIndex],b=document.getElementById('binfo');if(s.value){document.getElementById('bjudul').textContent=o.dataset.judul;document.getElementById('bpeng').textContent='Pengarang: '+o.dataset.peng+' · Stok tersedia: '+o.dataset.stok;b.style.display='block';}else{b.style.display='none';}}window.onload=function(){var s=document.getElementById('bukuSel');if(s)updateInfo(s);}</script>";
}

// ==============================================================
// ██ USER RIWAYAT
// ==============================================================
elseif ($page === 'user_riwayat') {
    $list=mysqli_query($conn,"SELECT p.*,b.judul,b.kode_buku,b.pengarang FROM peminjaman p JOIN buku b ON p.buku_id=b.id WHERE p.anggota_id=$anggota_id ORDER BY p.created_at DESC");
    topbar("Riwayat Peminjaman","Semua transaksi peminjaman buku Anda");
    echo "<div class='content'>";
    card_open("Riwayat Lengkap","📜","<span style='font-size:.78rem;color:var(--text3)'>".mysqli_num_rows($list)." transaksi</span>");
    echo "<div class='table-wrap'><table><thead><tr><th>Kode</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Dikembalikan</th><th>Status</th><th>Denda</th></tr></thead><tbody>";
    if(mysqli_num_rows($list)==0) echo "<tr><td colspan='7'><div class='empty-state'><div class='es-icon'>📭</div><p>Belum ada riwayat peminjaman</p></div></td></tr>";
    while($r=mysqli_fetch_assoc($list)){
        if($r['status']=='dipinjam'&&$r['tgl_kembali']<date('Y-m-d')){mysqli_query($conn,"UPDATE peminjaman SET status='terlambat' WHERE id=".$r['id']);$r['status']='terlambat';}
        echo "<tr><td><code>{$r['kode_pinjam']}</code></td><td><div style='font-weight:600;font-size:.87rem'>".htmlspecialchars($r['judul'])."</div><div style='font-size:.72rem;color:var(--text3)'>".htmlspecialchars($r['pengarang'])."</div></td><td>".date('d/m/Y',strtotime($r['tgl_pinjam']))."</td><td>".date('d/m/Y',strtotime($r['tgl_kembali']))."</td><td>".($r['tgl_pengembalian']?date('d/m/Y',strtotime($r['tgl_pengembalian'])):"<span style='color:var(--text3)'>—</span>")."</td><td>".status_badge($r['status'])."</td><td>".($r['denda']>0?"<span class='badge badge-red'>Rp ".number_format($r['denda'],0,',','.')."</span>":"<span style='color:var(--text3)'>—</span>")."</td></tr>";
    }
    echo "</tbody></table></div>";
    card_close();
    echo "</div>";
}

// ── FOOTER (inside .main, below content) ──
footer_bar();
echo "</div>"; // .main
echo "</div></body></html>"; // .app
?>