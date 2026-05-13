<?php
session_start();
include "../koneksi.php";
$page_title = "Pengembalian";
include "../includes/navbar_admin.php";

$msg = "";
$detail = null;

// Proses pengembalian
if (isset($_POST['proses'])) {
    $id = (int)$_POST['pinjam_id'];
    $tgl_kembali = date('Y-m-d');
    
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id=$id"));
    
    // Hitung denda (Rp 1.000/hari terlambat)
    $tgl_seharusnya = strtotime($data['tgl_kembali']);
    $tgl_sekarang   = strtotime($tgl_kembali);
    $selisih        = max(0, ($tgl_sekarang - $tgl_seharusnya) / 86400);
    $denda          = (int)$selisih * 1000;
    
    mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', tgl_pengembalian='$tgl_kembali', denda=$denda WHERE id=$id");
    mysqli_query($conn, "UPDATE buku SET stok=stok+1 WHERE id=".(int)$data['buku_id']);
    
    $msg = "success|Pengembalian berhasil dicatat. Denda: Rp " . number_format($denda, 0, ',', '.');
}

// Ambil detail jika dari link
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $detail = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, a.nama AS nama_anggota, a.nis, a.kelas, b.judul, b.kode_buku
        FROM peminjaman p
        JOIN anggota a ON p.anggota_id=a.id
        JOIN buku b ON p.buku_id=b.id
        WHERE p.id=$id AND p.status != 'dikembalikan'
    "));
}

// Riwayat pengembalian
$query = mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.nis, b.judul
    FROM peminjaman p
    JOIN anggota a ON p.anggota_id=a.id
    JOIN buku b ON p.buku_id=b.id
    WHERE p.status='dikembalikan'
    ORDER BY p.tgl_pengembalian DESC LIMIT 20
");

[$msg_type, $msg_text] = $msg ? explode('|', $msg, 2) : ['', ''];
?>
<div class="topbar">
    <div>
        <h1>Pengembalian Buku</h1>
        <div class="breadcrumb">Proses pengembalian dan perhitungan denda</div>
    </div>
</div>
<div class="content">
<?php if($msg_text): ?>
<div class="alert alert-<?= $msg_type=='success'?'ok':'err' ?>"><?= $msg_type=='success'?'✅':'⚠️' ?> <?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<?php if($detail): ?>
<div class="card" style="border-color:var(--gold);">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;color:var(--gold);">📦 Konfirmasi Pengembalian</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div>
            <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;margin-bottom:4px;">Kode Pinjam</div>
            <div style="font-weight:600;color:var(--gold);"><?= $detail['kode_pinjam'] ?></div>
        </div>
        <div>
            <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;margin-bottom:4px;">Anggota</div>
            <div style="font-weight:600;"><?= htmlspecialchars($detail['nama_anggota']) ?></div>
            <div style="font-size:.8rem;color:var(--muted);"><?= $detail['nis'] ?> - <?= $detail['kelas'] ?></div>
        </div>
        <div>
            <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;margin-bottom:4px;">Buku</div>
            <div style="font-weight:600;"><?= htmlspecialchars($detail['judul']) ?></div>
        </div>
        <div>
            <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;margin-bottom:4px;">Tanggal Pinjam → Kembali</div>
            <div style="font-weight:600;"><?= date('d/m/Y', strtotime($detail['tgl_pinjam'])) ?> → <?= date('d/m/Y', strtotime($detail['tgl_kembali'])) ?></div>
            <?php
            $sisa = (strtotime($detail['tgl_kembali']) - strtotime(date('Y-m-d'))) / 86400;
            if($sisa < 0): ?>
            <div style="color:#ff7070;font-size:.82rem;margin-top:4px;">⚠️ Terlambat <?= abs((int)$sisa) ?> hari — Denda: Rp <?= number_format(abs((int)$sisa)*1000,0,',','.') ?></div>
            <?php else: ?>
            <div style="color:#4dbd74;font-size:.82rem;margin-top:4px;">✅ Sisa <?= (int)$sisa ?> hari</div>
            <?php endif; ?>
        </div>
    </div>
    <form method="POST">
        <input type="hidden" name="pinjam_id" value="<?= $detail['id'] ?>">
        <div style="display:flex;gap:12px;">
            <button type="submit" name="proses" class="btn btn-primary" onclick="return confirm('Konfirmasi pengembalian buku ini?')">✅ Konfirmasi Pengembalian</button>
            <a href="peminjaman.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Batal</a>
        </div>
    </form>
</div>
<?php else: ?>
<div class="card" style="background:rgba(201,169,110,.05);border:1px solid rgba(201,169,110,.2);">
    <p style="color:var(--muted);font-size:.9rem;">Pilih transaksi dari halaman <a href="peminjaman.php" style="color:var(--gold);">Peminjaman</a> untuk memproses pengembalian.</p>
</div>
<?php endif; ?>

<div class="card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;">📋 Riwayat Pengembalian</h3>
    <table>
        <thead>
            <tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Tgl Dikembalikan</th><th>Denda</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['kode_pinjam'] ?></code></td>
                <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                <td style="font-size:.85rem;"><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                <td><?= $row['tgl_pengembalian'] ? date('d/m/Y', strtotime($row['tgl_pengembalian'])) : '-' ?></td>
                <td>
                    <?php if($row['denda'] > 0): ?>
                    <span class="badge badge-red">Rp <?= number_format($row['denda'],0,',','.') ?></span>
                    <?php else: ?>
                    <span class="badge badge-green">Rp 0</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
