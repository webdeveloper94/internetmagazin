<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Get cart items with product info
$stmt = $pdo->prepare("SELECT c.*, p.name, p.image, p.price as base_price, p.has_sizes,
                        ps.size_name, ps.price as size_price
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        LEFT JOIN product_sizes ps ON c.size_id = ps.id
                        WHERE c.user_id = ? 
                        ORDER BY c.id DESC");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $price = $item['size_price'] ?? $item['base_price'];
    $total += $price * $item['quantity'];
}

$pageTitle = 'Savat';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <div class="section-title">
        <h2><i class="bi bi-cart3"></i> Savat</h2>
    </div>

    <?php if (!empty($cartItems)): ?>
    <div class="row g-4">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <?php foreach ($cartItems as $item): ?>
            <?php $itemPrice = $item['size_price'] ?? $item['base_price']; ?>
            <div class="cart-item">
                <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $item['product_id'] ?>">
                    <?php if ($item['image']): ?>
                        <img src="<?= SITE_URL ?>/uploads/products/<?= $item['image'] ?>" alt="">
                    <?php else: ?>
                        <div style="width:80px;height:80px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-image" style="color:var(--border);"></i>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="cart-item-info">
                    <h6><?= sanitize($item['name']) ?></h6>
                    <?php if ($item['size_name']): ?>
                        <span class="size-label"><?= sanitize($item['size_name']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="qty-control">
                    <button onclick="updateCartQuantity(<?= $item['id'] ?>, -1)">−</button>
                    <span><?= $item['quantity'] ?></span>
                    <button onclick="updateCartQuantity(<?= $item['id'] ?>, 1)">+</button>
                </div>
                <div class="cart-item-price">
                    <?= formatPrice($itemPrice * $item['quantity']) ?> so'm
                </div>
                <button onclick="removeFromCart(<?= $item['id'] ?>)" 
                        style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:1.2rem;padding:4px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="cart-summary">
                <h5>Buyurtma</h5>
                <div class="summary-row">
                    <span>Mahsulotlar (<?= count($cartItems) ?>)</span>
                    <span><?= formatPrice($total) ?> so'm</span>
                </div>
                <div class="summary-row">
                    <span>Yetkazib berish</span>
                    <span style="color:var(--success);">Bepul</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Jami</span>
                    <span style="color:var(--primary);"><?= formatPrice($total) ?> so'm</span>
                </div>
                <a href="<?= SITE_URL ?>/pages/checkout.php" class="btn-checkout">
                    <i class="bi bi-bag-check"></i> Buyurtma berish
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-cart-x"></i>
            <h5>Savat bo'sh</h5>
            <p>Mahsulotlarni savatga qo'shing</p>
            <a href="<?= SITE_URL ?>" class="btn-checkout" style="display:inline-block;width:auto;padding:12px 24px;margin-top:12px;">
                <i class="bi bi-shop"></i> Xarid qilish
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
