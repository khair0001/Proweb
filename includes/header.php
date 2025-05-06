<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header>
    <div class="container">
        <nav>
            <a href="<?php echo $currentPage === 'index.php' ? '#' : '../index.php'; ?>" class="logo">Re<span>shina</span></a>
            
            <div class="nav-links">
                <a href="<?php echo $currentPage === 'index.php' ? '#' : '../index.php'; ?>" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Beranda</a>
                <a href="<?php echo $currentPage === 'index.php' ? '#about' : '../index.php#about'; ?>" class="<?php echo isset($_GET['section']) && $_GET['section'] === 'about' ? 'active' : ''; ?>">Tentang Kami</a>
                <a href="<?php echo $currentPage === 'buy-claim.php' ? '#' : ($currentPage === 'index.php' ? 'pages/buy-claim.php' : 'buy-claim.php'); ?>" class="<?php echo $currentPage === 'buy-claim.php' ? 'active' : ''; ?>">Beli/Klaim</a>
                <a href="<?php echo $currentPage === 'sell-donate.php' ? '#' : ($currentPage === 'index.php' ? 'pages/sell-donate.php' : 'sell-donate.php'); ?>" class="<?php echo $currentPage === 'sell-donate.php' ? 'active' : ''; ?>">Jual/Donasi</a>
            </div>
            
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $currentPage === 'profile.php' ? '#' : ($currentPage === 'index.php' ? 'pages/profile.php' : 'profile.php'); ?>" class="btn btn-outline">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <a href="<?php echo $currentPage === 'index.php' ? 'pages/logout.php' : 'logout.php'; ?>" class="btn btn-primary">Keluar</a>
                <?php else: ?>
                    <a href="<?php echo $currentPage === 'login.php' ? '#' : ($currentPage === 'index.php' ? 'pages/login.php' : 'login.php'); ?>" class="btn btn-outline">Masuk</a>
                    <a href="<?php echo $currentPage === 'register.php' ? '#' : ($currentPage === 'index.php' ? 'pages/register.php' : 'register.php'); ?>" class="btn btn-primary">Daftar</a>
                <?php endif; ?>
            </div>
            
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </div>
</header>
