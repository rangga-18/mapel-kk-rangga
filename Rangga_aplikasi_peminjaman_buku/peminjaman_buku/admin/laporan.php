<?php
session_start();
include "../koneksi.php";
$page_title = "Laporan";
include "../includes/navbar_admin.php";

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$bulan_sql = $bulan . '%';

$total_pinjam_bulan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE tgl_pinjam LIKE '$bulan_sql'"))[0];
$total_kembali_bulan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE tgl_pengembalian LIKE '$bulan_sql'"))[0];
$total_denda = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(denda) FROM peminjaman WHERE tgl_pengembalian LIKE '$bulan_sql'"))[0] ?? 0;

$query = mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.nis, a.kelas, b.judul, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.anggota_id=a.id
    JOIN buku b ON p.buku_id=b.id
    WHERE p.tgl_pinjam LIKE '$bulan_sql'
    ORDER BY p.tgl_pinjam DESC
");
?>
<div class="topbar">
    <div>
        <h1>Laporan Peminjaman</h1>
        <div class="breadcrumb">Laporan bulanan transaksi perpustakaan</div>
    </div>
</div>
<div class="content">

<div class="card">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;">
        <div class="form-group" style="margin:0;flex:1;">
            <label>Filter Bulan</label>
            <input type="month" name="bulan" value="<?= $bulan ?>">
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
        <button type="button" onclick="window.print()" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">🖨️ Cetak</button>
    </form>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-icon">📋</span>
        <div class="stat-label">Dipinjam Bulan Ini</div>
        <div class="stat-value" style="color:var(--gold);"><?= $total_pinjam_bulan ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">↩️</span>
        <div class="stat-label">Dikembalikan</div>
        <div class="stat-value" style="color:#4dbd74;"><?= $total_kembali_bulan ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">💰</span>
        <div class="stat-label">Total Denda</div>
        <div class="stat-value" style="color:#e05555;font-size:1.4rem;">Rp <?= number_format($total_denda,0,',','.') ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">📅</span>
        <div class="stat-label">Periode</div>
        <div class="stat-value" style="font-size:1.1rem;"><?= date('M Y', strtotime($bulan.'-01')) ?></div>
    </div>
</div>

<div class="card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;">Detail Transaksi — <?= date('F Y', strtotime($bulan.'-01')) ?></h3>
    <table>
        <thead>
            <tr><th>#</th><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Denda</th></tr>
        </thead>
        <tbody>
            <?php $no=1; while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td style="color:var(--muted);"><?= $no++ ?></td>
                <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['kode_pinjam'] ?></code></td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($row['nama_anggota']) ?></div>
                    <div style="font-size:.75rem;color:var(--muted);"><?= $row['nis'] ?> · <?= $row['kelas'] ?></div>
                </td>
                <td style="font-size:.85rem;"><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                <td>
                    <?php if($row['status']=='dikembalikan'): ?><span class="badge badge-green">Dikembalikan</span>
                    <?php elseif($row['status']=='terlambat'): ?><span class="badge badge-red">Terlambat</span>
                    <?php else: ?><span class="badge badge-gold">Dipinjam</span><?php endif; ?>
                </td>
                <td><?= $row['denda'] > 0 ? '<span style="color:#ff7070;">Rp '.number_format($row['denda'],0,',','.').'</span>' : '<span style="color:var(--muted);">-</span>' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
