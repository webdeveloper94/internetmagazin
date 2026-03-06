<?php
/**
 * Load Environment Variables
 */
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

loadEnv(__DIR__ . '/../.env');

// Database Configuration (Fallback to defaults if not in .env)
if (!defined('DB_HOST')) define('DB_HOST', 'localhost:3307');
if (!defined('DB_NAME')) define('DB_NAME', 'onlineshop');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', 'root');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Site Configuration
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/onlineshop');
if (!defined('SITE_NAME')) define('SITE_NAME', 'Online Shop');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
