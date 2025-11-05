<?php
function trackPageView($page_type, $product_id = null) {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Obtenir le pays via IP (service gratuit)
    $country = getCountryFromIP($_SERVER['REMOTE_ADDR']);
    
    $query = "INSERT INTO page_views (page_url, page_type, product_id, ip_address, country, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_SERVER['REQUEST_URI'],
        $page_type,
        $product_id,
        $_SERVER['REMOTE_ADDR'],
        $country,
        $_SERVER['HTTP_USER_AGENT']
    ]);
}

function getCountryFromIP($ip) {
    // Service gratuit pour géolocalisation
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return 'Localhost';
    }
    
    $url = "http://ip-api.com/json/{$ip}";
    $response = @file_get_contents($url);
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && $data['status'] === 'success') {
            return $data['country'];
        }
    }
    
    return null;
}
?>