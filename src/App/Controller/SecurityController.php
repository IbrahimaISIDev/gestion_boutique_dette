<?php
namespace Src\App\Controller;

use Src\Core\Controller;
use Src\Core\Database\MysqlDatabase;
use Src\App\Model\UtilisateurModel;

class SecurityController extends Controller
{
    private $utilisateurModel;

    public function __construct()
    {
        parent::__construct();
        $db = new MysqlDatabase();
        $this->utilisateurModel = new UtilisateurModel($db);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomUtilisateur = $_POST['nom_utilisateur'] ?? '';
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            $utilisateur = $this->utilisateurModel->authentifier($nomUtilisateur, $motDePasse);

            if ($utilisateur) {
                $this->session->set('user', $utilisateur);
                $this->redirect('/dashboard');
            } else {
                $this->session->set('error', 'Identifiants invalides');
            }
        }

        $this->renderView('security/login');
    }

    public function logout()
    {
        $this->session->remove('user');
        $this->redirect('/login');
    }
}