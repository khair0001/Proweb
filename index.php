<?php
// Include database connection and functions
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get featured items
$featuredItems = getItems('', ['per_page' => 6]);

// Get nearby items if user has location
$nearbyItems = [];
if (isset($_SESSION['user_lat']) && isset($_SESSION['user_lng'])) {
    $filters = [
        'latitude' => $_SESSION['user_lat'],
        'longitude' => $_SESSION['user_lng'],
        'distance' => 10, // 10km radius
        'per_page' => 6
    ];
    $nearbyItems = getItems('', $filters);
}

// Get categories
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reshina - Ubah Barang Tak Terpakai Jadi Berkah</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container" style="display: flex; align-items: center;">
            <div class="hero-content">
                <h1 class="hero-title">Ubah Barang Tak Terpakai <span>Jadi Berkah</span></h1>
                <p class="hero-subtitle">Jual, donasikan, atau lelang barang bekasmu dengan mudah. Bantu lingkungan, bantu sesama.</p>
                <div class="hero-buttons">
                    <a href="pages/buy-claim.php" class="btn btn-primary">Jelajahi Barang</a>
                    <a href="pages/sell-donate.php" class="btn btn-outline">Mulai Donasi Sekarang</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="assets/images/catler.jpg" alt="Orang memilah barang bekas">
            </div>
        </div>
        
        <!-- Geometric shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </section>
    
    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2 class="section-title">Mengapa <span>Reshina</span>?</h2>
            
            <div class="about-cards">
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3 class="about-card-title">Kurangi Sampah, Selamatkan Bumi</h3>
                    <p class="about-card-text">Dengan menjual atau mendaur ulang barang bekas, kamu berkontribusi langsung pada pengurangan sampah dan pelestarian lingkungan.</p>
                </div>
                
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="about-card-title">Barang Layak, Manfaat Berlipat</h3>
                    <p class="about-card-text">Barang yang tidak terpakai di rumahmu bisa jadi sangat berharga bagi orang lain. Berikan manfaat berlipat pada setiap barang.</p>
                </div>
                
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="about-card-title">Jembatan Kebaikan Antar Pengguna</h3>
                    <p class="about-card-text">Kami menghubungkan orang yang ingin membantu dengan yang membutuhkan. Jadilah bagian dari komunitas peduli.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Fitur <span>Utama</span></h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3 class="feature-title">Jual Barang Bekas</h3>
                    <p class="feature-description">Jual barang bekas layak pakaimu dengan mudah dan cepat.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3 class="feature-title">Donasi dengan Mudah</h3>
                    <p class="feature-description">Donasikan barangmu kepada yang membutuhkan dengan sistem yang transparan.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="feature-title">Lelang Barang Langka</h3>
                    <p class="feature-description">Lelang barang koleksi atau langka kepada penawar tertinggi.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="feature-title">Kirim ke Gudang / COD</h3>
                    <p class="feature-description">Pilih pengiriman yang paling nyaman untuk transaksimu.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="feature-title">Pencarian Lokasi Terdekat</h3>
                    <p class="feature-description">Temukan barang atau donasi terdekat dari lokasimu saat ini.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <h2 class="section-title">Cari Barang <span>di Sekitarmu</span></h2>
            
            <div class="search-container">
                <i class="fas fa-map-marker-alt location-icon"></i>
                <input type="text" class="search-bar" placeholder="Cari barang bekas di sekitar kamu...">
                <button class="search-button">Temukan Sekarang</button>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Kategori Barang <span>Populer</span></h2>
            
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                    <div class="category-icon">
                        <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                    </div>
                    <p class="category-name"><?php echo htmlspecialchars($category['name']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Featured Items Section -->
    <?php if (!empty($featuredItems)): ?>
    <section class="featured-items">
        <div class="container">
            <h2 class="section-title">Barang <span>Pilihan</span></h2>
            
            <div class="items-grid">
                <?php foreach ($featuredItems as $item): ?>
                <div class="item-card <?php echo htmlspecialchars($item['listing_type']); ?>">
                    <div class="item-badge">
                        <?php if ($item['listing_type'] == 'sale'): ?>
                            <span class="badge sale">Dijual</span>
                        <?php elseif ($item['listing_type'] == 'auction'): ?>
                            <span class="badge auction">Lelang</span>
                        <?php else: ?>
                            <span class="badge donation">Donasi</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>
                    
                    <div class="item-details">
                        <h3 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        
                        <?php if ($item['listing_type'] == 'sale'): ?>
                            <p class="item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        <?php elseif ($item['listing_type'] == 'auction'): ?>
                            <p class="item-price">Mulai Rp <?php echo number_format($item['starting_bid'], 0, ',', '.'); ?></p>
                            <p class="auction-ends">Berakhir: <?php echo formatDate($item['end_date']); ?></p>
                        <?php else: ?>
                            <p class="item-price donation-text">Gratis (Donasi)</p>
                        <?php endif; ?>
                        
                        <p class="item-location">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?php echo htmlspecialchars($item['city']); ?>
                        </p>
                    </div>
                    
                    <div class="item-actions">
                        <a href="pages/item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="view-more-container">
                <a href="pages/buy-claim.php" class="btn btn-outline">Lihat Semua Barang</a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Nearby Items Section -->
    <?php if (!empty($nearbyItems)): ?>
    <section class="nearby-items">
        <div class="container">
            <h2 class="section-title">Barang <span>Terdekat</span></h2>
            
            <div class="items-grid">
                <?php foreach ($nearbyItems as $item): ?>
                <div class="item-card <?php echo htmlspecialchars($item['listing_type']); ?>">
                    <div class="item-badge">
                        <?php if ($item['listing_type'] == 'sale'): ?>
                            <span class="badge sale">Dijual</span>
                        <?php elseif ($item['listing_type'] == 'auction'): ?>
                            <span class="badge auction">Lelang</span>
                        <?php else: ?>
                            <span class="badge donation">Donasi</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>
                    
                    <div class="item-details">
                        <h3 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        
                        <?php if ($item['listing_type'] == 'sale'): ?>
                            <p class="item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        <?php elseif ($item['listing_type'] == 'auction'): ?>
                            <p class="item-price">Mulai Rp <?php echo number_format($item['starting_bid'], 0, ',', '.'); ?></p>
                            <p class="auction-ends">Berakhir: <?php echo formatDate($item['end_date']); ?></p>
                        <?php else: ?>
                            <p class="item-price donation-text">Gratis (Donasi)</p>
                        <?php endif; ?>
                        
                        <p class="item-location">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?php echo htmlspecialchars($item['city']); ?>
                            <?php if (isset($item['distance'])): ?>
                                <span class="distance">(<?php echo round($item['distance'], 1); ?> km)</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="item-actions">
                        <a href="pages/item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="view-more-container">
                <a href="pages/buy-claim.php?nearby=1" class="btn btn-outline">Lihat Semua Barang Terdekat</a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Cerita <span>Pengguna</span></h2>
            
            <div class="testimonial-card">
                <p class="testimonial-quote">Lemari bekas saya laku dalam 2 hari, dan saya bantu orang lain yang membutuhkan! Platformnya sangat mudah digunakan dan transaksi berjalan lancar.</p>
                <div class="testimonial-author">
                    <span class="testimonial-name">Sari,</span>
                    <span class="testimonial-location">Yogyakarta</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include_once 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
