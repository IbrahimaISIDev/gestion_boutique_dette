<?php

namespace Src\App\Controller;

use Src\App\Model\ClientModel;
use Src\Core\Controller;
use Src\Core\Session;
use Src\Core\Database\MysqlDatabase; // Assurez-vous du chemin correct vers MysqlDatabase.php
use Exception;

class DashboardController extends Controller
{
    private $clientModel;
    private $db;

    public function __construct(MysqlDatabase $db, Session $session)
    {
        parent::__construct($session);
        $this->clientModel = new ClientModel($db);
        $this->db = $db;
    }

    public function index()
    {
        $message = '';
        $client = null;
        $totalDette = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['telephone'])) {
            $telephone = $_POST['telephone'];

            $client = $this->clientModel->getClientByTelephone($telephone);
            if ($client) {
                $totalDette = $this->clientModel->getTotalDetteByClientId($client['id']);
            } else {
                $message = 'Client non trouvé.';
            }
        }

        $this->renderView('dashboard/index', [
            'message' => $message,
            'client' => $client,
            'totalDette' => $totalDette
        ]);
    }
}
