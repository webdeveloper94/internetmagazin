/**
 * Admin Panel JavaScript
 */

// ==================== Users Management ====================
document.addEventListener('DOMContentLoaded', function () {
    // User block/unblock
    document.querySelectorAll('.toggle-block-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            const action = this.dataset.action;
            toggleUserBlock(userId, action);
        });
    });

    // User delete
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            deleteUser(userId);
        });
    });

    // Category management
    initCategoryManagement();

    // Product management
    initProductManagement();

    // Order management
    initOrderManagement();
});

// ==================== User Actions ====================
function toggleUserBlock(userId, action) {
    if (!confirm(action === 'block' ? 'Foydalanuvchini bloklamoqchimisiz?' : 'Foydalanuvchini aktivlashtirilsinmi?')) return;

    fetch('/onlineshop/admin/ajax/user_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=${action}&user_id=${userId}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        });
}

function deleteUser(userId) {
    if (!confirm('Foydalanuvchini o\'chirmoqchimisiz? Bu amalni bekor qilib bo\'lmaydi!')) return;

    fetch('/onlineshop/admin/ajax/user_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&user_id=${userId}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById(`user-row-${userId}`).remove();
            } else {
                showNotification(data.message, 'error');
            }
        });
}

// ==================== Category Management ====================
function initCategoryManagement() {
    // Edit buttons
    document.querySelectorAll('.edit-category-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryDescription').value = description;

            const icon = this.dataset.icon || 'bi-box';
            document.getElementById('categoryIcon').value = icon;
            document.getElementById('modalTitle').textContent = 'Kategoriyani Tahrirlash';

            // Highlight selected icon
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.classList.toggle('selected', opt.dataset.icon === icon);
            });

            showModal('categoryModal');
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-category-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            deleteCategory(id);
        });
    });

    // Form submit
    const form = document.getElementById('categoryForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            saveCategory();
        });
    }
}

function showAddModal() {
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDescription').value = '';
    document.getElementById('categoryIcon').value = 'bi-box';
    document.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.icon === 'bi-box');
    });
    document.getElementById('modalTitle').textContent = 'Yangi Kategoriya';
    showModal('categoryModal');
}

function saveCategory() {
    const id = document.getElementById('categoryId').value;
    const name = document.getElementById('categoryName').value;
    const description = document.getElementById('categoryDescription').value;
    const icon = document.getElementById('categoryIcon').value;
    const action = id ? 'edit' : 'add';

    const formData = new FormData();
    formData.append('action', action);
    formData.append('name', name);
    formData.append('description', description);
    formData.append('icon', icon);
    if (id) formData.append('id', id);

    fetch('/onlineshop/admin/ajax/category_action.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        });
}

function deleteCategory(id) {
    if (!confirm('Kategoriyani o\'chirmoqchimisiz?')) return;

    fetch('/onlineshop/admin/ajax/category_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&id=${id}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById(`category-row-${id}`).remove();
            } else {
                showNotification(data.message, 'error');
            }
        });
}

// ==================== Product Management ====================
function initProductManagement() {
    // Edit buttons
    document.querySelectorAll('.edit-product-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            loadProduct(id);
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-product-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const image = this.dataset.image;
            deleteProduct(id, image);
        });
    });

    // Form submit
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            saveProduct();
        });
    }

    // Image preview
    const imageInput = document.getElementById('productImage');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            previewImage(this);
        });
    }
}

function showAddProductModal() {
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('modalTitle').textContent = 'Yangi Mahsulot';
    document.getElementById('imagePreview').innerHTML = '';
    showModal('productModal');
}

function loadProduct(id) {
    fetch(`/onlineshop/admin/ajax/product_action.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('productCategory').value = product.category_id;
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productStock').value = product.stock;
                document.getElementById('productDescription').value = product.description || '';
                document.getElementById('existingImage').value = product.image || '';
                document.getElementById('modalTitle').textContent = 'Mahsulotni Tahrirlash';

                if (product.image) {
                    document.getElementById('imagePreview').innerHTML =
                        `<img src="/onlineshop/uploads/products/${product.image}" style="max-width: 200px; margin-top: 10px;">`;
                }

                showModal('productModal');
            }
        });
}

function saveProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);

    const id = document.getElementById('productId').value;
    formData.append('action', id ? 'edit' : 'add');

    fetch('/onlineshop/admin/ajax/product_action.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeProductModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        });
}

function deleteProduct(id, image) {
    if (!confirm('Mahsulotni o\'chirmoqchimisiz?')) return;

    fetch('/onlineshop/admin/ajax/product_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&id=${id}&image=${image}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById(`product-row-${id}`).remove();
            } else {
                showNotification(data.message, 'error');
            }
        });
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; margin-top: 10px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('show');
}

// ==================== Order Management ====================
function initOrderManagement() {
    // View order details
    document.querySelectorAll('.view-order-btn').forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.dataset.id;
            viewOrderDetails(orderId);
        });
    });

    // Update status
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.dataset.id;
            const status = this.dataset.status;
            updateOrderStatus(orderId, status);
        });
    });
}

function viewOrderDetails(orderId) {
    fetch(`/onlineshop/admin/ajax/order_action.php?action=get_details&order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                const items = data.items;

                let html = `
                <div style="padding: 20px;">
                    <h3>Buyurtma #${order.id}</h3>
                    <p><strong>Mijoz:</strong> ${order.user_name} (@${order.username})</p>
                    <p><strong>Telefon:</strong> ${order.phone || '-'}</p>
                    <p><strong>Manzil:</strong> ${order.address || '-'}</p>
                    <p><strong>Status:</strong> ${order.status}</p>
                    <p><strong>Sana:</strong> ${order.created_at}</p>
                    
                    <h4 style="margin-top: 20px;">Mahsulotlar:</h4>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mahsulot</th>
                                <th>Miqdor</th>
                                <th>Narx</th>
                                <th>Jami</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                items.forEach(item => {
                    html += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>${formatPrice(item.price)}</td>
                        <td>${formatPrice(item.price * item.quantity)}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>
                    
                    <p style="text-align: right; margin-top: 20px; font-size: 1.25rem; font-weight: bold;">
                        Jami: ${formatPrice(order.total_price)}
                    </p>
                </div>
            `;

                document.getElementById('orderDetails').innerHTML = html;
                showModal('orderModal');
            }
        });
}

function updateOrderStatus(orderId, status) {
    const message = status === 'tasdiqlandi' ? 'Buyurtmani tasdiqlaysizmi?' : 'Buyurtmani rad etasizmi?';
    if (!confirm(message)) return;

    fetch('/onlineshop/admin/ajax/order_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_status&order_id=${orderId}&status=${status}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        });
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('show');
}

// ==================== Modal Functions ====================
function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function closeModal() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.remove('show');
    });
}

// Icon selection using event delegation
document.addEventListener('click', function (e) {
    const iconBtn = e.target.closest('.icon-option');
    if (iconBtn) {
        document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
        iconBtn.classList.add('selected');
        const iconValue = iconBtn.dataset.icon;
        const iconInput = document.getElementById('categoryIcon');
        if (iconInput) {
            iconInput.value = iconValue;
        }
    }
});

// ==================== Helper Functions ====================
function formatPrice(price) {
    return new Intl.NumberFormat('uz-UZ').format(price) + ' so\'m';
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
