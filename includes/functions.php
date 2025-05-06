<?php
/**
 * Functions Library
 * 
 * Contains all helper functions for ReCycleIt application
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * User Authentication Functions
 */

/**
 * Register a new user
 * 
 * @param array $userData User registration data
 * @return array Result with success status and message
 */
function registerUser($userData) {
    global $conn;
    
    try {
        // Validate input
        if (empty($userData['name']) || empty($userData['email']) || empty($userData['password'])) {
            return ['success' => false, 'message' => 'Semua field wajib diisi'];
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $userData['email']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }
        
        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (:name, :email, :password, :phone)");
        $stmt->bindParam(':name', $userData['name']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $userData['phone']);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'];
    }
}

/**
 * Login a user
 * 
 * @param string $email User email
 * @param string $password User password
 * @return array Result with success status and message
 */
function loginUser($email, $password) {
    global $conn;
    
    try {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email dan password wajib diisi'];
        }
        
        // Get user by email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }
        
        $user = $stmt->fetch();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        return [
            'success' => true, 
            'message' => 'Login berhasil!',
            'user_id' => $user['id']
        ];
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat login. Silakan coba lagi.'];
    }
}

/**
 * Check if user is logged in
 * 
 * @return boolean True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Logout user
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

/**
 * Get user by ID
 * 
 * @param int $userId User ID
 * @return array|false User data or false if not found
 */
function getUserById($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            return false;
        }
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get user error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user account information
 * 
 * @param array $userData User data to update
 * @param array $fileData File upload data (for profile image)
 * @param int $userId User ID
 * @return array Result with success status and message
 */
function updateUserAccount($userData, $fileData, $userId) {
    global $conn;
    
    try {
        // Validate input
        if (empty($userData['name']) || empty($userData['email'])) {
            return ['success' => false, 'message' => 'Nama dan email wajib diisi'];
        }
        
        // Check if email already exists (for other users)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email sudah digunakan oleh pengguna lain'];
        }
        
        // Handle profile image upload
        $profileImage = null;
        if (!empty($fileData['profile_image']['name'])) {
            $uploadResult = uploadImage($fileData['profile_image'], 'profile');
            
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            
            $profileImage = $uploadResult['image_url'];
        }
        
        // Handle password update
        $passwordUpdate = '';
        $params = [
            ':name' => $userData['name'],
            ':email' => $userData['email'],
            ':phone' => $userData['phone'],
            ':id' => $userId
        ];
        
        if (!empty($userData['current_password']) && !empty($userData['new_password']) && !empty($userData['confirm_password'])) {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if (!password_verify($userData['current_password'], $user['password'])) {
                return ['success' => false, 'message' => 'Password saat ini salah'];
            }
            
            // Check if new passwords match
            if ($userData['new_password'] !== $userData['confirm_password']) {
                return ['success' => false, 'message' => 'Password baru dan konfirmasi tidak cocok'];
            }
            
            // Hash new password
            $hashedPassword = password_hash($userData['new_password'], PASSWORD_DEFAULT);
            $passwordUpdate = ', password = :password';
            $params[':password'] = $hashedPassword;
        }
        
        // Build update query
        $query = "UPDATE users SET name = :name, email = :email, phone = :phone";
        
        if ($profileImage) {
            $query .= ", profile_image = :profile_image";
            $params[':profile_image'] = $profileImage;
        }
        
        $query .= $passwordUpdate . " WHERE id = :id";
        
        // Execute update
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
    } catch (PDOException $e) {
        error_log("Update user error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.'];
    }
}

/**
 * Update user location information
 * 
 * @param array $locationData Location data to update
 * @param int $userId User ID
 * @return array Result with success status and message
 */
function updateUserLocation($locationData, $userId) {
    global $conn;
    
    try {
        // Validate input
        if (empty($locationData['address']) || empty($locationData['city']) || empty($locationData['province'])) {
            return ['success' => false, 'message' => 'Alamat, kota, dan provinsi wajib diisi'];
        }
        
        // Update location
        $stmt = $conn->prepare("
            UPDATE users 
            SET address = :address, 
                city = :city, 
                province = :province, 
                postal_code = :postal_code, 
                country = :country,
                latitude = :latitude,
                longitude = :longitude
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':address' => $locationData['address'],
            ':city' => $locationData['city'],
            ':province' => $locationData['province'],
            ':postal_code' => $locationData['postal_code'],
            ':country' => $locationData['country'],
            ':latitude' => $locationData['latitude'] ?: null,
            ':longitude' => $locationData['longitude'] ?: null,
            ':id' => $userId
        ]);
        
        return ['success' => true, 'message' => 'Informasi lokasi berhasil diperbarui'];
    } catch (PDOException $e) {
        error_log("Update location error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui lokasi. Silakan coba lagi.'];
    }
}

