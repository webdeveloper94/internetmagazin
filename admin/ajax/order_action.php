<?php
/**
 * Admin AJAX: Buyurtma Amallar
 */
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!is_admin()) {
    json_error('Ruxsat berilmagan', 403);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db = Database::getInstance();

switch ($action) {
    case 'update_status':
        $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $status = $_POST['status'] ?? '';
        
        if (!$order_id || !in_array($status, ['kutilmoqda', 'tasdiqlandi', 'rad_etildi'])) {
            json_error('Ma\'lumotlar noto\'g\'ri');
        }
        
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        if ($db->execute($sql, [$status, $order_id])) {
            json_success('Buyurtma statusi yangilandi', ['new_status' => $status]);
        }
        break;
        
    case 'get_details':
        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        
        if (!$order_id) {
            json_error('Buyurtma topilmadi');
        }
        
        // Buyurtma ma'lumotlari
        $order_sql = "SELECT o.*, u.name as user_name, u.username, p.phone, p.address 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      LEFT JOIN profiles p ON u.id = p.user_id 
                      WHERE o.id = ?";
        $order = $db->fetchOne($order_sql, [$order_id]);
        
        if (!$order) {
            json_error('Buyurtma topilmadi');
        }
        
        // Buyurtma mahsulotlari
        $items_sql = "SELECT * FROM order_items WHERE order_id = ?";
        $items = $db->fetchAll($items_sql, [$order_id]);
        
        json_success('OK', [
            'order' => $order,
            'items' => $items
        ]);
        break;
        
    default:
        json_error('Noto\'g\'ri amal');
}

json_error('Xatolik yuz berdi');
