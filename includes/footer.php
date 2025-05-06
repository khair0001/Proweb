<?php
// Get current page for correct path to assets
$currentPage = basename($_SERVER['PHP_SELF']);
$isRootPage = $currentPage === 'index.php';
$prefix = $isRootPage ? '' : '../';
?>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Reshina</h3>
                <p style="color: #BBBBBB; margin-bottom: 15px; font-size: 14px;">Platform jual beli, donasi, dan lelang barang bekas layak pakai untuk lingkungan yang lebih baik.</p>
                
                <ul class="footer-links">
                    <li><a href="<?php echo $prefix; ?>pages/faq.php">FAQ</a></li>
                    <li><a href="<?php echo $prefix; ?>pages/guide.php">Panduan</a></li>
                    <li><a href="<?php echo $prefix; ?>pages/privacy.php">Kebijakan Privasi</a></li>
                    <li><a href="<?php echo $prefix; ?>pages/terms.php">Syarat & Ketentuan</a></li>
                    <li><a href="<?php echo $prefix; ?>pages/contact.php">Kontak</a></li>
                </ul>
                
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Reshina. Semua Hak Dilindungi.</p>
        </div>
    </div>
</footer>
