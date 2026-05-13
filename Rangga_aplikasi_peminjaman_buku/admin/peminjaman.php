<?php
session_start();
include "../koneksi.php";
$page_title = "Peminjaman";
include "../includes/navbar_admin.php";

$msg = "";

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Kembalikan stok
    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT buku_id FROM peminjaman WHERE id=$id"));
    if($p) mysqli_query($conn, "UPDATE buku SET stok=stok+1 WHERE id=".(int)$p['buku_id']);
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id=$id");
    $msg = "success|Data peminjaman dihapus.";
}

// SIMPAN
if (isset($_POST['simpan'])) {
    $anggota_id  = (int)$_POST['anggota_id'];
    $buku_id     = (int)$_POST['buku_id'];
    $tgl_pinjam  = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];

    // Cek stok
    $stok_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id=$buku_id"));
    if ($stok_row['stok'] < 1) {
        $msg = "error|Stok buku habis, tidak bisa dipinjam!";
    } else {
        $kode = 'PJM-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        mysqli_query($conn, "INSERT INTO peminjaman (kode_pinjam,anggota_id,buku_id,tgl_pinjam,tgl_kembali,status)
            VALUES ('$kode',$anggota_id,$buku_id,'$tgl_pinjam','$tgl_kembali','dipinjam')");
        mysqli_query($conn, "UPDATE buku SET stok=stok-1 WHERE id=$buku_id");
        $msg = "success|Peminjaman berhasil dicatat! Kode: $kode";
    }
}

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where  = $search ? "WHERE a.nama LIKE '%$search%' OR p.kode_pinjam LIKE '%$search%' OR b.judul LIKE '%$search%'" : '';

$query = mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.nis, b.judul, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.anggota_id=a.id
    JOIN buku b ON p.buku_id=b.id
    $where ORDER BY p.id DESC
");

$anggota_list = mysqli_query($conn, "SELECT id, nis, nama, kelas FROM anggota ORDER BY nama");
$buku_list    = mysqli_query($conn, "SELECT id, kode_buku, judul, stok FROM buku WHERE stok>0 ORDER BY judul");

[$msg_type, $msg_text] = $msg ? explode('|', $msg, 2) : ['', ''];
?>
<div class="topbar">
    <div>
        <h1>Peminjaman Buku</h1>
        <div class="breadcrumb">Kelola transaksi peminjaman</div>
    </div>
</div>
<div class="content">
<?php if($msg_text): ?>
<div class="alert alert-<?= $msg_type=='success'?'ok':'err' ?>"><?= $msg_type=='success'?'✅':'⚠️' ?> <?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<div class="card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;">➕ Tambah Peminjaman</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Anggota</label>
                <select name="anggota_id" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php while($a = mysqli_fetch_assoc($anggota_list)): ?>
                    <option value="<?= $a['id'] ?>"><?= $a['nis'] ?> - <?= htmlspecialchars($a['nama']) ?> (<?= $a['kelas'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Buku</label>
                <select name="buku_id" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while($b = mysqli_fetch_assoc($buku_list)): ?>
                    <option value="<?= $b['id'] ?>"><?= $b['kode_buku'] ?> - <?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
            </div>
        </div>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan Peminjaman</button>
    </form>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:1rem;font-weight:600;">Riwayat Peminjaman</h3>
    </div>
    <form method="GET" class="search-box">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama anggota, kode pinjam, atau judul buku...">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if($search): ?><a href="peminjaman.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Reset</a><?php endif; ?>
    </form>
    <table>
        <thead>
            <tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)):
                $today = date('Y-m-d');
                // auto-update terlambat
                if($row['status']=='dipinjam' && $row['tgl_kembali'] < $today) {
                    mysqli_query($conn,"UPDATE peminjaman SET status='terlambat' WHERE id=".$row['id']);
                    $row['status']='terlambat';
                }
            ?>
            <tr>
                <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['kode_pinjam'] ?></code></td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($row['nama_anggota']) ?></div>
                    <div style="font-size:.76rem;color:var(--muted);"><?= $row['nis'] ?></div>
                </td>
                <td style="font-size:.85rem;"><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                <td>
                    <?php if($row['status']=='dikembalikan'): ?><span class="badge badge-green">Dikembalikan</span>
                    <?php elseif($row['status']=='terlambat'): ?><span class="badge badge-red">Terlambat</span>
                    <?php else: ?><span class="badge badge-gold">Dipinjam</span><?php endif; ?>
                </td>
                <td>
                    <?php if($row['status'] != 'dikembalikan'): ?>
                    <a href="pengembalian.php?id=<?= $row['id'] ?>" class="btn btn-sm" style="background:rgba(77,189,116,.15);border:1px solid rgba(77,189,116,.3);color:#4dbd74;">Kembalikan</a>
                    <?php endif; ?>
                    <a href="peminjaman.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
