<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$statusLabels = [
    'new' => 'Yangi',
    'rejected' => 'Rad etildi',
    'confirmed' => 'Tasdiqlangan',
    'assembling' => "Yig'ilmoqda",
    'shipped' => "Jo'natildi",
    'delivered' => 'Yetkazildi'
];

// Date filter
$dateFrom = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$dateTo = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// Get filtered orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC");
$stmt->execute([$dateFrom, $dateTo]);
$orders = $stmt->fetchAll();

// Summary stats
$totalOrders = count($orders);
$totalRevenue = 0;
$statusCounts = array_fill_keys(array_keys($statusLabels), 0);

foreach ($orders as $order) {
    if ($order['status'] !== 'rejected') {
        $totalRevenue += $order['total_amount'];
    }
    $statusCounts[$order['status']]++;
}

$adminPageTitle = 'Hisobotlar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-graph-up"></i> Hisobotlar</h3>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <label class="text-muted" style="font-size:0.85rem;white-space:nowrap;">Sanadan:</label>
            <input type="date" name="from" class="form-control form-control-sm" value="<?= $dateFrom ?>" style="width:150px;">
            <label class="text-muted" style="font-size:0.85rem;white-space:nowrap;">Sanagacha:</label>
            <input type="date" name="to" class="form-control form-control-sm" value="<?= $dateTo ?>" style="width:150px;">
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Ko'rsatish</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(112,0,255,0.1);color:var(--primary);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-label">Jami buyurtmalar</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(40,167,69,0.1);color:var(--success);">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-number"><?= formatPrice($totalRevenue) ?></div>
                <div class="stat-label">Jami daromad (so'm)</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(23,162,184,0.1);color:var(--info);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-number"><?= $statusCounts['delivered'] ?></div>
                <div class="stat-label">Yetkazilgan</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(220,53,69,0.1);color:var(--danger);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-number"><?= $statusCounts['rejected'] ?></div>
                <div class="stat-label">Rad etilgan</div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="admin-table mb-4">
        <div class="p-3">
            <h6 class="fw-bold mb-3">Buyurtmalar holati bo'yicha</h6>
            <div class="row g-2">
                <?php foreach ($statusLabels as $key => $label): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="text-center p-2" style="background:var(--bg);border-radius:var(--radius-sm);">
                        <div class="fw-bold" style="font-size:1.3rem;"><?= $statusCounts[$key] ?></div>
                        <span class="order-status-badge status-<?= $key ?>"><?= $label ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="admin-table">
        <div class="p-3">
            <h6 class="fw-bold mb-0">
                Buyurtmalar ro'yxati 
                <span class="text-muted fw-normal">(<?= date('d.m.Y', strtotime($dateFrom)) ?> — <?= date('d.m.Y', strtotime($dateTo)) ?>)</span>
            </h6>
        </div>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mijoz</th>
                    <th>Telefon</th>
                    <th>Manzil</th>
                    <th>Summa</th>
                    <th>Status</th>
                    <th>Sana</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Bu sanalar oralig'ida buyurtmalar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td><?= sanitize($order['full_name']) ?></td>
                        <td>
                            <a href="tel:<?= $order['phone'] ?>" style="color:var(--primary);">
                                <?= sanitize($order['phone']) ?>
                            </a>
                        </td>
                        <td class="text-muted" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= sanitize($order['address']) ?>
                        </td>
                        <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                        <td>
                            <span class="order-status-badge status-<?= $order['status'] ?>">
                                <?= $statusLabels[$order['status']] ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
