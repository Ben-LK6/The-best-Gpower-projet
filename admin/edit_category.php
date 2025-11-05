<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Vérifier l'ID de la catégorie
$category_id = $_GET['id'] ?? null;
if(!$category_id) {
    header('Location: categories.php');
    exit;
}

// Récupérer la catégorie
$query = "SELECT * FROM categories WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$category) {
    $_SESSION['message'] = "Category not found";
    $_SESSION['message_type'] = 'error';
    header('Location: categories.php');
    exit;
}

// Traitement du formulaire
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    
    // Validation
    if(empty($name_fr) || empty($name_en)) {
        $message = "All fields are required";
        $message_type = 'error';
    } else {
        // Vérifier si une autre catégorie a déjà ce nom
        $check_query = "SELECT COUNT(*) FROM categories WHERE (name_fr = ? OR name_en = ?) AND id != ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$name_fr, $name_en, $category_id]);
        $exists = $check_stmt->fetchColumn();
        
        if($exists > 0) {
            $message = "A category with this name already exists";
            $message_type = 'error';
        } else {
            // Mise à jour en base
            $query = "UPDATE categories SET name_fr = ?, name_en = ? WHERE id = ?";
            
            try {
                $stmt = $db->prepare($query);
                $success = $stmt->execute([$name_fr, $name_en, $category_id]);
                
                if($success) {
                    $_SESSION['message'] = "Category updated successfully!";
                    $_SESSION['message_type'] = 'success';
                    header('Location: categories.php');
                    exit;
                } else {
                    $message = "Error updating category";
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
} else {
    // Pré-remplir le formulaire avec les données existantes
    $_POST['name_fr'] = $category['name_fr'];
    $_POST['name_en'] = $category['name_en'];
}

// Compter les produits dans cette catégorie
$count_query = "SELECT COUNT(*) FROM products WHERE category_id = ?";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute([$category_id]);
$product_count = $count_stmt->fetchColumn();

// Variables pour le layout
$page_title = "Edit Category - GPower Admin";
$current_page = "categories";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Categories', 'url' => 'categories.php'],
    ['title' => 'Edit Category']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Edit Category</h1>
    <p class="page-subtitle">Update category information</p>
</div>

<?php if($message): ?>
    <div class="message message-<?php echo $message_type; ?>">
        <span class="message-icon"><?php echo $message_type === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- Category Stats -->
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon blue">📁</div>
        <div class="stat-info">
            <h3>Category ID</h3>
            <div class="stat-number">#<?php echo $category['id']; ?></div>
            <div class="stat-change">Unique identifier</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">📦</div>
        <div class="stat-info">
            <h3>Products</h3>
            <div class="stat-number"><?php echo $product_count; ?></div>
            <div class="stat-change"><?php echo $product_count === 1 ? 'product' : 'products'; ?> in this category</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon purple">📅</div>
        <div class="stat-info">
            <h3>Created</h3>
            <div class="stat-number"><?php echo date('M d', strtotime($category['created_at'])); ?></div>
            <div class="stat-change"><?php echo date('Y', strtotime($category['created_at'])); ?></div>
        </div>
    </div>
</div>

<div class="form-container">
    <div class="form-header">
        <h2>Category Information</h2>
    </div>
    
    <div class="form-content">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Category Name (French)</label>
                    <input type="text" name="name_fr" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_fr'] ?? ''); ?>" 
                           placeholder="e.g., Outils électriques" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Category Name (English)</label>
                    <input type="text" name="name_en" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_en'] ?? ''); ?>" 
                           placeholder="e.g., Power Tools" required>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="categories.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span>
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Category Preview -->
<div class="form-container" style="margin-top: 30px;">
    <div class="form-header">
        <h2>Preview</h2>
    </div>
    <div class="form-content">
        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-size: 24px;">📁</span>
                <div>
                    <div style="font-weight: 600; color: #1e293b;" id="preview-fr"><?php echo htmlspecialchars($category['name_fr']); ?></div>
                    <div style="font-size: 14px; color: #64748b;" id="preview-en"><?php echo htmlspecialchars($category['name_en']); ?></div>
                </div>
            </div>
            <div style="font-size: 12px; color: #64748b;">
                This is how your category will appear in the admin panel and on the website.
            </div>
        </div>
    </div>
</div>

<?php if($product_count > 0): ?>
<!-- Warning about products -->
<div class="form-container" style="margin-top: 30px;">
    <div class="form-content">
        <div style="background: #fef3cd; border: 1px solid #fbbf24; padding: 20px; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span style="font-size: 20px;">⚠️</span>
                <strong style="color: #92400e;">Important Notice</strong>
            </div>
            <p style="color: #92400e; margin: 0; font-size: 14px;">
                This category contains <?php echo $product_count; ?> product<?php echo $product_count === 1 ? '' : 's'; ?>. 
                Changing the category name will affect how it appears for all associated products.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Live preview
document.addEventListener('DOMContentLoaded', function() {
    const nameFrInput = document.querySelector('input[name="name_fr"]');
    const nameEnInput = document.querySelector('input[name="name_en"]');
    const previewFr = document.getElementById('preview-fr');
    const previewEn = document.getElementById('preview-en');
    
    function updatePreview() {
        previewFr.textContent = nameFrInput.value || 'Category Name (French)';
        previewEn.textContent = nameEnInput.value || 'Category Name (English)';
    }
    
    nameFrInput.addEventListener('input', updatePreview);
    nameEnInput.addEventListener('input', updatePreview);
});
</script>

<?php
$content = ob_get_clean();
include 'includes/admin-layout.php';
?>