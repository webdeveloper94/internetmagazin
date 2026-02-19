<?php
/**
 * Kategoriyalar sahifasi - Barcha kategoriyalarni ko'rsatadi
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$page_title = "Kategoriyalar - " . SITE_NAME;
$db = Database::getInstance();

// Barcha kategoriyalarni olish
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories = $db->fetchAll($categories_sql);

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>📁 Kategoriyalar</h1>
            <p>Mahsulotlarimizni kategoriyalar bo'yicha ko'ring</p>
        </div>

        <div class="categories-page-grid">
            <?php foreach ($categories as $category): ?>
                <?php
                // Har bir kategoriyada nechta mahsulot borligini sanash
                $count_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
                $count_result = $db->fetchOne($count_sql, [$category['id']]);
                $product_count = $count_result['count'];
                ?>
                
                <a href="products.php?category_id=<?php echo $category['id']; ?>" class="category-page-card">
                    <div class="category-page-icon">
                        <?php 
                        $iconClass = $category['icon'] ?: 'bi-box';
                        ?>
                        <i class="bi <?php echo $iconClass; ?>"></i>
                    </div>
                    
                    <div class="category-page-content">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        
                        <?php if ($category['description']): ?>
                            <p class="category-page-desc">
                                <?php echo htmlspecialchars($category['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="category-page-footer">
                            <span class="category-page-count">
                                <i class="bi bi-box-seam"></i>
                                <?php echo $product_count; ?> mahsulot
                            </span>
                            <span class="category-page-arrow">
                                Ko'rish <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="empty-icon">📁</div>
                <h3>Kategoriyalar topilmadi</h3>
                <p>Hozircha hech qanday kategoriya mavjud emas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.categories-page-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.category-page-card {
    display: flex;
    gap: 1.5rem;
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}

.category-page-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.category-page-card:hover::before {
    transform: scaleY(1);
}

.category-page-card:hover {
    transform: translateX(10px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.category-page-icon {
    font-size: 3.5rem;
    flex-shrink: 0;
    transition: transform 0.3s ease;
    color: #6366f1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-page-card:hover .category-page-icon {
    transform: scale(1.1) rotate(5deg);
    color: #4f46e5;
}

.category-page-content {
    flex: 1;
}

.category-page-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.category-page-desc {
    color: #6b7280;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    line-height: 1.5;
}

.category-page-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.category-page-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #667eea;
    font-weight: 600;
    font-size: 0.9rem;
}

.category-page-arrow {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #667eea;
    font-weight: 600;
    font-size: 0.9rem;
    transition: gap 0.3s ease;
}

.category-page-card:hover .category-page-arrow {
    gap: 0.75rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.empty-state p {
    color: #6b7280;
}

@media (max-width: 768px) {
    .categories-page-grid {
        grid-template-columns: 1fr;
    }
    
    .category-page-card {
        padding: 1.5rem;
    }
    
    .category-page-icon {
        font-size: 3rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
