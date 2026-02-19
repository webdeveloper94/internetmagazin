<?php
/**
 * Admin Dashboard
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Admin huquqini tekshirish
require_admin();

$page_title = 'Dashboard - Admin Panel';
$db = Database::getInstance();

// Statistika ma'lumotlarini olish
$stats = [];

// Jami foydalanuvchilar
$users_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$stats['users'] = $db->fetchOne($users_sql)['total'] ?? 0;

// Jami mahsulotlar
$products_sql = "SELECT COUNT(*) as total FROM products";
$stats['products'] = $db->fetchOne($products_sql)['total'] ?? 0;

// Jami buyurtmalar
$orders_sql = "SELECT COUNT(*) as total FROM orders";
$stats['orders'] = $db->fetchOne($orders_sql)['total'] ?? 0;

// Jami kategoriyalar
$categories_sql = "SELECT COUNT(*) as total FROM categories";
$stats['categories'] = $db->fetchOne($categories_sql)['total'] ?? 0;

// Kutilayotgan buyurtmalar
$pending_sql = "SELECT COUNT(*) as total FROM orders WHERE status = 'kutilmoqda'";
$stats['pending'] = $db->fetchOne($pending_sql)['total'] ?? 0;

// Jami savdo
$revenue_sql = "SELECT SUM(total_price) as total FROM orders WHERE status = 'tasdiqlandi'";
$stats['revenue'] = $db->fetchOne($revenue_sql)['total'] ?? 0;

// So'nggi buyurtmalar
$latest_orders_sql = "SELECT o.*, u.name as user_name 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      ORDER BY o.created_at DESC 
                      LIMIT 10";
$latest_orders = $db->fetchAll($latest_orders_sql);

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Dashboard</h1>
    <p>Admin panelga xush kelibsiz</p>
</div>

<!-- Statistika Kartochkalari -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['users']); ?></h3>
            <p>Foydalanuvchilar</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['products']); ?></h3>
            <p>Mahsulotlar</p>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon">🛒</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['orders']); ?></h3>
            <p>Buyurtmalar</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">📁</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['categories']); ?></h3>
            <p>Kategoriyalar</p>
        </div>
    </div>
    
    <div class="stat-card stat-danger">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['pending']); ?></h3>
            <p>Kutilmoqda</p>
        </div>
    </div>
    
    <div class="stat-card stat-revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <h3><?php echo format_price($stats['revenue']); ?></h3>
            <p>Tasdiqlangan Savdo</p>
        </div>
    </div>
</div>

<!-- So'nggi Buyurtmalar -->
<div class="admin-card">
    <div class="card-header">
        <h2>So'nggi Buyurtmalar</h2>
        <a href="orders.php" class="btn btn-sm btn-primary">Barchasini ko'rish</a>
    </div>
    
    <div class="table-responsive">
        <?php if ($latest_orders && count($latest_orders) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foydalanuvchi</th>
                        <th>Jami narx</th>
                        <th>Status</th>
                        <th>Sana</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest_orders as $order): ?>
                        <?php
                        $status_class = '';
                        switch ($order['status']) {
                            case 'kutilmoqda': $status_class = 'badge-warning'; break;
                            case 'tasdiqlandi': $status_class = 'badge-success'; break;
                            case 'rad_etildi': $status_class = 'badge-danger'; break;
                        }
                        ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo format_price($order['total_price']); ?></td>
                            <td>
                                <span class="badge <?php echo $status_class; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo format_date($order['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Buyurtmalar yo'q</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
