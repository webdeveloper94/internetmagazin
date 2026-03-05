<?php
require_once __DIR__ . '/../includes/auth.php';

$productId = intval($_GET['id'] ?? 0);
if ($productId <= 0) {
    header('Location: ' . SITE_URL);
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.id as category_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
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
$userFavorites = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    $isFavorited = (bool)$stmt->fetch();
    
    $favStmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $favStmt->execute([$_SESSION['user_id']]);
    $userFavorites = $favStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Collect product images
$productImages = [];
if ($product['image']) $productImages[] = $product['image'];
if (!empty($product['image2'])) $productImages[] = $product['image2'];
if (!empty($product['image3'])) $productImages[] = $product['image3'];

// Related products (same category, excluding current)
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name,
                       (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price
                       FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.category_id = ? AND p.id != ?
                       ORDER BY p.created_at DESC
                       LIMIT 20");
$stmt->execute([$product['category_id'], $productId]);
$relatedProducts = $stmt->fetchAll();

$pageTitle = $product['name'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <a href="<?= SITE_URL ?>">Bosh sahifa</a>
        <span>/</span>
        <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $product['category_id'] ?>"><?= sanitize($product['category_name']) ?></a>
        <span>/</span>
        <?= sanitize($product['name']) ?>
    </div>

    <div class="product-detail">
        <!-- LEFT: Image Gallery -->
        <div class="product-gallery">
            <!-- Main Image -->
            <div class="gallery-main">
                <img src="<?= SITE_URL ?>/uploads/products/<?= $productImages[0] ?? '' ?>" 
                     alt="<?= sanitize($product['name']) ?>" 
                     id="mainProductImage"
                     class="gallery-main-img">
                <?php if (count($productImages) > 1): ?>
                <button class="gallery-nav gallery-prev" onclick="galleryNav(-1)">‹</button>
                <button class="gallery-nav gallery-next" onclick="galleryNav(1)">›</button>
                <div class="gallery-counter" id="galleryCounter">1 / <?= count($productImages) ?></div>
                <?php endif; ?>
            </div>
            <!-- Thumbnails -->
            <?php if (count($productImages) > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($productImages as $i => $img): ?>
                <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" onclick="selectGalleryImage(<?= $i ?>)">
                    <img src="<?= SITE_URL ?>/uploads/products/<?= $img ?>" alt="">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Product Info -->
        <div class="product-info-panel">
            <div class="product-info-card">
                <!-- Price -->
                <div class="product-detail-price" id="productPrice">
                    <?php if ($product['has_sizes'] && !empty($sizes)): ?>
                        <?= formatPrice($sizes[0]['price']) ?> <span>so'm</span>
                    <?php else: ?>
                        <?= formatPrice($product['price']) ?> <span>so'm</span>
                    <?php endif; ?>
                </div>

                <!-- Name -->
                <h1 class="product-detail-name"><?= sanitize($product['name']) ?></h1>
                <div class="product-detail-category">
                    <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $product['category_id'] ?>">
                        <i class="bi bi-tag"></i> <?= sanitize($product['category_name']) ?>
                    </a>
                </div>

                <!-- Sizes -->
                <?php if (!empty($sizes)): ?>
                <div class="product-sizes-section">
                    <label class="sizes-label">O'lcham tanlang:</label>
                    <div class="size-selector">
                        <?php foreach ($sizes as $i => $size): ?>
                            <div class="size-option <?= $i === 0 ? 'active' : '' ?>" 
                                 data-size-id="<?= $size['id'] ?>" 
                                 data-price="<?= $size['price'] ?>"
                                 onclick="selectSize(this)">
                                <?= sanitize($size['size_name']) ?>
                                <div class="size-price"><?= formatPrice($size['price']) ?> so'm</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <hr style="border-color:var(--border);margin:16px 0;">

                <!-- Shipping info -->
                <div class="product-shipping-info">
                    <div class="shipping-item">
                        <i class="bi bi-truck text-success"></i>
                        <div>
                            <strong>Bepul yetkazib berish</strong>
                            <small>1-3 ish kuni ichida</small>
                        </div>
                    </div>
                    <div class="shipping-item">
                        <i class="bi bi-shield-check text-primary"></i>
                        <div>
                            <strong>Sifat kafolati</strong>
                            <small>14 kun ichida qaytarish</small>
                        </div>
                    </div>
                </div>

                <hr style="border-color:var(--border);margin:16px 0;">

                <!-- Actions -->
                <?php if (isLoggedIn()): ?>
                <div class="product-detail-actions">
                    <button class="btn-add-cart" id="addToCartBtn" onclick="addToCartDetail()">
                        <i class="bi bi-cart-plus"></i> Savatga qo'shish
                    </button>
                    <button class="btn-fav-detail <?= $isFavorited ? 'active' : '' ?>" 
                            onclick="toggleFavorite(<?= $product['id'] ?>, this)">
                        <i class="bi bi-heart<?= $isFavorited ? '-fill' : '' ?>"></i>
                    </button>
                </div>
                <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php" class="btn-add-cart d-block text-center">
                    <i class="bi bi-box-arrow-in-right"></i> Sotib olish uchun kiring
                </a>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <?php if ($product['description']): ?>
            <div class="product-description-card">
                <h6><i class="bi bi-info-circle"></i> Mahsulot haqida</h6>
                <p><?= nl2br(sanitize($product['description'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="section-title" style="margin-top:40px;">
        <h2>O'xshash mahsulotlar</h2>
        <a href="<?= SITE_URL ?>/pages/category.php?id=<?= $product['category_id'] ?>">Hammasi <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="products-grid">
        <?php foreach ($relatedProducts as $product): ?>
            <?php include __DIR__ . '/../includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// ===== Gallery =====
const galleryImages = <?= json_encode(array_values($productImages)) ?>;
let currentGalleryIndex = 0;

function selectGalleryImage(index) {
    currentGalleryIndex = index;
    const mainImg = document.getElementById('mainProductImage');
    mainImg.src = '<?= SITE_URL ?>/uploads/products/' + galleryImages[index];
    
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === index);
    });
    
    const counter = document.getElementById('galleryCounter');
    if (counter) counter.textContent = (index + 1) + ' / ' + galleryImages.length;
}

function galleryNav(dir) {
    let next = currentGalleryIndex + dir;
    if (next < 0) next = galleryImages.length - 1;
    if (next >= galleryImages.length) next = 0;
    selectGalleryImage(next);
}

// ===== Size Selection =====
let selectedSizeId = <?= !empty($sizes) ? $sizes[0]['id'] : 'null' ?>;

function selectSize(el) {
    document.querySelectorAll('.size-option').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    selectedSizeId = el.dataset.sizeId;
    const price = parseInt(el.dataset.price);
    document.getElementById('productPrice').innerHTML = 
        new Intl.NumberFormat('uz').format(price).replace(/,/g, ' ') + ' <span>so\'m</span>';
}

function addToCartDetail() {
    addToCart(<?= $productId ?>, selectedSizeId);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
