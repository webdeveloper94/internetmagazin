-- Kategoriyalar jadvaliga icon ustunini qo'shish
ALTER TABLE categories ADD COLUMN icon VARCHAR(50) DEFAULT '📦' AFTER name;
