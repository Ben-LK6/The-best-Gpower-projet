<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "✅ Connexion à la base de données réussie!";
} else {
    echo "❌ Erreur de connexion";
}
?>