<?php
/**
 * AJAX - Sevimlilar sonini olish
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

try {
    $count_result = $db->fetchOne(
        "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?",
        [$user_id]
    );
    
    echo json_encode([
        'success' => true,
        'count' => (int)$count_result['count']
    ]);
    
} catch (Exception $e) {
    error_log("Get favorites count error: " . $e->getMessage());
    echo json_encode(['success' => false, 'count' => 0]);
}
