<?php
// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php?redirect=settings.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine which form was submitted
    if (isset($_POST['update_account'])) {
        // Process account update
        $result = updateUserAccount($_POST, $_FILES, $userId);
    } elseif (isset($_POST['update_location'])) {
        // Process location update
        $result = updateUserLocation($_POST, $userId);
    } elseif (isset($_POST['update_payment'])) {
        // Process payment update
        $result = updateUserPayment($_POST, $userId);
    }
    
    if (isset($result)) {
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            
            // Refresh user data
            $user = getUserById($userId);
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}

// Page title
$pageTitle = "Pengaturan";

// Active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'account';
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
    <link rel="stylesheet" href="../assets/css/settings.css">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="settings-page">
        <div class="container">
            <h1 class="page-title"><span>Pengaturan</span> Akun</h1>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="settings-container">
                <div class="settings-sidebar">
                    <a href="?tab=account" class="settings-tab <?php echo $activeTab == 'account' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Akun
                    </a>
                    <a href="?tab=location" class="settings-tab <?php echo $activeTab == 'location' ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt"></i> Lokasi
                    </a>
                    <a href="?tab=payment" class="settings-tab <?php echo $activeTab == 'payment' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i> Pembayaran
                    </a>
                    <a href="profile.php" class="settings-tab">
                        <i class="fas fa-arrow-left"></i> Kembali ke Profil
                    </a>
                </div>
                
                <div class="settings-content">
                    <!-- Account Settings Tab -->
                    <div class="settings-tab-content <?php echo $activeTab == 'account' ? 'active' : ''; ?>" id="account-tab">
                        <h2 class="settings-section-title">Informasi Akun</h2>
                        
                        <form action="settings.php?tab=account" method="post" enctype="multipart/form-data">
                            <div class="form-group profile-image-group">
                                <label>Foto Profil</label>
                                <div class="profile-image-container">
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <img src="<?php echo $user['profile_image']; ?>" alt="<?php echo $user['name']; ?>" id="profile-image-preview">
                                    <?php else: ?>
                                        <div class="avatar-placeholder" id="profile-image-preview">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="profile-image-upload">
                                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                                        <label for="profile_image" class="btn btn-outline">
                                            <i class="fas fa-camera"></i> Ubah Foto
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Nomor Telepon</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Masukkan untuk mengubah password">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password Baru</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_account" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Location Settings Tab -->
                    <div class="settings-tab-content <?php echo $activeTab == 'location' ? 'active' : ''; ?>" id="location-tab">
                        <h2 class="settings-section-title">Informasi Lokasi</h2>
                        
                        <form action="settings.php?tab=location" method="post">
                            <div class="form-group">
                                <label for="address">Alamat Lengkap</label>
                                <textarea id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">Kota</label>
                                    <input type="text" id="city" name="city" value="<?php echo $user['city']; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="province">Provinsi</label>
                                    <input type="text" id="province" name="province" value="<?php echo $user['province']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="postal_code">Kode Pos</label>
                                    <input type="text" id="postal_code" name="postal_code" value="<?php echo $user['postal_code']; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="country">Negara</label>
                                    <input type="text" id="country" name="country" value="<?php echo $user['country'] ?? 'Indonesia'; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="button" class="btn btn-outline use-my-location">
                                    <i class="fas fa-map-marker-alt"></i> Gunakan Lokasi Saya
                                </button>
                            </div>
                            
                            <div class="location-map">
                                <div id="map" style="height: 300px; border-radius: 10px;"></div>
                                <input type="hidden" id="latitude" name="latitude" value="<?php echo $user['latitude']; ?>">
                                <input type="hidden" id="longitude" name="longitude" value="<?php echo $user['longitude']; ?>">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_location" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Payment Settings Tab -->
                    <div class="settings-tab-content <?php echo $activeTab == 'payment' ? 'active' : ''; ?>" id="payment-tab">
                        <h2 class="settings-section-title">Informasi Pembayaran</h2>
                        
                        <form action="settings.php?tab=payment" method="post">
                            <div class="payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" <?php echo ($user['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>>
                                    <label for="bank_transfer">
                                        <i class="fas fa-university"></i>
                                        <span>Transfer Bank</span>
                                    </label>
                                </div>
                                
                                <div class="payment-method">
                                    <input type="radio" id="e_wallet" name="payment_method" value="e_wallet" <?php echo ($user['payment_method'] == 'e_wallet') ? 'checked' : ''; ?>>
                                    <label for="e_wallet">
                                        <i class="fas fa-wallet"></i>
                                        <span>E-Wallet</span>
                                    </label>
                                </div>
                                
                                <div class="payment-method">
                                    <input type="radio" id="cash" name="payment_method" value="cash" <?php echo ($user['payment_method'] == 'cash') ? 'checked' : ''; ?>>
                                    <label for="cash">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Tunai (COD)</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Bank Transfer Details -->
                            <div class="payment-details" id="bank_transfer_details" style="<?php echo ($user['payment_method'] != 'bank_transfer') ? 'display: none;' : ''; ?>">
                                <div class="form-group">
                                    <label for="bank_name">Nama Bank</label>
                                    <input type="text" id="bank_name" name="bank_name" value="<?php echo $user['bank_name']; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="account_number">Nomor Rekening</label>
                                    <input type="text" id="account_number" name="account_number" value="<?php echo $user['account_number']; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="account_holder">Nama Pemilik Rekening</label>
                                    <input type="text" id="account_holder" name="account_holder" value="<?php echo $user['account_holder']; ?>">
                                </div>
                            </div>
                            
                            <!-- E-Wallet Details -->
                            <div class="payment-details" id="e_wallet_details" style="<?php echo ($user['payment_method'] != 'e_wallet') ? 'display: none;' : ''; ?>">
                                <div class="form-group">
                                    <label for="wallet_provider">Penyedia E-Wallet</label>
                                    <select id="wallet_provider" name="wallet_provider">
                                        <option value="">Pilih Penyedia</option>
                                        <option value="gopay" <?php echo ($user['wallet_provider'] == 'gopay') ? 'selected' : ''; ?>>GoPay</option>
                                        <option value="ovo" <?php echo ($user['wallet_provider'] == 'ovo') ? 'selected' : ''; ?>>OVO</option>
                                        <option value="dana" <?php echo ($user['wallet_provider'] == 'dana') ? 'selected' : ''; ?>>DANA</option>
                                        <option value="linkaja" <?php echo ($user['wallet_provider'] == 'linkaja') ? 'selected' : ''; ?>>LinkAja</option>
                                        <option value="shopeepay" <?php echo ($user['wallet_provider'] == 'shopeepay') ? 'selected' : ''; ?>>ShopeePay</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="wallet_number">Nomor E-Wallet</label>
                                    <input type="text" id="wallet_number" name="wallet_number" value="<?php echo $user['wallet_number']; ?>">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_payment" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/settings.js"></script>
    <!-- Include map API for location tab -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
</body>
</html>
