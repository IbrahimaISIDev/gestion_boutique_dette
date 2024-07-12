<?php
require_once 'vendor/autoload.php';

use Core\Database\MysqlDatabase;
use Dotenv\Dotenv;

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Création d'une instance de MysqlDatabase
    $db = new MysqlDatabase();
    
    // Tentative d'exécution d'une requête simple
    $result = $db->query("SELECT 1");
    
    if ($result) {
        echo "Connexion à la base de données réussie !";
        
        // Test supplémentaire : récupération des tables
        $tables = $db->query("SHOW TABLES");
        echo "\n\nTables dans la base de données :\n";
        while ($row = $tables->fetch(PDO::FETCH_NUM)) {
            echo "- " . $row[0] . "\n";
        }
    } else {
        echo "La connexion a été établie, mais la requête de test a échoué.";
    }
} catch (Exception $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}