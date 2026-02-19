    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Internet Magazin</h3>
                    <p>Eng sifatli mahsulotlar, qulay narxlarda!</p>
                </div>
                
                <div class="footer-section">
                    <h4>Havolalar</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Asosiy sahifa</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/cart.php">Savatcha</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/orders.php">Buyurtmalar</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Aloqa</h4>
                    <ul>
                        <li>📞 +998 90 123 45 67</li>
                        <li>📧 info@onlineshop.uz</li>
                        <li>📍 Toshkent, O'zbekiston</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Barcha huquqlar himoyalangan.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="<?php echo SITE_URL; ?>" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-house-fill"></i>
                </div>
                <div class="bottom-nav-label">Asosiy</div>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/categories.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-grid-fill"></i>
                </div>
                <div class="bottom-nav-label">Katalog</div>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/cart.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cart.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-cart-fill"></i>
                    <?php if (is_logged_in()): ?>
                        <?php 
                        $cart_count = get_cart_count();
                        if ($cart_count > 0): 
                        ?>
                            <span class="bottom-nav-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="bottom-nav-label">Savat</div>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/orders.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="bottom-nav-label">Buyurtmalar</div>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/favorites.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'favorites.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-heart-fill"></i>
                    <?php if (is_logged_in()): ?>
                        <span class="bottom-nav-badge favorites-count" style="display: none;">0</span>
                    <?php endif; ?>
                </div>
                <div class="bottom-nav-label">Sevimli</div>
            </a>
            
            <a href="<?php echo SITE_URL; ?><?php echo is_logged_in() ? '/profile.php' : '/auth/login.php'; ?>" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="bottom-nav-label">Kabinet</div>
            </a>
        </div>
    </nav>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/horizontal-scroll.js"></script>
</body>
</html>
