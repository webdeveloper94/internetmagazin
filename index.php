<?php
/**
 * Asosiy Sahifa - Home Page
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$page_title = 'Asosiy Sahifa - ' . SITE_NAME;

// Eng yangi mahsulotlarni olish (12 ta)
$db = Database::getInstance();
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT 12";
$products = $db->fetchAll($sql);

require_once 'includes/header.php';
?>

<!-- Slider -->
<section class="hero-slider">
    <div class="slider-container">
        <div class="slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="container">
                <div class="slide-content">
                    <h1 class="slide-title animate-fade-in">Yuqori Sifatli Mahsulotlar</h1>
                    <p class="slide-text animate-fade-in-delay">Eng so'nggi texnologiyalar va trendlar</p>
                    <a href="#products" class="btn btn-primary btn-lg animate-fade-in-delay-2">Xarid qilish</a>
                </div>
            </div>
        </div>
        
        <div class="slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="container">
                <div class="slide-content">
                    <h1 class="slide-title">Arzon Narxlar</h1>
                    <p class="slide-text">Sifatli mahsulotlar hamyonbop narxlarda</p>
                    <a href="#products" class="btn btn-primary btn-lg">Katalogni ko'rish</a>
                </div>
            </div>
        </div>
        
        <div class="slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="container">
                <div class="slide-content">
                    <h1 class="slide-title">Tez Yetkazib Berish</h1>
                    <p class="slide-text">Buyurtmangizni tezkor yetkazib beramiz</p>
                    <a href="#products" class="btn btn-primary btn-lg">Buyurtma berish</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Slider Controls -->
    <button class="slider-prev" onclick="changeSlide(-1)">&#10094;</button>
    <button class="slider-next" onclick="changeSlide(1)">&#10095;</button>
    
    <!-- Slider Dots -->
    <div class="slider-dots">
        <span class="dot active" onclick="goToSlide(0)"></span>
        <span class="dot" onclick="goToSlide(1)"></span>
        <span class="dot" onclick="goToSlide(2)"></span>
    </div>
</section>

<!-- Info Boxes -->
<section class="info-boxes">
    <div class="container">
        <div class="info-grid">
            <div class="info-box animate-on-scroll">
                <div class="info-icon">✨</div>
                <h3>Yuqori Sifat</h3>
                <p>Barcha mahsulotlarimiz sifat sertifikatiga ega. Biz faqat eng yaxshi brendlar bilan ishlaymiz.</p>
            </div>
            
            <div class="info-box animate-on-scroll">
                <div class="info-icon">🚚</div>
                <h3>Tez Yetkazish</h3>
                <p>O'zbekiston bo'ylab 1-3 kun ichida yetkazib berish. Toshkent shahrida - 24 soat ichida!</p>
            </div>
            
            <div class="info-box animate-on-scroll">
                <div class="info-icon">💰</div>
                <h3>Qulay To'lov</h3>
                <p>Naqd pul yoki plastik karta orqali to'lash imkoniyati. Click, Payme, Uzum qo'llab-quvvatlanadi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item animate-on-scroll">
                <div class="stat-icon">👥</div>
                <div class="stat-number" data-target="1250">0</div>
                <div class="stat-label">Baxtli Mijozlar</div>
            </div>
            <div class="stat-item animate-on-scroll">
                <div class="stat-icon">📦</div>
                <div class="stat-number" data-target="5000">0</div>
                <div class="stat-label">Mahsulotlar</div>
            </div>
            <div class="stat-item animate-on-scroll">
                <div class="stat-icon">⭐</div>
                <div class="stat-number" data-target="98">0</div>
                <div class="stat-label">% Mamnunlik</div>
            </div>
            <div class="stat-item animate-on-scroll">
                <div class="stat-icon">🚚</div>
                <div class="stat-number" data-target="24">0</div>
                <div class="stat-label">Soatlik Yetkazish</div>
            </div>
        </div>
    </div>
</section>

<!-- Horizontal Product Scroll (Mobile Optimized) -->
<section class="product-scroll-container">
    <div class="product-scroll-header">
        <h2>🔥 Ommabop Mahsulotlar</h2>
    </div>
    
    <div class="product-scroll-wrapper">
        <div class="product-scroll">
            <?php
            // Ommabop mahsulot lar (yangi qo'shilganlar)
            $popular_sql = "SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.stock > 0
                           ORDER BY p.created_at DESC 
                           LIMIT 10";
            $popular_products = $db->fetchAll($popular_sql);
            
            foreach ($popular_products as $product):
                // Check if favorited
                $is_favorited = false;
                if (is_logged_in()) {
                    $fav_check = $db->fetchOne(
                        "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
                        [$_SESSION['user_id'], $product['id']]
                    );
                    $is_favorited = !empty($fav_check);
                }
            ?>
                <div class="product-scroll-card">
                    <div class="product-scroll-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo SITE_URL . '/' . UPLOAD_PATH . $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="product-placeholder">📦</div>
                        <?php endif; ?>
                        
                        <!-- Favorite Button -->
                        <?php if (is_logged_in()): ?>
                            <button class="favorite-btn <?php echo $is_favorited ? 'active' : ''; ?>" 
                                    data-product-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-scroll-info">
                        <div class="product-scroll-name">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </div>
                        <div class="product-scroll-price">
                            <?php echo format_price($product['price']); ?>
                        </div>
                        
                        <?php if (is_logged_in()): ?>
                            <button class="product-scroll-cart-btn add-to-cart" 
                                    data-product-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-cart-plus"></i> Savatga
                            </button>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php" 
                               class="product-scroll-cart-btn">
                                Kirish
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Category Showcase -->
<section class="category-showcase">
    <div class="container">
        <div class="section-header">
            <h2>Kategoriyalar</h2>
            <p>Kerakli mahsulotingizni toping</p>
        </div>
        
        <?php
        $categories_sql = "SELECT * FROM categories ORDER BY name";
        $categories = $db->fetchAll($categories_sql);
        ?>
        
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="#products" class="category-card animate-on-scroll">
                    <div class="category-icon">
                        <i class="bi <?php echo $category['icon'] ?: 'bi-box'; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <?php if ($category['description']): ?>
                        <p><?php echo htmlspecialchars($category['description']); ?></p>
                    <?php endif; ?>
                    <span class="category-arrow">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="promo-banner">
    <div class="container">
        <div class="promo-content">
            <div class="promo-text animate-on-scroll">
                <span class="promo-badge">🔥 Maxsus Taklif</span>
                <h2>Yangi Kelgan Mahsulotlarga 20% Chegirma!</h2>
                <p>Birinchi xaridingizda maxsus chegirma olish imkoniyati</p>
                <a href="#products" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus"></i>
                    Xarid Qilish
                </a>
            </div>
            <div class="promo-image animate-on-scroll">
                <div class="promo-circle"></div>
                <div class="promo-shape">🎁</div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section" id="products">
    <div class="container">
        <div class="section-header">
            <h2>Eng So'nggi Mahsulotlar</h2>
            <p>Bizning eng yangi va mashhur mahsulotlarimiz</p>
        </div>
        
        <?php if ($products && count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card animate-on-scroll">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="product-placeholder">
                                    <span>📦</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Favorite Button -->
                            <?php if (is_logged_in()): ?>
                                <?php
                                // Check if favorited
                                $is_favorited = false;
                                $fav_check = $db->fetchOne(
                                    "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
                                    [$_SESSION['user_id'], $product['id']]
                                );
                                $is_favorited = !empty($fav_check);
                                ?>
                                <button class="favorite-btn <?php echo $is_favorited ? 'active' : ''; ?>" 
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="product-badge out-of-stock">Tugagan</span>
                            <?php elseif ($product['stock'] < 5): ?>
                                <span class="product-badge low-stock">Kam qoldi</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            
                            <?php if ($product['description']): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(mb_substr($product['description'], 0, 60)) . '...'; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="product-footer">
                                <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                
                                <?php if ($product['stock'] > 0): ?>
                                    <?php if (is_logged_in()): ?>
                                        <button class="btn btn-primary btn-sm add-to-cart" 
                                                data-product-id="<?php echo $product['id']; ?>">
                                            Savatchaga
                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-secondary btn-sm">
                                            Kirish
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-disabled btn-sm" disabled>
                                        Tugagan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Hozircha mahsulotlar yo'q</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2>📧 Yangiliklar uchun obuna bo'ling!</h2>
                <p>Eng so'nggi mahsulotlar va maxsus takliflardan xabardor bo'ling</p>
            </div>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" placeholder="Email manzilingiz" required>
                <button type="submit" class="btn btn-primary">
                    Obuna bo'lish
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" title="Yuqoriga chiqish">
    <i class="bi bi-arrow-up"></i>
</button>

<?php require_once 'includes/footer.php'; ?>
