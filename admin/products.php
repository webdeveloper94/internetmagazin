<?php
/**
 * Admin: Mahsulotlar Boshqaruvi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Mahsulotlar - Admin Panel';
$db = Database::getInstance();

// Mahsulotlar va kategoriyalarni olish
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$products = $db->fetchAll($sql);

$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories = $db->fetchAll($categories_sql);

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Mahsulotlar Boshqaruvi</h1>
    <button class="btn btn-primary" onclick="showAddProductModal()">+ Yangi Mahsulot</button>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <?php if ($products && count($products) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rasm</th>
                        <th>Nom</th>
                        <th>Kategoriya</th>
                        <th>Narx</th>
                        <th>Stock</th>
                        <th>Yaratilgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr id="product-row-<?php echo $product['id']; ?>">
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                         class="table-image" alt="">
                                <?php else: ?>
                                    <div class="table-placeholder">📦</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo format_price($product['price']); ?></td>
                            <td>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="badge badge-danger"><?php echo $product['stock']; ?></span>
                                <?php elseif ($product['stock'] < 5): ?>
                                    <span class="badge badge-warning"><?php echo $product['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo $product['stock']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo format_date($product['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary edit-product-btn" 
                                            data-id="<?php echo $product['id']; ?>">
                                        Tahrirlash
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-product-btn" 
                                            data-id="<?php echo $product['id']; ?>"
                                            data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                        O'chirish
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Mahsulotlar yo'q</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="productModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="modalTitle">Yangi Mahsulot</h2>
            <span class="modal-close" onclick="closeProductModal()">&times;</span>
        </div>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" id="productId" name="id">
            <input type="hidden" id="existingImage" name="existing_image">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="productName">Mahsulot Nomi *</label>
                    <input type="text" id="productName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="productCategory">Kategoriya *</label>
                    <select id="productCategory" name="category_id" required>
                        <option value="">Tanlang</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="productPrice">Narx (so'm) *</label>
                    <input type="number" id="productPrice" name="price" min="0" step="1000" required>
                </div>
                
                <div class="form-group">
                    <label for="productStock">Miqdor (stock) *</label>
                    <input type="number" id="productStock" name="stock" min="0" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="productDescription">Tavsif</label>
                <textarea id="productDescription" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="productImage">Rasm (JPG, PNG, max 5MB)</label>
                <input type="file" id="productImage" name="image" accept="image/*">
                <div id="imagePreview"></div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Bekor qilish</button>
                <button type="submit" class="btn btn-primary">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
