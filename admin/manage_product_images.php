<?php
session_start();

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Vérifier l'ID du produit
$product_id = $_GET['id'] ?? null;
if(!$product_id) {
    header('Location: products.php');
    exit;
}

// Récupérer le produit
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header('Location: products.php');
    exit;
}

// Traitement des actions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if($action === 'add_images') {
        $success_count = 0;
        
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
                            $label = $_POST['labels'][$i] ?? '';
                            
                            $query = "INSERT INTO product_images (product_id, image_path, image_label, sort_order) 
                                      VALUES (?, ?, ?, ?)";
                            $stmt = $db->prepare($query);
                            if($stmt->execute([$product_id, $image_path, $label, $i + 1])) {
                                $success_count++;
                            }
                        }
                    }
                }
            }
            
            $_SESSION['message'] = "$success_count images added successfully!";
            $_SESSION['message_type'] = 'success';
        }
    }
    
    if($action === 'delete_image') {
        $image_id = $_POST['image_id'] ?? null;
        if($image_id) {
            // Récupérer le chemin de l'image
            $query = "SELECT image_path FROM product_images WHERE id = ? AND product_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$image_id, $product_id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($image) {
                // Supprimer le fichier
                if(file_exists('../uploads/' . $image['image_path'])) {
                    unlink('../uploads/' . $image['image_path']);
                }
                
                // Supprimer de la base
                $query = "DELETE FROM product_images WHERE id = ? AND product_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$image_id, $product_id]);
                
                $_SESSION['message'] = "Image deleted successfully!";
                $_SESSION['message_type'] = 'success';
            }
        }
    }
    
    if($action === 'set_primary') {
        $image_id = $_POST['image_id'] ?? null;
        if($image_id) {
            // Retirer le statut primary de toutes les images
            $query = "UPDATE product_images SET is_primary = FALSE WHERE product_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$product_id]);
            
            // Définir la nouvelle image principale
            $query = "UPDATE product_images SET is_primary = TRUE WHERE id = ? AND product_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$image_id, $product_id]);
            
            $_SESSION['message'] = "Primary image updated!";
            $_SESSION['message_type'] = 'success';
        }
    }
    
    header("Location: manage_product_images.php?id=$product_id");
    exit;
}

// Récupérer toutes les images du produit
$query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables pour le layout
$page_title = "Manage Images - GPower Admin";
$current_page = "products";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'dashboard.php'],
    ['title' => 'Products', 'url' => 'products.php'],
    ['title' => 'Manage Images']
];

// Contenu de la page
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Manage Product Images</h1>
    <p class="page-subtitle"><?php echo htmlspecialchars($product['name_fr']); ?></p>
</div>

<?php if(isset($_SESSION['message'])): ?>
    <div class="message message-<?php echo $_SESSION['message_type'] ?? 'success'; ?>">
        <span class="message-icon"><?php echo ($_SESSION['message_type'] ?? 'success') === 'success' ? '✅' : '⚠️'; ?></span>
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<!-- Add Images Form -->
<div class="form-container">
    <div class="form-header">
        <h2>Add New Images</h2>
    </div>
    
    <div class="form-content">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_images">
            
            <div class="form-group">
                <label class="form-label">Select Images</label>
                <div class="file-input-wrapper">
                    <input type="file" name="images[]" class="file-input" multiple 
                           accept="image/jpeg,image/png,image/gif" onchange="handleImageUpload(this)" required>
                    <div class="file-input-display" id="fileDisplay">
                        <span class="file-input-icon">📷</span>
                        <span class="file-input-text">Choose images</span>
                    </div>
                </div>
                <div id="imageLabels" class="image-labels"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <span>📷</span>
                    Add Images
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Current Images -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Current Images (<?php echo count($images); ?>)</h2>
        <div class="table-actions">
            <a href="products.php" class="btn btn-secondary">← Back to Products</a>
        </div>
    </div>
    
    <?php if(empty($images)): ?>
        <div class="empty-state">
            <span class="empty-state-icon">📷</span>
            <h3 class="empty-state-title">No images yet</h3>
            <p class="empty-state-text">Add some images to showcase this product</p>
        </div>
    <?php else: ?>
        <div class="images-grid">
            <?php foreach($images as $image): ?>
                <div class="image-card">
                    <div class="image-preview">
                        <img src="../uploads/<?php echo $image['image_path']; ?>" alt="Product image">
                        <?php if($image['is_primary']): ?>
                            <div class="primary-badge">Primary</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="image-info">
                        <div class="image-label">
                            <?php echo htmlspecialchars($image['image_label'] ?: 'No label'); ?>
                        </div>
                        
                        <div class="image-actions">
                            <?php if(!$image['is_primary']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="set_primary">
                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                    <button type="submit" class="btn-icon" title="Set as primary">⭐</button>
                                </form>
                            <?php endif; ?>
                            
                            <button onclick="deleteImage(<?php echo $image['id']; ?>)" 
                                    class="btn-icon delete" title="Delete image">🗑️</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.image-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.image-card:hover {
    transform: translateY(-2px);
}

.image-preview {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.primary-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #10b981;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.image-info {
    padding: 16px;
}

.image-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
}

.image-actions {
    display: flex;
    gap: 8px;
}

.image-labels {
    margin-top: 12px;
    display: none;
}

.image-labels.show {
    display: block;
}

.label-input {
    margin-bottom: 8px;
}

.label-input label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}

.label-input input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}
</style>

<script>
function handleImageUpload(input) {
    const files = input.files;
    const labelsContainer = document.getElementById('imageLabels');
    
    if (files.length > 0) {
        document.getElementById('fileDisplay').querySelector('.file-input-text').textContent = 
            `${files.length} images selected`;
        
        // Créer les champs de label
        labelsContainer.innerHTML = '';
        labelsContainer.classList.add('show');
        
        for (let i = 0; i < files.length; i++) {
            const labelDiv = document.createElement('div');
            labelDiv.className = 'label-input';
            labelDiv.innerHTML = `
                <label>Label for "${files[i].name}":</label>
                <input type="text" name="labels[]" placeholder="e.g., Front view, Back view, Detail..." 
                       value="${i === 0 ? 'Front view' : i === 1 ? 'Back view' : 'View ' + (i + 1)}">
            `;
            labelsContainer.appendChild(labelDiv);
        }
    } else {
        labelsContainer.classList.remove('show');
    }
}

function deleteImage(imageId) {
    showConfirmModal(
        'Delete Image',
        'Are you sure you want to delete this image? This action cannot be undone.',
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_image">
                <input type="hidden" name="image_id" value="${imageId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}
</script>

<?php
$content = ob_get_clean();
include 'includes/admin-layout.php';
?>