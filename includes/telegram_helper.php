<?php
/**
 * Telegram Notification Helper
 */

/**
 * Send a simple text message via Telegram Bot API
 */
function sendTelegramMessage($message) {
    global $pdo;
    
    // Get settings from database
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings WHERE `key` IN ('tg_bot_token', 'tg_admin_chat_id', 'tg_notifications_enabled')");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }

    if (($settings['tg_notifications_enabled'] ?? '0') !== '1') return false;
    if (empty($settings['tg_bot_token']) || empty($settings['tg_admin_chat_id'])) return false;

    $url = "https://api.telegram.org/bot" . $settings['tg_bot_token'] . "/sendMessage";
    $data = [
        'chat_id' => $settings['tg_admin_chat_id'],
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    return $result !== false;
}

/**
 * Send full order notification to Telegram
 */
function sendOrderNotification($orderId) {
    global $pdo;

    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) return false;

    // Get order items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll();

    // Format message
    $message = "<b>🛍 YANGI BUYURTMA!</b>\n";
    $message .= "🆔 Buyurtma: #{$orderId}\n";
    $message .= "━━━━━━━━━━━━━━━\n";
    $message .= "👤 Mijoz: <b>" . htmlspecialchars($order['full_name']) . "</b>\n";
    $message .= "📞 Tel: <code>" . htmlspecialchars($order['phone']) . "</code>\n";
    $message .= "📍 Manzil: " . htmlspecialchars($order['address']) . "\n";
    $message .= "━━━━━━━━━━━━━━━\n";
    $message .= "<b>📦 Mahsulotlar:</b>\n";

    foreach ($items as $item) {
        $size = $item['size_name'] ? " ({$item['size_name']})" : "";
        $message .= "• " . htmlspecialchars($item['product_name']) . $size . " x " . $item['quantity'] . " dona\n";
    }

    $message .= "━━━━━━━━━━━━━━━\n";
    $message .= "💰 Jami: <b>" . number_format($order['total_amount'], 0, '', ' ') . " so'm</b>\n";
    $message .= "📅 Vaqt: " . date('d.m.Y H:i', strtotime($order['created_at'])) . "\n\n";
    $message .= "<a href='" . SITE_URL . "/admin/orders.php'>Admin panelda ko'rish</a>";

    return sendTelegramMessage($message);
}
