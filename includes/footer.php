<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h5><i class="bi bi-shop"></i> Online Shop</h5>
                <p style="font-size:0.85rem; color:#999;">Eng yaxshi narxlarda sifatli mahsulotlar. Tez yetkazib berish xizmati.</p>
            </div>
            <div class="footer-col">
                <h5>Foydali havolalar</h5>
                <ul>
                    <li><a href="<?= SITE_URL ?>">Bosh sahifa</a></li>
                    <li><a href="<?= SITE_URL ?>/?view=catalog">Katalog</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/cart.php">Savat</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Yordam</h5>
                <ul>
                    <li><a href="#">Yetkazib berish</a></li>
                    <li><a href="#">To'lov usullari</a></li>
                    <li><a href="#">Qaytarish</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Aloqa</h5>
                <ul>
                    <li><i class="bi bi-telephone"></i> +998 90 123-45-67</li>
                    <li><i class="bi bi-envelope"></i> info@onlineshop.uz</li>
                    <li style="margin-top:12px;">
                        <a href="#" style="margin-right:12px;font-size:1.2rem;"><i class="bi bi-telegram"></i></a>
                        <a href="#" style="margin-right:12px;font-size:1.2rem;"><i class="bi bi-instagram"></i></a>
                        <a href="#" style="font-size:1.2rem;"><i class="bi bi-facebook"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> Online Shop. Barcha huquqlar himoyalangan.
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <ul class="nav-items">
        <li class="nav-item">
            <a href="<?= SITE_URL ?>" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' && !isset($_GET['view']) ? 'active' : '' ?>">
                <i class="bi bi-house"></i>
                Bosh sahifa
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= SITE_URL ?>/?view=catalog">
                <i class="bi bi-grid"></i>
                Katalog
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= SITE_URL ?>/pages/cart.php">
                <i class="bi bi-cart3"></i>
                Savat
                <?php if (isset($cartCount) && $cartCount > 0): ?>
                    <span class="nav-badge cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= SITE_URL ?>/pages/favorites.php">
                <i class="bi bi-heart"></i>
                Saralangan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= isLoggedIn() ? SITE_URL . '/pages/profile.php' : SITE_URL . '/auth/login.php' ?>">
                <i class="bi bi-person"></i>
                Kabinet
            </a>
        </li>
    </ul>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
