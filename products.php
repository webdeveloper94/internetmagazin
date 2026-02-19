<?php
/**
 * Mahsulotlar sahifasi - Kategoriya bo'yicha filtrlangan mahsulotlar
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$db = Database::getInstance();

// Kategoriya ID ni olish
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    // Kategoriya ma'lumotlarini olish
    $category_sql = "SELECT * FROM categories WHERE id = ?";
    $category = $db->fetchOne($category_sql, [$category_id]);
    
    if (!$category) {
        header('Location: ' . SITE_URL . '/categories.php');
        exit;
    }
    
    $page_title = $category['name'] . " - " . SITE_NAME;
    
    // Kategoriya bo'yicha mahsulotlarni olish
    $products_sql = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.category_id = ? 
                     ORDER BY p.created_at DESC";
    $products = $db->fetchAll($products_sql, [$category_id]);
} else {
    // Barcha mahsulotlarni ko'rsatish
    $page_title = "Barcha Mahsulotlar - " . SITE_NAME;
    $category = null;
    
    $products_sql = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC";
    $products = $db->fetchAll($products_sql);
}

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Asosiy</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?php echo SITE_URL; ?>/categories.php">Kategoriyalar</a>
            <?php if ($category): ?>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current"><?php echo htmlspecialchars($category['name']); ?></span>
            <?php endif; ?>
        </nav>
        
        <!-- Page Header -->
        <div class="page-header">
            <?php if ($category): ?>
                <h1>
                    <?php 
                    $icons = [
                        '📱' => 'Elektronika',
                        '👔' => 'Kiyim',
                        '🏠' => 'Uy',
                        '⚽' => 'Sport',
                        '📚' => 'Kitob',
                        '🎮' => 'O\'yin',
                    ];
                    
                    $icon = '📦';
                    foreach ($icons as $emoji => $keyword) {
                        if (stripos($category['name'], $keyword) !== false) {
                            $icon = $emoji;
                            break;
                        }
                    }
                    echo $icon . ' ';
                    echo htmlspecialchars($category['name']);
                    ?>
                </h1>
                <?php if ($category['description']): ?>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                <?php endif; ?>
                <div class="products-count">
                    <?php echo count($products); ?> ta mahsulot topildi
                </div>
            <?php else: ?>
                <h1>📦 Barcha Mahsulotlar</h1>
                <p>Bizning barcha mahsulotlarimiz</p>
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL . '/' . UPLOAD_PATH . $product['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="product-placeholder">📦</div>
                            <?php endif; ?>
                            
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
                                
                                <?php if (is_logged_in()): ?>
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
                                <?php else: ?>
                                    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-primary btn-sm">
                                        Kirish
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Mahsulotlar topilmadi</h3>
                <p>Bu kategoriyada hozircha mahsulotlar yo'q.</p>
                <a href="<?php echo SITE_URL; ?>/categories.php" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i>
                    Kategoriyalarga qaytish
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    font-size: 0.9rem;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: #764ba2;
    text-decoration: underline;
}

.breadcrumb-separator {
    color: #d1d5db;
}

.breadcrumb-current {
    color: #6b7280;
    font-weight: 600;
}

.products-count {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 1rem;
    margin-top: 2rem;
}

.empty-icon {
    font-size: 6rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 2rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>
