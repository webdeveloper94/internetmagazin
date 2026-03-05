<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Avval tizimga kiring']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$userId = $_SESSION['user_id'];

switch ($action) {
    case 'add':
        $productId = intval($data['product_id'] ?? 0);
        $sizeId = !empty($data['size_id']) ? intval($data['size_id']) : null;
        $quantity = intval($data['quantity'] ?? 1);

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri mahsulot']);
            exit;
        }

        // Check if already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND (size_id = ? OR (size_id IS NULL AND ? IS NULL))");
        $stmt->execute([$userId, $productId, $sizeId, $sizeId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$quantity, $existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, size_id, quantity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $productId, $sizeId, $quantity]);
        }

        echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        break;

    case 'update':
        $cartId = intval($data['cart_id'] ?? 0);
        $change = intval($data['change'] ?? 0);

        $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $userId]);
        $item = $stmt->fetch();

        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Mahsulot topilmadi']);
            exit;
        }

        $newQty = $item['quantity'] + $change;
        if ($newQty <= 0) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->execute([$cartId]);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQty, $cartId]);
        }

        echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        break;

    case 'remove':
        $cartId = intval($data['cart_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $userId]);
        echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri amal']);
}
