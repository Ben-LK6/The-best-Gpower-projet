<?php
echo "PHP fonctionne !<br>";
echo "Version PHP: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

if (file_exists('index.php')) {
    echo "index.php existe<br>";
} else {
    echo "index.php n'existe pas<br>";
}

echo "<br>Variables d'environnement:<br>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'non défini') . "<br>";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'non défini') . "<br>";
?>