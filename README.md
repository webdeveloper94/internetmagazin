# Internet Magazin

To'liq funksional internet magazin sayti (PHP, MySQL, HTML, CSS, JavaScript)

## 🚀 Xususiyatlar

### Foydalanuvchi Tomoni
- ✅ Zamonaviy va responsiv dizayn
- ✅ Animatsiyali slider
- ✅ Mahsulotlar katalogi
- ✅ Savatcha tizimi
- ✅ Buyurtma berish
- ✅ Foydalanuvchi profili
- ✅ Buyurtmalar tarixi

### Admin Panel
- ✅ Dashboard statistikasi
- ✅ Foydalanuvchilar boshqaruvi
- ✅ Kategoriyalar boshqaruvi
- ✅ Mahsulotlar boshqaruvi (rasm yuklash bilan)
- ✅ Buyurtmalar boshqaruvi
- ✅ Hisobotlar va grafik (Chart.js)

### Texnologiyalar
- **Backend:** PHP 7.4+ (PDO, Prepared Statements)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Dizayn:** Custom CSS (CSS Variables, Flexbox, Grid)
- **Xavfsizlik:** Password hashing, SQL injection himoyasi, XSS himoyasi, Session security

## 📦 O'rnatish

### 1. Talablar
- XAMPP (yoki boshqa PHP server)
- PHP 7.4 yoki yuqori
- MySQL 5.7 yoki yuqori
- Modern brauzer

### 2. Loyihani Yuklab Olish

```bash
# XAMPP htdocs papkasiga joylashtiring
cd C:\xampp\htdocs
git clone <repo-url> onlineshop
# yoki ZIP faylni yuklab olib, onlineshop papkasiga extract qiling
```

### 3. Ma'lumotlar Bazasini Sozlash

1. XAMPP Control Panel'dan Apache va MySQL'ni ishga tushiring
2. Brauzerda `http://localhost/phpmyadmin` ochingъ
3. `database.sql` faylini import qiling:
   - phpmyadmin'da yangi "onlineshop" bazasi yarating
   - Import qismidan `database.sql` ni tanlang
   - Execute bosing

Yoki MySQL CLI orqali:

```bash
mysql -u root -p
CREATE DATABASE onlineshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE onlineshop;
SOURCE C:/xampp/htdocs/onlineshop/database.sql;
```

### 4. Konfiguratsiya

`config/config.php` faylini ochib, kerakli sozlamalarni tekshiring:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'onlineshop');
define('DB_USER', 'root');
define('DB_PASS', '');  // Parolni o'zgartiring (kerak bo'lsa)
```

### 5. Ruxsatlar

Windows'da odatda kerak emas, lekin Linux/Mac'da:

```bash
chmod 755 uploads/
chmod 755 uploads/products/
```

## 🎯 Ishga Tushirish

1. XAMPP'da Apache va MySQL ishga tushganligini tekshiring
2. Brauzerda: `http://localhost/onlineshop`

## 👤 Demo Hisoblar

### Admin
- **Login:** admin
- **Parol:** admin123

### Foydalanuvchi
- **Login:** user1
- **Parol:** password123

## 📁 Loyiha Strukturasi

