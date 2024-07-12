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


// Dispatch de la route actuelle
$router->routePage($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);