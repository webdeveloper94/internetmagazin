<?php
/**
 * Admin AJAX: Kategoriya Amallar
 */
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!is_admin()) {
    json_error('Ruxsat berilmagan', 403);
}

$action = $_POST['action'] ?? '';
$db = Database::getInstance();

switch ($action) {
    case 'add':
        $name = sanitize_input($_POST['name'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $icon = sanitize_input($_POST['icon'] ?? '📦');
        
        if (empty($name)) {
            json_error('Kategoriya nomini kiriting');
        }
        
        $sql = "INSERT INTO categories (name, description, icon) VALUES (?, ?, ?)";
        if ($db->execute($sql, [$name, $description, $icon])) {
            json_success('Kategoriya qo\'shildi', ['id' => $db->lastInsertId()]);
        }
        break;
        
    case 'edit':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = sanitize_input($_POST['name'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $icon = sanitize_input($_POST['icon'] ?? '📦');
        
        if (!$id || empty($name)) {
            json_error('Ma\'lumotlar to\'liq emas');
        }
        
        $sql = "UPDATE categories SET name = ?, description = ?, icon = ? WHERE id = ?";
        if ($db->execute($sql, [$name, $description, $icon, $id])) {
            json_success('Kategoriya yangilandi');
        }
        break;
        
    case 'delete':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            json_error('Kategoriya topilmadi');
        }
        
        $sql = "DELETE FROM categories WHERE id = ?";
        if ($db->execute($sql, [$id])) {
            json_success('Kategoriya o\'chirildi');
        }
        break;
        
    default:
        json_error('Noto\'g\'ri amal');
}

json_error('Xatolik yuz berdi');
