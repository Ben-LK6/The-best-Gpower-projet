<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;    
}

$page_title = "Ajouter Produit";
include 'header-sidebar.php'; // Au lieu de header seul


require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les catégories
$categories_query = "SELECT * FROM categories ORDER BY name_fr";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// DEBUG: Vérifier la connexion
if(!$db) {
    die("Erreur de connexion à la base de données");
}

// Traitement du formulaire
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $description_fr = $_POST['description_fr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // DEBUG: Vérifier les données reçues
    error_log("Données reçues: name_fr=$name_fr, price=$price, category_id=$category_id");
    
    // Gestion de l'upload d'image
    $image_path = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = uniqid() . '.' . $extension;
            $upload_path = '../uploads/' . $image_path;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                error_log("Image uploadée: $upload_path");
            } else {
                error_log("Erreur upload image");
                $image_path = null;
            }
        }
    }
    
    // Validation des données requises
    if(empty($name_fr) || empty($name_en) || empty($price) || empty($category_id) || empty($location)) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } else {
        // Insertion en base
        $query = "INSERT INTO products (name_fr, name_en, description_fr, description_en, price, category_id, location, image_path) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $db->prepare($query);
            $success = $stmt->execute([
                $name_fr, 
                $name_en, 
                $description_fr, 
                $description_en, 
                floatval($price), 
                intval($category_id), 
                $location, 
                $image_path
            ]);
            
            if($success) {
                $last_id = $db->lastInsertId();
                error_log("Produit inséré avec ID: $last_id");
                $_SESSION['message'] = "✅ Produit ajouté avec succès (ID: $last_id)";
                header('Location: products.php');
                exit;
            } else {
                $error = "❌ Erreur lors de l'insertion en base de données";
                error_log("Erreur insertion: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            $error = "❌ Erreur base de données: " . $e->getMessage();
            error_log("PDOException: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Ajouter Produit</title>
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
                <h1>➕ Ajouter un Produit</h1>
                <a href="products.php" class="btn btn-secondary">← Retour aux produits</a>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Erreur:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom (Français) *</label>
                        <input type="text" name="name_fr" value="<?php echo htmlspecialchars($_POST['name_fr'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom (English) *</label>
                        <input type="text" name="name_en" value="<?php echo htmlspecialchars($_POST['name_en'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (Français)</label>
                        <textarea name="description_fr" rows="3"><?php echo htmlspecialchars($_POST['description_fr'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (English)</label>
                        <textarea name="description_en" rows="3"><?php echo htmlspecialchars($_POST['description_en'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Prix (€) *</label>
                        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="category_id" required>
                            <option value="">Choisir une catégorie</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name_fr']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Localisation *</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Image du produit</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
                        <small>Formats acceptés: JPG, PNG, GIF</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Enregistrer le produit</button>
                    <a href="products.php" class="btn btn-outline">❌ Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>