<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Get favorite products
$stmt = $pdo->prepare("SELECT p.*, f.id as fav_id,
                        (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price
                        FROM favorites f 
                        JOIN products p ON f.product_id = p.id 
                        WHERE f.user_id = ? 
                        ORDER BY f.id DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();

$pageTitle = 'Sevimlilar';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <div class="section-title">
        <h2><i class="bi bi-heart"></i> Sevimli mahsulotlar</h2>
        <?php if (!empty($favorites)): ?>
            <span class="text-muted"><?= count($favorites) ?> ta mahsulot</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($favorites)): ?>
    <div class="products-grid">
        <?php foreach ($favorites as $product): ?>
        <div class="product-card" id="fav-card-<?= $product['id'] ?>">
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
                </div>
                <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $product['id'] ?>" class="card-title">
                    <?= sanitize($product['name']) ?>
                </a>
                <div class="card-actions">
                    <button class="btn-cart" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="bi bi-cart-plus"></i> Savatga
                    </button>
                    <button class="btn-fav active" onclick="removeFavAndHide(<?= $product['id'] ?>, this)">
                        <i class="bi bi-heart-fill"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-heart"></i>
            <h5>Sevimli mahsulotlar yo'q</h5>
            <p>Mahsulot kartasidagi ♡ tugmasini bosing</p>
            <a href="<?= SITE_URL ?>" class="btn-checkout" style="display:inline-block;width:auto;padding:12px 24px;margin-top:12px;">
                <i class="bi bi-shop"></i> Xarid qilish
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function removeFavAndHide(productId, btn) {
    toggleFavorite(productId, btn);
    setTimeout(() => {
        const card = document.getElementById('fav-card-' + productId);
        if (card) {
            card.style.transition = 'opacity 0.3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
        }
    }, 500);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
