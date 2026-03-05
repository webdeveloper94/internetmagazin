<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$currentUser = getCurrentUser();

$statusLabels = [
    'new' => 'Yangi',
    'confirmed' => 'Tasdiqlangan',
    'assembling' => "Yig'ilmoqda",
    'shipped' => "Jo'natildi",
    'delivered' => 'Yetkazildi',
    'rejected' => 'Rad etildi'
];

// Get user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'Profil';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <div class="row g-4">
        <!-- Profile Info -->
        <div class="col-lg-4">
            <div class="profile-card text-center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#fff;font-size:2rem;font-weight:700;">
                    <?= mb_substr($currentUser['full_name'], 0, 1) ?>
                </div>
                <h5 style="font-weight:700;"><?= sanitize($currentUser['full_name']) ?></h5>
                <p class="text-muted"><?= sanitize($currentUser['phone']) ?></p>
                <hr>
                <a href="<?= SITE_URL ?>/pages/favorites.php" class="d-block text-start py-2" style="color:var(--text-dark);">
                    <i class="bi bi-heart me-2"></i> Sevimlilar 
                    <span class="float-end text-muted"><?= getFavoritesCount() ?></span>
                </a>
                <a href="<?= SITE_URL ?>/pages/cart.php" class="d-block text-start py-2" style="color:var(--text-dark);">
                    <i class="bi bi-cart3 me-2"></i> Savat
                    <span class="float-end text-muted"><?= getCartCount() ?></span>
                </a>
                <hr>
                <a href="<?= SITE_URL ?>/auth/logout.php" class="d-block text-start py-2" style="color:var(--danger);">
                    <i class="bi bi-box-arrow-left me-2"></i> Chiqish
                </a>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-lg-8">
            <div class="profile-card">
                <h5 style="font-weight:700;margin-bottom:20px;">
                    <i class="bi bi-clock-history"></i> Buyurtmalarim
                </h5>

                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <div style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>Buyurtma #<?= $order['id'] ?></strong>
                                <span class="text-muted ms-2" style="font-size:0.85rem;">
                                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                </span>
                            </div>
                            <span class="order-status-badge status-<?= $order['status'] ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size:0.85rem;">
                                <i class="bi bi-geo-alt"></i> <?= sanitize(mb_substr($order['address'], 0, 40)) ?>...
                            </span>
                            <strong style="color:var(--primary);"><?= formatPrice($order['total_amount']) ?> so'm</strong>
                        </div>

                        <?php
                        // Get order items
                        $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                        $itemsStmt->execute([$order['id']]);
                        $orderItems = $itemsStmt->fetchAll();
                        ?>
                        <?php if (!empty($orderItems)): ?>
                        <div class="mt-2 pt-2" style="border-top:1px solid var(--border);font-size:0.85rem;color:var(--text-muted);">
                            <?php foreach ($orderItems as $oi): ?>
                                <div><?= sanitize($oi['product_name']) ?>
                                    <?php if ($oi['size_name']): ?>(<?= sanitize($oi['size_name']) ?>)<?php endif; ?>
                                    × <?= $oi['quantity'] ?> — <?= formatPrice($oi['price'] * $oi['quantity']) ?> so'm
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="padding:40px;">
                        <i class="bi bi-receipt"></i>
                        <h5>Buyurtmalar yo'q</h5>
                        <p>Birinchi buyurtmangizni bering!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
