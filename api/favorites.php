<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Avval tizimga kiring']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = intval($data['product_id'] ?? 0);
$userId = $_SESSION['user_id'];

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri mahsulot']);
    exit;
}

// Check if already favorited
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$existing = $stmt->fetch();

if ($existing) {
    // Remove from favorites
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
    $stmt->execute([$existing['id']]);
    $action = 'removed';
} else {
    // Add to favorites
    $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$userId, $productId]);
    $action = 'added';
}

echo json_encode([
    'success' => true, 
    'action' => $action, 
    'fav_count' => getFavoritesCount()
]);
