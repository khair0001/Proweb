<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reshina - Beranda</title>
  <!-- <link rel="stylesheet" href="../assets/css/dashboard.css"> -->
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../include/header.php'; ?>
      <!-- Categories Section -->
      <section class="categories-section">
        <div class="section-header">
          <h2>Kategori</h2>
          <a href="kategori.html" class="see-all">Lihat Semua</a>
        </div>
        <div class="categories-grid">
          <a href="search.html?category=elektronik" class="category-card">
            <div class="category-icon"><i class="fas fa-laptop"></i></div>
            <span>Elektronik</span>
          </a>
          <a href="search.html?category=fashion" class="category-card">
            <div class="category-icon"><i class="fas fa-tshirt"></i></div>
            <span>Fashion</span>
          </a>
          <a href="search.html?category=furniture" class="category-card">
            <div class="category-icon"><i class="fas fa-couch"></i></div>
            <span>Furniture</span>
          </a>
          <a href="search.html?category=otomotif" class="category-card">
            <div class="category-icon"><i class="fas fa-car"></i></div>
            <span>Otomotif</span>
          </a>
          <a href="search.html?category=hobi" class="category-card">
            <div class="category-icon"><i class="fas fa-gamepad"></i></div>
            <span>Hobi</span>
          </a>
          <a href="search.html?category=olahraga" class="category-card">
            <div class="category-icon"><i class="fas fa-volleyball-ball"></i></div>
            <span>Olahraga</span>
          </a>
          <a href="search.html?category=lainnya" class="category-card">
            <div class="category-icon"><i class="fas fa-ellipsis-h"></i></div>
            <span>Lainnya</span>
          </a>
        </div>
      </section>

      <!-- Latest Products Section -->
      <section class="products-section">
        <div class="section-header">
          <h2>Barang Terbaru</h2>
          <a href="search.html?sort=newest" class="see-all">Lihat Semua</a>
        </div>
        <div class="products-grid">
          <div class="product-card">
            <div class="product-badge new">Baru</div>
            <img src="../assets/image/nike.jpeg" alt="Sepatu Nike" class="product-image">
            <div class="product-details">
              <h3 class="product-title">Sepatu Nike Air Max</h3>
              <p class="product-price">Rp 800.000</p>
              <p class="product-location"><i class="fas fa-map-marker-alt"></i> Jakarta Selatan</p>
              <div class="product-meta">
                <span class="product-time"><i class="far fa-clock"></i> 2 jam yang lalu</span>
                <span class="product-type sell"><i class="fas fa-tag"></i> Jual</span>
              </div>
            </div>
          </div>
          <div class="product-card">
            <div class="product-badge donation">Donasi</div>
            <img src="../assets/image/buku.jpeg" alt="Buku Bekas" class="product-image">
            <div class="product-details">
              <h3 class="product-title">Koleksi Buku Pelajaran SD</h3>
              <p class="product-price">Gratis</p>
              <p class="product-location"><i class="fas fa-map-marker-alt"></i> Bandung</p>
              <div class="product-meta">
                <span class="product-time"><i class="far fa-clock"></i> 5 jam yang lalu</span>
                <span class="product-type donation"><i class="fas fa-hand-holding-heart"></i> Donasi</span>
              </div>
            </div>
          </div>
          <div class="product-card">
            <img src="../assets/image/rog.jpg" alt="Laptop Bekas" class="product-image">
            <div class="product-details">
              <h3 class="product-title">Laptop Asus ROG 2019</h3>
              <p class="product-price">Rp 5.500.000</p>
              <p class="product-location"><i class="fas fa-map-marker-alt"></i> Surabaya</p>
              <div class="product-meta">
                <span class="product-time"><i class="far fa-clock"></i> 1 hari yang lalu</span>
                <span class="product-type sell"><i class="fas fa-tag"></i> Jual</span>
              </div>
            </div>
          </div>
          <div class="product-card">
            <div class="product-badge auction">Lelang</div>
            <img src="../assets/image/ipon.jpg" alt="Jam Tangan" class="product-image">
            <div class="product-details">
              <h3 class="product-title">Iphone 20 Pro Max</h3>
              <p class="product-price">Mulai Rp 1.200.000</p>
              <p class="product-location"><i class="fas fa-map-marker-alt"></i> Yogyakarta</p>
              <div class="product-meta">
                <span class="product-time"><i class="far fa-clock"></i> 2 hari yang lalu</span>
                <span class="product-type auction"><i class="fas fa-gavel"></i> Lelang</span>
              </div>
            </div>
          </div>
        </div>
      </section>
      <?php include '../include/footer.php'; ?>
  <script src="../assets/js/script.js"></script>
</body>
</html>