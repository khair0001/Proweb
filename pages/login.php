<?php
// Include database connection and functions
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

// Process login form
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        // Redirect to requested page or profile
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'profile.php';
        header("Location: $redirect");
        exit();
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}

// Page title
$pageTitle = "Masuk";
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
            max-width: 450px;
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
        
        .forgot-password {
            display: block;
            text-align: right;
            font-size: 13px;
            color: #AAAAAA;
            margin-top: 5px;
            text-decoration: none;
        }
        
        .forgot-password:hover {
            color: var(--neon-green);
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
                    <h1 class="auth-title">Masuk ke Akun</h1>
                    <p class="auth-subtitle">Masukkan email dan password untuk melanjutkan</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password Anda">
                        <a href="forgot-password.php" class="forgot-password">Lupa password?</a>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    Belum punya akun? <a href="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>">Daftar sekarang</a>
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
