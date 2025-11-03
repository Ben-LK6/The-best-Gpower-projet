<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$page_title = "produit - Modifier Produit";
include 'header-sidebar.php'; 

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les catégories
$categories_query = "SELECT * FROM categories ORDER BY name_fr";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le produit à modifier
$product_id = $_GET['id'] ?? null;
if(!$product_id) {
    header('Location: products.php');
    exit;
}

$query = "SELECT * FROM products WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header('Location: products.php');
    exit;
}

// Traitement du formulaire
if($_POST) {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $description_fr = $_POST['description_fr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Gestion de l'upload d'image
    $image_path = $product['image_path'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            // Supprimer l'ancienne image si elle existe
            if($image_path && file_exists('../uploads/' . $image_path)) {
                unlink('../uploads/' . $image_path);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_path);
        }
    }
    
    // Mise à jour en base
    $query = "UPDATE products SET 
              name_fr = ?, name_en = ?, description_fr = ?, description_en = ?, 
              price = ?, category_id = ?, location = ?, image_path = ? 
              WHERE id = ?";
    
    $stmt = $db->prepare($query);
    if($stmt->execute([$name_fr, $name_en, $description_fr, $description_en, $price, $category_id, $location, $image_path, $product_id])) {
        $_SESSION['message'] = "Produit modifié avec succès";
        header('Location: products.php');
        exit;
    } else {
        $error = "Erreur lors de la modification du produit";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Modifier Produit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>GPower Admin</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Produits</a></li>
                <li><a href="categories.php">Catégories</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <div class="page-header">
                <h1>✏️ Modifier le Produit</h1>
                <a href="products.php" class="btn btn-secondary">← Retour</a>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="current-image">
                    <?php if($product['image_path']): ?>
                        <p><strong>Image actuelle :</strong></p>
                        <img src="../uploads/<?php echo $product['image_path']; ?>" alt="Image actuelle" style="max-width: 200px; border-radius: 5px;">
                    <?php else: ?>
                        <p>Aucune image actuelle</p>
                    <?php endif; ?>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom (Français) *</label>
                        <input type="text" name="name_fr" value="<?php echo htmlspecialchars($product['name_fr']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom (English) *</label>
                        <input type="text" name="name_en" value="<?php echo htmlspecialchars($product['name_en']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (Français)</label>
                        <textarea name="description_fr" rows="3"><?php echo htmlspecialchars($product['description_fr']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (English)</label>
                        <textarea name="description_en" rows="3"><?php echo htmlspecialchars($product['description_en']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Prix (€) *</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="category_id" required>
                            <option value="">Choisir une catégorie</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo $category['name_fr']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Localisation *</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($product['location']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nouvelle image</label>
                        <input type="file" name="image" accept="image/*">
                        <small>Laissez vide pour conserver l'image actuelle</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                    <a href="products.php" class="btn btn-outline">❌ Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>