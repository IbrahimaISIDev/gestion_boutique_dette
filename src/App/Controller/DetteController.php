<?php

namespace Src\App\Controller;

use Src\App\Model\DetteModel;
use Src\App\Model\ClientModel;
use Src\Core\Controller;
use Src\Core\Database\MysqlDatabase;

class DetteController extends Controller
{
    private $detteModel;
    private $clientModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->detteModel = new DetteModel($database);
        $this->clientModel = new ClientModel($database);
    }

    public function suiviDette()
    {
        $clientId = $_GET['client_id'] ?? null;
        $page = $_GET['page'] ?? 1; // Par défaut, afficher la page 1
        $itemsPerPage = 5; // Nombre d'éléments par page

        if ($clientId) {
            $client = $this->clientModel->obtenirClientParId($clientId);
            if ($client !== null) {
                $dettes = $this->detteModel->obtenirDettesParClientId($clientId);

                if (is_array($dettes) && count($dettes) > 0) {
                    $totalItems = count($dettes);
                    $totalPages = ceil($totalItems / $itemsPerPage);

                    $start = ($page - 1) * $itemsPerPage;
                    $pagedDettes = array_slice($dettes, $start, $itemsPerPage);

                    $this->renderView('suiviDette', [
                        'client' => $client,
                        'dettes' => $pagedDettes,
                        'pagination' => [
                            'totalPages' => $totalPages,
                            'currentPage' => $page,
                            'itemsPerPage' => $itemsPerPage,
                        ],
                    ]);
                    return;
                } else {
                    $error = "Aucune dette trouvée pour ce client";
                }
            } else {
                $error = "Client non trouvé";
            }
        } else {
            $error = "ID du client manquant";
        }

        $this->renderView('suiviDette', ['error' => $error]);
    }

    public function details()
    {
        if (isset($_POST['idDette'])) {
            $detteId = $_POST['idDette'];
            $detteDetails = $this->detteModel->obtenirDetteParId($detteId);

            if ($detteDetails) {
                $clientDetails = $this->clientModel->obtenirClientParId($detteDetails->client_id);
                $this->renderView('detailsDette', [
                    'dette' => $detteDetails,
                    'client' => $clientDetails
                ]);
            } else {
                $this->renderView('detailsDette', ['error' => 'Aucune dette trouvée pour cet ID']);
            }
        } else {
            $this->renderView('detailsDette', ['error' => 'ID de dette manquant']);
        }
    }

    public function nouvelleDette()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_POST['client_id'];
            $montantInitial = $_POST['montant_initial'];
            $dateCreation = date('Y-m-d H:i:s'); // Utilisez la date et l'heure actuelles

            $this->detteModel->creerNouvelleDette($clientId, $montantInitial, $dateCreation);

            header('Location: /suivi-dette?client_id=' . $clientId);
            exit;
        } else {
            $this->renderView('nouvelleDette');
        }
    }
}
