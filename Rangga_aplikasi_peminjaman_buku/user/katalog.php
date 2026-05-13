<?php
session_start();
include "../koneksi.php";
$page_title = "Katalog Buku";
include "../includes/navbar_user.php";

$search   = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$kategori = isset($_GET['kat']) ? mysqli_real_escape_string($conn, $_GET['kat']) : '';
$where    = "WHERE 1=1";
if($search)   $where .= " AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR kode_buku LIKE '%$search%')";
if($kategori) $where .= " AND kategori='$kategori'";

$query = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY judul ASC");
$kategori_list = mysqli_query($conn, "SELECT DISTINCT kategori FROM buku WHERE kategori != '' ORDER BY kategori");
?>
<div class="topbar">
    <div>
        <h1>Katalog Buku</h1>
        <div class="breadcrumb">Temukan buku yang ingin kamu pinjam</div>
    </div>
</div>
<div class="content">
<div class="card">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul, pengarang, kode..." style="flex:1;min-width:200px;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-size:.9rem;font-family:'DM Sans',sans-serif;outline:none;">
        <select name="kat" style="background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;">
            <option value="">Semua Kategori</option>
            <?php while($k = mysqli_fetch_assoc($kategori_list)): ?>
            <option value="<?= $k['kategori'] ?>" <?= $kategori==$k['kategori']?'selected':'' ?>><?= $k['kategori'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if($search || $kategori): ?><a href="katalog.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Reset</a><?php endif; ?>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
<?php while($row = mysqli_fetch_assoc($query)): ?>
<div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;transition:transform .2s,border-color .2s;" onmouseover="this.style.borderColor='var(--gold)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border)';this.style.transform='none'">
    <div style="font-size:2.5rem;margin-bottom:12px;">📗</div>
    <div style="font-weight:600;font-size:.95rem;margin-bottom:4px;line-height:1.4;"><?= htmlspecialchars($row['judul']) ?></div>
    <div style="font-size:.8rem;color:var(--muted);margin-bottom:12px;"><?= htmlspecialchars($row['pengarang']) ?> · <?= $row['tahun_terbit'] ?></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
        <span class="badge badge-blue"><?= htmlspecialchars($row['kategori']) ?></span>
        <span class="badge <?= $row['stok']>0?'badge-green':'badge-red' ?>"><?= $row['stok']>0?'Tersedia ('.$row['stok'].')':'Habis' ?></span>
    </div>
    <div style="font-size:.76rem;color:var(--muted);margin-bottom:14px;"><?= $row['kode_buku'] ?> · <?= $row['penerbit'] ?></div>
    <?php if($row['stok'] > 0): ?>
    <a href="pinjam.php?buku_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm" style="width:100%;text-align:center;">Pinjam Sekarang</a>
    <?php else: ?>
    <span class="btn btn-sm" style="width:100%;text-align:center;background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--muted);cursor:not-allowed;">Stok Habis</span>
    <?php endif; ?>
</div>
<?php endwhile; ?>
</div>
</div>
</div></body></html>
