<?php
require_once __DIR__ . '/../includes/auth.php';

$productId = intval($_GET['id'] ?? 0);
if ($productId <= 0) {
    header('Location: ' . SITE_URL);
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get sizes if any
$sizes = [];
if ($product['has_sizes']) {
    $stmt = $pdo->prepare("SELECT * FROM product_sizes WHERE product_id = ? ORDER BY price");
    $stmt->execute([$productId]);
    $sizes = $stmt->fetchAll();
}

// Check if in favorites
$isFavorited = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    $isFavorited = (bool)$stmt->fetch();
}

$pageTitle = $product['name'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <div class="breadcrumb-custom">
        <a href="<?= SITE_URL ?>">Bosh sahifa</a>
        <span>/</span>
        <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $product['category_id'] ?>"><?= sanitize($product['category_name']) ?></a>
        <span>/</span>
        <?= sanitize($product['name']) ?>
    </div>

    <div class="row g-4">
        <!-- Product Image -->
        <div class="col-md-5">
            <?php if ($product['image']): ?>
                <img src="<?= SITE_URL ?>/uploads/products/<?= $product['image'] ?>" 
                     alt="<?= sanitize($product['name']) ?>" class="product-detail-img">
            <?php else: ?>
                <div class="product-detail-img d-flex align-items-center justify-content-center" style="height:400px;background:#fff;">
                    <i class="bi bi-image" style="font-size:4rem;color:var(--border);"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <div class="profile-card">
                <h3 style="font-weight:700;margin-bottom:8px;"><?= sanitize($product['name']) ?></h3>
                <p class="text-muted mb-3"><?= sanitize($product['category_name']) ?></p>

                <!-- Price -->
                <?php if ($product['has_sizes'] && !empty($sizes)): ?>
                    <div id="productPrice" class="mb-3" style="font-size:1.8rem;font-weight:800;color:var(--primary);">
                        <?= formatPrice($sizes[0]['price']) ?> <span style="font-size:1rem;font-weight:400;">so'm</span>
                    </div>
                <?php else: ?>
                    <div class="mb-3" style="font-size:1.8rem;font-weight:800;color:var(--primary);">
                        <?= formatPrice($product['price']) ?> <span style="font-size:1rem;font-weight:400;">so'm</span>
                    </div>
                <?php endif; ?>

                <!-- Sizes -->
                <?php if (!empty($sizes)): ?>
                <div class="mb-3">
                    <label class="mb-2" style="font-weight:600;">O'lcham tanlang:</label>
                    <div class="size-selector">
                        <?php foreach ($sizes as $i => $size): ?>
                            <div class="size-option <?= $i === 0 ? 'active' : '' ?>" 
                                 data-size-id="<?= $size['id'] ?>" 
                                 data-price="<?= $size['price'] ?>"
                                 onclick="selectSize(this)">
                                <?= sanitize($size['size_name']) ?>
                                <div style="font-size:0.75rem;color:var(--text-muted);"><?= formatPrice($size['price']) ?> so'm</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <?php if ($product['description']): ?>
                    <div class="mb-4">
                        <h6 style="font-weight:600;">Tavsif:</h6>
                        <p style="color:var(--text-muted);line-height:1.6;"><?= nl2br(sanitize($product['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Actions -->
                <?php if (isLoggedIn()): ?>
                <div class="d-flex gap-2">
                    <button class="btn-checkout flex-grow-1" id="addToCartBtn"
                            onclick="addToCartDetail()">
                        <i class="bi bi-cart-plus"></i> Savatga qo'shish
                    </button>
                    <button class="btn-fav <?= $isFavorited ? 'active' : '' ?>" 
                            style="width:48px;height:48px;font-size:1.2rem;"
                            onclick="toggleFavorite(<?= $product['id'] ?>, this)">
                        <i class="bi bi-heart<?= $isFavorited ? '-fill' : '' ?>"></i>
                    </button>
                </div>
                <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php" class="btn-checkout d-block text-center">
                    <i class="bi bi-box-arrow-in-right"></i> Sotib olish uchun kiring
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSizeId = <?= !empty($sizes) ? $sizes[0]['id'] : 'null' ?>;

function selectSize(el) {
    document.querySelectorAll('.size-option').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    selectedSizeId = el.dataset.sizeId;
    const price = parseInt(el.dataset.price);
    document.getElementById('productPrice').innerHTML = 
        new Intl.NumberFormat('uz').format(price).replace(/,/g, ' ') + ' <span style="font-size:1rem;font-weight:400;">so\'m</span>';
}

function addToCartDetail() {
    addToCart(<?= $product['id'] ?>, selectedSizeId);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
