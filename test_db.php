<?php
/**
 * Database Tekshirish Skripti
 */
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();

echo "<h2>Database Ma'lumotlari:</h2>";

// Mahsulotlar soni
$count_sql = "SELECT COUNT(*) as count FROM products";
$result = $db->fetchOne($count_sql);
echo "<p>Jami Mahsulotlar: <strong>" . $result['count'] . "</strong></p>";

// Birinchi 5 ta mahsulot
$products_sql = "SELECT * FROM products LIMIT 5";
$products = $db->fetchAll($products_sql);

echo "<h3>Birinchi 5 ta mahsulot:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nom</th><th>Narx</th><th>Stok</th><th>Rasm</th></tr>";

foreach ($products as $product) {
    echo "<tr>";
    echo "<td>" . $product['id'] . "</td>";
    echo "<td>" . $product['name'] . "</td>";
    echo "<td>" . number_format($product['price']) . " so'm</td>";
    echo "<td>" . $product['stock'] . "</td>";
    echo "<td>" . ($product['image'] ?? 'NULL') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Kategoriyalar
$cat_sql = "SELECT * FROM categories";
$categories = $db->fetchAll($cat_sql);
echo "<h3>Kategoriyalar (" . count($categories) . " ta):</h3>";
foreach ($categories as $cat) {
    echo "<p>- " . $cat['name'] . "</p>";
}
?>
