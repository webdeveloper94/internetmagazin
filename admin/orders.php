<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$message = '';
$messageType = '';

$statusLabels = [
    'new' => 'Yangi',
    'rejected' => 'Rad etildi',
    'confirmed' => 'Tasdiqlangan',
    'assembling' => "Yig'ilmoqda",
    'shipped' => "Jo'natildi",
    'delivered' => 'Yetkazildi'
];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['status'];
    $validStatuses = array_keys($statusLabels);
    if (in_array($status, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $message = "Buyurtma #{$orderId} holati yangilandi";
        $messageType = 'success';
    }
}

// Filter
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
if ($filterStatus && array_key_exists($filterStatus, $statusLabels)) {
    $stmt = $pdo->prepare("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = ? ORDER BY o.created_at DESC");
    $stmt->execute([$filterStatus]);
} else {
    $stmt = $pdo->query("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
}
$orders = $stmt->fetchAll();

$adminPageTitle = 'Buyurtmalar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-receipt"></i> Buyurtmalar</h3>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:180px;">
                <option value="">Barcha statuslar</option>
                <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $filterStatus === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="admin-table p-4 text-center text-muted">
            <i class="bi bi-receipt" style="font-size:2rem;"></i>
            <p class="mt-2">Buyurtmalar topilmadi</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
        <?php
            $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$order['id']]);
            $orderItems = $itemsStmt->fetchAll();
        ?>
        <div class="admin-table mb-3">
            <div class="p-3">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong style="font-size:1.1rem;">Buyurtma #<?= $order['id'] ?></strong>
                        <div class="text-muted" style="font-size:0.85rem;">
                            <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div><i class="bi bi-person"></i> <?= sanitize($order['full_name']) ?></div>
                        <div><i class="bi bi-telephone"></i> 
                            <a href="tel:<?= $order['phone'] ?>" style="color:var(--primary);"><?= sanitize($order['phone']) ?></a>
                        </div>
                        <div class="text-muted" style="font-size:0.8rem;">
                            <i class="bi bi-geo-alt"></i> <?= sanitize($order['address']) ?>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <strong style="font-size:1.2rem;color:var(--primary);"><?= formatPrice($order['total_amount']) ?></strong>
                        <div class="text-muted" style="font-size:0.8rem;">so'm</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <span class="order-status-badge status-<?= $order['status'] ?>">
                            <?= $statusLabels[$order['status']] ?>
                        </span>
                    </div>
                    <div class="col-md-2">
                        <form method="POST" class="d-flex gap-1">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach ($statusLabels as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check"></i></button>
                        </form>
                    </div>
                </div>

                <!-- Order Items -->
                <?php if (!empty($orderItems)): ?>
                <div class="mt-3 pt-3" style="border-top:1px solid var(--border);">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Mahsulot</th>
                                <th>O'lcham</th>
                                <th>Narx</th>
                                <th>Soni</th>
                                <th>Jami</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $oi): ?>
                            <tr>
                                <td><?= sanitize($oi['product_name']) ?></td>
                                <td><?= $oi['size_name'] ? sanitize($oi['size_name']) : '—' ?></td>
                                <td><?= formatPrice($oi['price']) ?></td>
                                <td><?= $oi['quantity'] ?></td>
                                <td><strong><?= formatPrice($oi['price'] * $oi['quantity']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
