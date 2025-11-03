<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer la catégorie à modifier
$category_id = $_GET['id'] ?? null;
if(!$category_id) {
    header('Location: categories.php');
    exit;
}

$query = "SELECT * FROM categories WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$category) {
    header('Location: categories.php');
    exit;
}

// Traitement du formulaire
if($_POST) {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    
    if(!empty($name_fr) && !empty($name_en)) {
        $query = "UPDATE categories SET name_fr = ?, name_en = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        if($stmt->execute([$name_fr, $name_en, $category_id])) {
            $_SESSION['message'] = "Catégorie modifiée avec succès";
            header('Location: categories.php');
            exit;
        } else {
            $error = "Erreur lors de la modification";
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Modifier Catégorie</title>
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
                <h1>✏️ Modifier la Catégorie</h1>
                <a href="categories.php" class="btn btn-secondary">← Retour</a>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="category-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom Français *</label>
                        <input type="text" name="name_fr" value="<?php echo htmlspecialchars($category['name_fr']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom English *</label>
                        <input type="text" name="name_en" value="<?php echo htmlspecialchars($category['name_en']); ?>" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                    <a href="categories.php" class="btn btn-outline">❌ Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>