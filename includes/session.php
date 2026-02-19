<?php
/**
 * Xavfsiz Session Boshqaruvi
 */

// Session parametrlarini sozlash
if (session_status() === PHP_SESSION_NONE) {
    // Session sozlamalari
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // HTTPS bo'lsa 1 qiling
    ini_set('session.cookie_samesite', 'Strict');
    
    // Session nomini o'rnatish
    session_name(SESSION_NAME);
    
    // Session lifetime
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    
    // Session boshlash
    session_start();
    
    // Session hijacking himoyasi
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    // Session validatsiya
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        session_unset();
        session_destroy();
        session_start();
    }
    
    // Session timeout tekshirish
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        session_start();
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * CSRF token yaratish
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF token tekshirish
 */
function verify_csrf_token($token) {
    if (!ENABLE_CSRF_PROTECTION) {
        return true;
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash xabar qo'shish
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Flash xabarni olish va o'chirish
 */
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
