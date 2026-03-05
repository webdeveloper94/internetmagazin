<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Bosh sahifa';

// Search
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$viewCatalog = isset($_GET['view']) && $_GET['view'] === 'catalog';

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Get user favorites
$userFavorites = [];
if (isLoggedIn()) {
    $favStmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $favStmt->execute([$_SESSION['user_id']]);
    $userFavorites = $favStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get products
if ($searchQuery) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, 
                          (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.name LIKE ? 
                          ORDER BY p.created_at DESC");
    $stmt->execute(["%{$searchQuery}%"]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query("SELECT p.*, c.name as category_name,
                            (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.created_at DESC")->fetchAll();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container page-wrapper">
    <?php if ($searchQuery): ?>
        <div class="section-title">
            <h2><i class="bi bi-search"></i> "<?= sanitize($searchQuery) ?>" bo'yicha natijalar</h2>
        </div>
    <?php elseif ($viewCatalog): ?>
        <!-- Catalog View: Show categories grid -->
        <div class="section-title">
            <h2><i class="bi bi-grid-fill"></i> Katalog</h2>
        </div>
        <?php if (!empty($categories)): ?>
        <div class="row g-3 mb-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $cat['id'] ?>" class="text-decoration-none">
                    <div class="product-card text-center" style="padding: 24px;">
                        <?php if ($cat['icon']): ?>
                            <img src="<?= SITE_URL ?>/uploads/categories/<?= $cat['icon'] ?>" 
                                 alt="<?= sanitize($cat['name']) ?>"
                                 style="width:64px;height:64px;object-fit:cover;border-radius:12px;margin-bottom:12px;">
                        <?php else: ?>
                            <div style="width:64px;height:64px;background:var(--bg);border-radius:12px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-folder" style="font-size:1.5rem;color:var(--primary);"></i>
                            </div>
                        <?php endif; ?>
                        <h6 class="mb-1" style="font-weight:600;"><?= sanitize($cat['name']) ?></h6>
                        <?php if ($cat['description']): ?>
                            <small class="text-muted"><?= mb_substr(sanitize($cat['description']), 0, 50) ?></small>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-folder-x"></i>
                <h5>Kategoriyalar topilmadi</h5>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Hero Slider -->
        <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="hero-slide" style="background:linear-gradient(135deg, #7000FF, #9B4DFF);">
                        <div class="slide-content">
                            <h2>🛍️ Online Shop</h2>
                            <p>Eng yaxshi narxlarda sifatli mahsulotlar — tez yetkazib berish!</p>
                            <a href="<?= SITE_URL ?>/?view=catalog" class="btn-hero">
                                <i class="bi bi-grid-fill"></i> Katalogni ko'rish
                            </a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide" style="background:linear-gradient(135deg, #FF6B35, #FF9F1C);">
                        <div class="slide-content">
                            <h2>🔥 Chegirmalar</h2>
                            <p>Eng sara mahsulotlarga 50% gacha chegirma!</p>
                            <a href="<?= SITE_URL ?>/?view=catalog" class="btn-hero">
                                <i class="bi bi-tag"></i> Xarid qilish
                            </a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide" style="background:linear-gradient(135deg, #2EC4B6, #00B4D8);">
                        <div class="slide-content">
                            <h2>🚚 Bepul yetkazib berish</h2>
                            <p>Barcha buyurtmalarga bepul yetkazib berish xizmati!</p>
                            <a href="<?= SITE_URL ?>/?view=catalog" class="btn-hero">
                                <i class="bi bi-truck"></i> Buyurtma berish
                            </a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide" style="background:linear-gradient(135deg, #E91E63, #FF5722);">
                        <div class="slide-content">
                            <h2>⭐ Yangi mahsulotlar</h2>
                            <p>Har kuni yangi mahsulotlar — eng so'nggi trendlar!</p>
                            <a href="<?= SITE_URL ?>/?view=catalog" class="btn-hero">
                                <i class="bi bi-stars"></i> Ko'rish
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Categories Row -->
        <?php if (!empty($categories)): ?>
        <div class="section-title">
            <h2>Kategoriyalar</h2>
            <a href="<?= SITE_URL ?>/?view=catalog">Hammasi <i class="bi bi-arrow-right"></i></a>
        </div>
        <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:8px;" class="mb-3">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $cat['id'] ?>" 
               style="flex-shrink:0;text-align:center;width:100px;">
                <div style="width:64px;height:64px;margin:0 auto 8px;background:#fff;border-radius:16px;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow);">
                    <?php if ($cat['icon']): ?>
                        <img src="<?= SITE_URL ?>/uploads/categories/<?= $cat['icon'] ?>" 
                             style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                    <?php else: ?>
                        <i class="bi bi-folder" style="font-size:1.3rem;color:var(--primary);"></i>
                    <?php endif; ?>
                </div>
                <span style="font-size:0.78rem;color:var(--text-dark);font-weight:500;"><?= sanitize($cat['name']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Products Grid -->
    <?php if (!$viewCatalog): ?>
    <div class="section-title">
        <h2><?= $searchQuery ? '' : 'Tavsiya etamiz' ?></h2>
    </div>

    <?php if (!empty($products)): ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
            <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $product['id'] ?>">
                <?php if ($product['image']): ?>
                    <img src="<?= SITE_URL ?>/uploads/products/<?= $product['image'] ?>" 
                         alt="<?= sanitize($product['name']) ?>" class="card-img">
                <?php else: ?>
                    <div class="card-img" style="display:flex;align-items:center;justify-content:center;background:var(--bg);">
                        <i class="bi bi-image" style="font-size:2rem;color:var(--border);"></i>
                    </div>
                <?php endif; ?>
            </a>
            <div class="card-body">
                <div class="card-price">
                    <?php
                    $displayPrice = $product['has_sizes'] && $product['min_size_price'] 
                        ? $product['min_size_price'] 
                        : $product['price'];
                    ?>
                    <?= formatPrice($displayPrice) ?> <span>so'm</span>
                    <?php if ($product['has_sizes']): ?>
                        <small style="color:var(--text-light);font-size:0.7rem;">dan</small>
                    <?php endif; ?>
                </div>
                <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $product['id'] ?>" class="card-title">
                    <?= sanitize($product['name']) ?>
                </a>
                <div class="card-actions">
                    <?php if (isLoggedIn()): ?>
                        <button class="btn-cart" onclick="addToCart(<?= $product['id'] ?>)">
                            <i class="bi bi-cart-plus"></i> Savatga
                        </button>
                        <button class="btn-fav <?= in_array($product['id'], $userFavorites) ? 'active' : '' ?>" 
                                onclick="toggleFavorite(<?= $product['id'] ?>, this)">
                            <i class="bi bi-heart<?= in_array($product['id'], $userFavorites) ? '-fill' : '' ?>"></i>
                        </button>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/auth/login.php" class="btn-cart">
                            <i class="bi bi-cart-plus"></i> Savatga
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <h5>Mahsulotlar topilmadi</h5>
            <p>Boshqa so'rov bilan izlab ko'ring</p>
        </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
