<?php

namespace Src\App\Controller;

use Src\App\Model\PaiementModel;
use Src\App\Model\DetteModel;
use Src\Core\Controller;
use Src\Core\Session;
use Src\Core\Database\MysqlDatabase;

class PaiementController extends Controller
{
    private $paiementModel;
    private $detteModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->paiementModel = new PaiementModel($database);
        $this->detteModel = new DetteModel($database);
    }

    public function payerDette()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $detteId = $_GET['idDette'] ?? null;
            $montantVerse = $_GET['montant_verser'] ?? null;

            if ($detteId !== null && $montantVerse !== null) {
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette !== null) {
                    $nouveauMontantRestant = $dette->getMontantRestant() - $montantVerse;

                    if ($nouveauMontantRestant >= 0) {
                        $this->detteModel->mettreAJourMontantRestant($detteId, $nouveauMontantRestant);
                        // Redirection vers les détails de la dette après le paiement
                        header('Location: /details-dette?idDette=' . $detteId);
                        exit;
                    } else {
                        $this->renderView('payerDette', ['error' => 'Le montant versé ne peut pas être supérieur au montant restant', 'dette' => $dette]);
                    }
                } else {
                    $this->renderView('payerDette', ['error' => 'Dette non trouvée']);
                }
            } else {
                $this->renderView('payerDette', ['error' => 'ID de dette ou montant versé manquant']);
            }
        } else {
            $detteId = $_GET['idDette'] ?? null;

            if ($detteId) {
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette !== null) {
                    $this->renderView('payerDette', ['dette' => $dette]);
                } else {
                    $this->renderView('payerDette', ['error' => 'Dette non trouvée']);
                }
            } else {
                $this->renderView('payerDette', ['error' => 'ID de dette manquant']);
            }
        }
    }
}
