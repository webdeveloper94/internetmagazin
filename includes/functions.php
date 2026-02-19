<?php
/**
 * Umumiy Helper Funksiyalar
 */

/**
 * Inputni tozalash (XSS himoyasi)
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sahifaga yo'naltirish
 */
function redirect($path) {
    $url = SITE_URL . '/' . ltrim($path, '/');
    header("Location: $url");
    exit();
}

/**
 * Foydalanuvchi login qilganmi tekshirish
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Admin huquqini tekshirish
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Login talab qilish
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash_message('error', 'Iltimos avval tizimga kiring');
        redirect('auth/login.php');
    }
}

/**
 * Admin huquqini talab qilish
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        set_flash_message('error', 'Sizda bu sahifaga kirish huquqi yo\'q');
        redirect('index.php');
    }
}

/**
 * Foydalanuvchi ma'lumotlarini olish
 */
function get_user_info($user_id = null) {
    if ($user_id === null) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if (!$user_id) {
        return null;
    }
    
    $db = Database::getInstance();
    $sql = "SELECT u.*, p.phone, p.address 
            FROM users u 
            LEFT JOIN profiles p ON u.id = p.user_id 
            WHERE u.id = ?";
    return $db->fetchOne($sql, [$user_id]);
}

/**
 * Narxni formatlash
 */
function format_price($price) {
    return number_format($price, 0, '.', ' ') . ' so\'m';
}

/**
 * Sanani formatlash
 */
function format_date($date) {
    return date('d.m.Y H:i', strtotime($date));
}

/**
 * Faylni yuklash (rasmlar uchun)
 */
function upload_image($file, $directory = PRODUCT_UPLOAD_DIR) {
    // Fayl mavjudligini tekshirish
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Fayl tanlanmadi'];
    }
    
    // Xatoliklarni tekshirish
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Faylni yuklashda xatolik'];
    }
    
    // Fayl hajmini tekshirish
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Fayl hajmi juda katta (maksimal 5MB)'];
    }
    
    // Fayl kengaytmasini tekshirish
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGES)) {
        return ['success' => false, 'message' => 'Faqat rasm fayllarini yuklash mumkin'];
    }
    
    // Noyob nom yaratish
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $directory . '/' . $filename;
    
    // Faylni ko'chirish
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Faylni saqlashda xatolik'];
}

/**
 * Faylni o'chirish
 */
function delete_image($filename, $directory = PRODUCT_UPLOAD_DIR) {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = $directory . '/' . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Savatcha mahsulotlari sonini olish
 */
function get_cart_count() {
    if (!is_logged_in()) {
        return 0;
    }
    
    $db = Database::getInstance();
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $result = $db->fetchOne($sql, [$_SESSION['user_id']]);
    return $result ? (int)$result['total'] : 0;
}

/**
 * JSON javob yuborish
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Xatoni JSON formatda yuborish
 */
function json_error($message, $status_code = 400) {
    json_response(['success' => false, 'message' => $message], $status_code);
}

/**
 * Muvaffaqiyatli javobni JSON formatda yuborish
 */
function json_success($message, $data = []) {
    json_response(array_merge(['success' => true, 'message' => $message], $data));
}

/**
 * Validatsiya: username
 */
function validate_username($username) {
    if (strlen($username) < USERNAME_MIN_LENGTH) {
        return "Login kamida " . USERNAME_MIN_LENGTH . " ta belgidan iborat bo'lishi kerak";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return "Login faqat harflar, raqamlar va pastki chiziqdan iborat bo'lishi kerak";
    }
    return true;
}

/**
 * Validatsiya: parol
 */
function validate_password($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return "Parol kamida " . PASSWORD_MIN_LENGTH . " ta belgidan iborat bo'lishi kerak";
    }
    return true;
}

/**
 * Validatsiya: telefon raqam
 */
function validate_phone($phone) {
    if (empty($phone)) {
        return true; // Ixtiyoriy maydon
    }
    if (!preg_match('/^\+?998[0-9]{9}$/', str_replace([' ', '-', '(', ')'], '', $phone))) {
        return "Telefon raqam noto'g'ri formatda (+998901234567)";
    }
    return true;
}
