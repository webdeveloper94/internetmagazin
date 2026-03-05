<?php
$cartCount = getCartCount();
$favCount = getFavoritesCount();
$currentUser = getCurrentUser();

// Get categories for navbar
$catStmt = $pdo->query("SELECT id, name, icon FROM categories ORDER BY name");
$navCategories = $catStmt->fetchAll();
?>

<!-- Top Navbar -->
<nav class="top-navbar">
    <div class="container">
        <div class="navbar-inner">
            <!-- Logo -->
            <a href="<?= SITE_URL ?>" class="navbar-logo">
                <i class="bi bi-shop"></i>
                <span>Online Shop</span>
            </a>

            <!-- Catalog Button -->
            <a href="<?= SITE_URL ?>/?view=catalog" class="catalog-btn">
                <i class="bi bi-grid-fill"></i>
                <span>Katalog</span>
            </a>

            <!-- Search -->
            <form class="search-box" id="searchForm" action="<?= SITE_URL ?>" method="GET">
                <input type="text" name="q" placeholder="Mahsulotlar va turkumlar izlash" value="<?= isset($_GET['q']) ? sanitize($_GET['q']) : '' ?>">
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>

            <!-- Action Buttons -->
            <div class="navbar-actions">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="<?= SITE_URL ?>/admin/" class="nav-action-btn">
                            <i class="bi bi-gear"></i>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/pages/profile.php" class="nav-action-btn">
                        <i class="bi bi-person"></i>
                        <span>Kirish</span>
                    </a>
                    <a href="<?= SITE_URL ?>/pages/favorites.php" class="nav-action-btn">
                        <i class="bi bi-heart"></i>
                        <span>Saralangan</span>
                        <?php if ($favCount > 0): ?>
                            <span class="nav-badge fav-badge"><?= $favCount ?></span>
                        <?php else: ?>
                            <span class="nav-badge fav-badge" style="display:none">0</span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= SITE_URL ?>/pages/cart.php" class="nav-action-btn nav-cart-btn">
                        <i class="bi bi-cart3"></i>
                        <span>Savat</span>
                        <?php if ($cartCount > 0): ?>
                            <span class="nav-badge cart-badge"><?= $cartCount ?></span>
                        <?php else: ?>
                            <span class="nav-badge cart-badge" style="display:none">0</span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/auth/login.php" class="nav-action-btn">
                        <i class="bi bi-person"></i>
                        <span>Kirish</span>
                    </a>
                    <a href="<?= SITE_URL ?>/auth/login.php" class="nav-action-btn">
                        <i class="bi bi-heart"></i>
                        <span>Saralangan</span>
                    </a>
                    <a href="<?= SITE_URL ?>/auth/login.php" class="nav-action-btn nav-cart-btn">
                        <i class="bi bi-cart3"></i>
                        <span>Savat</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Categories Bar -->
<?php if (!empty($navCategories)): ?>
<div class="categories-bar">
    <div class="container">
        <ul class="categories-list">
            <li>
                <a href="<?= SITE_URL ?>" class="<?= !isset($_GET['category']) && !isset($_GET['id']) ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i> Hammasi
                </a>
            </li>
            <?php foreach ($navCategories as $cat): ?>
                <li>
                    <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $cat['id'] ?>"
                       class="<?= (isset($_GET['id']) && $_GET['id'] == $cat['id']) ? 'active' : '' ?>">
                        <?php if ($cat['icon']): ?>
                            <img src="<?= SITE_URL ?>/uploads/categories/<?= $cat['icon'] ?>" alt="">
                        <?php endif; ?>
                        <?= sanitize($cat['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
