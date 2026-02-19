-- =====================================================
-- Internet Magazin - Ma'lumotlar Bazasi
-- =====================================================

-- Database yaratish
CREATE DATABASE IF NOT EXISTS onlineshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE onlineshop;

-- =====================================================
-- Jadvallar
-- =====================================================

-- Foydalanuvchilar jadvali
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_blocked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foydalanuvchi profillari jadvali
CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategoriyalar jadvali
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT '📦',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mahsulotlar jadvali
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_price (price),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Savatcha jadvali
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buyurtmalar jadvali
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('kutilmoqda', 'tasdiqlandi', 'rad_etildi') DEFAULT 'kutilmoqda',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buyurtma mahsulotlari jadvali
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Demo Ma'lumotlar
-- =====================================================

-- Admin foydalanuvchi (parol: admin123)
INSERT INTO users (username, name, password_hash, role) VALUES
('admin', 'Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Admin profili
INSERT INTO profiles (user_id, phone, address) VALUES
(1, '+998901234567', 'Toshkent shahar, Chilonzor tumani');

-- Demo foydalanuvchilar (parol: password123)
INSERT INTO users (username, name, password_hash, role) VALUES
('user1', 'Sardor Alijonov', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('user2', 'Dilnoza Karimova', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Demo profillar
INSERT INTO profiles (user_id, phone, address) VALUES
(2, '+998901111111', 'Samarqand shahar, Registon ko\'chasi'),
(3, '+998902222222', 'Buxoro shahar, Lyabi Hovuz');

-- Kategoriyalar
INSERT INTO categories (name, description) VALUES
('Elektronika', 'Smartfonlar, noutbuklar, planshetlar va boshqa elektron qurilmalar'),
('Kiyim-Kechak', 'Erkaklar, ayollar va bolalar kiyimlari'),
('Uy-Ro\'zg\'or', 'Mebel, oshxona buyumlari va uy jihozlari');

-- Mahsulotlar - Elektronika
INSERT INTO products (category_id, name, description, price, image, stock) VALUES
(1, 'Samsung Galaxy S23', 'Eng so\'nggi Samsung smartfoni, 256GB xotira, 8GB RAM', 8500000, NULL, 15),
(1, 'iPhone 14 Pro', 'Apple iPhone 14 Pro, 128GB, Deep Purple', 12000000, NULL, 10),
(1, 'Lenovo IdeaPad', '15.6 dyuymli ekran, Intel Core i5, 8GB RAM, 512GB SSD', 5500000, NULL, 8),
(1, 'JBL Bluetooth Speaker', 'Portativ bluetooth karnay, 12 soat batareya', 450000, NULL, 25);

-- Mahsulotlar - Kiyim-Kechak
INSERT INTO products (category_id, name, description, price, image, stock) VALUES
(2, 'Erkaklar Kurtka', 'Qishki erkaklar kurtka, qora rang, L o\'lcham', 650000, NULL, 20),
(2, 'Ayollar Ko\'ylagi', 'Zamonaviy ayollar ko\'ylagi, turli ranglar mavjud', 280000, NULL, 30),
(2, 'Sport Kiyim To\'plami', 'Erkaklar uchun sport kiyim to\'plami', 350000, NULL, 18),
(2, 'Bolalar Palto', 'Qishgi bolalar palto, 6-8 yosh', 420000, NULL, 12);

-- Mahsulotlar - Uy-Ro'zg'or
INSERT INTO products (category_id, name, description, price, image, stock) VALUES
(3, 'Yotoq Xonasi Mebeli', '2-kishi yotoq, shkaf, tumba to\'plami', 4500000, NULL, 5),
(3, 'Elektr Choynak', '1.8L elektr choynak, avtomatik o\'chish', 180000, NULL, 40),
(3, 'Changyutgich', 'Samsung changyutgich, 2000W quvvat', 1200000, NULL, 15),
(3, 'Pishirish Idishlari', 'Teflon qoplamali idishlar to\'plami', 320000, NULL, 35);

-- Demo buyurtma
INSERT INTO orders (user_id, total_price, status) VALUES
(2, 9280000, 'kutilmoqda'),
(3, 650000, 'tasdiqlandi');

-- Buyurtma mahsulotlari
INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES
(1, 1, 'Samsung Galaxy S23', 1, 8500000),
(1, 4, 'JBL Bluetooth Speaker', 1, 450000),
(1, 6, 'Ayollar Ko\'ylagi', 1, 280000),
(1, 11, 'Elektr Choynak', 1, 180000),
(2, 5, 'Erkaklar Kurtka', 1, 650000);

-- =====================================================
-- Tugallandi
-- =====================================================
