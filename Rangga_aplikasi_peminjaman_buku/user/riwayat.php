<?php
session_start();
include "../koneksi.php";
$page_title = "Riwayat Saya";
include "../includes/navbar_user.php";

$query = mysqli_query($conn, "
    SELECT p.*, b.judul, b.kode_buku, b.pengarang
    FROM peminjaman p JOIN buku b ON p.buku_id=b.id
    WHERE p.anggota_id=$anggota_id
    ORDER BY p.created_at DESC
");
?>
<div class="topbar">
    <div>
        <h1>Riwayat Peminjaman</h1>
        <div class="breadcrumb">Semua transaksi peminjaman buku Anda</div>
    </div>
</div>
<div class="content">
<div class="card">
    <table>
        <thead>
            <tr><th>Kode</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Tgl Dikembalikan</th><th>Status</th><th>Denda</th></tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query)==0): ?>
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">Belum ada riwayat peminjaman</td></tr>
            <?php endif; ?>
            <?php while($row = mysqli_fetch_assoc($query)):
                // Auto update status terlambat
                if($row['status']=='dipinjam' && $row['tgl_kembali'] < date('Y-m-d')) {
                    mysqli_query($conn,"UPDATE peminjaman SET status='terlambat' WHERE id=".$row['id']);
                    $row['status']='terlambat';
                }
            ?>
            <tr>
                <td><code style="color:var(--gold);font-size:.78rem;"><?= $row['kode_pinjam'] ?></code></td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($row['judul']) ?></div>
                    <div style="font-size:.75rem;color:var(--muted);"><?= $row['pengarang'] ?></div>
                </td>
                <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                <td><?= $row['tgl_pengembalian'] ? date('d/m/Y', strtotime($row['tgl_pengembalian'])) : '<span style="color:var(--muted)">-</span>' ?></td>
                <td>
                    <?php if($row['status']=='dikembalikan'): ?><span class="badge badge-green">Dikembalikan</span>
                    <?php elseif($row['status']=='terlambat'): ?><span class="badge badge-red">Terlambat</span>
                    <?php else: ?><span class="badge badge-gold">Dipinjam</span><?php endif; ?>
                </td>
                <td>
                    <?= $row['denda'] > 0 ? '<span style="color:#ff7070;font-weight:600;">Rp '.number_format($row['denda'],0,',','.').'</span>' : '<span style="color:var(--muted)">-</span>' ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
