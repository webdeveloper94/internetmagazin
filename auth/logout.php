<?php
/**
 * Logout - Session Tugatish
 */
require_once '../config/config.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Session tozalash
session_unset();
session_destroy();

// Cookie o'chirish
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Asosiy sahifaga yo'naltirish
header("Location: " . SITE_URL);
exit();
