<?php

use Src\Core\Router;
use Dotenv\Dotenv;

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialisation du routeur
$router = new Router();

// Routes
$router->get('/dette/add', ["Controller" => 'ExoController', "action" => "index"]);

$router->get('/dashboard', ["Controller" => 'DashboardController', "action" => "index"]);
$router->post('/dashboard', ["Controller" => 'DashboardController', "action" => "index"]);

$router->post('/recherche', ['Controller' => 'ClientController', 'action' => 'searchClient']);



$router->get('/suivi-dette', ['Controller' => 'DetteController', 'action' => 'suiviDette']);

$router->post('/filtre', ['Controller' => 'DetteController', 'action' => 'suiviDette']);

$router->post('/details-dette', ['Controller' => 'DetteController', 'action' => 'details']);

// $router->get('/suivi-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'suiviDette']);

$router->post('/details-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'details']);
$router->post('/payer-dette', ['Controller' => 'PaiementController', 'action' => 'payerDette']);




// Router::post('/details-dette/client/{id}', [
//     'Controller' => 'DetteController',
//     'action' => 'details'
// ]);

// Router::post('/payer-dette/client/{id}', [
//     'Controller' => 'DetteController',
//     'action' => 'payerDette'
// ]);


// Router::get('/details-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'details']);
// Router::post('/payer-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'payerDette']);
// Router::get('/liste-paiements/{id}', ['Controller' => 'PaiementController', 'action' => 'listePaiements']);


$router->get('/suivi-dette-', ['Controller' => 'DetteController', 'action' => 'detailDette']);
$router->get('/nouvelle-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'nouvelleDette']);
$router->get('/payer-dette/client/{id}', ['Controller' => 'PaiementController', 'action' => 'payerDette']);

// $router->get('/nouvelle-dette', ['Controller' => 'DetteController', 'action' => 'nouvelleDette']);
// $router->get('/details-dette/client/{id}', ['Controller' => 'DetteController', 'action' => 'details']);
// $router->get('/payer-dette/client/{id}', ['Controller' => 'PaiementController', 'action' => 'payerDette']);
// $router->get('/liste-paiements/{id}', ['Controller' => 'PaiementController', 'action' => 'listePaiements']);


// $router->get('/client/{id}', ['Controller' => 'ClientController', 'action' => 'detailsClient']);

// Redirections
// $router->redirect('/login', '/dashboard');

// Erreur 404
// $router->error(404, function () {
//     return "Page non trouvée";
// });

// Lancement du routeur
// $router->run();


// Dispatch de la route actuelle
$router->routePage($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
