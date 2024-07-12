<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Base de données créée avec succès ou déjà existante.\n";

    $pdo->exec("USE $dbname");

    $pdo->exec("CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        adresse TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'clients' créée avec succès.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS dettes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        montant_initial DECIMAL(10, 2) NOT NULL,
        montant_restant DECIMAL(10, 2) NOT NULL,
        date_creation DATE NOT NULL,
        statut ENUM('en_cours', 'remboursee') DEFAULT 'en_cours',
        FOREIGN KEY (client_id) REFERENCES clients(id)
    )");
    echo "Table 'dettes' créée avec succès.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS paiements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dette_id INT NOT NULL,
        montant DECIMAL(10, 2) NOT NULL,
        date_paiement DATE NOT NULL,
        FOREIGN KEY (dette_id) REFERENCES dettes(id)
    )");
    echo "Table 'paiements' créée avec succès.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS utilisateurs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM('admin', 'vendeur') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'utilisateurs' créée avec succès.\n";

    // Insertion de données de test
    $pdo->exec("INSERT INTO clients (nom, prenom, telephone, adresse) VALUES
        ('Diallo', 'Mamadou', '0123456789', '123 Rue de Dakar, Dakar'),
        ('Sow', 'Fatou', '0987654321', '456 Avenue de Bamako, Bamako')
    ");
    echo "Clients de test ajoutés.\n";

    $pdo->exec("INSERT INTO dettes (client_id, montant_initial, montant_restant, date_creation) VALUES
        (1, 100000, 100000, '2023-07-01'),
        (2, 50000, 30000, '2023-07-15')
    ");
    echo "Dettes de test ajoutées.\n";

    $pdo->exec("INSERT INTO paiements (dette_id, montant, date_paiement) VALUES
        (2, 20000, '2023-07-20')
    ");
    echo "Paiement de test ajouté.\n";

    $pdo->exec("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role) VALUES
        ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin'),
        ('vendeur', '" . password_hash('vendeur123', PASSWORD_DEFAULT) . "', 'vendeur')
    ");
    echo "Utilisateurs de test ajoutés.\n";

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}