/**
 * Update user payment information
 * 
 * @param array $paymentData Payment data to update
 * @param int $userId User ID
 * @return array Result with success status and message
 */
function updateUserPayment($paymentData, $userId) {
    global $conn;
    
    try {
        // Validate payment method
        if (empty($paymentData['payment_method'])) {
            return ['success' => false, 'message' => 'Metode pembayaran wajib dipilih'];
        }
        
        // Prepare parameters
        $params = [
            ':payment_method' => $paymentData['payment_method'],
            ':id' => $userId
        ];
        
        // Add method-specific parameters
        if ($paymentData['payment_method'] === 'bank_transfer') {
            $params[':bank_name'] = $paymentData['bank_name'];
            $params[':account_number'] = $paymentData['account_number'];
            $params[':account_holder'] = $paymentData['account_holder'];
            $params[':wallet_provider'] = null;
            $params[':wallet_number'] = null;
        } elseif ($paymentData['payment_method'] === 'e_wallet') {
            $params[':bank_name'] = null;
            $params[':account_number'] = null;
            $params[':account_holder'] = null;
            $params[':wallet_provider'] = $paymentData['wallet_provider'];
            $params[':wallet_number'] = $paymentData['wallet_number'];
        } else {
            // Cash payment
            $params[':bank_name'] = null;
            $params[':account_number'] = null;
            $params[':account_holder'] = null;
            $params[':wallet_provider'] = null;
            $params[':wallet_number'] = null;
        }
        
        // Update payment info
        $stmt = $conn->prepare("
            UPDATE users 
            SET payment_method = :payment_method,
                bank_name = :bank_name,
                account_number = :account_number,
                account_holder = :account_holder,
                wallet_provider = :wallet_provider,
                wallet_number = :wallet_number
            WHERE id = :id
        ");
        
        $stmt->execute($params);
        
        return ['success' => true, 'message' => 'Informasi pembayaran berhasil diperbarui'];
    } catch (PDOException $e) {
        error_log("Update payment error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui pembayaran. Silakan coba lagi.'];
    }
}

/**
 * Item Functions
 */

/**
 * Upload an image
 * 
 * @param array $file File data from $_FILES
 * @param string $type Type of image (item, profile)
 * @return array Result with success status and image URL
 */
function uploadImage($file, $type = 'item') {
    // Define upload directory based on type
    $uploadDir = '../uploads/';
    if ($type === 'profile') {
        $uploadDir .= 'profiles/';
    } else {
        $uploadDir .= 'items/';
    }
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5000000) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB)'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak didukung (hanya JPG, PNG, dan GIF)'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '_' . basename($file['name']);
    $targetFile = $uploadDir . $filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return [
            'success' => true, 
            'message' => 'File berhasil diunggah',
            'image_url' => str_replace('../', '', $targetFile)
        ];
    } else {
        return ['success' => false, 'message' => 'Gagal mengunggah file'];
    }
}

/**
 * Process item listing (create or update)
 * 
 * @param array $itemData Item data from form
 * @param array $fileData File upload data from $_FILES
 * @return array Result with success status and message
 */
