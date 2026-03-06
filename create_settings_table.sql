CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT,
    `description` VARCHAR(255),
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (`key`, `value`, `description`) VALUES 
('tg_bot_token', '', 'Telegram Bot Tokeni'),
('tg_admin_chat_id', '', 'Admin Telegram ID-si'),
('tg_notifications_enabled', '1', 'Telegram xabarnomalari (1-yoqilgan, 0-o\'chirilgan)')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);
