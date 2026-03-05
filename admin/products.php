<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $name = trim($_POST['name']);
            $categoryId = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $hasSizes = isset($_POST['has_sizes']) ? 1 : 0;
            $image = null;

            // Upload image
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $image = uniqid('prod_') . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, description, price, image, has_sizes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $categoryId, $description, $price, $image, $hasSizes]);
            $productId = $pdo->lastInsertId();

            // Add sizes if applicable
            if ($hasSizes && !empty($_POST['size_name'])) {
                $sizeNames = $_POST['size_name'];
                $sizePrices = $_POST['size_price'];
                for ($i = 0; $i < count($sizeNames); $i++) {
                    if (!empty($sizeNames[$i]) && isset($sizePrices[$i])) {
                        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)");
                        $stmt->execute([$productId, trim($sizeNames[$i]), floatval($sizePrices[$i])]);
                    }
                }
            }

            $message = "Mahsulot qo'shildi";
            $messageType = 'success';
            break;

        case 'update':
            $prodId = intval($_POST['prod_id']);
            $name = trim($_POST['name']);
            $categoryId = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $hasSizes = isset($_POST['has_sizes']) ? 1 : 0;

            // Upload new image
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $old = $pdo->prepare("SELECT image FROM products WHERE id = ?");
                    $old->execute([$prodId]);
                    $oldImg = $old->fetchColumn();
                    if ($oldImg && file_exists($uploadDir . $oldImg)) unlink($uploadDir . $oldImg);

                    $image = uniqid('prod_') . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
                    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, image=?, has_sizes=? WHERE id=?");
                    $stmt->execute([$name, $categoryId, $description, $price, $image, $hasSizes, $prodId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, has_sizes=? WHERE id=?");
                    $stmt->execute([$name, $categoryId, $description, $price, $hasSizes, $prodId]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, has_sizes=? WHERE id=?");
                $stmt->execute([$name, $categoryId, $description, $price, $hasSizes, $prodId]);
            }

            // Update sizes
            $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?")->execute([$prodId]);
            if ($hasSizes && !empty($_POST['size_name'])) {
                $sizeNames = $_POST['size_name'];
                $sizePrices = $_POST['size_price'];
                for ($i = 0; $i < count($sizeNames); $i++) {
                    if (!empty($sizeNames[$i]) && isset($sizePrices[$i])) {
                        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)");
                        $stmt->execute([$prodId, trim($sizeNames[$i]), floatval($sizePrices[$i])]);
                    }
                }
            }

            $message = "Mahsulot yangilandi";
            $messageType = 'success';
            break;

        case 'delete':
            $prodId = intval($_POST['prod_id']);
            $old = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $old->execute([$prodId]);
            $oldImg = $old->fetchColumn();
            if ($oldImg) {
                $path = __DIR__ . '/../uploads/products/' . $oldImg;
                if (file_exists($path)) unlink($path);
            }
            $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$prodId]);
            $message = "Mahsulot o'chirildi";
            $messageType = 'success';
            break;
    }
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Get products
$filterCat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
if ($filterCat) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.created_at DESC");
    $stmt->execute([$filterCat]);
} else {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
}
$products = $stmt->fetchAll();

