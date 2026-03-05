<?php
require_once __DIR__ . '/../includes/auth.php';

$categoryId = intval($_GET['id'] ?? 0);
if ($categoryId <= 0) {
    header('Location: ' . SITE_URL);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get products in category
$stmt = $pdo->prepare("SELECT p.*, (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price
                        FROM products p WHERE p.category_id = ? ORDER BY p.created_at DESC");
$stmt->execute([$categoryId]);
$products = $stmt->fetchAll();

// User favorites
$userFavorites = [];
if (isLoggedIn()) {
    $favStmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $favStmt->execute([$_SESSION['user_id']]);
    $userFavorites = $favStmt->fetchAll(PDO::FETCH_COLUMN);
}

$pageTitle = $category['name'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <div class="breadcrumb-custom">
        <a href="<?= SITE_URL ?>">Bosh sahifa</a>
        <span>/</span>
        <?= sanitize($category['name']) ?>
    </div>

    <div class="section-title">
        <h2>
            <?php if ($category['icon']): ?>
                <img src="<?= SITE_URL ?>/uploads/categories/<?= $category['icon'] ?>" 
                     style="width:28px;height:28px;object-fit:cover;border-radius:6px;vertical-align:middle;">
            <?php endif; ?>
            <?= sanitize($category['name']) ?>
        </h2>
        <span class="text-muted"><?= count($products) ?> ta mahsulot</span>
    </div>

    <?php if ($category['description']): ?>
        <p class="text-muted mb-3"><?= sanitize($category['description']) ?></p>
    <?php endif; ?>

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
            <i class="bi bi-box-seam"></i>
            <h5>Bu kategoriyada mahsulotlar yo'q</h5>
            <p>Tez orada yangi mahsulotlar qo'shiladi</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
