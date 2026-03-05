<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.image, p.price as base_price,
                        ps.size_name, ps.price as size_price
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        LEFT JOIN product_sizes ps ON c.size_id = ps.id
                        WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    header('Location: ' . SITE_URL . '/pages/cart.php');
    exit;
}

$total = 0;
foreach ($cartItems as $item) {
    $price = $item['size_price'] ?? $item['base_price'];
    $total += $price * $item['quantity'];
}

$currentUser = getCurrentUser();
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($fullName) || empty($phone) || empty($address)) {
        $error = "Barcha maydonlarni to'ldiring";
    } else {
        try {
            $pdo->beginTransaction();

            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, phone, address, total_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $fullName, $phone, $address, $total]);
            $orderId = $pdo->lastInsertId();

            // Add order items
            foreach ($cartItems as $item) {
                $price = $item['size_price'] ?? $item['base_price'];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size_id, product_name, size_name, price, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['size_id'],
                    $item['name'],
                    $item['size_name'],
                    $price,
                    $item['quantity']
                ]);
            }

            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Xatolik yuz berdi. Qaytadan urinib ko'ring.";
        }
    }
}

$pageTitle = 'Buyurtma berish';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container page-wrapper">
    <?php if ($success): ?>
        <div class="empty-state">
            <i class="bi bi-check-circle" style="color:var(--success);"></i>
            <h5>Buyurtma muvaffaqiyatli qabul qilindi!</h5>
            <p>Buyurtma raqami: #<?= $orderId ?>. Tez orada siz bilan bog'lanamiz.</p>
            <a href="<?= SITE_URL ?>" class="btn-checkout" style="display:inline-block;width:auto;padding:12px 24px;margin-top:12px;">
                <i class="bi bi-house"></i> Bosh sahifaga
            </a>
            <a href="<?= SITE_URL ?>/pages/profile.php" class="btn-checkout" 
               style="display:inline-block;width:auto;padding:12px 24px;margin-top:12px;background:var(--text-dark);">
                <i class="bi bi-clock-history"></i> Buyurtmalarim
            </a>
        </div>
    <?php else: ?>
        <div class="section-title">
            <h2><i class="bi bi-bag-check"></i> Buyurtma berish</h2>
        </div>

        <div class="row g-4">
            <!-- Checkout Form -->
            <div class="col-lg-7">
                <div class="checkout-form">
                    <h5 style="font-weight:700;margin-bottom:20px;">Yetkazib berish ma'lumotlari</h5>

                    <?php if ($error): ?>
                        <div class="alert-custom alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ism va familiya</label>
                            <input type="text" name="full_name" class="form-control" 
                                   value="<?= sanitize($currentUser['full_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telefon raqam</label>
                            <input type="tel" name="phone" class="form-control phone-mask" 
                                   value="<?= sanitize($currentUser['phone']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Manzil (to'liq)</label>
                            <textarea name="address" class="form-control" rows="3" 
                                      placeholder="Shahar, tuman, ko'cha, uy raqami" required></textarea>
                        </div>
                        <button type="submit" class="btn-checkout">
                            <i class="bi bi-check2-circle"></i> Buyurtma berish
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="cart-summary">
                    <h5>Buyurtma tafsilotlari</h5>
                    <?php foreach ($cartItems as $item): ?>
                    <?php $itemPrice = $item['size_price'] ?? $item['base_price']; ?>
                    <div class="d-flex gap-2 align-items-center mb-2" style="font-size:0.9rem;">
                        <?php if ($item['image']): ?>
                            <img src="<?= SITE_URL ?>/uploads/products/<?= $item['image'] ?>" 
                                 style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <?= sanitize($item['name']) ?>
                            <?php if ($item['size_name']): ?>
                                <small class="text-muted">(<?= sanitize($item['size_name']) ?>)</small>
                            <?php endif; ?>
                            <small class="text-muted">× <?= $item['quantity'] ?></small>
                        </div>
                        <span class="fw-bold"><?= formatPrice($itemPrice * $item['quantity']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="summary-row summary-total">
                        <span>Jami</span>
                        <span style="color:var(--primary);"><?= formatPrice($total) ?> so'm</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
