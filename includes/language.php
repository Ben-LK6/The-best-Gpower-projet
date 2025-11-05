<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Langues supportées
$supported_languages = ['fr', 'en', 'es'];
$default_language = 'en';

// Détecter la langue
function detectLanguage() {
    global $supported_languages, $default_language;
    
    // 1. Langue dans l'URL (?lang=fr)
    if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
        $_SESSION['language'] = $_GET['lang'];
        return $_GET['lang'];
    }
    
    // 2. Langue en session
    if (isset($_SESSION['language']) && in_array($_SESSION['language'], $supported_languages)) {
        return $_SESSION['language'];
    }
    
    // 3. Langue du navigateur
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($browser_lang, $supported_languages)) {
            $_SESSION['language'] = $browser_lang;
            return $browser_lang;
        }
    }
    
    // 4. Langue par défaut
    $_SESSION['language'] = $default_language;
    return $default_language;
}

// Langue actuelle
$current_language = detectLanguage();

// Charger les traductions
$translations = [];
$lang_file = __DIR__ . "/../lang/{$current_language}.php";
if (file_exists($lang_file)) {
    $translations = include $lang_file;
}

// Fonction de traduction
function t($key, $default = '') {
    global $translations;
    return $translations[$key] ?? $default ?? $key;
}

// Fonction pour les URLs avec langue
function langUrl($lang) {
    $url = $_SERVER['REQUEST_URI'];
    $url = preg_replace('/[?&]lang=[^&]*/', '', $url);
    $separator = strpos($url, '?') !== false ? '&' : '?';
    return $url . $separator . 'lang=' . $lang;
}
?>