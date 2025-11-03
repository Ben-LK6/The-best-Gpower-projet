<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Traitement du formulaire
if($_POST) {
    $name_fr = $_POST['name_fr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    
    if(!empty($name_fr) && !empty($name_en)) {
        $query = "INSERT INTO categories (name_fr, name_en) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        if($stmt->execute([$name_fr, $name_en])) {
            $_SESSION['message'] = "Catégorie ajoutée avec succès";
            header('Location: categories.php');
            exit;
        } else {
            $error = "Erreur lors de l'ajout de la catégorie";
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
    <title>GPower - Ajouter Catégorie</title>
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
                <h1>➕ Ajouter une Catégorie</h1>
                <a href="categories.php" class="btn btn-secondary">← Retour</a>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="category-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom Français *</label>
                        <input type="text" name="name_fr" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom English *</label>
                        <input type="text" name="name_en" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Enregistrer la catégorie</button>
                    <a href="categories.php" class="btn btn-outline">❌ Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>