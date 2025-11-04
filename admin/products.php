<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Action : Supprimer un produit
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    if($stmt->execute([$_GET['id']])) {
        $_SESSION['message'] = "Produit supprimé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression";
    }
    header('Location: products.php');
    exit;
}

// Récupérer tous les produits
$query = "SELECT p.*, c.name_fr as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Gestion Produits</title>
    <link rel="stylesheet" href="products.css"> <!-- Ajout de la feuille de style spécifique à la page des produits -->
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>GPower Admin</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="active">Produits</a></li>
                <li><a href="categories.php">Catégories</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <div class="page-header">
                <h1>📦 Gestion des Produits</h1>
                <a href="add_product.php" class="btn btn-primary">➕ Ajouter un produit</a>
            </div>

            <!-- Messages -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Tableau des produits -->
            <div class="table-container">
                <?php if(empty($products)): ?>
                    <div class="no-data">
                        <p>Aucun produit enregistré.</p>
                        <a href="add_product.php" class="btn btn-primary">Ajouter votre premier produit</a>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Localisation</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if($product['image_path']): ?>
                                        <img src="../uploads/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name_fr']; ?>" class="product-thumb">
                                    <?php else: ?>
                                        <div class="no-thumb">🛠️</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name_fr']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                                <td><?php echo htmlspecialchars($product['location']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-edit">✏️</a>
                                    <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">🗑️</a>
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