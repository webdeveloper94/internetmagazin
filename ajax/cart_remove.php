<?php
/**
 * AJAX: Savatchadan Mahsulot O'chirish
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

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (!$product_id) {
    json_error('Mahsulot topilmadi');
}

$db = Database::getInstance();
$sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";

if ($db->execute($sql, [$_SESSION['user_id'], $product_id])) {
    json_success('Mahsulot o\'chirildi', [
        'cart_count' => get_cart_count()
    ]);
} else {
    json_error('Xatolik yuz berdi');
}
