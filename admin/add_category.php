<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

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
        // Vérifier si la catégorie existe déjà
        $check_query = "SELECT COUNT(*) FROM categories WHERE name_fr = ? OR name_en = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$name_fr, $name_en]);
        $exists = $check_stmt->fetchColumn();
        
        if($exists > 0) {
            $message = "A category with this name already exists";
            $message_type = 'error';
        } else {
            // Insertion en base
            $query = "INSERT INTO categories (name_fr, name_en) VALUES (?, ?)";
            
            try {
                $stmt = $db->prepare($query);
                $success = $stmt->execute([$name_fr, $name_en]);
                
                if($success) {
                    $_SESSION['message'] = "Category added successfully!";
                    $_SESSION['message_type'] = 'success';
                    header('Location: categories.php');
                    exit;
                } else {
                    $message = "Error adding category to database";
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Variables pour le layout
$page_title = "Add Category - GPower Admin";
$current_page = "categories";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Categories', 'url' => 'categories.php'],
    ['title' => 'Add Category']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Add New Category</h1>
    <p class="page-subtitle">Create a new category to organize your products</p>
</div>

<?php if($message): ?>
    <div class="message message-<?php echo $message_type; ?>">
        <span class="message-icon"><?php echo $message_type === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

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
                    Save Category
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
                    <div style="font-weight: 600; color: #1e293b;" id="preview-fr">Category Name (French)</div>
                    <div style="font-size: 14px; color: #64748b;" id="preview-en">Category Name (English)</div>
                </div>
            </div>
            <div style="font-size: 12px; color: #64748b;">
                This is how your category will appear in the admin panel and on the website.
            </div>
        </div>
    </div>
</div>

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