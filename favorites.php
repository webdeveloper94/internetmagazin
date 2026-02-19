<?php
/**
 * Sevimlilar sahifasi
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

require_login();

$page_title = "Sevimlilar - " . SITE_NAME;
$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

// Sevimlilarni olish
$favorites_sql = "SELECT p.*, c.name as category_name, f.created_at as favorited_at
                  FROM favorites f
                  INNER JOIN products p ON f.product_id = p.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE f.user_id = ?
                  ORDER BY f.created_at DESC";
$favorites = $db->fetchAll($favorites_sql, [$user_id]);

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>💖 Sevimlilar</h1>
            <p>Sizning tanlovlaringiz</p>
        </div>

        <?php if (!empty($favorites)): ?>
            <div class="products-grid">
                <?php foreach ($favorites as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL . '/' . UPLOAD_PATH . $product['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="product-placeholder">📦</div>
                            <?php endif; ?>
                            
                            <!-- Favorite Button -->
                            <button class="favorite-btn active" data-product-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                            
                            <?php if ($product['stock'] == 0): ?>
                                <span class="product-badge out-of-stock">Tugagan</span>
                            <?php elseif ($product['stock'] < 10): ?>
                                <span class="product-badge low-stock">Kam qoldi</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            
                            <?php if ($product['description']): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="product-footer">
                                <div class="product-price">
                                    <?php echo format_price($product['price']); ?>
                                </div>
                                
                                <?php if ($product['stock'] > 0): ?>
                                    <button class="btn btn-primary btn-sm add-to-cart" 
                                            data-product-id="<?php echo $product['id']; ?>">
                                        <i class="bi bi-cart-plus"></i>
                                        Savatga
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        Tugagan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">💖</div>
                <h3>Sevimlilar bo'sh</h3>
                <p>Hali hech narsa tanlamagansiz. Mahsulotlarni ko'rib, ❤️ tugmasini bosing!</p>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    <i class="bi bi-shop"></i>
                    Xarid qilish
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
