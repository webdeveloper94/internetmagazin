<?php
/**
 * AJAX: Savatchaga Mahsulot Qo'shish
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

// Product ID olish
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (!$product_id) {
    json_error('Mahsulot topilmadi');
}

$db = Database::getInstance();

// Mahsulot mavjudligini tekshirish
$product_sql = "SELECT * FROM products WHERE id = ?";
$product = $db->fetchOne($product_sql, [$product_id]);

if (!$product) {
    json_error('Mahsulot topilmadi');
}

// Stock tekshirish
if ($product['stock'] <= 0) {
    json_error('Bu mahsulot tugagan');
}

// Savatchada borligini tekshirish
$check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$existing = $db->fetchOne($check_sql, [$_SESSION['user_id'], $product_id]);

if ($existing) {
    // Miqdorni oshirish
    $new_quantity = $existing['quantity'] + 1;
    
    // Stock tekshirish
    if ($new_quantity > $product['stock']) {
        json_error('Omborda yetarli mahsulot yo\'q');
    }
    
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    if ($db->execute($update_sql, [$new_quantity, $existing['id']])) {
        json_success('Mahsulot miqdori oshirildi', [
            'cart_count' => get_cart_count()
        ]);
    } else {
        json_error('Xatolik yuz berdi');
    }
} else {
    // Yangi qo'shish
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    if ($db->execute($insert_sql, [$_SESSION['user_id'], $product_id])) {
        json_success('Mahsulot savatchaga qo\'shildi', [
            'cart_count' => get_cart_count()
        ]);
    } else {
        json_error('Xatolik yuz berdi');
    }
}
