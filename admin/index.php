<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Dashboard stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'rejected'")->fetchColumn();
$newOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

$adminPageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline">Dashboard</h3>
        </div>
        <span class="text-muted">Bugun: <?= date('d.m.Y') ?></span>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(112,0,255,0.1);color:var(--primary);">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-number"><?= $totalUsers ?></div>
                <div class="stat-label">Foydalanuvchilar</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(40,167,69,0.1);color:var(--success);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-number"><?= $totalProducts ?></div>
                <div class="stat-label">Mahsulotlar</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(255,193,7,0.1);color:var(--warning);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-label">Buyurtmalar <?php if ($newOrders > 0): ?><span class="badge bg-danger"><?= $newOrders ?> yangi</span><?php endif; ?></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(23,162,184,0.1);color:var(--info);">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-number"><?= formatPrice($totalRevenue) ?></div>
                <div class="stat-label">Jami daromad (so'm)</div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-table">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">So'nggi buyurtmalar</h5>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">Hammasi</a>
        </div>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mijoz</th>
                    <th>Telefon</th>
                    <th>Summa</th>
                    <th>Status</th>
                    <th>Sana</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Buyurtmalar yo'q</td></tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td><?= sanitize($order['full_name']) ?></td>
                        <td><?= sanitize($order['phone']) ?></td>
                        <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                        <td>
                            <?php
                            $statusLabels = ['new'=>'Yangi','confirmed'=>'Tasdiqlangan','assembling'=>"Yig'ilmoqda",'shipped'=>"Jo'natildi",'delivered'=>'Yetkazildi','rejected'=>'Rad etildi'];
                            ?>
                            <span class="order-status-badge status-<?= $order['status'] ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
