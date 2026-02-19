<?php
/**
 * Global Konfiguratsiya Fayli
 * Barcha asosiy sozlamalar
 */

// =====================================================
// .env Faylini Yuklash
// =====================================================
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Trim quotes if present
            $value = trim($value, '"\'');

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    return true;
}

loadEnv(__DIR__ . '/../.env');

// =====================================================
// Ma'lumotlar Bazasi Sozlamalari
// =====================================================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'onlineshop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// =====================================================
// Sayt Sozlamalari
// =====================================================
define('SITE_NAME', getenv('SITE_NAME') ?: 'Internet Magazin');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/onlineshop');
define('BASE_PATH', __DIR__ . '/..');

// =====================================================
// Fayl Yuklash Sozlamalari
// =====================================================
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('PRODUCT_UPLOAD_DIR', UPLOAD_DIR . '/products');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// =====================================================
// Session Sozlamalari
// =====================================================
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: (3600 * 24)));
define('SESSION_NAME', getenv('SESSION_NAME') ?: 'ONLINESHOP_SESSION');

// =====================================================
// Xavfsizlik Sozlamalari
// =====================================================
define('PASSWORD_MIN_LENGTH', (int)(getenv('PASSWORD_MIN_LENGTH') ?: 6));
define('USERNAME_MIN_LENGTH', (int)(getenv('USERNAME_MIN_LENGTH') ?: 3));
define('ENABLE_CSRF_PROTECTION', getenv('ENABLE_CSRF_PROTECTION') === 'true');

// =====================================================
// Sahifalash Sozlamalari
// =====================================================
define('PRODUCTS_PER_PAGE', (int)(getenv('PRODUCTS_PER_PAGE') ?: 12));
define('ORDERS_PER_PAGE', (int)(getenv('ORDERS_PER_PAGE') ?: 20));
define('USERS_PER_PAGE', (int)(getenv('USERS_PER_PAGE') ?: 20));

// =====================================================
// Vaqt Zonasi
// =====================================================
date_default_timezone_set('Asia/Tashkent');

// =====================================================
// Xatolarni Ko'rsatish (Development uchun)
// Production'da o'chirish kerak!
// =====================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =====================================================
// Papkalarni Yaratish
// =====================================================
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!file_exists(PRODUCT_UPLOAD_DIR)) {
    mkdir(PRODUCT_UPLOAD_DIR, 0755, true);
}
