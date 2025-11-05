<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Action : Supprimer une catégorie
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // Vérifier s'il y a des produits dans cette catégorie
    $check_query = "SELECT COUNT(*) FROM products WHERE category_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$_GET['id']]);
    $product_count = $check_stmt->fetchColumn();
    
    if($product_count > 0) {
        $_SESSION['message'] = "Cannot delete category: $product_count products are using this category";
        $_SESSION['message_type'] = 'error';
    } else {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $db->prepare($query);
        if($stmt->execute([$_GET['id']])) {
            $_SESSION['message'] = "Category deleted successfully";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting category";
            $_SESSION['message_type'] = 'error';
        }
    }
    header('Location: categories.php');
    exit;
}

// Récupérer toutes les catégories avec le nombre de produits
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id, c.name_fr, c.name_en, c.created_at 
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables pour le layout
$page_title = "Categories - GPower Admin";
$current_page = "categories";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Categories']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Categories Management</h1>
    <p class="page-subtitle">Organize your products into categories</p>
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
        <h2 class="table-title">All Categories</h2>
        <div class="search-and-actions">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search categories..." onkeyup="filterTable()">
                <span class="search-icon">🔍</span>
            </div>
            <div class="table-actions">
                <a href="add_category.php" class="btn btn-primary">
                    <span>➕</span>
                    Add Category
                </a>
            </div>
        </div>
    </div>
    
    <?php if(empty($categories)): ?>
        <div class="empty-state">
            <span class="empty-state-icon">📁</span>
            <h3 class="empty-state-title">No categories yet</h3>
            <p class="empty-state-text">Create categories to organize your products</p>
            <a href="add_category.php" class="btn btn-primary">Add First Category</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Products Count</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $category): ?>
                <tr>
                    <td><strong>#<?php echo $category['id']; ?></strong></td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($category['name_fr']); ?></div>
                        <div style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($category['name_en']); ?></div>
                    </td>
                    <td>
                        <span style="background: <?php echo $category['product_count'] > 0 ? '#dbeafe' : '#f1f5f9'; ?>; 
                                     color: <?php echo $category['product_count'] > 0 ? '#1d4ed8' : '#64748b'; ?>; 
                                     padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            <?php echo $category['product_count']; ?> products
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                    <td>
                        <div class="table-actions-cell">
                            <a href="edit_category.php?id=<?php echo $category['id']; ?>" 
                               class="btn-icon edit" title="Edit category">✏️</a>
                            <?php if($category['product_count'] == 0): ?>
                                <button onclick="deleteCategory(<?php echo $category['id']; ?>)" 
                                        class="btn-icon delete" title="Delete category">🗑️</button>
                            <?php else: ?>
                                <button class="btn-icon" style="opacity: 0.3; cursor: not-allowed;" 
                                        title="Cannot delete: category has products">🔒</button>
                            <?php endif; ?>
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
function deleteCategory(id) {
    showConfirmModal(
        'Delete Category',
        'Are you sure you want to delete this category? This action cannot be undone. Make sure no products are using this category.',
        function() {
            window.location.href = `categories.php?action=delete&id=${id}`;
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
        
        // Rechercher dans le nom de la catégorie (colonne 1)
        if (cells[1]) {
            const categoryName = cells[1].textContent.toLowerCase();
            if (categoryName.includes(filter)) {
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