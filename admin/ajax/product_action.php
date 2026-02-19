<?php
/**
 * Admin AJAX: Mahsulot Amallar
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
    case 'add':
    case 'edit':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = sanitize_input($_POST['name'] ?? '');
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
        $description = sanitize_input($_POST['description'] ?? '');
        $existing_image = $_POST['existing_image'] ?? '';
        
        if (empty($name) || !$category_id || $price <= 0) {
            json_error('Ma\'lumotlar to\'liq emas');
        }
        
        // Rasm yuklash
        $image_name = $existing_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['image']);
            if ($upload_result['success']) {
                // Eski rasmni o'chirish
                if ($existing_image) {
                    delete_image($existing_image);
                }
                $image_name = $upload_result['filename'];
            } else {
                json_error($upload_result['message']);
            }
        }
        
        if ($action === 'add') {
            $sql = "INSERT INTO products (category_id, name, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$category_id, $name, $description, $price, $image_name, $stock];
        } else {
            $sql = "UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?";
            $params = [$category_id, $name, $description, $price, $image_name, $stock, $id];
        }
        
        if ($db->execute($sql, $params)) {
            json_success($action === 'add' ? 'Mahsulot qo\'shildi' : 'Mahsulot yangilandi', [
                'id' => $action === 'add' ? $db->lastInsertId() : $id
            ]);
        }
        break;
        
    case 'get':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            json_error('Mahsulot topilmadi');
        }
        
        $sql = "SELECT * FROM products WHERE id = ?";
        $product = $db->fetchOne($sql, [$id]);
        
        if ($product) {
            json_success('OK', ['product' => $product]);
        }
        break;
        
    case 'delete':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $image = $_POST['image'] ?? '';
        
        if (!$id) {
            json_error('Mahsulot topilmadi');
        }
        
        // Rasmni o'chirish
        if ($image) {
            delete_image($image);
        }
        
        $sql = "DELETE FROM products WHERE id = ?";
        if ($db->execute($sql, [$id])) {
            json_success('Mahsulot o\'chirildi');
        }
        break;
        
    default:
        json_error('Noto\'g\'ri amal');
}

json_error('Xatolik yuz berdi');
