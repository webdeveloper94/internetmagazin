<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Check if column exists
    $result = $conn->query("SHOW COLUMNS FROM categories LIKE 'icon'");
    if ($result->rowCount() == 0) {
        $conn->exec("ALTER TABLE categories ADD COLUMN icon VARCHAR(50) DEFAULT '📦' AFTER name");
        echo "Successfully added 'icon' column to 'categories' table.\n";
    } else {
        echo "Column 'icon' already exists in 'categories' table.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
