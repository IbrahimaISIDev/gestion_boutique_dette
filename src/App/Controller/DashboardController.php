<?php

namespace Src\App\Controller;

use Src\App\Model\ClientModel; // Assurez-vous que le chemin correspond au véritable emplacement de ClientModel.php
use Src\Core\Controller;
use Src\Core\Session;
use Src\Core\Database\MysqlDatabase;
use Exception; // Assurez-vous que l'Exception est correctement référencée

class DashboardController extends Controller
{
    private $clientModel;

    public function __construct(MysqlDatabase $db, Session $session)
    {
        parent::__construct($session);
        $this->clientModel = new ClientModel($db); // Assurez-vous que le chemin vers ClientModel est correctement configuré
    }

    public function index()
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $photoUrl = $_POST['photo-url'] ?? '';

            // Insertion des données dans la base de données
            try {
                $this->clientModel->ajouterClient($nom, $prenom, $email, $telephone, $photoUrl);
                $message = 'Client ajouté avec succès !';
            } catch (Exception $e) {
                $message = 'Erreur lors de l\'ajout du client : ' . $e->getMessage();
            }
        }

        // Afficher la vue par défaut du dashboard avec le message
        $this->renderView('dashboard/index', ['message' => $message]);
    }
}
