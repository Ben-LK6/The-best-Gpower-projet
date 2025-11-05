<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les catégories
$categories_query = "SELECT * FROM categories ORDER BY name_fr";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $description_fr = $_POST['description_fr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Gestion de l'upload d'image
    $image_path = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = uniqid() . '.' . $extension;
            $upload_path = '../uploads/' . $image_path;
            
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = null;
            }
        }
    }
    
    // Validation
    if(empty($name_fr) || empty($name_en) || empty($price) || empty($category_id) || empty($location)) {
        $message = "All required fields must be filled";
        $message_type = 'error';
    } else {
        // Insertion en base
        $query = "INSERT INTO products (name_fr, name_en, description_fr, description_en, price, category_id, location, image_path) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $db->prepare($query);
            $success = $stmt->execute([
                $name_fr, $name_en, $description_fr, $description_en, 
                floatval($price), intval($category_id), $location, $image_path
            ]);
            
            if($success) {
                $_SESSION['message'] = "Product added successfully!";
                $_SESSION['message_type'] = 'success';
                header('Location: products.php');
                exit;
            } else {
                $message = "Error adding product to database";
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Variables pour le layout
$page_title = "Add Product - GPower Admin";
$current_page = "products";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Products', 'url' => 'products.php'],
    ['title' => 'Add Product']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Add New Product</h1>
    <p class="page-subtitle">Create a new product for your catalog</p>
</div>

<?php if($message): ?>
    <div class="message message-<?php echo $message_type; ?>">
        <span class="message-icon"><?php echo $message_type === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <div class="form-header">
        <h2>Product Information</h2>
    </div>
    
    <div class="form-content">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Product Name (French)</label>
                    <input type="text" name="name_fr" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_fr'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Product Name (English)</label>
                    <input type="text" name="name_en" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_en'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Price (USD)</label>
                    <input type="number" name="price" class="form-input" step="0.01" min="0" 
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name_fr']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Location</label>
                    <input type="text" name="location" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="image" class="file-input" accept="image/jpeg,image/png,image/gif" 
                               onchange="handleFileSelect(this)">
                        <div class="file-input-display" id="fileDisplay">
                            <span class="file-input-icon">📷</span>
                            <span class="file-input-text">Choose image file</span>
                        </div>
                    </div>
                    <div class="image-preview" id="imagePreview">
                        <img class="preview-image" id="previewImg" alt="Preview">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">Description (French)</label>
                    <textarea name="description_fr" class="form-textarea" rows="4"><?php echo htmlspecialchars($_POST['description_fr'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">Description (English)</label>
                    <textarea name="description_en" class="form-textarea" rows="4"><?php echo htmlspecialchars($_POST['description_en'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="products.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span>
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const file = input.files[0];
    const display = document.getElementById('fileDisplay');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        display.classList.add('has-file');
        display.querySelector('.file-input-text').textContent = file.name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(file);
    } else {
        display.classList.remove('has-file');
        display.querySelector('.file-input-text').textContent = 'Choose image file';
        preview.classList.remove('show');
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/admin-layout.php';
?>