<?php
/**
 * Admin: Buyurtmalar Boshqaruvi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Buyurtmalar - Admin Panel';
$db = Database::getInstance();

// Filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Buyurtmalarni olish
$sql = "SELECT o.*, u.name as user_name, u.username FROM orders o JOIN users u ON o.user_id = u.id";
if ($status_filter) {
    $sql .= " WHERE o.status = ?";
    $orders = $db->fetchAll($sql, [$status_filter]);
} else {
    $sql .= " ORDER BY o.created_at DESC";
    $orders = $db->fetchAll($sql);
}

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Buyurtmalar Boshqaruvi</h1>
    
    <!-- Filter -->
    <div class="filter-buttons">
        <a href="orders.php" class="btn btn-sm <?php echo !$status_filter ? 'btn-primary' : 'btn-secondary'; ?>">
            Barchasi
        </a>
        <a href="orders.php?status=kutilmoqda" class="btn btn-sm <?php echo $status_filter === 'kutilmoqda' ? 'btn-warning' : 'btn-secondary'; ?>">
            Kutilmoqda
        </a>
        <a href="orders.php?status=tasdiqlandi" class="btn btn-sm <?php echo $status_filter === 'tasdiqlandi' ? 'btn-success' : 'btn-secondary'; ?>">
            Tasdiqlandi
        </a>
        <a href="orders.php?status=rad_etildi" class="btn btn-sm <?php echo $status_filter === 'rad_etildi' ? 'btn-danger' : 'btn-secondary'; ?>">
            Rad etildi
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <?php if ($orders && count($orders) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foydalanuvchi</th>
                        <th>Jami narx</th>
                        <th>Status</th>
                        <th>Sana</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $status_class = '';
                        switch ($order['status']) {
                            case 'kutilmoqda': $status_class = 'badge-warning'; break;
                            case 'tasdiqlandi': $status_class = 'badge-success'; break;
                            case 'rad_etildi': $status_class = 'badge-danger'; break;
                        }
                        ?>
                        <tr id="order-row-<?php echo $order['id']; ?>">
                            <td><?php echo $order['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['user_name']); ?>
                                <small>(@<?php echo htmlspecialchars($order['username']); ?>)</small>
                            </td>
                            <td><?php echo format_price($order['total_price']); ?></td>
                            <td>
                                <span class="badge <?php echo $status_class; ?>" id="status-badge-<?php echo $order['id']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo format_date($order['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info view-order-btn" 
                                            data-id="<?php echo $order['id']; ?>">
                                        Ko'rish
                                    </button>
                                    
                                    <?php if ($order['status'] === 'kutilmoqda'): ?>
                                        <button class="btn btn-sm btn-success update-status-btn" 
                                                data-id="<?php echo $order['id']; ?>" 
                                                data-status="tasdiqlandi">
                                            Tasdiqlash
                                        </button>
                                        <button class="btn btn-sm btn-danger update-status-btn" 
                                                data-id="<?php echo $order['id']; ?>" 
                                                data-status="rad_etildi">
                                            Rad etish
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Buyurtmalar yo'q</p>
        <?php endif; ?>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Buyurtma Tafsilotlari</h2>
            <span class="modal-close" onclick="closeOrderModal()">&times;</span>
        </div>
        <div id="orderDetails">
            <!-- AJAX orqali yuklanadi -->
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
