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

// Get products sorted by most ordered, then by created_at
if ($searchQuery) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, 
                          (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price,
                          (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.product_name = p.name) as order_count
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.name LIKE ? 
                          ORDER BY order_count DESC, p.created_at DESC");
    $stmt->execute(["%{$searchQuery}%"]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query("SELECT p.*, c.name as category_name,
                            (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_size_price,
                            (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.product_name = p.name) as order_count
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            ORDER BY order_count DESC, p.created_at DESC")->fetchAll();
}

// Split products: first 20 for "Tavsiya etamiz", next 10 for "Arzon narxlar", rest loaded via button
$recommendedProducts = array_slice($products, 0, 20);
$cheapProducts = array_slice($products, 20, 10);
$remainingProducts = array_slice($products, 30);
$loadMoreOffset = 30;

// Get active sliders
$sliders = $pdo->query("SELECT * FROM home_sliders WHERE status = 1 ORDER BY sort_order ASC, id DESC")->fetchAll();

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
        <?php if (!empty($sliders)): ?>
        <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <?php foreach ($sliders as $index => $slide): ?>
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($sliders as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="hero-slide" style="background: <?= $slide['bg_color'] ?>;">
                        <?php if ($slide['image']): ?>
                            <div class="slide-bg-image" style="background-image: url('<?= SITE_URL ?>/uploads/sliders/<?= $slide['image'] ?>')"></div>
                        <?php endif; ?>
                        <div class="slide-content">
                            <h2><?= sanitize($slide['title']) ?></h2>
                            <?php if ($slide['subtitle']): ?>
                                <p><?= sanitize($slide['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if ($slide['btn_text']): ?>
                                <?php 
                                    $link = sanitize($slide['btn_link']);
                                    // If link is external (starts with http://, https://, //)
                                    if (preg_match('/^(https?:\/\/|\/\/)/i', $link)) {
                                        $finalLink = $link;
                                    } else {
                                        // Internal link - ensure it starts with / if not already
                                        $finalLink = SITE_URL . (str_starts_with($link, '/') ? '' : '/') . $link;
                                    }
                                ?>
                                <a href="<?= $finalLink ?>" class="btn-hero">
                                    <i class="bi bi-arrow-right-circle"></i> <?= sanitize($slide['btn_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <?php endif; ?>

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

    <!-- ===== TAVSIYA ETAMIZ (5x4 = 20 products) ===== -->
    <?php if (!$viewCatalog): ?>
    <div class="section-title">
        <h2><?= $searchQuery ? '' : 'Tavsiya etamiz' ?></h2>
    </div>

    <?php 
    $displayProducts = $searchQuery ? $products : $recommendedProducts;
    ?>
    <?php if (!empty($displayProducts)): ?>
    <div class="products-grid">
        <?php foreach ($displayProducts as $product): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <h5>Mahsulotlar topilmadi</h5>
            <p>Boshqa so'rov bilan izlab ko'ring</p>
        </div>
    <?php endif; ?>

    <!-- ===== ARZON NARXLAR KAFOLATI ===== -->
    <?php if (!$searchQuery && !empty($cheapProducts)): ?>
    <div class="section-title" style="margin-top:32px;">
        <h2>💰 Arzon narxlar kafolati</h2>
        <a href="<?= SITE_URL ?>/?view=catalog">Hammasi <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="cheap-products-banner">
        <div class="products-grid">
            <?php foreach ($cheapProducts as $product): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== YANA KO'RSATISH TUGMASI ===== -->
    <?php if (!$searchQuery && !empty($remainingProducts)): ?>
    <div id="moreProductsContainer"></div>
    <div class="text-center" style="margin:24px 0 40px;">
        <button class="btn-load-more" id="loadMoreBtn" 
                data-offset="<?= $loadMoreOffset ?>" 
                data-total="<?= count($products) ?>"
                onclick="loadMoreProducts()">
            <i class="bi bi-arrow-down-circle"></i> Yana ko'rsatish 10
        </button>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Product data for load more -->
<?php if (!$searchQuery && !empty($remainingProducts)): ?>
<script>
const allRemainingProducts = <?= json_encode(array_values($remainingProducts)) ?>;
const siteUrl = '<?= SITE_URL ?>';
const isUserLoggedIn = <?= isLoggedIn() ? 'true' : 'false' ?>;
const userFavorites = <?= json_encode($userFavorites) ?>;

function loadMoreProducts() {
    const container = document.getElementById('moreProductsContainer');
    const btn = document.getElementById('loadMoreBtn');
    const currentCount = container.querySelectorAll('.product-card').length;
    const nextProducts = allRemainingProducts.slice(currentCount, currentCount + 10);
    
    if (nextProducts.length === 0) {
        btn.style.display = 'none';
        return;
    }

    let html = '';
    if (currentCount === 0) {
        html += '<div class="products-grid">';
    }
    
    nextProducts.forEach(function(p) {
        const price = (p.has_sizes && p.min_size_price) ? p.min_size_price : p.price;
        const formattedPrice = Number(price).toLocaleString('uz-UZ');
        const isFav = userFavorites.includes(p.id);

        // Collect images
        const imgs = [];
        if (p.image) imgs.push(p.image);
        if (p.image2) imgs.push(p.image2);
        if (p.image3) imgs.push(p.image3);
        const sliderId = 'slider_' + p.id;

        let imgHtml = '';
        if (imgs.length > 1) {
            let slides = '', dots = '';
            imgs.forEach(function(img, i) {
                slides += `<a href="${siteUrl}/pages/product.php?id=${p.id}" class="card-slide ${i===0?'active':''}">
                    <img src="${siteUrl}/uploads/products/${img}" alt="${p.name}" class="card-img"></a>`;
                dots += `<span class="card-dot ${i===0?'active':''}" onclick="slideCard('${sliderId}', ${i})"></span>`;
            });
            imgHtml = `<div class="card-slider" id="${sliderId}">
                <div class="card-slider-track">${slides}</div>
                <div class="card-slider-dots">${dots}</div>
                <button class="card-slider-btn card-slider-prev" onclick="slideCard('${sliderId}', 'prev')">‹</button>
                <button class="card-slider-btn card-slider-next" onclick="slideCard('${sliderId}', 'next')">›</button>
            </div>`;
        } else if (p.image) {
            imgHtml = `<a href="${siteUrl}/pages/product.php?id=${p.id}"><img src="${siteUrl}/uploads/products/${p.image}" alt="${p.name}" class="card-img"></a>`;
        } else {
            imgHtml = `<a href="${siteUrl}/pages/product.php?id=${p.id}"><div class="card-img" style="display:flex;align-items:center;justify-content:center;background:var(--bg);"><i class="bi bi-image" style="font-size:2rem;color:var(--border);"></i></div></a>`;
        }
        
        let actionsHtml = '';
        if (isUserLoggedIn) {
            actionsHtml = `
                <button class="btn-cart" onclick="addToCart(${p.id})"><i class="bi bi-cart-plus"></i> Savatga</button>
                <button class="btn-fav ${isFav ? 'active' : ''}" onclick="toggleFavorite(${p.id}, this)">
                    <i class="bi bi-heart${isFav ? '-fill' : ''}"></i>
                </button>`;
        } else {
            actionsHtml = `<a href="${siteUrl}/auth/login.php" class="btn-cart"><i class="bi bi-cart-plus"></i> Savatga</a>`;
        }

        html += `
        <div class="product-card">
            ${imgHtml}
            <div class="card-body">
                <div class="card-price">
                    ${formattedPrice} <span>so'm</span>
                    ${p.has_sizes ? '<small style="color:var(--text-light);font-size:0.7rem;">dan</small>' : ''}
                </div>
                <a href="${siteUrl}/pages/product.php?id=${p.id}" class="card-title">${p.name}</a>
                <div class="card-actions">${actionsHtml}</div>
            </div>
        </div>`;
    });

    if (currentCount === 0) {
        html += '</div>';
        container.innerHTML = html;
    } else {
        container.querySelector('.products-grid').insertAdjacentHTML('beforeend', html);
    }

    // Hide button if no more products
    const totalShown = currentCount + nextProducts.length;
    if (totalShown >= allRemainingProducts.length) {
        btn.style.display = 'none';
    }
}
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
