<?php
session_start();
include "../koneksi.php";
$page_title = "Kelola Anggota";
include "../includes/navbar_admin.php";

$msg = "";

// DELETE
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM anggota WHERE id=$id");
    $msg = "success|Anggota berhasil dihapus.";
}

// SIMPAN/UPDATE
if (isset($_POST['simpan'])) {
    $id       = (int)$_POST['id'];
    $nis      = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $kelas    = mysqli_real_escape_string($conn, trim($_POST['kelas']));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $telepon  = mysqli_real_escape_string($conn, trim($_POST['telepon']));

    if ($id > 0) {
        mysqli_query($conn, "UPDATE anggota SET nis='$nis',nama='$nama',kelas='$kelas',alamat='$alamat',telepon='$telepon' WHERE id=$id");
        $msg = "success|Data anggota berhasil diperbarui.";
    } else {
        mysqli_query($conn, "INSERT INTO anggota (nis,nama,kelas,alamat,telepon) VALUES ('$nis','$nama','$kelas','$alamat','$telepon')");
        $msg = "success|Anggota baru berhasil ditambahkan.";
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE id=$id"));
}

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where  = $search ? "WHERE nama LIKE '%$search%' OR nis LIKE '%$search%' OR kelas LIKE '%$search%'" : '';
$query  = mysqli_query($conn, "SELECT * FROM anggota $where ORDER BY id DESC");

[$msg_type, $msg_text] = $msg ? explode('|', $msg, 2) : ['', ''];
?>
<div class="topbar">
    <div>
        <h1>Kelola Anggota</h1>
        <div class="breadcrumb">Manajemen data anggota perpustakaan</div>
    </div>
</div>
<div class="content">
<?php if($msg_text): ?>
<div class="alert alert-<?= $msg_type=='success'?'ok':'err' ?>"><?= $msg_type=='success'?'✅':'⚠️' ?> <?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<div class="card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:20px;"><?= $edit_data ? '✏️ Edit Anggota' : '➕ Tambah Anggota' ?></h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? 0 ?>">
        <div class="form-row">
            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="nis" value="<?= htmlspecialchars($edit_data['nis'] ?? '') ?>" placeholder="Nomor Induk Siswa" required>
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <input type="text" name="kelas" value="<?= htmlspecialchars($edit_data['kelas'] ?? '') ?>" placeholder="Contoh: XII RPL 1" required>
            </div>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" placeholder="Nama lengkap anggota" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telepon" value="<?= htmlspecialchars($edit_data['telepon'] ?? '') ?>" placeholder="Nomor telepon">
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" value="<?= htmlspecialchars($edit_data['alamat'] ?? '') ?>" placeholder="Alamat lengkap">
            </div>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" name="simpan" class="btn btn-primary"><?= $edit_data ? 'Update Anggota' : 'Simpan Anggota' ?></button>
            <?php if($edit_data): ?><a href="anggota.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Batal</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:1rem;font-weight:600;">Daftar Anggota</h3>
        <span style="font-size:.82rem;color:var(--muted);"><?= mysqli_num_rows($query) ?> anggota</span>
    </div>
    <form method="GET" class="search-box">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama, NIS, atau kelas...">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if($search): ?><a href="anggota.php" class="btn" style="background:rgba(255,255,255,.06);border:1px solid var(--border);">Reset</a><?php endif; ?>
    </form>
    <table>
        <thead>
            <tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><code style="color:var(--gold);font-size:.8rem;"><?= $row['nis'] ?></code></td>
                <td style="font-weight:500;"><?= htmlspecialchars($row['nama']) ?></td>
                <td><span class="badge badge-blue"><?= htmlspecialchars($row['kelas']) ?></span></td>
                <td style="color:var(--muted);font-size:.85rem;"><?= $row['telepon'] ?: '-' ?></td>
                <td style="color:var(--muted);font-size:.85rem;"><?= htmlspecialchars($row['alamat'] ?: '-') ?></td>
                <td style="display:flex;gap:8px;">
                    <a href="anggota.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background:rgba(201,169,110,.15);border:1px solid rgba(201,169,110,.3);color:var(--gold);">Edit</a>
                    <a href="anggota.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus anggota ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</div></body></html>
