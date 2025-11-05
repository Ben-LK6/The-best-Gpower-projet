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
    
    // Validation
    if(empty($name_fr) || empty($name_en) || empty($price) || empty($category_id) || empty($location)) {
        $message = "All required fields must be filled";
        $message_type = 'error';
    } else {
        $success_count = 0;
        $error_count = 0;
        
        // Traiter chaque image uploadée
        if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $total_files = count($_FILES['images']['name']);
            
            for($i = 0; $i < $total_files; $i++) {
                if($_FILES['images']['error'][$i] === 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    $file_type = $_FILES['images']['type'][$i];
                    
                    if(in_array($file_type, $allowed_types)) {
                        $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                        $image_path = uniqid() . '.' . $extension;
                        $upload_path = '../uploads/' . $image_path;
                        
                        if(move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_path)) {
                            // Créer un nom unique pour chaque produit
                            $unique_name_fr = $name_fr . ' #' . ($i + 1);
                            $unique_name_en = $name_en . ' #' . ($i + 1);
                            
                            // Insertion en base
                            $query = "INSERT INTO products (name_fr, name_en, description_fr, description_en, price, category_id, location, image_path) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            
                            try {
                                $stmt = $db->prepare($query);
                                $success = $stmt->execute([
                                    $unique_name_fr, $unique_name_en, $description_fr, $description_en, 
                                    floatval($price), intval($category_id), $location, $image_path
                                ]);
                                
                                if($success) {
                                    $success_count++;
                                } else {
                                    $error_count++;
                                }
                            } catch (PDOException $e) {
                                $error_count++;
                            }
                        } else {
                            $error_count++;
                        }
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++;
                }
            }
            
            if($success_count > 0) {
                $_SESSION['message'] = "$success_count products added successfully!" . ($error_count > 0 ? " ($error_count failed)" : "");
                $_SESSION['message_type'] = 'success';
                header('Location: products.php');
                exit;
            } else {
                $message = "Failed to add products. Please check your images.";
                $message_type = 'error';
            }
        } else {
            $message = "Please select at least one image";
            $message_type = 'error';
        }
    }
}

// Variables pour le layout
$page_title = "Bulk Add Products - GPower Admin";
$current_page = "products";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Products', 'url' => 'products.php'],
    ['title' => 'Bulk Add Products']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Bulk Add Products</h1>
    <p class="page-subtitle">Add multiple products with the same information</p>
</div>

<?php if($message): ?>
    <div class="message message-<?php echo $message_type; ?>">
        <span class="message-icon"><?php echo $message_type === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <div class="form-header">
        <h2>Product Information (Applied to All)</h2>
    </div>
    
    <div class="form-content">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Base Product Name (French)</label>
                    <input type="text" name="name_fr" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_fr'] ?? ''); ?>" 
                           placeholder="e.g., Perceuse électrique" required>
                    <small>Each product will get a unique number: "Perceuse électrique #1", "Perceuse électrique #2", etc.</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Base Product Name (English)</label>
                    <input type="text" name="name_en" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['name_en'] ?? ''); ?>" 
                           placeholder="e.g., Electric Drill" required>
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
                    <label class="form-label required">Product Images</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="images[]" class="file-input" multiple 
                               accept="image/jpeg,image/png,image/gif" onchange="handleMultipleFiles(this)" required>
                        <div class="file-input-display" id="fileDisplay">
                            <span class="file-input-icon">📷</span>
                            <span class="file-input-text">Choose multiple images</span>
                        </div>
                    </div>
                    <div id="selectedFiles" class="selected-files"></div>
                    <div class="images-preview" id="imagesPreview"></div>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">Description (French)</label>
                    <textarea name="description_fr" class="form-textarea" rows="4" 
                              placeholder="This description will be applied to all products"><?php echo htmlspecialchars($_POST['description_fr'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">Description (English)</label>
                    <textarea name="description_en" class="form-textarea" rows="4" 
                              placeholder="This description will be applied to all products"><?php echo htmlspecialchars($_POST['description_en'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="products.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span>
                    Create Products
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.selected-files {
    margin-top: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    display: none;
}

.selected-files.show {
    display: block;
}

.file-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    font-size: 14px;
    color: #374151;
}

.file-icon {
    color: #10b981;
}

.images-preview {
    margin-top: 12px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.preview-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.preview-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 8px;
    font-size: 12px;
    text-align: center;
}
</style>

<script>
function handleMultipleFiles(input) {
    const files = input.files;
    const display = document.getElementById('fileDisplay');
    const selectedFiles = document.getElementById('selectedFiles');
    const imagesPreview = document.getElementById('imagesPreview');
    
    if (files.length > 0) {
        display.classList.add('has-file');
        display.querySelector('.file-input-text').textContent = `${files.length} images selected`;
        
        // Afficher la liste des fichiers
        selectedFiles.innerHTML = '';
        selectedFiles.classList.add('show');
        
        for (let i = 0; i < files.length; i++) {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <span class="file-icon">📷</span>
                <span>${files[i].name}</span>
            `;
            selectedFiles.appendChild(fileItem);
        }
        
        // Afficher les previews
        imagesPreview.innerHTML = '';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" class="preview-image" alt="Preview ${i + 1}">
                    <div class="preview-label">Product #${i + 1}</div>
                `;
                imagesPreview.appendChild(previewItem);
            };
            
            reader.readAsDataURL(file);
        }
    } else {
        display.classList.remove('has-file');
        display.querySelector('.file-input-text').textContent = 'Choose multiple images';
        selectedFiles.classList.remove('show');
        imagesPreview.innerHTML = '';
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/admin-layout.php';
?>