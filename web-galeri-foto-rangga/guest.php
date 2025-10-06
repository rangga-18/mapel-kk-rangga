<?php 
session_start(); 

if (!isset($_SESSION['username'])) { 
  header("Location: index.php"); 
  exit();
} 
?> 

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Galeri Pesona Kalimantan Utara</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #9ec8f3; }
    .gallery-card {
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform .3s;
    }
    .gallery-card:hover { transform: scale(1.03); }
    .gallery-card img { height: 200px; object-fit: cover; }
    .btn-blue { background-color: blue; color: white; }
    .modal-title { width: 100%; text-align: center; } /* judul modal di tengah */
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Galeri Foto</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Kontak</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text me-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Galeri -->
<div class="container my-5">
  <h2 class="text-center mb-4">Galeri Pesona Kalimantan Utara</h2>
  <div class="row g-4">

    <!-- Card 1 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="pantai amal.jpg" class="card-img-top" alt="Pantai Amal" />
        <div class="card-body text-center">
          <h5 class="card-title">Pantai Amal (Tarakan)</h5>
          <p class="card-text">Pantai indah dengan sunset menawan.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal1">Lihat</button>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="putih.jpg" class="card-img-top" alt="Gunung Putih" />
        <div class="card-body text-center">
          <h5 class="card-title">Gunung Putih (Tanjung Palas)</h5>
          <p class="card-text">Bukit kapur bersejarah nan eksotis.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal2">Lihat</button>
        </div>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="pulau bunyu.jpg" class="card-img-top" alt="Pulau Sebatik" />
        <div class="card-body text-center">
          <h5 class="card-title">Pulau Sebatik (Nunukan)</h5>
          <p class="card-text">Pulau perbatasan dengan panorama laut.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal3">Lihat</button>
        </div>
      </div>
    </div>

    <!-- Card 4 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="taman nasional .jpg" class="card-img-top" alt="Taman Nasional Kayan Mentarang" />
        <div class="card-body text-center">
          <h5 class="card-title">Taman Nasional Kayan Mentarang (Malinau)</h5>
          <p class="card-text">Hutan tropis luas, rumah flora fauna langka.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal4">Lihat</button>
        </div>
      </div>
    </div>

    <!-- Card 5 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="air terjun somolon.jpg" class="card-img-top" alt="Air Terjun Semolon" />
        <div class="card-body text-center">
          <h5 class="card-title">Air Terjun Semolon (Malinau)</h5>
          <p class="card-text">Air terjun unik dengan aliran dingin & hangat.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal5">Lihat</button>
        </div>
      </div>
    </div>

    <!-- Card 6 -->
    <div class="col-md-4">
      <div class="card gallery-card">
        <img src="air terjun gunung rian.jpg" class="card-img-top" alt="Air Terjun Gunung Rian" />
        <div class="card-body text-center">
          <h5 class="card-title">Air Terjun Gunung Rian (Tanah Tidung)</h5>
          <p class="card-text">Air terjun setinggi ±90m dengan suasana alami.</p>
          <button class="btn btn-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modal6">Lihat</button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal 1 -->
<div class="modal fade" id="modal1" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pantai Amal (Tarakan)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="pantai amal.jpg" class="img-fluid rounded mb-3">
        <p>Pantai indah dengan sunset menawan.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 2 -->
<div class="modal fade" id="modal2" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Gunung Putih (Tanjung Palas)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="putih.jpg" class="img-fluid rounded mb-3">
        <p>Bukit kapur bersejarah nan eksotis.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 3 -->
<div class="modal fade" id="modal3" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pulau Sebatik (Nunukan)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="pulau bunyu.jpg" class="img-fluid rounded mb-3">
        <p>Pulau perbatasan dengan panorama laut.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 4 -->
<div class="modal fade" id="modal4" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Taman Nasional Kayan Mentarang (Malinau)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="taman nasional .jpg" class="img-fluid rounded mb-3">
        <p>Hutan tropis luas, rumah flora fauna langka.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 5 -->
<div class="modal fade" id="modal5" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Air Terjun Semolon (Malinau)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="air terjun somolon.jpg" class="img-fluid rounded mb-3">
        <p>Air terjun unik dengan aliran dingin & hangat.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 6 -->
<div class="modal fade" id="modal6" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Air Terjun Gunung Rian (Tanah Tidung)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="air terjun gunung rian.jpg" class="img-fluid rounded mb-3">
        <p>Air terjun setinggi ±90m dengan suasana alami.</p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
