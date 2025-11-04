<?php
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => $_ENV['APP_ENV'] ?? 'development'
];

// Test de connexion à la base de données
try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        $status['database'] = 'connected';
    } else {
        $status['database'] = 'failed';
        $status['status'] = 'error';
    }
} catch (Exception $e) {
    $status['database'] = 'error: ' . $e->getMessage();
    $status['status'] = 'error';
}

echo json_encode($status, JSON_PRETTY_PRINT);
?>