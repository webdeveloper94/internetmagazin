<?php
// Collect all images
$images = [];
if ($product['image']) $images[] = $product['image'];
if (!empty($product['image2'])) $images[] = $product['image2'];
if (!empty($product['image3'])) $images[] = $product['image3'];
$hasMultiple = count($images) > 1;
$sliderId = 'slider_' . $product['id'];
?>
<div class="product-card">
    <?php if ($hasMultiple): ?>
    <!-- Mini Slider -->
    <div class="card-slider" id="<?= $sliderId ?>">
        <div class="card-slider-track">
            <?php foreach ($images as $i => $img): ?>
            <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $product['id'] ?>" class="card-slide <?= $i === 0 ? 'active' : '' ?>">
                <img src="<?= SITE_URL ?>/uploads/products/<?= $img ?>" alt="<?= sanitize($product['name']) ?>" class="card-img">
            </a>
            <?php endforeach; ?>
        </div>
        <div class="card-slider-dots">
            <?php for ($i = 0; $i < count($images); $i++): ?>
            <span class="card-dot <?= $i === 0 ? 'active' : '' ?>" onclick="slideCard('<?= $sliderId ?>', <?= $i ?>)"></span>
            <?php endfor; ?>
        </div>
        <button class="card-slider-btn card-slider-prev" onclick="slideCard('<?= $sliderId ?>', 'prev')">‹</button>
        <button class="card-slider-btn card-slider-next" onclick="slideCard('<?= $sliderId ?>', 'next')">›</button>
    </div>
    <?php else: ?>
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
    <?php endif; ?>
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
