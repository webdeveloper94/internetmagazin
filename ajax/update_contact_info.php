<?php
/**
 * AJAX: Kontakt Ma'lumotlarini Yangilash
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

// POST ma'lumotlarini olish
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

// Validatsiya
$errors = [];

if (empty($phone)) {
    $errors[] = 'Telefon raqam kiritilishi shart';
} else {
    $phone_validation = validate_phone($phone);
    if ($phone_validation !== true) {
        $errors[] = $phone_validation;
    }
}

if (empty($address)) {
    $errors[] = 'Manzil kiritilishi shart';
} elseif (strlen($address) < 10) {
    $errors[] = 'Manzil kamida 10 ta belgidan iborat bo\'lishi kerak';
}

if (!empty($errors)) {
    json_error(implode(', ', $errors), 400);
}

$db = Database::getInstance();

// Profilni yangilash
$update_sql = "UPDATE profiles SET phone = ?, address = ? WHERE user_id = ?";

try {
    $result = $db->execute($update_sql, [$phone, $address, $_SESSION['user_id']]);
    
    if ($result) {
        json_success('Kontakt ma\'lumotlari saqlandi', [
            'phone' => $phone,
            'address' => $address
        ]);
    } else {
        json_error('Ma\'lumotlarni saqlashda xatolik yuz berdi');
    }
} catch (Exception $e) {
    error_log('Contact info update error: ' . $e->getMessage());
    json_error('Ma\'lumotlarni saqlashda xatolik yuz berdi');
}