function processItemListing($itemData, $fileData) {
    global $conn;
    
    try {
        // Validate required fields
        if (empty($itemData['title']) || empty($itemData['category']) || empty($itemData['condition']) || 
            empty($itemData['description']) || empty($itemData['location']) || empty($itemData['city'])) {
            return ['success' => false, 'message' => 'Semua field wajib diisi'];
        }
        
        // Validate listing type specific fields
        if ($itemData['listing_type'] === 'sale' && empty($itemData['price'])) {
            return ['success' => false, 'message' => 'Harga wajib diisi untuk barang yang dijual'];
        }
        
        if ($itemData['listing_type'] === 'auction' && (empty($itemData['price']) || empty($itemData['auction_end']))) {
            return ['success' => false, 'message' => 'Harga awal dan tanggal berakhir wajib diisi untuk lelang'];
        }
        
        // Validate at least one shipping option
        if (empty($itemData['pickup_home']) && empty($itemData['pickup_warehouse']) && empty($itemData['cod'])) {
            return ['success' => false, 'message' => 'Pilih minimal satu opsi pengiriman'];
        }
        
        // Validate main image
        if (empty($fileData['main_image']['name'])) {
            return ['success' => false, 'message' => 'Foto utama wajib diunggah'];
        }
        
        // Get user ID from session
        $userId = $_SESSION['user_id'];
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Insert item data
        $stmt = $conn->prepare("
            INSERT INTO items (
                user_id, title, description, category_id, price, condition_status, 
                listing_type, starting_bid, end_date, pickup_home, pickup_warehouse, 
                cod, warehouse_id, location, city
            ) VALUES (
                :user_id, :title, :description, :category_id, :price, :condition_status, 
                :listing_type, :starting_bid, :end_date, :pickup_home, :pickup_warehouse, 
                :cod, :warehouse_id, :location, :city
            )
        ");
        
        $params = [
            ':user_id' => $userId,
            ':title' => $itemData['title'],
            ':description' => $itemData['description'],
            ':category_id' => $itemData['category'],
            ':condition_status' => $itemData['condition'],
            ':listing_type' => $itemData['listing_type'],
            ':location' => $itemData['location'],
            ':city' => $itemData['city'],
            ':pickup_home' => isset($itemData['pickup_home']) ? 1 : 0,
            ':pickup_warehouse' => isset($itemData['pickup_warehouse']) ? 1 : 0,
            ':cod' => isset($itemData['cod']) ? 1 : 0,
            ':warehouse_id' => isset($itemData['warehouse_id']) ? $itemData['warehouse_id'] : null
        ];
        
        // Set price, starting_bid, and end_date based on listing type
        if ($itemData['listing_type'] === 'sale') {
            $params[':price'] = $itemData['price'];
            $params[':starting_bid'] = null;
            $params[':end_date'] = null;
        } elseif ($itemData['listing_type'] === 'auction') {
            $params[':price'] = null;
            $params[':starting_bid'] = $itemData['price'];
            $params[':end_date'] = $itemData['auction_end'];
        } else {
            // Donation
            $params[':price'] = 0;
            $params[':starting_bid'] = null;
            $params[':end_date'] = null;
        }
        
        $stmt->execute($params);
        $itemId = $conn->lastInsertId();
        
        // Upload main image
        $mainImageResult = uploadImage($fileData['main_image']);
        if (!$mainImageResult['success']) {
            $conn->rollBack();
            return $mainImageResult;
        }
        
        // Insert main image
        $stmt = $conn->prepare("
            INSERT INTO item_images (item_id, image_url, is_main)
            VALUES (:item_id, :image_url, 1)
        ");
        $stmt->execute([
            ':item_id' => $itemId,
            ':image_url' => $mainImageResult['image_url']
        ]);
        
        // Upload additional images if any
        if (!empty($fileData['additional_images'])) {
            foreach ($fileData['additional_images'] as $index => $file) {
                if (!empty($file['name'])) {
                    $imageResult = uploadImage($file);
                    if ($imageResult['success']) {
                        $stmt = $conn->prepare("
                            INSERT INTO item_images (item_id, image_url, is_main)
                            VALUES (:item_id, :image_url, 0)
                        ");
                        $stmt->execute([
                            ':item_id' => $itemId,
                            ':image_url' => $imageResult['image_url']
                        ]);
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true, 
            'message' => 'Barang berhasil dipasang',
            'item_id' => $itemId
        ];
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        error_log("Process item listing error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat memproses barang. Silakan coba lagi.'];
    }
}

/**
 * Get items based on filters
 * 
 * @param string $category Category filter
 * @param array $filters Additional filters
 * @return array List of items
 */
function getItems($category = '', $filters = []) {
    global $conn;
    
    try {
        $query = "
            SELECT i.*, c.name as category_name, u.name as seller_name, 
                   (SELECT image_url FROM item_images WHERE item_id = i.id AND is_main = 1 LIMIT 1) as image_url
            FROM items i
            JOIN categories c ON i.category_id = c.id
            JOIN users u ON i.user_id = u.id
            WHERE i.status = 'active'
        ";
        
        $params = [];
        
        // Apply category filter
        if (!empty($category) && $category !== 'all') {
            $query .= " AND c.name = :category";
            $params[':category'] = $category;
        }
        
        // Apply listing type filter
        if (!empty($filters['listing_type']) && is_array($filters['listing_type'])) {
            $placeholders = [];
            foreach ($filters['listing_type'] as $index => $type) {
                $key = ":listing_type_$index";
                $placeholders[] = $key;
                $params[$key] = $type;
            }
            $query .= " AND i.listing_type IN (" . implode(", ", $placeholders) . ")";
        }
        
        // Apply shipping method filter
        if (!empty($filters['shipping'])) {
            $shippingConditions = [];
            
            if (in_array('warehouse', $filters['shipping'])) {
                $shippingConditions[] = "i.pickup_warehouse = 1";
            }
            
            if (in_array('home', $filters['shipping'])) {
                $shippingConditions[] = "i.pickup_home = 1";
            }
            
            if (in_array('cod', $filters['shipping'])) {
                $shippingConditions[] = "i.cod = 1";
            }
            
            if (!empty($shippingConditions)) {
                $query .= " AND (" . implode(" OR ", $shippingConditions) . ")";
            }
        }
        
        // Apply location filter if coordinates provided
        if (!empty($filters['latitude']) && !empty($filters['longitude']) && !empty($filters['distance'])) {
            $lat = $filters['latitude'];
            $lng = $filters['longitude'];
            $distance = $filters['distance'];
            
            // Haversine formula to calculate distance
            $query .= " AND (
                6371 * acos(
                    cos(radians(:latitude)) * 
                    cos(radians(i.latitude)) * 
                    cos(radians(i.longitude) - radians(:longitude)) + 
                    sin(radians(:latitude)) * 
                    sin(radians(i.latitude))
                )
            ) <= :distance";
            
            $params[':latitude'] = $lat;
            $params[':longitude'] = $lng;
            $params[':distance'] = $distance;
        }
        
        // Apply sorting
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'newest':
                    $query .= " ORDER BY i.created_at DESC";
                    break;
                case 'price-low':
                    $query .= " ORDER BY COALESCE(i.price, i.starting_bid) ASC";
                    break;
                case 'price-high':
                    $query .= " ORDER BY COALESCE(i.price, i.starting_bid) DESC";
                    break;
                case 'distance':
                    if (!empty($filters['latitude']) && !empty($filters['longitude'])) {
                        $query .= " ORDER BY (
                            6371 * acos(
                                cos(radians(:latitude_sort)) * 
                                cos(radians(i.latitude)) * 
                                cos(radians(i.longitude) - radians(:longitude_sort)) + 
                                sin(radians(:latitude_sort)) * 
                                sin(radians(i.latitude))
                            )
                        ) ASC";
                        $params[':latitude_sort'] = $filters['latitude'];
                        $params[':longitude_sort'] = $filters['longitude'];
                    } else {
                        $query .= " ORDER BY i.created_at DESC";
                    }
                    break;
                default:
                    $query .= " ORDER BY i.created_at DESC";
            }
        } else {
            $query .= " ORDER BY i.created_at DESC";
        }
        
        // Apply pagination
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $perPage = isset($filters['per_page']) ? (int)$filters['per_page'] : 12;
        $offset = ($page - 1) * $perPage;
        
        $query .= " LIMIT :offset, :per_page";
        $params[':offset'] = $offset;
        $params[':per_page'] = $perPage;
        
        $stmt = $conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get items error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's items
 * 
 * @param int $userId User ID
 * @return array List of user's items
 */
function getUserItems($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT i.*, c.name as category_name,
                   (SELECT image_url FROM item_images WHERE item_id = i.id AND is_main = 1 LIMIT 1) as image_url
            FROM items i
            JOIN categories c ON i.category_id = c.id
            WHERE i.user_id = :user_id AND i.listing_type IN ('sale', 'auction')
            ORDER BY i.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get user items error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's donations
 * 
 * @param int $userId User ID
 * @return array List of user's donations
 */
function getUserDonations($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT i.*, c.name as category_name,
                   (SELECT image_url FROM item_images WHERE item_id = i.id AND is_main = 1 LIMIT 1) as image_url,
                   (SELECT u.name FROM transactions t JOIN users u ON t.buyer_id = u.id 
                    WHERE t.item_id = i.id AND t.transaction_type = 'donation' LIMIT 1) as recipient_name
            FROM items i
            JOIN categories c ON i.category_id = c.id
            WHERE i.user_id = :user_id AND i.listing_type = 'donation'
            ORDER BY i.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get user donations error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's purchases
 * 
 * @param int $userId User ID
 * @return array List of user's purchases
 */
function getUserPurchases($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT t.*, i.title, i.id as item_id, u.name as seller_name,
                   (SELECT image_url FROM item_images WHERE item_id = i.id AND is_main = 1 LIMIT 1) as image_url,
                   (SELECT COUNT(*) FROM reviews r WHERE r.transaction_id = t.id AND r.reviewer_id = :user_id_check) as has_review
            FROM transactions t
            JOIN items i ON t.item_id = i.id
            JOIN users u ON t.seller_id = u.id
            WHERE t.buyer_id = :user_id
            ORDER BY t.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':user_id_check', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get user purchases error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's reviews
 * 
 * @param int $userId User ID
 * @return array List of user's reviews
 */
function getUserReviews($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT r.*, i.title as item_title,
                   (SELECT image_url FROM item_images WHERE item_id = i.id AND is_main = 1 LIMIT 1) as item_image
            FROM reviews r
            JOIN items i ON r.item_id = i.id
            WHERE r.reviewer_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get user reviews error: " . $e->getMessage());
        return [];
    }
}

/**
 * Format date to readable format
 * 
 * @param string $date Date string
 * @param bool $withTime Include time
 * @return string Formatted date
 */
function formatDate($date, $withTime = false) {
    if (empty($date)) {
        return '';
    }
    
    $format = $withTime ? 'd M Y, H:i' : 'd M Y';
    $timestamp = strtotime($date);
    
    return date($format, $timestamp);
}
