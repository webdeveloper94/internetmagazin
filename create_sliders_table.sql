-- Create home_sliders table
CREATE TABLE IF NOT EXISTS home_sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    image VARCHAR(255),
    bg_color VARCHAR(100) DEFAULT 'linear-gradient(135deg, #7000FF, #9B4DFF)',
    btn_text VARCHAR(100) DEFAULT 'Katalogni ko\'rish',
    btn_link VARCHAR(255) DEFAULT '/?view=catalog',
    status TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert initial data based on current static slider
INSERT INTO home_sliders (title, subtitle, bg_color, btn_text, btn_link, sort_order) VALUES
('🛍️ Online Shop', 'Eng yaxshi narxlarda sifatli mahsulotlar — tez yetkazib berish!', 'linear-gradient(135deg, #7000FF, #9B4DFF)', 'Katalogni ko\'rish', '/?view=catalog', 1),
('🔥 Chegirmalar', 'Eng sara mahsulotlarga 50% gacha chegirma!', 'linear-gradient(135deg, #FF6B35, #FF9F1C)', 'Xarid qilish', '/?view=catalog', 2),
('🚚 Bepul yetkazib berish', 'Barcha buyurtmalarga bepul yetkazib berish xizmati!', 'linear-gradient(135deg, #2EC4B6, #00B4D8)', 'Buyurtma berish', '/?view=catalog', 3),
('⭐ Yangi mahsulotlar', 'Har kuni yangi mahsulotlar — eng so\'nggi trendlar!', 'linear-gradient(135deg, #E91E63, #FF5722)', 'Ko\'rish', '/?view=catalog', 4);
