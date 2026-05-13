<?php
session_start();
include "../koneksi.php";
$page_title = "Dashboard";
include "../includes/navbar_admin.php";

// Statistik
$total_buku    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM buku"))[0];
$total_anggota = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM anggota"))[0];
$total_pinjam  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'"))[0];
$total_terlambat = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='terlambat'"))[0];

// Peminjaman terbaru
$query_terbaru = mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.nis, b.judul
    FROM peminjaman p
    JOIN anggota a ON p.anggota_id = a.id
    JOIN buku b ON p.buku_id = b.id
    ORDER BY p.created_at DESC LIMIT 8
");
?>
<div class="topbar">
    <div>
        <h1>Dashboard</h1>
        <div class="breadcrumb">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</div>
    </div>
    <div style="font-size:.82rem;color:var(--muted);"><?= date('l, d F Y') ?></div>
</div>
<div class="content">

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-icon">📚</span>
            <div class="stat-label">Total Buku</div>
            <div class="stat-value"><?= $total_buku ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">👥</span>
            <div class="stat-label">Total Anggota</div>
            <div class="stat-value"><?= $total_anggota ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">📋</span>
            <div class="stat-label">Sedang Dipinjam</div>
            <div class="stat-value" style="color:var(--gold)"><?= $total_pinjam ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⚠️</span>
            <div class="stat-label">Terlambat</div>
            <div class="stat-value" style="color:#e05555"><?= $total_terlambat ?></div>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:1rem;font-weight:600;">Peminjaman Terbaru</h3>
            <a href="peminjaman.php" class="btn btn-primary btn-sm">Lihat Semua</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Kode Pinjam</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query_terbaru)): ?>
                <tr>
                    <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['kode_pinjam'] ?></code></td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($row['nama_anggota']) ?></div>
                        <div style="font-size:.76rem;color:var(--muted);"><?= $row['nis'] ?></div>
                    </td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                    <td>
                        <?php
                        if($row['status']=='dikembalikan') echo '<span class="badge badge-green">Dikembalikan</span>';
                        elseif($row['status']=='terlambat') echo '<span class="badge badge-red">Terlambat</span>';
                        else echo '<span class="badge badge-gold">Dipinjam</span>';
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</body>
</html>
