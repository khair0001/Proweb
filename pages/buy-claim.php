<?php
// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Get category filter if provided
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Get items based on filter
$items = getItems($category);

// Page title
$pageTitle = "Beli & Klaim Barang";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Reshina</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/buy-claim.css">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="buy-claim-page">
        <div class="container">
            <h1 class="page-title">Beli & Klaim <span>Barang</span></h1>
            
            <!-- Filters Section -->
            <section class="filters-section">
                <div class="filter-container">
                    <div class="filter-group">
                        <h3>Jenis Listing</h3>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="listing_type" value="sale" checked>
                                <span class="checkmark"></span>
                                Dijual
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="listing_type" value="auction" checked>
                                <span class="checkmark"></span>
                                Lelang
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="listing_type" value="donation" checked>
                                <span class="checkmark"></span>
                                Donasi
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Kategori</h3>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="all" checked>
                                <span class="checkmark"></span>
                                Semua Kategori
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="elektronik">
                                <span class="checkmark"></span>
                                Elektronik
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="fashion">
                                <span class="checkmark"></span>
                                Fashion
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="buku">
                                <span class="checkmark"></span>
                                Buku
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="mainan">
                                <span class="checkmark"></span>
                                Mainan
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="dapur">
                                <span class="checkmark"></span>
                                Dapur
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="furnitur">
                                <span class="checkmark"></span>
                                Furnitur
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Lokasi</h3>
                        <div class="location-slider">
                            <label>Jarak (km): <span id="distance-value">10</span></label>
                            <input type="range" min="1" max="50" value="10" class="slider" id="distance-slider">
                        </div>
                        <button class="btn btn-primary use-my-location">
                            <i class="fas fa-map-marker-alt"></i> Gunakan Lokasi Saya
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Pengiriman</h3>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="shipping" value="all" checked>
                                <span class="checkmark"></span>
                                Semua Opsi
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="shipping" value="warehouse">
                                <span class="checkmark"></span>
                                Ambil di Gudang
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="shipping" value="home">
                                <span class="checkmark"></span>
                                Ambil di Rumah Penjual
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="shipping" value="cod">
                                <span class="checkmark"></span>
                                COD (Bertemu Langsung)
                            </label>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary apply-filters">Terapkan Filter</button>
                </div>
            </section>
            
            <!-- Items Grid Section -->
            <section class="items-section">
                <div class="items-header">
                    <h2>Hasil Pencarian</h2>
                    <div class="sort-options">
                        <label for="sort-by">Urutkan: </label>
                        <select id="sort-by">
                            <option value="newest">Terbaru</option>
                            <option value="price-low">Harga: Rendah ke Tinggi</option>
                            <option value="price-high">Harga: Tinggi ke Rendah</option>
                            <option value="distance">Jarak Terdekat</option>
                        </select>
                    </div>
                </div>
                
                <div class="items-grid">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="item-card <?php echo $item['listing_type']; ?>">
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
                                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>">
                                </div>
                                
                                <div class="item-details">
                                    <h3 class="item-title"><?php echo $item['title']; ?></h3>
                                    
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
                                        <?php echo $item['location']; ?> 
                                        <?php if (isset($item['distance'])): ?>
                                            <span class="distance">(<?php echo $item['distance']; ?> km)</span>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <div class="item-shipping">
                                        <?php if ($item['pickup_warehouse']): ?>
                                            <span class="shipping-option"><i class="fas fa-warehouse"></i> Gudang</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['pickup_home']): ?>
                                            <span class="shipping-option"><i class="fas fa-home"></i> Rumah</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['cod']): ?>
                                            <span class="shipping-option"><i class="fas fa-handshake"></i> COD</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="item-actions">
                                    <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Lihat Detail</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-items">
                            <i class="fas fa-search"></i>
                            <h3>Tidak ada barang ditemukan</h3>
                            <p>Coba ubah filter pencarian atau cari barang lainnya</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <div class="pagination">
                    <a href="#" class="page-link active">1</a>
                    <a href="#" class="page-link">2</a>
                    <a href="#" class="page-link">3</a>
                    <a href="#" class="page-link">4</a>
                    <a href="#" class="page-link">5</a>
                    <a href="#" class="page-link next"><i class="fas fa-angle-right"></i></a>
                </div>
            </section>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/buy-claim.js"></script>
</body>
</html>