$adminPageTitle = 'Mahsulotlar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-box-seam"></i> Mahsulotlar</h3>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <select name="cat" class="form-select form-select-sm" onchange="this.form.submit()" style="width:160px;">
                    <option value="0">Barcha kategoriyalar</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filterCat == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg"></i> Qo'shish
            </button>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="admin-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Rasm</th>
                    <th>Nomi</th>
                    <th>Kategoriya</th>
                    <th>Narx</th>
                    <th>O'lchamlar</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Mahsulotlar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                    <?php
                        $sizes = [];
                        if ($prod['has_sizes']) {
                            $sStmt = $pdo->prepare("SELECT * FROM product_sizes WHERE product_id = ?");
                            $sStmt->execute([$prod['id']]);
                            $sizes = $sStmt->fetchAll();
                        }
                    ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td>
                            <?php if ($prod['image']): ?>
                                <img src="<?= SITE_URL ?>/uploads/products/<?= $prod['image'] ?>" 
                                     style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <div style="width:40px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= sanitize($prod['name']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= sanitize($prod['category_name']) ?></span></td>
                        <td>
                            <?php if ($prod['has_sizes'] && !empty($sizes)): ?>
                                <small>
                                    <?php foreach ($sizes as $s): ?>
                                        <?= sanitize($s['size_name']) ?>: <?= formatPrice($s['price']) ?><br>
                                    <?php endforeach; ?>
                                </small>
                            <?php else: ?>
                                <strong><?= formatPrice($prod['price']) ?></strong> so'm
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($prod['has_sizes']): ?>
                                <span class="badge bg-info"><?= count($sizes) ?> ta</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProd<?= $prod['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('O\'chirilsinmi?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="prod_id" value="<?= $prod['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>

                            <!-- Edit Product Modal -->
                            <div class="modal fade" id="editProd<?= $prod['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mahsulotni tahrirlash</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="prod_id" value="<?= $prod['id'] ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Nomi</label>
                                                        <input type="text" name="name" class="form-control" value="<?= sanitize($prod['name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kategoriya</label>
                                                        <select name="category_id" class="form-select" required>
                                                            <?php foreach ($categories as $cat): ?>
                                                                <option value="<?= $cat['id'] ?>" <?= $prod['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                                    <?= sanitize($cat['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tavsif</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= sanitize($prod['description'] ?? '') ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Rasm</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                                <div class="mb-3" id="basePriceFieldEdit<?= $prod['id'] ?>" style="<?= $prod['has_sizes'] ? 'display:none;' : '' ?>">
                                                    <label class="form-label">Narxi (so'm)</label>
                                                    <input type="number" name="price" class="form-control" value="<?= $prod['price'] ?>" step="100">
                                                </div>
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" name="has_sizes" class="form-check-input" id="hasSizesEdit<?= $prod['id'] ?>" 
                                                           <?= $prod['has_sizes'] ? 'checked' : '' ?>
                                                           onchange="toggleSizesEdit(this, <?= $prod['id'] ?>)">
                                                    <label class="form-check-label" for="hasSizesEdit<?= $prod['id'] ?>">O'lchamlar mavjud</label>
                                                </div>
                                                <div id="sizesSectionEdit<?= $prod['id'] ?>" style="<?= $prod['has_sizes'] ? '' : 'display:none;' ?>">
                                                    <label class="form-label fw-bold">O'lchamlar va narxlar:</label>
                                                    <div id="sizesContainerEdit<?= $prod['id'] ?>">
                                                        <?php foreach ($sizes as $s): ?>
                                                        <div class="row mb-2 size-row">
                                                            <div class="col-5">
                                                                <input type="text" name="size_name[]" class="form-control" value="<?= sanitize($s['size_name']) ?>" placeholder="O'lcham" required>
                                                            </div>
                                                            <div class="col-5">
                                                                <input type="number" name="size_price[]" class="form-control" value="<?= $s['price'] ?>" placeholder="Narxi" step="100" required>
                                                            </div>
                                                            <div class="col-2">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.size-row').remove()">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="addSizeRowTo('sizesContainerEdit<?= $prod['id'] ?>')">
                                                        <i class="bi bi-plus"></i> O'lcham qo'shish
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                <button type="submit" class="btn btn-primary">Saqlash</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Mahsulot qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomi</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategoriya</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Tanlang...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rasm</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3" id="basePriceField">
                        <label class="form-label">Narxi (so'm)</label>
                        <input type="number" name="price" class="form-control" step="100" value="0">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="has_sizes" class="form-check-input" id="hasSizesNew" onchange="toggleSizes(this)">
                        <label class="form-check-label" for="hasSizesNew">O'lchamlar mavjud (dona, gram, millilitr, kiyim o'lchami va h.k.)</label>
                    </div>
                    <div id="sizesSection" style="display:none;">
                        <label class="form-label fw-bold">O'lchamlar va narxlar:</label>
                        <div id="sizesContainer"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSizeRow()">
                            <i class="bi bi-plus"></i> O'lcham qo'shish
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
function toggleSizesEdit(checkbox, prodId) {
    const section = document.getElementById('sizesSectionEdit' + prodId);
    const priceField = document.getElementById('basePriceFieldEdit' + prodId);
    section.style.display = checkbox.checked ? 'block' : 'none';
    priceField.style.display = checkbox.checked ? 'none' : 'block';
}

function addSizeRowTo(containerId) {
    const container = document.getElementById(containerId);
    const row = document.createElement('div');
    row.className = 'row mb-2 size-row';
    row.innerHTML = `
        <div class="col-5">
            <input type="text" name="size_name[]" class="form-control" placeholder="O'lcham nomi (500ml, XL...)" required>
        </div>
        <div class="col-5">
            <input type="number" name="size_price[]" class="form-control" placeholder="Narxi" step="100" required>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.size-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}
</script>
</body>
</html>
