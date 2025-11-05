<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Action : Supprimer un produit
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    if($stmt->execute([$_GET['id']])) {
        $_SESSION['message'] = "Product deleted successfully";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Error deleting product";
        $_SESSION['message_type'] = 'error';
    }
    header('Location: products.php');
    exit;
}

// Récupérer tous les produits
$query = "SELECT p.*, c.name_fr as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables pour le layout
$page_title = "Products - GPower Admin";
$current_page = "products";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Products']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Products Management</h1>
    <p class="page-subtitle">Manage your product catalog</p>
</div>

<?php if(isset($_SESSION['message'])): ?>
    <div class="message message-<?php echo $_SESSION['message_type'] ?? 'success'; ?>">
        <span class="message-icon"><?php echo ($_SESSION['message_type'] ?? 'success') === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">All Products</h2>
        <div class="search-and-actions">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterTable()">
                <span class="search-icon">🔍</span>
            </div>
            <div class="table-actions">
                <a href="bulk_add_products.php" class="btn btn-secondary">
                    <span>📦</span>
                    Bulk Add
                </a>
                <a href="add_product.php" class="btn btn-primary">
                    <span>➕</span>
                    Add Product
                </a>
            </div>
        </div>
    </div>
    
    <?php if(empty($products)): ?>
        <div class="empty-state">
            <span class="empty-state-icon">📦</span>
            <h3 class="empty-state-title">No products yet</h3>
            <p class="empty-state-text">Start building your catalog by adding your first product</p>
            <a href="add_product.php" class="btn btn-primary">Add First Product</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Location</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                <tr>
                    <td><strong>#<?php echo $product['id']; ?></strong></td>
                    <td>
                        <?php if($product['image_path']): ?>
                            <img src="../uploads/<?php echo $product['image_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name_fr']); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📦</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($product['name_fr']); ?></div>
                        <div style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($product['name_en']); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?></td>
                    <td><strong style="color: #059669;">$<?php echo number_format($product['price'], 2); ?></strong></td>
                    <td><?php echo htmlspecialchars($product['location']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                    <td>
                        <div class="table-actions-cell">
                            <a href="manage_product_images.php?id=<?php echo $product['id']; ?>" 
                               class="btn-icon" style="background: #dbeafe; color: #1d4ed8;" title="Manage images">📷</a>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                               class="btn-icon edit" title="Edit product">✏️</a>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                    class="btn-icon delete" title="Delete product">🗑️</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.search-and-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    min-width: 200px;
}

.search-box input {
    width: 100%;
    padding: 8px 35px 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    pointer-events: none;
}

@media (max-width: 768px) {
    .search-and-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        min-width: auto;
        width: 100%;
    }
    
    .table-actions {
        justify-content: center;
    }
}
</style>

<script>
function deleteProduct(id) {
    showConfirmModal(
        'Delete Product',
        'Are you sure you want to delete this product? This action cannot be undone and will permanently remove all product data.',
        function() {
            window.location.href = `products.php?action=delete&id=${id}`;
        }
    );
}

function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.querySelector('.data-table tbody');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        // Rechercher dans le nom du produit (colonne 2) et catégorie (colonne 3)
        if (cells[2] && cells[3]) {
            const productName = cells[2].textContent.toLowerCase();
            const category = cells[3].textContent.toLowerCase();
            if (productName.includes(filter) || category.includes(filter)) {
                found = true;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Fix pour mobile - ajouter des event listeners tactiles
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-icon.delete');
    deleteButtons.forEach(button => {
        button.addEventListener('touchstart', function(e) {
            e.preventDefault();
            this.click();
        }, { passive: false });
    });
});
</script>

<?php
$content = ob_get_clean();
include 'includes/admin-layout.php';
?>