<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Galeri Foto - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background-color:#a9ddf1;font-family: 'Poppins', sans-serif;overflow-x:hidden}
    .gallery-title{text-align:center;margin:40px 0;color:#1e3a5f;font-weight:700;text-shadow:2px 2px 4px rgba(0,0,0,0.2)}
    .btn-tambah{display:block;margin:0 auto 30px;padding:10px 25px;font-weight:600;box-shadow:0 3px 6px rgba(0,0,0,0.2);transition:.3s}
    .btn-tambah:hover{transform:scale(1.05);box-shadow:0 5px 10px rgba(0,0,0,0.3)}
    .gallery-item{border-radius:15px;overflow:hidden;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,0.2);transition:transform .4s ease,box-shadow .4s ease}
    .gallery-item:hover{transform:scale(1.03);box-shadow:0 8px 20px rgba(0,0,0,0.3)}
    .gallery-item img{width:100%;height:230px;object-fit:cover;transition:transform .5s ease}
    .gallery-item:hover img{transform:scale(1.08)}
    .image-caption{padding:15px;text-align:center;background:#fff}
    .image-caption h5{margin:5px 0;font-weight:600;color:#000}
    .image-caption p{color:#6c757d;font-size:14px;margin-bottom:10px}
    footer{background:#343a40;color:#fff;text-align:center;padding:15px;margin-top:30px}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Galeri Foto</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Kontak</a></li>
      </ul>
      <span class="navbar-text text-white me-2">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?></span>
      <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>
<div class="container my-4">
  <h1 class="gallery-title">GALERI PESONA</h1>
  <button type="button" class="btn btn-success btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Foto</button>
  <div class="row justify-content-center">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM foto ORDER BY id_foto DESC");
    if (!$result) {
        echo '<div class="text-center text-danger">Terjadi kesalahan: ' . mysqli_error($conn) . '</div>';
    } elseif (mysqli_num_rows($result) == 0) {
        echo '<div class="text-center text-muted mt-3">Belum ada foto yang ditambahkan.</div>';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = (int)$row['id_foto'];
            $judul = htmlspecialchars($row['judul_foto']);
            $lokasi = htmlspecialchars($row['lokasi_foto']);
            $file = htmlspecialchars($row['lokasi_file']); 
    ?>
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="gallery-item">
        <img src="<?= $file ?>" alt="<?= $judul ?>">
        <div class="image-caption">
          <h5><?= $judul ?></h5>
          <p><?= $lokasi ?></p>
          <div class="btn-group" role="group">
            <a href="<?= $file ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $id ?>">Edit</button>
            <a href="../proses/hapus_foto.php?id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus foto ini?')">Hapus</a>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modalEdit<?= $id ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $id ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="../proses/edit_foto.php" method="post" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel<?= $id ?>">Edit Foto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id_foto" value="<?= $id ?>">
              <label class="form-label">Foto Saat Ini:</label>
              <div class="text-center mb-3">
                <img id="preview_old_<?= $id ?>" src="<?= $file ?>" alt="" class="img-thumbnail" style="max-height:180px; object-fit:cover;">
              </div>
              <div class="mb-3">
                <label class="form-label">Ganti Foto (opsional)</label>
                <input type="file" name="lokasi_file" class="form-control" accept="image/*" onchange="previewImage(this, <?= $id ?>)">
              </div>
              <div id="preview_new_container_<?= $id ?>" class="text-center mb-3" style="display:none;">
                <label class="form-label">Preview Foto Baru:</label>
                <img id="preview_new_<?= $id ?>" class="img-thumbnail" style="max-height:180px; object-fit:cover;">
              </div>
              <div class="mb-3">
                <label class="form-label">Judul Foto</label>
                <input type="text" name="judul_foto" class="form-control" required value="<?= $judul ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" name="lokasi_foto" class="form-control" value="<?= $lokasi ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi_foto" class="form-control" rows="3"><?= htmlspecialchars($row['deskripsi_foto']) ?></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
        } 
    }
    ?>
  </div>
</div>
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="../proses/tambah_foto.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahLabel">Tambah Foto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Judul Foto</label>
            <input type="text" name="judul_foto" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi_foto" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi_foto" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Pilih Foto (jpg, png, jpeg)</label>
            <input type="file" name="lokasi_file" class="form-control" accept="image/*" onchange="previewImage(this, 'tambah')" required>
          </div>
          <div id="preview_new_container_tambah" class="text-center mb-3" style="display:none;">
            <label class="form-label">Preview Foto Baru:</label>
            <img id="preview_new_tambah" class="img-thumbnail" style="max-height:180px; object-fit:cover;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="tambah" class="btn btn-success">Tambah Foto</button>
        </div>
      </form>
    </div>
  </div>
</div>
<footer>
  &copy; <?= date('Y') ?> Galeri Foto
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input, id) {
  const file = input.files && input.files[0];
  const previewOld = document.getElementById('preview_old_' + id);
  const previewNewContainer = document.getElementById('preview_new_container_' + id) || document.getElementById('preview_new_container_' + id);
  const previewNew = document.getElementById('preview_new_' + id) || document.getElementById('preview_new_' + id);
  const previewNewT = document.getElementById('preview_new_tambah');
  const previewNewContainerT = document.getElementById('preview_new_container_tambah');
  if (id === 'tambah') {
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        previewNewT.src = e.target.result;
        previewNewContainerT.style.display = 'block';
      }
      reader.readAsDataURL(file);
    } else {
      previewNewContainerT.style.display = 'none';
    }
    return;
  }
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      if (previewNew) {
        previewNew.src = e.target.result;
        previewNewContainer.style.display = 'block';
      }
      if (previewOld) previewOld.style.opacity = '0.3';
    }
    reader.readAsDataURL(file);
  } else {
    if (previewNewContainer) previewNewContainer.style.display = 'none';
    if (previewOld) previewOld.style.opacity = '1';
  }
}
</script>
</body>
</html>