```
onlineshop/
├── admin/                  # Admin panel
│   ├── ajax/              # Admin AJAX endpoints
│   ├── includes/          # Admin header/footer
│   ├── index.php          # Dashboard
│   ├── users.php          # Foydalanuvchilar
│   ├── categories.php     # Kategoriyalar
│   ├── products.php       # Mahsulotlar
│   ├── orders.php         # Buyurtmalar
│   └── reports.php        # Hisobotlar
├── ajax/                  # Foydalanuvchi AJAX endpoints
│   ├── cart_add.php       # Savatchaga qo'shish
│   ├── cart_remove.php    # Savatchadan o'chirish
│   ├── cart_update.php    # Miqdorni yangilash
│   └── place_order.php    # Buyurtma berish
├── assets/                # Frontend fayllar
│   ├── css/
│   │   ├── style.css      # Asosiy CSS
│   │   └── admin.css      # Admin CSS
│   ├── js/
│   │   ├── main.js        # Asosiy JavaScript
│   │   └── admin.js       # Admin JavaScript
│   └── images/            # Rasm fayllar
├── auth/                  # Autentifikatsiya
│   ├── login.php          # Login sahifasi
│   ├── register.php       # Ro'yxatdan o'tish
│   └── logout.php         # Chiqish
├── config/                # Konfiguratsiya
│   ├── config.php         # Asosiy sozlamalar
│   └── database.php       # Database klassi
├── includes/              # Umumiy fayllar
│   ├── functions.php      # Helper funksiyalar
│   ├── session.php        # Session boshqaruvi
│   ├── header.php         # Header
│   └── footer.php         # Footer
├── uploads/               # Yuklangan fayllar
│   └── products/          # Mahsulot rasmlari
├── index.php              # Asosiy sahifa
├── profile.php            # Profil sahifasi
├── cart.php               # Savatcha sahifasi
├── orders.php             # Buyurtmalar sahifasi
├── database.sql           # Database struktura
└── README.md              # Bu fayl
```

## 🔐 Xavfsizlik

Loyihada qo'llangan xavfsizlik choralari:

- **SQL Injection:** PDO Prepared Statements
- **XSS:** htmlspecialchars() funksiyasi
- **Password:** password_hash() va password_verify()
- **Session:** Secure session settings, regeneration
- **CSRF:** Token validatsiyasi (opsional)
- **File Upload:** Fayl turi va hajm tekshiruvi

## 🎨 Dizayn Xususiyatlari

- CSS Variables (ranglar, o'lchamlar)
- Flexbox va Grid Layout
- Responsive dizayn (Mobile, Tablet, Desktop)
- Smooth animatsiyalar
- Glassmorphism effektlari
- Modern gradientlar

## 📱 Responsiv Dizayn

- **Desktop:** 1200px+
- **Tablet:** 768px - 1199px
- **Mobile:** < 768px

## 🛠️ Texnik Ma'lumotlar

### Database Jadvallar
1. `users` - Foydalanuvchilar
2. `profiles` - Profil ma'lumotlari
3. `categories` - Mahsulot kategoriyalari
4. `products` - Mahsulotlar
5. `cart` - Savatcha
6. `orders` - Buyurtmalar
7. `order_items` - Buyurtma mahsulotlari

### AJAX Endpoints
- Savatcha operatsiyalari
- Buyurtma berish
- Admin CRUD amallar
- Order status yangilash

## 🐛 Muammolarni Hal Qilish

### Database ulanmayapti
- MySQL ishga tushganligini tekshiring
- `config/config.php` dagi ma'lumotlar to'g'riligini tekshiring
- Database yaratilganligini tekshiring

### Rasm yuklanmayapti
- `uploads/products/` papkasi mavjudligini tekshiring
- Windows'da odatda muammo bo'lmaydi
- Linux/Mac'da: `chmod 755 uploads/products/`

### Session ishlamayapti
- PHP session extension yoqilganligini tekshiring
- `php.ini` da session sozlamalarini tekshiring

## 📝 Kengaytirish

Loyihani kengaytirish uchun g'oyalar:

- [ ] To'lov tizimini qo'shish (Click, Payme, Uzum)
- [ ] Email bildirishnomalar
- [ ] Mahsulot sharhlari va reytinglar
- [ ] Izoh va tavsiyalar
- [ ] Social media integratsiya
- [ ] Multilingual qo'llab-quvvatlash
- [ ] Izlash funksiyasi
- [ ] Filter va saralash

## 👨‍💻 Muallif

Bu loyiha o'quv maqsadlarida yaratilgan.

## 📄 Litsenziya

MIT License - o'quv maqsadlari uchun erkin foydalanish mumkin.

## 🙏 Minnatdorchilik

- Google Fonts (Inter)
- Chart.js (hisobotlar uchun)
- Inspiration: Zamonaviy e-commerce saytlar

---

**Eslatma:** Bu loyiha o'quv maqsadlari uchun yaratilgan. Production muhitda ishlatishdan avval qo'shimcha xavfsizlik tadbirlarini qo'llang (HTTPS, environment variables, error logging, va boshqalar).
