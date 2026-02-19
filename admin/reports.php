<?php
/**
 * Admin: Hisobotlar
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Hisobotlar - Admin Panel';
$db = Database::getInstance();

// Umumiy statistika
$total_revenue_sql = "SELECT SUM(total_price) as total FROM orders WHERE status = 'tasdiqlandi'";
$total_revenue = $db->fetchOne($total_revenue_sql)['total'] ?? 0;

$total_orders_sql = "SELECT COUNT(*) as total FROM orders WHERE status = 'tasdiqlandi'";
$total_orders = $db->fetchOne($total_orders_sql)['total'] ?? 0;

// Kategoriya bo'yicha sotuvlar
$category_sales_sql = "SELECT c.name, COUNT(DISTINCT oi.order_id) as order_count, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
                       FROM categories c
                       LEFT JOIN products p ON c.id = p.category_id
                       LEFT JOIN order_items oi ON p.id = oi.product_id
                       LEFT JOIN orders o ON oi.order_id = o.id
                       WHERE o.status = 'tasdiqlandi' OR o.status IS NULL
                       GROUP BY c.id, c.name
                       ORDER BY revenue DESC";
$category_sales = $db->fetchAll($category_sales_sql);

// Eng ko'p sotilgan mahsulotlar
$top_products_sql = "SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
                     FROM products p
                     JOIN order_items oi ON p.id = oi.product_id
                     JOIN orders o ON oi.order_id = o.id
                     WHERE o.status = 'tasdiqlandi'
                     GROUP BY p.id, p.name
                     ORDER BY total_sold DESC
                     LIMIT 10";
$top_products = $db->fetchAll($top_products_sql);

// Oxirgi 30 kun statistikasi
$daily_sales_sql = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_price) as revenue
                    FROM orders
                    WHERE status = 'tasdiqlandi' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date ASC";
$daily_sales = $db->fetchAll($daily_sales_sql);

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Hisobotlar va Statistika</h1>
</div>

<!-- Umumiy Statistika -->
<div class="stats-grid">
    <div class="stat-card stat-revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <h3><?php echo format_price($total_revenue); ?></h3>
            <p>Jami Daromad</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo number_format($total_orders); ?></h3>
            <p>Tasdiqlangan Buyurtmalar</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?php echo $total_orders > 0 ? format_price($total_revenue / $total_orders) : '0 so\'m'; ?></h3>
            <p>O'rtacha Buyurtma</p>
        </div>
    </div>
</div>

<!-- Kategoriya bo'yicha sotuvlar -->
<div class="admin-card">
    <div class="card-header">
        <h2>Kategoriya bo'yicha Sotuvlar</h2>
    </div>
    <div class="table-responsive">
        <?php if ($category_sales && count($category_sales) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kategoriya</th>
                        <th>Buyurtmalar</th>
                        <th>Sotilgan miqdor</th>
                        <th>Daromad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($category_sales as $cat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo number_format($cat['order_count'] ?? 0); ?></td>
                            <td><?php echo number_format($cat['total_sold'] ?? 0); ?> dona</td>
                            <td><?php echo format_price($cat['revenue'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Ma'lumot yo'q</p>
        <?php endif; ?>
    </div>
</div>

<!-- Eng ko'p sotilgan mahsulotlar -->
<div class="admin-card">
    <div class="card-header">
        <h2>Top 10 Mahsulotlar</h2>
    </div>
    <div class="table-responsive">
        <?php if ($top_products && count($top_products) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mahsulot</th>
                        <th>Sotilgan</th>
                        <th>Daromad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($top_products as $product): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo number_format($product['total_sold']); ?> dona</td>
                            <td><?php echo format_price($product['revenue']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Ma'lumot yo'q</p>
        <?php endif; ?>
    </div>
</div>

<!-- Kunlik Sotuvlar Grafigi (Chart.js) -->
<div class="admin-card">
    <div class="card-header">
        <h2>Oxirgi 30 Kun Sotuvlari</h2>
    </div>
    <canvas id="salesChart" width="400" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart data
const salesData = <?php echo json_encode($daily_sales); ?>;
const dates = salesData.map(item => item.date);
const revenues = salesData.map(item => parseFloat(item.revenue));

// Chart
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Daromad (so\'m)',
            data: revenues,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
