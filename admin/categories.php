<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Action : Supprimer
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // Vérifier si la catégorie est utilisée
    $check_query = "SELECT COUNT(*) FROM products WHERE category_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$_GET['id']]);
    $product_count = $check_stmt->fetchColumn();
    
    if($product_count > 0) {
        $_SESSION['error'] = "Impossible de supprimer : cette catégorie contient des produits";
    } else {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $db->prepare($query);
        if($stmt->execute([$_GET['id']])) {
            $_SESSION['message'] = "Catégorie supprimée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
    }
    header('Location: categories.php');
    exit;
}

// Récupérer toutes les catégories
$query = "SELECT * FROM categories ORDER BY name_fr";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Gestion Catégories</title>
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
                <li><a href="categories.php" class="active">Catégories</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <div class="page-header">
                <h1>📁 Gestion des Catégories</h1>
                <a href="add_category.php" class="btn btn-primary">➕ Ajouter une catégorie</a>
            </div>

            <!-- Messages -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Liste des catégories -->
            <div class="table-container">
                <?php if(empty($categories)): ?>
                    <div class="no-data">
                        <p>Aucune catégorie enregistrée.</p>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom Français</th>
                                <th>Nom English</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name_fr']); ?></td>
                                <td><?php echo htmlspecialchars($category['name_en']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($category['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-edit">✏️</a>
                                    <a href="categories.php?action=delete&id=<?php echo $category['id']; ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>