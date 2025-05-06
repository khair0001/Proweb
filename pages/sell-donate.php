<?php
// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php?redirect=sell-donate.php");
    exit();
}

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission
    $result = processItemListing($_POST, $_FILES);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}

// Page title
$pageTitle = "Jual & Donasi Barang";
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
    <link rel="stylesheet" href="../assets/css/sell-donate.css">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="sell-donate-page">
        <div class="container">
            <h1 class="page-title">Jual & Donasi <span>Barang</span></h1>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form action="sell-donate.php" method="post" enctype="multipart/form-data" id="listing-form">
                    <!-- Listing Type Selection -->
                    <div class="form-section">
                        <h2 class="section-title">Jenis Listing</h2>
                        <div class="listing-type-selector">
                            <div class="listing-type-option">
                                <input type="radio" name="listing_type" id="type-sale" value="sale" checked>
                                <label for="type-sale">
                                    <i class="fas fa-tag"></i>
                                    <span>Jual</span>
                                </label>
                            </div>
                            <div class="listing-type-option">
                                <input type="radio" name="listing_type" id="type-auction" value="auction">
                                <label for="type-auction">
                                    <i class="fas fa-gavel"></i>
                                    <span>Lelang</span>
                                </label>
                            </div>
                            <div class="listing-type-option">
                                <input type="radio" name="listing_type" id="type-donation" value="donation">
                                <label for="type-donation">
                                    <i class="fas fa-heart"></i>
                                    <span>Donasi</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Item Details -->
                    <div class="form-section">
                        <h2 class="section-title">Detail Barang</h2>
                        
                        <div class="form-group">
                            <label for="title">Judul Barang <span class="required">*</span></label>
                            <input type="text" id="title" name="title" required placeholder="Contoh: Laptop Asus ROG 2020 Bekas">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Kategori <span class="required">*</span></label>
                            <select id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="elektronik">Elektronik</option>
                                <option value="fashion">Fashion</option>
                                <option value="buku">Buku</option>
                                <option value="mainan">Mainan</option>
                                <option value="dapur">Dapur</option>
                                <option value="furnitur">Furnitur</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group price-group">
                            <label for="price">Harga <span class="required">*</span></label>
                            <div class="price-input">
                                <span class="currency">Rp</span>
                                <input type="number" id="price" name="price" required placeholder="0" min="0">
                            </div>
                        </div>
                        
                        <div class="form-group auction-group" style="display: none;">
                            <label for="auction_end">Tanggal Berakhir Lelang <span class="required">*</span></label>
                            <input type="datetime-local" id="auction_end" name="auction_end">
                        </div>
                        
                        <div class="form-group">
                            <label for="condition">Kondisi Barang <span class="required">*</span></label>
                            <select id="condition" name="condition" required>
                                <option value="">Pilih Kondisi</option>
                                <option value="baru">Baru (90-100%)</option>
                                <option value="sangat_bagus">Sangat Bagus (80-90%)</option>
                                <option value="bagus">Bagus (70-80%)</option>
                                <option value="cukup_bagus">Cukup Bagus (50-70%)</option>
                                <option value="kurang_bagus">Kurang Bagus (dibawah 50%)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Deskripsi Barang <span class="required">*</span></label>
                            <textarea id="description" name="description" rows="5" required placeholder="Ceritakan detail barang, seperti ukuran, warna, bahan, usia pakai, alasan menjual, dll."></textarea>
                        </div>
                    </div>
                    
                    <!-- Item Images -->
                    <div class="form-section">
                        <h2 class="section-title">Foto Barang</h2>
                        
                        <div class="image-upload-container">
                            <div class="image-upload-box main-image">
                                <input type="file" name="main_image" id="main_image" accept="image/*" required>
                                <label for="main_image">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Foto Utama</span>
                                    <small>Klik untuk mengunggah</small>
                                </label>
                                <div class="image-preview" id="main-image-preview"></div>
                            </div>
                            
                            <div class="additional-images">
                                <div class="image-upload-box">
                                    <input type="file" name="additional_images[]" id="additional_image_1" accept="image/*">
                                    <label for="additional_image_1">
                                        <i class="fas fa-plus"></i>
                                        <span>Tambah Foto</span>
                                    </label>
                                    <div class="image-preview" id="preview-1"></div>
                                </div>
                                
                                <div class="image-upload-box">
                                    <input type="file" name="additional_images[]" id="additional_image_2" accept="image/*">
                                    <label for="additional_image_2">
                                        <i class="fas fa-plus"></i>
                                        <span>Tambah Foto</span>
                                    </label>
                                    <div class="image-preview" id="preview-2"></div>
                                </div>
                                
                                <div class="image-upload-box">
                                    <input type="file" name="additional_images[]" id="additional_image_3" accept="image/*">
                                    <label for="additional_image_3">
                                        <i class="fas fa-plus"></i>
                                        <span>Tambah Foto</span>
                                    </label>
                                    <div class="image-preview" id="preview-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Options -->
                    <div class="form-section">
                        <h2 class="section-title">Opsi Pengiriman</h2>
                        
                        <div class="shipping-options">
                            <div class="shipping-option">
                                <input type="checkbox" id="pickup_home" name="pickup_home" value="1" checked>
                                <label for="pickup_home">
                                    <i class="fas fa-home"></i>
                                    <span>Simpan di Rumah</span>
                                    <small>Pembeli datang ke alamat Anda</small>
                                </label>
                            </div>
                            
                            <div class="shipping-option">
                                <input type="checkbox" id="pickup_warehouse" name="pickup_warehouse" value="1">
                                <label for="pickup_warehouse">
                                    <i class="fas fa-warehouse"></i>
                                    <span>Kirim ke Gudang</span>
                                    <small>Kami akan menyimpan barang Anda</small>
                                </label>
                            </div>
                            
                            <div class="shipping-option">
                                <input type="checkbox" id="cod" name="cod" value="1">
                                <label for="cod">
                                    <i class="fas fa-handshake"></i>
                                    <span>COD (Bertemu Langsung)</span>
                                    <small>Bertemu di tempat yang disepakati</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group warehouse-address" style="display: none;">
                            <label for="warehouse_id">Pilih Gudang</label>
                            <select id="warehouse_id" name="warehouse_id">
                                <option value="">Pilih Gudang Terdekat</option>
                                <option value="1">Gudang Jakarta Pusat</option>
                                <option value="2">Gudang Jakarta Selatan</option>
                                <option value="3">Gudang Jakarta Barat</option>
                                <option value="4">Gudang Jakarta Timur</option>
                                <option value="5">Gudang Jakarta Utara</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="form-section">
                        <h2 class="section-title">Lokasi Barang</h2>
                        
                        <div class="form-group">
                            <label for="location">Alamat <span class="required">*</span></label>
                            <input type="text" id="location" name="location" required placeholder="Masukkan alamat lengkap">
                        </div>
                        
                        <div class="form-group">
                            <label for="city">Kota <span class="required">*</span></label>
                            <input type="text" id="city" name="city" required placeholder="Masukkan kota">
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-outline use-my-location">
                                <i class="fas fa-map-marker-alt"></i> Gunakan Lokasi Saya
                            </button>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-check"></i> Pasang Barang Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/sell-donate.js"></script>
</body>
</html>
