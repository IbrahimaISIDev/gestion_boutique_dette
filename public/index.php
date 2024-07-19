<?php

define('ROOT', '/var/www/html/Diallo_Lebalmaa1/');

require_once __DIR__ . '/../vendor/autoload.php';
require_once ROOT . 'routes/web.php';

use Symfony\Component\Yaml\Yaml;
use Dotenv\Dotenv;

// Charger les variables d'environnement depuis le fichier .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    // Charger les configurations depuis le fichier YAML
    $Yaml = Yaml::parseFile('/var/www/html/Diallo_Lebalmaa1/ibrahima.yaml');

    // Assurez-vous que le fichier YAML contient le nom complet de la classe
    if (!isset($Yaml['MysqlDatabase'])) {
        throw new Exception('Configuration MysqlDatabase non trouvée dans ibrahima.yaml');
    }

    $className = $Yaml['MysqlDatabase'];

    // Création de l'objet PDO avec les paramètres nécessaires
    $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    $pdo = new PDO($dsn, $user, $password);

    // Utilisation de la réflexion pour instancier la classe
    $reflectionClass = new ReflectionClass($className);

    // Création de l'instance de la classe avec l'argument PDO
    $instance = $reflectionClass->newInstanceArgs([$pdo]);

    // Stockage de l'instance dans une variable globale pour un accès global
    $GLOBALS['mysqlDatabase'] = $instance;

} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit;
}
?>
