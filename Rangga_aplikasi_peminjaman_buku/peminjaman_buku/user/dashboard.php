<?php
session_start();
include "../koneksi.php";
$page_title = "Beranda";
include "../includes/navbar_user.php";

$sedang_pinjam  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id AND status='dipinjam'"))[0];
$terlambat      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id AND status='terlambat'"))[0];
$total_pinjam   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE anggota_id=$anggota_id"))[0];

$aktif = mysqli_query($conn, "
    SELECT p.*, b.judul, b.pengarang, b.kode_buku
    FROM peminjaman p JOIN buku b ON p.buku_id=b.id
    WHERE p.anggota_id=$anggota_id AND p.status IN ('dipinjam','terlambat')
    ORDER BY p.tgl_kembali ASC
");
?>
<div class="topbar">
    <div>
        <h1>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</h1>
        <div class="breadcrumb"><?= $anggota_data['kelas'] ?? '' ?> · <?= date('d F Y') ?></div>
    </div>
</div>
<div class="content">

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Sedang Dipinjam</div>
        <div class="stat-value" style="color:var(--gold);"><?= $sedang_pinjam ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Terlambat</div>
        <div class="stat-value" style="color:#e05555;"><?= $terlambat ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Peminjaman</div>
        <div class="stat-value"><?= $total_pinjam ?></div>
    </div>
</div>

<?php if($terlambat > 0): ?>
<div class="alert alert-err">⚠️ Anda memiliki <strong><?= $terlambat ?></strong> buku yang terlambat dikembalikan! Segera kembalikan ke perpustakaan.</div>
<?php endif; ?>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <h3 style="font-size:.98rem;font-weight:600;">📋 Buku Yang Sedang Dipinjam</h3>
        <a href="pinjam.php" class="btn btn-primary btn-sm">+ Pinjam Buku</a>
    </div>
    <?php if(mysqli_num_rows($aktif) == 0): ?>
    <div style="text-align:center;padding:40px;color:var(--muted);">
        <div style="font-size:3rem;margin-bottom:12px;">📭</div>
        <div>Belum ada buku yang dipinjam</div>
        <a href="katalog.php" style="color:var(--gold);text-decoration:none;font-size:.88rem;margin-top:8px;display:inline-block;">Lihat katalog buku →</a>
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($aktif)):
                $sisa = (strtotime($row['tgl_kembali']) - time()) / 86400;
            ?>
            <tr>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($row['judul']) ?></div>
                    <div style="font-size:.76rem;color:var(--muted);"><?= $row['kode_buku'] ?></div>
                </td>
                <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                <td>
                    <?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?>
                    <?php if($sisa < 0): ?>
                    <div style="font-size:.74rem;color:#ff7070;">Telat <?= abs((int)$sisa) ?> hari</div>
                    <?php elseif($sisa <= 2): ?>
                    <div style="font-size:.74rem;color:#e0a855;">Sisa <?= (int)$sisa ?> hari</div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['status']=='terlambat'): ?><span class="badge badge-red">Terlambat</span>
                    <?php else: ?><span class="badge badge-gold">Dipinjam</span><?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</div>
</div></body></html>
