<?php
/**
 * Ma'lumotlar Bazasi Ulanish Klassi
 * Singleton pattern va PDO ishlatadi
 */

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private konstruktor - Singleton pattern
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Ma'lumotlar bazasiga ulanishda xatolik: " . $e->getMessage());
        }
    }
    
    /**
     * Singleton instance olish
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO connection olish
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * So'rov bajarish (SELECT)
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bitta qator olish
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    /**
     * Barcha qatorlarni olish
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }
    
    /**
     * Insert/Update/Delete bajarish
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database execute error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Oxirgi insert ID olish
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Transaction boshlash
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Transaction tasdiqlash
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Transaction bekor qilish
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Clone va unserialize to'sish
     */
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Singleton ni unserialize qilish mumkin emas");
    }
}
