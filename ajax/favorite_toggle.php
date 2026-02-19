<?php
/**
 * AJAX - Sevimlilar toggle
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Tizimga kirish kerak']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri so\'rov']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mahsulot topilmadi']);
    exit;
}

$db = Database::getInstance();

try {
    // Mahsulot mavjudligini tekshirish
    $product_check = $db->fetchOne("SELECT id FROM products WHERE id = ?", [$product_id]);
    if (!$product_check) {
        echo json_encode(['success' => false, 'message' => 'Mahsulot topilmadi']);
        exit;
    }
    
    // Sevimlilar ro'yxatida borligini tekshirish
    $favorite_check = $db->fetchOne(
        "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
        [$user_id, $product_id]
    );
    
    if ($favorite_check) {
        // O'chirish
        $db->query(
            "DELETE FROM favorites WHERE user_id = ? AND product_id = ?",
            [$user_id, $product_id]
        );
        
        $action = 'removed';
        $message = 'Sevimlilardan o\'chirildi';
    } else {
        // Qo'shish
        $db->query(
            "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)",
            [$user_id, $product_id]
        );
        
        $action = 'added';
        $message = 'Sevimlilarga qo\'shildi';
    }
    
    // Sevimlilar sonini olish
    $count_result = $db->fetchOne(
        "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?",
        [$user_id]
    );
    $favorites_count = $count_result['count'];
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'action' => $action,
        'count' => $favorites_count
    ]);
    
} catch (Exception $e) {
    error_log("Favorite toggle error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Xatolik yuz berdi']);
}
