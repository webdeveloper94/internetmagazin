<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="O'zbekistondagi eng yaxshi online do'kon - sifatli mahsulotlar, arzon narxlar">
    <title><?php echo $page_title ?? SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/enhancements.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/desktop.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/mobile.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>" class="navbar-brand">
                    <span class="logo-icon">🛒</span>
                    <span class="logo-text">Internet Magazin</span>
                </a>
                
                <!-- Catalog Button (Desktop) -->
                <a href="<?php echo SITE_URL; ?>/categories.php" class="catalog-btn desktop-only">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    Katalog
                </a>
                
                <!-- Search Bar (Desktop) -->
                <div class="navbar-search desktop-only">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Mahsulotlar va kategoriyalar qidirish" id="navbarSearch">
                </div>
                
                <!-- Right Section -->
                <div class="navbar-right">
                    <?php if (is_logged_in()): ?>
                        <!-- Favorites Icon -->
                        <a href="<?php echo SITE_URL; ?>/favorites.php" class="navbar-icon-btn desktop-only">
                            <i class="bi bi-heart"></i>
                            <span class="navbar-icon-label">Tanlanganlar</span>
                            <span class="navbar-icon-badge favorites-count" style="display: none;">0</span>
                        </a>
                        
                        <!-- Cart Icon -->
                        <a href="<?php echo SITE_URL; ?>/cart.php" class="navbar-icon-btn desktop-only">
                            <i class="bi bi-cart3"></i>
                            <span class="navbar-icon-label">Savat</span>
                            <?php 
                            $cart_count = get_cart_count();
                            if ($cart_count > 0): 
                            ?>
                                <span class="navbar-icon-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- Orders Icon -->
                        <a href="<?php echo SITE_URL; ?>/orders.php" class="navbar-icon-btn desktop-only">
                            <i class="bi bi-box-seam"></i>
                            <span class="navbar-icon-label">Buyurtmalar</span>
                        </a>
                        
                        <!-- User Menu -->
                        <a href="<?php echo SITE_URL; ?>/profile.php" class="navbar-icon-btn desktop-only">
                            <i class="bi bi-person-circle"></i>
                            <span class="navbar-icon-label">Kabinet</span>
                        </a>
                    <?php else: ?>
                        <!-- Login Button -->
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="navbar-icon-btn desktop-only">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span class="navbar-icon-label">Kirish</span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Category Quick Links (Desktop) -->
    <div class="category-quick-nav desktop-only">
        <div class="container">
            <div class="category-quick-scroll">
                <?php
                $db_cats = Database::getInstance();
                $quick_cats_sql = "SELECT * FROM categories ORDER BY name LIMIT 8";
                $quick_cats = $db_cats->fetchAll($quick_cats_sql);
                
                foreach ($quick_cats as $cat):
                    $icon = $cat['icon'] ?: 'bi-box';
                ?>
                    <a href="<?php echo SITE_URL; ?>/products.php?category_id=<?php echo $cat['id']; ?>" class="category-quick-item">
                        <span class="category-quick-icon"><i class="bi <?php echo $icon; ?>"></i></span>
                        <span class="category-quick-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php 
    $flash = get_flash_message();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible">
            <div class="container">
                <?php echo $flash['message']; ?>
                <button class="alert-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>
