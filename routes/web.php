<?php

use Src\Core\Router;
use Dotenv\Dotenv;

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialisation du routeur
$router = new Router();

// Définition des routes

// Route pour ajouter une dette
$router->get('/dette/add', ["Controller" => 'ExoController', "action" => "index"]);

// Routes pour le dashboard
$router->get('/dashboard', ["Controller" => 'DashboardController', "action" => "index"]);
$router->post('/dashboard', ["Controller" => 'DashboardController', "action" => "index"]);

// Route pour rechercher un client
$router->post('/recherche', ['Controller' => 'ClientController', 'action' => 'searchClient']);

// Routes pour suivre les dettes
$router->get('/suivi-dette', ['Controller' => 'DetteController', 'action' => 'suiviDette']);
$router->post('/filtre', ['Controller' => 'DetteController', 'action' => 'suiviDette']);

// Route pour afficher les détails d'une dette
$router->post('/details-dette', ['Controller' => 'DetteController', 'action' => 'details']);
$router->post('/details-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'details']);

$router->post('/generer-facture', ['Controller' => 'PaiementController', 'action' => 'genererFacture']);


// Définir les routes pour le paiement de la dette
Router::get('/payer-dette', ['Controller' => 'PaiementController', 'action' => 'afficherFormulairePaiement']);
Router::post('/payer-dette', ['Controller' => 'PaiementController', 'action' => 'payerDette']);

// Route pour ajouter une nouvelle dette
$router->get('/nouvelle-dette', ['Controller' => 'DetteController', 'action' => 'nouvelleDette']);
$router->get('/nouvelle-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'nouvelleDette']);

// Route pour afficher le détail d'une dette
$router->get('/suivi-dette-', ['Controller' => 'DetteController', 'action' => 'detailDette']);

// Dispatch de la route actuelle
$router->routePage($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
