<?php


namespace Src\App\Controller;

use Src\Core\Controller;
use Src\Core\Validator;
use Src\App\Model\ClientModel;
use Src\Core\Database\MysqlDatabase;

class ClientController extends Controller
{
    private $clientModel;

    public function __construct()
    {
        parent::__construct();
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->clientModel = new ClientModel($database);
    }

    public function index()
    {
        $clients = $this->clientModel->getAllClients();
        $this->renderView('dashboard/index', ['clients' => $clients]);
    }

    public function voir($id)
    {
        $client = $this->clientModel->getClientById($id);
        $this->renderView('dashboard/index', ['client' => $client]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifiez si les clés sont présentes dans $_POST
            if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'])) {
                $validator = new Validator();
                $rules = [
                    'nom' => ['required', 'min:2', 'max:50'],
                    'prenom' => ['required', 'min:2', 'max:50'],
                    'email' => ['required', 'email'],
                    'telephone' => ['required', 'min:10', 'max:15'],
                ];

                if ($validator->validate($_POST, $rules)) {
                    $photoUrl = null;
                    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $photoUrl = $this->handleFileUpload($_FILES['photo']);
                    }

                    if ($photoUrl === null && isset($_FILES['photo'])) {
                        $this->session->setFlash('error', 'Erreur lors du téléchargement de la photo');
                        $this->renderView('dashboard/index');
                        return;
                    }

                    $result = $this->clientModel->ajouterClient(
                        $_POST['nom'],
                        $_POST['prenom'],
                        $_POST['email'],
                        $_POST['telephone'],
                        $photoUrl
                    );

                    if ($result) {
                        error_log('Client ajouté avec succès : ' . $_POST['telephone']); // Debug message
                        $this->session->setFlash('success', 'Client ajouté avec succès');
                        $this->redirect('/dashboard');
                        return;
                    } else {
                        $this->session->setFlash('error', 'Erreur lors de l\'ajout du client');
                    }
                } else {
                    $this->session->setFlash('error', 'Erreurs de validation');
                    $this->renderView('dashboard/index', ['errors' => $validator->getErrors()]);
                    return;
                }
            } else {
                $this->session->setFlash('error', 'Données de formulaire manquantes');
                $this->renderView('dashboard/index');
                return;
            }
        }

        $this->renderView('dashboard/index');
    }


    private function handleFileUpload($file)
    {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '/var/www/html/Diallo_Lebalmaa1/public/uploads/';
            $fileName = uniqid() . '_' . basename($file['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                return '/uploads/' . $fileName;
            } else {
                $this->session->setFlash('error', 'Erreur lors du téléchargement du fichier');
            }
        }
        return null;
    }

    public function searchClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['telephone'])) {
            $telephone = $_POST['telephone'];
            $client = $this->clientModel->getClientByTelephone($telephone);

            if ($client) {
                $dette = $this->clientModel->getDetteByClientId($client['id']);
                $this->renderView('dashboard/index', [
                    'client' => $client,
                    'dette' => $dette
                ]);
            } else {
                $this->session->setFlash('error', 'Aucun client trouvé');
                $this->renderView('dashboard/index');
            }
        } else {
            $this->renderView('dashboard/index');
        }
    }

    public function viewClient($id)
    {
        $client = $this->clientModel->getClientById($id);
        $dette = $this->clientModel->getDetteByClientId($id); // Récupérer les détails des dettes
        if ($client) {
            $this->renderView('dashboard/index', [
                'client' => $client,
                'dette' => $dette // Passer les détails des dettes à la vue
            ]);
        } else {
            $this->session->setFlash('error', 'Client non trouvé');
            $this->renderView('dashboard/index');
        }
    }
}
