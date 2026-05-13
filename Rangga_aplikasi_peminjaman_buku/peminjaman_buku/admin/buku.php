<?php
session_start();
include "../koneksi.php";
$page_title = "Data Buku";
include "../includes/navbar_admin.php";

$msg = "";

// DELETE
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id=$id");
    $msg = "success|Buku berhasil dihapus.";
}

// SIMPAN / UPDATE
if (isset($_POST['simpan'])) {
    $id          = (int)$_POST['id'];
    $kode_buku   = mysqli_real_escape_string($conn, trim($_POST['kode_buku']));
    $judul       = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $pengarang   = mysqli_real_escape_string($conn, trim($_POST['pengarang']));
    $penerbit    = mysqli_real_escape_string($conn, trim($_POST['penerbit']));
    $tahun       = (int)$_POST['tahun_terbit'];
    $kategori    = mysqli_real_escape_string($conn, trim($_POST['kategori']));
    $stok        = (int)$_POST['stok'];

    if ($id > 0) {
        mysqli_query($conn, "UPDATE buku SET kode_buku='$kode_buku',judul='$judul',pengarang='$pengarang',penerbit='$penerbit',tahun_terbit=$tahun,kategori='$kategori',stok=$stok WHERE id=$id");
        $msg = "success|Data buku berhasil diperbarui.";
    } else {
        $cek = mysqli_query($conn, "SELECT id FROM buku WHERE kode_buku='$kode_buku'");
        if (mysqli_num_rows($cek) > 0) {
            $msg = "error|Kode buku sudah ada!";
        } else {
            mysqli_query($conn, "INSERT INTO buku (kode_buku,judul,pengarang,penerbit,tahun_terbit,kategori,stok) VALUES ('$kode_buku','$judul','$pengarang','$penerbit',$tahun,'$kategori',$stok)");
            $msg = "success|Buku baru berhasil ditambahkan.";
        }
    }
}

// EDIT - ambil data
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM buku WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

// SEARCH
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where  = $search ? "WHERE judul LIKE '%$search%' OR kode_buku LIKE '%$search%' OR pengarang LIKE '%$search%'" : '';
$query  = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY id DESC");

[$msg_type, $msg_text] = $msg ? explode('|', $msg, 2) : ['', ''];
?>
<div class="topbar">
    <div>
        <h1>Data Buku</h1>
        <div class="breadcrumb">Kelola koleksi buku perpustakaan</div>
    </div>
</div>
<div class="content">

<?php if($msg_text): ?>
<div class="alert alert-<?= $msg_type=='success'?'ok':'err' ?>"><?= $msg_type=='success'?'✅':'⚠️' ?> <?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<!-- Form Tambah/Edit -->
<div class="card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;"><?= $edit_data ? '✏️ Edit Buku' : '➕ Tambah Buku Baru' ?></h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? 0 ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Kode Buku</label>
                <input type="text" name="kode_buku" value="<?= htmlspecialchars($edit_data['kode_buku'] ?? '') ?>" placeholder="Contoh: BK007" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="kategori" value="<?= htmlspecialchars($edit_data['kategori'] ?? '') ?>" placeholder="Teknologi, Sains, dll">
            </div>
        </div>
        <div class="form-group">
            <label>Judul Buku</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($edit_data['judul'] ?? '') ?>" placeholder="Judul lengkap buku" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pengarang</label>
                <input type="text" name="pengarang" value="<?= htmlspecialchars($edit_data['pengarang'] ?? '') ?>" placeholder="Nama pengarang">
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" value="<?= htmlspecialchars($edit_data['penerbit'] ?? '') ?>" placeholder="Nama penerbit">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" value="<?= $edit_data['tahun_terbit'] ?? date('Y') ?>" min="1900" max="2026">
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $edit_data['stok'] ?? 1 ?>" min="0" required>
            </div>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" name="simpan" class="btn btn-primary"><?= $edit_data ? 'Update Buku' : 'Simpan Buku' ?></button>
            <?php if($edit_data): ?><a href="buku.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Batal</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Buku -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:1rem;font-weight:600;">Daftar Buku</h3>
        <span style="font-size:.82rem;color:var(--muted);"><?= mysqli_num_rows($query) ?> buku ditemukan</span>
    </div>
    <form method="GET" class="search-box">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul, kode, atau pengarang...">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if($search): ?><a href="buku.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Reset</a><?php endif; ?>
    </form>
    <table>
        <thead>
            <tr><th>Kode</th><th>Judul</th><th>Pengarang</th><th>Kategori</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['kode_buku'] ?></code></td>
                <td style="font-weight:500;"><?= htmlspecialchars($row['judul']) ?></td>
                <td style="color:var(--muted);font-size:.85rem;"><?= htmlspecialchars($row['pengarang']) ?></td>
                <td><span class="badge badge-blue"><?= htmlspecialchars($row['kategori']) ?></span></td>
                <td><?= $row['tahun_terbit'] ?></td>
                <td>
                    <span class="badge <?= $row['stok'] > 0 ? 'badge-green' : 'badge-red' ?>"><?= $row['stok'] ?></span>
                </td>
                <td style="display:flex;gap:8px;">
                    <a href="buku.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background:rgba(201,169,110,.15);border:1px solid rgba(201,169,110,.3);color:var(--gold);">Edit</a>
                    <a href="buku.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
