<?php
session_start();
include "../koneksi.php";
$page_title = "Pinjam Buku";
include "../includes/navbar_user.php";

$msg = "";

// Cek anggota valid
if (!$anggota_id) {
    $msg = "error|Data anggota Anda tidak ditemukan. Hubungi admin perpustakaan.";
}

// Proses pinjam
if (isset($_POST['pinjam']) && $anggota_id) {
    $buku_id    = (int)$_POST['buku_id'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime('+7 days'));

    // Cek sudah meminjam buku ini?
    $cek_dup = mysqli_query($conn, "SELECT id FROM peminjaman WHERE anggota_id=$anggota_id AND buku_id=$buku_id AND status='dipinjam'");
    if (mysqli_num_rows($cek_dup) > 0) {
        $msg = "error|Anda sudah meminjam buku ini!";
    } else {
        $stok_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id=$buku_id"));
        if ($stok_row['stok'] < 1) {
            $msg = "error|Stok buku habis!";
        } else {
            $kode = 'PJM-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            mysqli_query($conn, "INSERT INTO peminjaman (kode_pinjam,anggota_id,buku_id,tgl_pinjam,tgl_kembali,status)
                VALUES ('$kode',$anggota_id,$buku_id,'$tgl_pinjam','$tgl_kembali','dipinjam')");
            mysqli_query($conn, "UPDATE buku SET stok=stok-1 WHERE id=$buku_id");
            $msg = "success|Peminjaman berhasil! Kode: $kode. Kembalikan sebelum " . date('d/m/Y', strtotime($tgl_kembali));
        }
    }
}

$buku_list = mysqli_query($conn, "SELECT * FROM buku WHERE stok>0 ORDER BY judul");

// Pre-select buku dari katalog
$preselect = isset($_GET['buku_id']) ? (int)$_GET['buku_id'] : 0;

[$msg_type, $msg_text] = $msg ? explode('|', $msg, 2) : ['', ''];
?>
<div class="topbar">
    <div>
        <h1>Pinjam Buku</h1>
        <div class="breadcrumb">Ajukan peminjaman buku perpustakaan</div>
    </div>
</div>
<div class="content">
<?php if($msg_text): ?>
<div class="alert alert-<?= $msg_type=='success'?'ok':'err' ?>"><?= $msg_type=='success'?'✅':'⚠️' ?> <?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<div class="card" style="<?= $msg_type=='success'?'border-color:rgba(77,189,116,.3)':'' ?>">
    <h3 style="font-size:.98rem;font-weight:600;margin-bottom:20px;">📋 Form Peminjaman Buku</h3>
    <div style="background:rgba(201,169,110,.06);border:1px solid rgba(201,169,110,.15);border-radius:10px;padding:14px;margin-bottom:20px;font-size:.85rem;color:var(--muted);">
        ℹ️ Masa pinjam 7 hari. Denda keterlambatan Rp 1.000/hari. Maksimal 3 buku bersamaan.
    </div>
    <?php if($anggota_id): ?>
    <form method="POST">
        <div class="form-group">
            <label>Pilih Buku</label>
            <select name="buku_id" required onchange="updateInfo(this)">
                <option value="">-- Pilih buku yang ingin dipinjam --</option>
                <?php while($b = mysqli_fetch_assoc($buku_list)): ?>
                <option value="<?= $b['id'] ?>" <?= $preselect==$b['id']?'selected':'' ?> data-judul="<?= htmlspecialchars($b['judul']) ?>" data-pengarang="<?= htmlspecialchars($b['pengarang']) ?>" data-stok="<?= $b['stok'] ?>">
                    <?= $b['kode_buku'] ?> — <?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div id="buku-info" style="display:none;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:16px;font-size:.86rem;">
            <div id="info-judul" style="font-weight:600;margin-bottom:4px;"></div>
            <div id="info-pengarang" style="color:var(--muted);"></div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="text" value="<?= date('d/m/Y') ?>" disabled style="color:var(--muted);">
            </div>
            <div class="form-group">
                <label>Batas Kembali</label>
                <input type="text" value="<?= date('d/m/Y', strtotime('+7 days')) ?>" disabled style="color:var(--gold);">
            </div>
        </div>
        <button type="submit" name="pinjam" class="btn btn-primary">📋 Ajukan Peminjaman</button>
    </form>
    <?php else: ?>
    <div style="color:#ff7070;font-size:.9rem;">Akun Anda belum terdaftar sebagai anggota. Hubungi admin perpustakaan.</div>
    <?php endif; ?>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="font-size:.95rem;font-weight:600;">📌 Peminjaman Aktif Anda</h3>
        <a href="riwayat.php" style="font-size:.82rem;color:var(--gold);text-decoration:none;">Lihat semua riwayat →</a>
    </div>
    <?php
    $aktif = mysqli_query($conn, "
        SELECT p.*, b.judul, b.kode_buku FROM peminjaman p JOIN buku b ON p.buku_id=b.id
        WHERE p.anggota_id=$anggota_id AND p.status IN ('dipinjam','terlambat') ORDER BY p.tgl_kembali ASC
    ");
    if(mysqli_num_rows($aktif)==0): ?>
    <p style="color:var(--muted);font-size:.88rem;">Tidak ada peminjaman aktif.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Buku</th><th>Batas Kembali</th><th>Status</th></tr></thead>
        <tbody>
            <?php while($r=mysqli_fetch_assoc($aktif)):?>
            <tr>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($r['judul']) ?></div>
                    <div style="font-size:.75rem;color:var(--muted);"><?= $r['kode_pinjam'] ?></div>
                </td>
                <td><?= date('d/m/Y',strtotime($r['tgl_kembali'])) ?></td>
                <td><?= $r['status']=='terlambat'?'<span class="badge badge-red">Terlambat</span>':'<span class="badge badge-gold">Dipinjam</span>' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</div>
</div>
<script>
function updateInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('buku-info');
    if (sel.value) {
        document.getElementById('info-judul').textContent = opt.dataset.judul;
        document.getElementById('info-pengarang').textContent = 'Pengarang: ' + opt.dataset.pengarang + ' · Stok tersedia: ' + opt.dataset.stok;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
// Auto trigger if pre-selected
window.onload = function() {
    const sel = document.querySelector('select[name=buku_id]');
    if(sel) updateInfo(sel);
}
</script>
</body></html>
