<?php
/**
 * AJAX: Buyurtma Berish
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Login tekshirish
if (!is_logged_in()) {
    json_error('Avval tizimga kiring', 401);
}

$db = Database::getInstance();

// Profil ma'lumotlarini tekshirish (telefon va manzil)
$profile_sql = "SELECT phone, address FROM profiles WHERE user_id = ?";
$profile = $db->fetchOne($profile_sql, [$_SESSION['user_id']]);

if (!$profile || empty($profile['phone']) || empty($profile['address'])) {
    json_error('Buyurtma berish uchun telefon raqam va manzil kiritish shart', 400, [
        'requires_contact_info' => true,
        'missing_phone' => empty($profile['phone'] ?? ''),
        'missing_address' => empty($profile['address'] ?? '')
    ]);
}

// Savatchani tekshirish
$cart_sql = "SELECT c.*, p.name, p.price, p.stock 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
$cart_items = $db->fetchAll($cart_sql, [$_SESSION['user_id']]);

if (!$cart_items || count($cart_items) === 0) {
    json_error('Savatchada mahsulot yo\'q');
}

// Stock tekshirish
foreach ($cart_items as $item) {
    if ($item['stock'] < $item['quantity']) {
        json_error("'{$item['name']}' mahsulotidan omborda yetarli miqdor yo'q");
    }
}

// Jami narxni hisoblash
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Transaction boshlash
$db->beginTransaction();

try {
    // Buyurtma yaratish
    $order_sql = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'kutilmoqda')";
    $db->execute($order_sql, [$_SESSION['user_id'], $total_price]);
    $order_id = $db->lastInsertId();
    
    // Buyurtma elementlarini yaratish
    $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    
    foreach ($cart_items as $item) {
        $db->execute($item_sql, [
            $order_id,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['price']
        ]);
        
        // Stockni kamaytirish
        $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $db->execute($update_stock_sql, [$item['quantity'], $item['product_id']]);
    }
    
    // Savatchani tozalash
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $db->execute($clear_cart_sql, [$_SESSION['user_id']]);
    
    // Transaction tasdiqlash
    $db->commit();
    
    json_success('Buyurtma muvaffaqiyatli yuborildi!', [
        'order_id' => $order_id,
        'cart_count' => 0
    ]);
    
} catch (Exception $e) {
    // Transaction bekor qilish
    $db->rollback();
    json_error('Buyurtma berishda xatolik yuz berdi');
}
