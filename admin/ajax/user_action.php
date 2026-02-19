<?php
/**
 * Admin AJAX: Foydalanuvchi Amallar
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
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if (!$user_id) {
    json_error('Foydalanuvchi topilmadi');
}

$db = Database::getInstance();

switch ($action) {
    case 'block':
        $sql = "UPDATE users SET is_blocked = 1 WHERE id = ? AND role = 'user'";
        if ($db->execute($sql, [$user_id])) {
            json_success('Foydalanuvchi bloklandi');
        }
        break;
        
    case 'unblock':
        $sql = "UPDATE users SET is_blocked = 0 WHERE id = ? AND role = 'user'";
        if ($db->execute($sql, [$user_id])) {
            json_success('Foydalanuvchi aktivlashtirildi');
        }
        break;
        
    case 'delete':
        $sql = "DELETE FROM users WHERE id = ? AND role = 'user'";
        if ($db->execute($sql, [$user_id])) {
            json_success('Foydalanuvchi o\'chirildi');
        }
        break;
        
    default:
        json_error('Noto\'g\'ri amal');
}

json_error('Xatolik yuz berdi');
