<?php
// Include database connection and functions
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

// Process registration form
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    
    // Validate password match
    if ($userData['password'] !== $userData['confirm_password']) {
        $message = 'Password dan konfirmasi password tidak cocok';
        $messageType = 'error';
    } else {
        $result = registerUser($userData);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            
            // Redirect to login page after successful registration
            header("Location: login.php" . (isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''));
            exit();
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}

// Page title
$pageTitle = "Daftar";
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
    <style>
        .auth-page {
            padding: 120px 0 60px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        
        .auth-card {
            background-color: var(--dark-gray);
            border-radius: 12px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .auth-subtitle {
            font-size: 16px;
            color: #BBBBBB;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--medium-gray);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--white);
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: var(--neon-green);
            box-shadow: 0 0 10px rgba(0, 255, 132, 0.2);
            outline: none;
        }
        
        .form-actions {
            margin-top: 30px;
        }
        
        .form-actions button {
            width: 100%;
            padding: 14px;
            font-size: 16px;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #AAAAAA;
        }
        
        .auth-footer a {
            color: var(--neon-green);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .alert-error {
            background-color: rgba(255, 94, 132, 0.1);
            border: 1px solid #FF5E84;
            color: #FF5E84;
        }
        
        .alert-success {
            background-color: rgba(0, 255, 132, 0.1);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
        }
        
        .alert:before {
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .alert-error:before {
            content: "\f057"; /* times-circle */
        }
        
        .alert-success:before {
            content: "\f058"; /* check-circle */
        }
        
        .password-requirements {
            font-size: 12px;
            color: #AAAAAA;
            margin-top: 5px;
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            margin-top: 20px;
        }
        
        .terms-checkbox input {
            margin-top: 3px;
            margin-right: 10px;
            width: auto;
        }
        
        .terms-checkbox label {
            font-size: 14px;
            color: #BBBBBB;
        }
        
        .terms-checkbox a {
            color: var(--neon-green);
            text-decoration: none;
        }
        
        .terms-checkbox a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1 class="auth-title">Buat Akun Baru</h1>
                    <p class="auth-subtitle">Daftar untuk mulai menjual atau membeli barang bekas</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>" method="post">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required placeholder="Masukkan nama lengkap Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone" placeholder="Masukkan nomor telepon Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Buat password Anda">
                        <p class="password-requirements">Password minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Masukkan kembali password Anda">
                    </div>
                    
                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">Saya menyetujui <a href="../pages/terms.php">Syarat & Ketentuan</a> dan <a href="../pages/privacy.php">Kebijakan Privasi</a> ReCycleIt</label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    Sudah punya akun? <a href="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>">Masuk di sini</a>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
</body>
</html>
