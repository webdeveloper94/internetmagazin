<?php
/**
 * Savatcha Sahifasi
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Login talab qilish
require_login();

$page_title = 'Savatcha - ' . SITE_NAME;

// Savatcha mahsulotlarini olish
$db = Database::getInstance();
$sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? 
        ORDER BY c.added_at DESC";
$cart_items = $db->fetchAll($sql, [$_SESSION['user_id']]);

// Jami narxni hisoblash
$total_price = 0;
if ($cart_items) {
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Savatcham</h1>
        </div>
        
        <?php if ($cart_items && count($cart_items) > 0): ?>
            <div class="cart-layout">
                <!-- Savatcha mahsulotlari -->
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                            <div class="cart-item-image">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <div class="product-placeholder-small">📦</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="cart-item-price"><?php echo format_price($item['price']); ?></p>
                                
                                <?php if ($item['stock'] < $item['quantity']): ?>
                                    <p class="text-danger">⚠️ Omborda faqat <?php echo $item['stock']; ?> dona mavjud</p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="cart-item-actions">
                                <div class="quantity-control">
                                    <button class="qty-btn qty-minus" data-product-id="<?php echo $item['product_id']; ?>">-</button>
                                    <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['stock']; ?>" readonly>
                                    <button class="qty-btn qty-plus" data-product-id="<?php echo $item['product_id']; ?>" 
                                            data-max="<?php echo $item['stock']; ?>">+</button>
                                </div>
                                
                                <p class="cart-item-total">
                                    <?php echo format_price($item['price'] * $item['quantity']); ?>
                                </p>
                                
                                <button class="btn-remove" data-product-id="<?php echo $item['product_id']; ?>">
                                    🗑️ O'chirish
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Buyurtma summasi -->
                <div class="cart-summary">
                    <h3>Buyurtma Ma'lumotlari</h3>
                    
                    <div class="summary-row">
                        <span>Mahsulotlar:</span>
                        <span><?php echo count($cart_items); ?> dona</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Jami miqdor:</span>
                        <span><?php echo array_sum(array_column($cart_items, 'quantity')); ?> dona</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row summary-total">
                        <span>Jami narx:</span>
                        <span class="total-price"><?php echo format_price($total_price); ?></span>
                    </div>
                    
                    <button class="btn btn-primary btn-block" id="placeOrderBtn">
                        Buyurtma Berish
                    </button>
                    
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary btn-block">
                        Xaridni davom ettirish
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <h2>Savatchangiz bo'sh</h2>
                <p>Hozircha savatchada mahsulot yo'q</p>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    Xarid qilishni boshlash
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Contact Info Modal -->
<div class="modal fade" id="contactInfoModal" tabindex="-1" aria-labelledby="contactInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactInfoModalLabel">
                    <i class="bi bi-info-circle"></i> Kontakt Ma'lumotlari
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3 text-muted">
                    Buyurtmani qabul qilish va yetkazib berish uchun telefon raqam va manzil kerak.
                </p>
                
                <div id="contactInfoError" class="alert alert-danger" style="display: none;"></div>
                
                <form id="contactInfoForm">
                    <div class="mb-3">
                        <label for="modalPhone" class="form-label">
                            <i class="bi bi-telephone"></i> Telefon Raqam <span class="text-danger">*</span>
                        </label>
                        <input type="tel" class="form-control" id="modalPhone" name="phone" 
                               placeholder="+998901234567" required>
                        <small class="text-muted">Format: +998XXXXXXXXX</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modalAddress" class="form-label">
                            <i class="bi bi-geo-alt"></i> To'liq Manzil <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="modalAddress" name="address" rows="3" 
                                  placeholder="Shahar, tuman, ko'cha, uy" required></textarea>
                        <small class="text-muted">Kamida 10 ta belgi</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" id="saveContactInfoBtn">
                    <i class="bi bi-check-circle"></i> Saqlash va Davom Etish
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
