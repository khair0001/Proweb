<?php
// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php?redirect=profile.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Get user's items
$userItems = getUserItems($userId);

// Get user's donations
$userDonations = getUserDonations($userId);

// Get user's purchase history
$purchaseHistory = getUserPurchases($userId);

// Get user's reviews
$reviews = getUserReviews($userId);

// Page title
$pageTitle = "Profil Pengguna";

// Active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'items';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - ReCycleIt</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="profile-page">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo $user['profile_image']; ?>" alt="<?php echo $user['name']; ?>">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo $user['name']; ?></h1>
                    <p class="profile-location"><i class="fas fa-map-marker-alt"></i> <?php echo $user['city']; ?></p>
                    <p class="profile-member-since">Anggota sejak: <?php echo formatDate($user['created_at']); ?></p>
                    
                    <div class="profile-stats">
                        <div class="stat">
                            <span class="stat-value"><?php echo count($userItems); ?></span>
                            <span class="stat-label">Barang</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo count($userDonations); ?></span>
                            <span class="stat-label">Donasi</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo count($purchaseHistory); ?></span>
                            <span class="stat-label">Pembelian</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo count($reviews); ?></span>
                            <span class="stat-label">Ulasan</span>
                        </div>
                    </div>
                    
                    <a href="settings.php" class="btn btn-outline">
                        <i class="fas fa-cog"></i> Edit Profil
                    </a>
                </div>
            </div>
            
            <div class="profile-tabs">
                <a href="?tab=items" class="tab <?php echo $activeTab == 'items' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Barang Saya
                </a>
                <a href="?tab=donations" class="tab <?php echo $activeTab == 'donations' ? 'active' : ''; ?>">
                    <i class="fas fa-heart"></i> Donasi Saya
                </a>
                <a href="?tab=purchases" class="tab <?php echo $activeTab == 'purchases' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Riwayat Beli
                </a>
                <a href="?tab=reviews" class="tab <?php echo $activeTab == 'reviews' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> Ulasan
                </a>
            </div>
            
            <div class="profile-content">
                <!-- My Items Tab -->
                <div class="tab-content <?php echo $activeTab == 'items' ? 'active' : ''; ?>" id="items-tab">
                    <div class="tab-header">
                        <h2>Barang Saya</h2>
                        <a href="sell-donate.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Barang Baru
                        </a>
                    </div>
                    
                    <?php if (!empty($userItems)): ?>
                        <div class="items-grid">
                            <?php foreach ($userItems as $item): ?>
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
                                        
                                        <p class="item-status">
                                            <?php if ($item['status'] == 'active'): ?>
                                                <span class="status active">Aktif</span>
                                            <?php elseif ($item['status'] == 'sold'): ?>
                                                <span class="status sold">Terjual</span>
                                            <?php elseif ($item['status'] == 'donated'): ?>
                                                <span class="status donated">Terdonasi</span>
                                            <?php else: ?>
                                                <span class="status inactive">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-outline btn-sm delete-item" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>Belum Ada Barang</h3>
                            <p>Anda belum memiliki barang yang dijual atau dilelang</p>
                            <a href="sell-donate.php" class="btn btn-primary">Mulai Jual Barang</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- My Donations Tab -->
                <div class="tab-content <?php echo $activeTab == 'donations' ? 'active' : ''; ?>" id="donations-tab">
                    <div class="tab-header">
                        <h2>Donasi Saya</h2>
                        <a href="sell-donate.php" class="btn btn-primary">
                            <i class="fas fa-heart"></i> Donasi Baru
                        </a>
                    </div>
                    
                    <?php if (!empty($userDonations)): ?>
                        <div class="items-grid">
                            <?php foreach ($userDonations as $donation): ?>
                                <div class="item-card donation">
                                    <div class="item-badge">
                                        <span class="badge donation">Donasi</span>
                                    </div>
                                    
                                    <div class="item-image">
                                        <img src="<?php echo $donation['image_url']; ?>" alt="<?php echo $donation['title']; ?>">
                                    </div>
                                    
                                    <div class="item-details">
                                        <h3 class="item-title"><?php echo $donation['title']; ?></h3>
                                        <p class="item-price donation-text">Gratis (Donasi)</p>
                                        
                                        <p class="item-status">
                                            <?php if ($donation['status'] == 'active'): ?>
                                                <span class="status active">Aktif</span>
                                            <?php elseif ($donation['status'] == 'donated'): ?>
                                                <span class="status donated">Terdonasi</span>
                                            <?php else: ?>
                                                <span class="status inactive">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <?php if ($donation['status'] == 'donated'): ?>
                                            <p class="donation-recipient">
                                                <i class="fas fa-user"></i> Diterima oleh: <?php echo $donation['recipient_name']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <a href="item-detail.php?id=<?php echo $donation['id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        <?php if ($donation['status'] == 'active'): ?>
                                            <a href="edit-item.php?id=<?php echo $donation['id']; ?>" class="btn btn-outline btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn btn-outline btn-sm delete-item" data-id="<?php echo $donation['id']; ?>">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-heart"></i>
                            <h3>Belum Ada Donasi</h3>
                            <p>Anda belum memiliki barang yang didonasikan</p>
                            <a href="sell-donate.php" class="btn btn-primary">Mulai Donasi</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Purchase History Tab -->
                <div class="tab-content <?php echo $activeTab == 'purchases' ? 'active' : ''; ?>" id="purchases-tab">
                    <div class="tab-header">
                        <h2>Riwayat Pembelian</h2>
                    </div>
                    
                    <?php if (!empty($purchaseHistory)): ?>
                        <div class="purchase-list">
                            <?php foreach ($purchaseHistory as $purchase): ?>
                                <div class="purchase-card">
                                    <div class="purchase-image">
                                        <img src="<?php echo $purchase['image_url']; ?>" alt="<?php echo $purchase['title']; ?>">
                                    </div>
                                    
                                    <div class="purchase-details">
                                        <h3 class="purchase-title"><?php echo $purchase['title']; ?></h3>
                                        
                                        <p class="purchase-price">
                                            Rp <?php echo number_format($purchase['price'], 0, ',', '.'); ?>
                                        </p>
                                        
                                        <p class="purchase-date">
                                            <i class="fas fa-calendar"></i> Dibeli pada: <?php echo formatDate($purchase['purchase_date']); ?>
                                        </p>
                                        
                                        <p class="purchase-seller">
                                            <i class="fas fa-user"></i> Penjual: <?php echo $purchase['seller_name']; ?>
                                        </p>
                                        
                                        <p class="purchase-status">
                                            <?php if ($purchase['status'] == 'completed'): ?>
                                                <span class="status completed">Selesai</span>
                                            <?php elseif ($purchase['status'] == 'processing'): ?>
                                                <span class="status processing">Diproses</span>
                                            <?php elseif ($purchase['status'] == 'cancelled'): ?>
                                                <span class="status cancelled">Dibatalkan</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    
                                    <div class="purchase-actions">
                                        <a href="item-detail.php?id=<?php echo $purchase['item_id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Barang
                                        </a>
                                        
                                        <?php if ($purchase['status'] == 'completed' && !$purchase['has_review']): ?>
                                            <a href="add-review.php?purchase_id=<?php echo $purchase['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-star"></i> Beri Ulasan
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Belum Ada Pembelian</h3>
                            <p>Anda belum melakukan pembelian barang</p>
                            <a href="buy-claim.php" class="btn btn-primary">Jelajahi Barang</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-content <?php echo $activeTab == 'reviews' ? 'active' : ''; ?>" id="reviews-tab">
                    <div class="tab-header">
                        <h2>Ulasan Saya</h2>
                    </div>
                    
                    <?php if (!empty($reviews)): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="review-item-image">
                                        <img src="<?php echo $review['item_image']; ?>" alt="<?php echo $review['item_title']; ?>">
                                    </div>
                                    
                                    <div class="review-content">
                                        <h3 class="review-item-title"><?php echo $review['item_title']; ?></h3>
                                        
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                        
                                        <p class="review-text"><?php echo $review['review_text']; ?></p>
                                        
                                        <p class="review-date">
                                            <i class="fas fa-calendar"></i> Diulas pada: <?php echo formatDate($review['created_at']); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="review-actions">
                                        <a href="edit-review.php?id=<?php echo $review['id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-outline btn-sm delete-review" data-id="<?php echo $review['id']; ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-star"></i>
                            <h3>Belum Ada Ulasan</h3>
                            <p>Anda belum memberikan ulasan untuk pembelian</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/profile.js"></script>
</body>
</html>
