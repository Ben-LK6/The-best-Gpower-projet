<?php
// Fichier de test simple - mettez-le à la racine
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test de déploiement</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic du déploiement</h1>
    
    <h2 class="success">✅ 1. PHP fonctionne !</h2>
    <div class="info">Version PHP: <?php echo phpversion(); ?></div>
    
    <h2>📁 2. Chemins et fichiers</h2>
    <div class="info">
        <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
        <strong>Script actuel:</strong> <?php echo $_SERVER['SCRIPT_FILENAME']; ?><br>
        <strong>URI demandée:</strong> <?php echo $_SERVER['REQUEST_URI']; ?><br>
    </div>
    
    <h2>📂 3. Fichiers dans le dossier racine</h2>
    <div class="info">
        <?php
        $files = scandir($_SERVER['DOCUMENT_ROOT']);
        echo "<ul>";
        foreach($files as $file) {
            if($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
        ?>
    </div>
    
    <h2>🔧 4. Variables d'environnement</h2>
    <div class="info">
        <?php
        $env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'APP_ENV'];
        foreach($env_vars as $var) {
            $value = getenv($var);
            if($value) {
                // Masquer le mot de passe
                if($var == 'DB_PASSWORD') {
                    $value = str_repeat('*', strlen($value));
                }
                echo "<strong>$var:</strong> $value<br>";
            } else {
                echo "<strong class='error'>$var:</strong> ❌ Non défini<br>";
            }
        }
        ?>
    </div>
    
    <h2>🗄️ 5. Test de connexion à la base de données</h2>
    <div class="info">
        <?php
        $host = getenv('DB_HOST');
        $db = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        
        if($host && $db && $user && $pass) {
            try {
                // Tester avec MySQL
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                echo "<span class='success'>✅ Connexion MySQL réussie !</span><br>";
                
                // Lister les tables
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                if(count($tables) > 0) {
                    echo "<strong>Tables trouvées (" . count($tables) . "):</strong><ul>";
                    foreach($tables as $table) {
                        echo "<li>$table</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<span class='error'>⚠️ Aucune table trouvée dans la base de données</span>";
                }
                
            } catch(PDOException $e) {
                // Si MySQL échoue, essayer PostgreSQL
                try {
                    $dsn = "pgsql:host=$host;dbname=$db";
                    $pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    echo "<span class='success'>✅ Connexion PostgreSQL réussie !</span><br>";
                    
                    // Lister les tables
                    $tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'")->fetchAll(PDO::FETCH_COLUMN);
                    if(count($tables) > 0) {
                        echo "<strong>Tables trouvées (" . count($tables) . "):</strong><ul>";
                        foreach($tables as $table) {
                            echo "<li>$table</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<span class='error'>⚠️ Aucune table trouvée dans la base de données</span>";
                    }
                    
                } catch(PDOException $e2) {
                    echo "<span class='error'>❌ Erreur de connexion: " . $e->getMessage() . "</span>";
                }
            }
        } else {
            echo "<span class='error'>❌ Variables de base de données non configurées</span>";
        }
        ?>
    </div>
    
    <h2>🔗 6. Test du fichier index.php</h2>
    <div class="info">
        <?php
        $index_path = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
        if(file_exists($index_path)) {
            echo "<span class='success'>✅ index.php existe</span><br>";
            echo "Taille: " . filesize($index_path) . " octets<br>";
            echo "<a href='/'>Tester index.php</a>";
        } else {
            echo "<span class='error'>❌ index.php n'existe pas à la racine</span>";
        }
        ?>
    </div>
    
    <h2>⚙️ 7. Extensions PHP chargées</h2>
    <div class="info">
        <?php
        $extensions = ['pdo', 'pdo_mysql', 'mysqli', 'gd', 'zip'];
        foreach($extensions as $ext) {
            if(extension_loaded($ext)) {
                echo "<span class='success'>✅ $ext</span><br>";
            } else {
                echo "<span class='error'>❌ $ext</span><br>";
            }
        }
        ?>
    </div>
</body>
</html>