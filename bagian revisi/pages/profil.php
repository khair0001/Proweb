<?php
// Mulai session untuk mengambil data user yang sedang login
session_start();

// Jika user belum login, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Koneksi ke database untuk mengambil informasi user tambahan
include '../include/koneksi.php';

// Ambil informasi user tambahan jika ada
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

$phone = "";
$address = "";
$city = "";
$join_date = "";
$bio = "";
$profile_pic = "../assets/image/user.png"; // Gambar profil default

if ($result && mysqli_num_rows($result) === 1) {
    $userData = mysqli_fetch_assoc($result);
    $phone = isset($userData['phone']) ? $userData['phone'] : "Belum diatur";
    $address = isset($userData['address']) ? $userData['address'] : "Belum diatur";
    $city = isset($userData['city']) ? $userData['city'] : "Belum diatur";
    $join_date = isset($userData['created_at']) ? date('d F Y', strtotime($userData['created_at'])) : "";
    $bio = isset($userData['bio']) ? $userData['bio'] : "Belum ada bio";
    
    // Ambil gambar profil jika ada
    if (isset($userData['profile_pic']) && !empty($userData['profile_pic'])) {
        $profile_pic = $userData['profile_pic'];
    }
}

// Proses update profil jika form di submit
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_address = $_POST['address'];
    $new_city = $_POST['city'];
    $new_bio = $_POST['bio'];
    $profile_pic_path = $profile_pic; // Default ke gambar profil saat ini
    
    // Proses upload gambar profil
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../upload/profile";
        $temp_name = $_FILES['profile_pic']['tmp_name'];
        $file_name = basename($_FILES['profile_pic']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Cek jika file adalah gambar asli
        $check = getimagesize($temp_name);
        if ($check !== false) {
            // Hanya izinkan format file tertentu
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_ext, $allowed_types)) {
                // Buat nama file unik untuk mencegah penggantian
                $new_filename = $user_id . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($temp_name, $upload_path)) {
                    $profile_pic_path = $upload_path;
                } else {
                    $error_message = "Gagal mengunggah foto profil.";
                }
            } else {
                $error_message = "Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            }
        } else {
            $error_message = "File yang diunggah bukan gambar.";
        }
    }
    
    // Update di database
    $update_query = "UPDATE users SET 
                    name = '$new_username',
                    email = '$new_email',
                    phone = '$new_phone',
                    address = '$new_address',
                    city = '$new_city',
                    bio = '$new_bio',
                    profile_pic = '$profile_pic_path'
                    WHERE id = '$user_id'";
    
    if (!isset($error_message) && mysqli_query($conn, $update_query)) {
        // Update session variables
        $_SESSION['username'] = $new_username;
        $_SESSION['email'] = $new_email;
        
        // Refresh page untuk menampilkan informasi yang diperbarui
        header("Location: profil.php?updated=true");
        exit();
    } else if (!isset($error_message)) {
        $error_message = "Gagal memperbarui profil: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<?php include '../include/header.php'; ?>
    
    <!-- Main content -->
    <main class="profile-container">
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true'): ?>
            <div class="alert alert-success">
                Profil berhasil diperbarui!
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-header">
            <img src="<?php echo $profile_pic; ?>" alt="<?php echo $username; ?>" class="profile-avatar">
            <div class="profile-info">
                <h1 class="profile-name"><?php echo $username; ?></h1>
                <p class="profile-email"><i class="fas fa-envelope"></i> <?php echo $email; ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo $city; ?></p>
                <p><i class="fas fa-calendar-alt"></i> Bergabung sejak <?php echo $join_date ?: 'N/A'; ?></p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">15</div>
                        <div class="stat-label">Barang Dijual</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">8</div>
                        <div class="stat-label">Transaksi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">4.8</div>
                        <div class="stat-label">Rating</div>
                    </div>
                </div>
                
                <?php if (!empty($bio)): ?>
                <div class="profile-bio">
                    <i class="fas fa-quote-left"></i> <?php echo $bio; ?> <i class="fas fa-quote-right"></i>
                </div>
                <?php endif; ?>
            </div>
            </div>
            
            <div class="profile-tabs">
                <div class="profile-tab active" onclick="openTab('info', event)" data-tab="info">Informasi Profil</div>
                <div class="profile-tab" onclick="openTab('products', event)" data-tab="products">Barang Saya</div>
                <div class="profile-tab" onclick="openTab('transactions', event)" data-tab="transactions">Riwayat Transaksi</div>
                <div class="profile-tab" onclick="openTab('edit', event)" data-tab="edit">Edit Profil</div>
            </div>
            
            <!-- Tab Content: Info -->
            <div id="info" class="tab-content active">
                <h2><i class="fas fa-info-circle"></i> Informasi Pengguna</h2>
                <div class="profile-grid">
                    <div>
                        <h3>Kontak</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td class="label"><strong>Nama</strong></td>
                                <td><?php echo $username; ?></td>
                            </tr>
                            <tr>
                                <td class="label"><strong>Email</strong></td>
                                <td><?php echo $email; ?></td>
                            </tr>
                            <tr>
                                <td class="label"><strong>Telepon</strong></td>
                                <td><?php echo $phone; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <h3>Alamat</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td class="label"><strong>Alamat Lengkap</strong></td>
                                <td><?php echo $address; ?></td>
                            </tr>
                            <tr>
                                <td class="label"><strong>Kota</strong></td>
                                <td><?php echo $city; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Products -->
            <div id="products" class="tab-content">
                <h2><i class="fas fa-box"></i> Barang Saya</h2>
                <div class="product-grid">
                    <!-- Sample products, would be populated from database in a real implementation -->
                    <div class="product-card">
                        <div class="product-badge sell">Jual</div>
                        <img src="../assets/image/nike.jpeg" alt="Sepatu Nike" class="product-image">
                        <div class="product-details">
                            <h3 class="product-title">Sepatu Nike Air Max</h3>
                            <p class="product-price">Rp 800.000</p>
                            <div class="product-meta">
                                <span class="product-time"><i class="far fa-clock"></i> 2 jam yang lalu</span>
                                <span class="product-views"><i class="far fa-eye"></i> 24 dilihat</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-badge donation">Donasi</div>
                        <img src="../assets/image/buku.jpeg" alt="Buku Bekas" class="product-image">
                        <div class="product-details">
                            <h3 class="product-title">Koleksi Buku Pelajaran SD</h3>
                            <p class="product-price">Gratis</p>
                            <div class="product-meta">
                                <span class="product-time"><i class="far fa-clock"></i> 5 jam yang lalu</span>
                                <span class="product-views"><i class="far fa-eye"></i> 12 dilihat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Transactions -->
            <div id="transactions" class="tab-content">
                <h2><i class="fas fa-history"></i> Riwayat Transaksi</h2>
                <div class="transaction-list">
                    <!-- Sample transactions, would be populated from database in a real implementation -->
                    <div class="transaction-item">
                        <img src="../assets/image/rog.jpg" alt="Laptop Asus ROG" class="transaction-image">
                        <div class="transaction-details">
                            <div class="transaction-title">Laptop Asus ROG 2019</div>
                            <p>Rp 5.500.000</p>
                            <div class="transaction-meta">
                                <span><i class="far fa-calendar-alt"></i> 15 April 2025</span>
                                <span><i class="fas fa-user"></i> Pembeli: Ahmad</span>
                            </div>
                        </div>
                        <span class="transaction-status status-completed">Selesai</span>
                    </div>
                    
                    <div class="transaction-item">
                        <img src="../assets/image/ipon.jpg" alt="iPhone" class="transaction-image">
                        <div class="transaction-details">
                            <div class="transaction-title">iPhone 20 Pro Max</div>
                            <p>Rp 1.200.000</p>
                            <div class="transaction-meta">
                                <span><i class="far fa-calendar-alt"></i> 10 April 2025</span>
                                <span><i class="fas fa-user"></i> Penjual: Budi</span>
                            </div>
                        </div>
                        <span class="transaction-status status-pending">Dalam Proses</span>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Edit Profile -->
            <div id="edit" class="tab-content">
                <h2><i class="fas fa-user-edit"></i> Edit Profil</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_pic">Foto Profil</label>
                        <div class="profile-image-upload">
                            <img src="<?php echo $profile_pic; ?>" alt="Preview" id="profile-preview" class="profile-preview">
                            <div>
                                <input type="file" id="profile_pic" name="profile_pic" class="form-control file-upload-input" accept="image/*" onchange="previewImage(this)">
                                <p class="file-upload-help">Format yang didukung: JPG, JPEG, PNG, GIF. Ukuran maks: 2MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username">Nama Lengkap</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo $phone; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" id="address" name="address" class="form-control" value="<?php echo $address; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="city">Kota</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?php echo $city; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control"><?php echo $bio; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="update_profile" class="btn-update">Perbarui Profil</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        function openTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tabs
            const tabs = document.getElementsByClassName('profile-tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Show the selected tab content and mark the tab as active
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>
