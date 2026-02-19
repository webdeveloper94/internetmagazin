<?php
/**
 * AJAX: Savatchada Miqdorni Yangilash
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
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$product_id || !in_array($action, ['increase', 'decrease'])) {
    json_error('Noto\'g\'ri so\'rov');
}

$db = Database::getInstance();

// Savatcha elementini topish
$cart_sql = "SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND c.product_id = ?";
$cart_item = $db->fetchOne($cart_sql, [$_SESSION['user_id'], $product_id]);

if (!$cart_item) {
    json_error('Mahsulot topilmadi');
}

$new_quantity = $cart_item['quantity'];

if ($action === 'increase') {
    $new_quantity++;
    if ($new_quantity > $cart_item['stock']) {
        json_error('Omborda yetarli mahsulot yo\'q');
    }
} else {
    $new_quantity--;
    if ($new_quantity < 1) {
        // Miqdor 0 dan kam bo'lsa, o'chirish
        $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $db->execute($delete_sql, [$_SESSION['user_id'], $product_id]);
        json_success('Mahsulot savatchadan o\'chirildi', [
            'removed' => true,
            'cart_count' => get_cart_count()
        ]);
    }
}

// Miqdorni yangilash
$update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
if ($db->execute($update_sql, [$new_quantity, $_SESSION['user_id'], $product_id])) {
    // Yangilangan jami narxni hisoblash
    $total_sql = "SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
    $total_result = $db->fetchOne($total_sql, [$_SESSION['user_id']]);
    
    json_success('Miqdor yangilandi', [
        'new_quantity' => $new_quantity,
        'total_price' => $total_result['total'] ?? 0,
        'cart_count' => get_cart_count()
    ]);
} else {
    json_error('Xatolik yuz berdi');
}
