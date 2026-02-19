<?php
/**
 * Buyurtmalar Tarixi
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Login talab qilish
require_login();

$page_title = 'Buyurtmalar - ' . SITE_NAME;

// Buyurtmalarni olish
$db = Database::getInstance();
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$orders = $db->fetchAll($sql, [$_SESSION['user_id']]);

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Mening Buyurtmalarim</h1>
        </div>
        
        <?php if ($orders && count($orders) > 0): ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <?php
                    // Buyurtma mahsulotlarini olish
                    $items_sql = "SELECT * FROM order_items WHERE order_id = ?";
                    $items = $db->fetchAll($items_sql, [$order['id']]);
                    
                    // Status rangini aniqlash
                    $status_class = '';
                    $status_text = '';
                    switch ($order['status']) {
                        case 'kutilmoqda':
                            $status_class = 'status-pending';
                            $status_text = '⏳ Kutilmoqda';
                            break;
                        case 'tasdiqlandi':
                            $status_class = 'status-approved';
                            $status_text = '✅ Tasdiqlandi';
                            break;
                        case 'rad_etildi':
                            $status_class = 'status-rejected';
                            $status_text = '❌ Rad etildi';
                            break;
                    }
                    ?>
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                <strong>Buyurtma #<?php echo $order['id']; ?></strong>
                                <span class="order-date"><?php echo format_date($order['created_at']); ?></span>
                            </div>
                            <span class="order-status <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <span class="item-qty">×<?php echo $item['quantity']; ?></span>
                                    <span class="item-price"><?php echo format_price($item['price']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <span>Jami:</span>
                            <span class="order-total"><?php echo format_price($order['total_price']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2>Buyurtmalar yo'q</h2>
                <p>Siz hali buyurtma bermagansiz</p>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    Xarid qilishni boshlash
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
