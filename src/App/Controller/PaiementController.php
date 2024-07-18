<?php

namespace Src\App\Controller;

use Src\App\Model\PaiementModel;
use Src\App\Model\DetteModel;
use Src\App\Model\FactureModel;
use Src\Core\Controller;
use Src\Core\Database\MysqlDatabase;

class PaiementController extends Controller
{
    private $paiementModel;
    private $detteModel;
    private $factureModel; // Ajout de la propriété $factureModel

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->paiementModel = new PaiementModel($database);
        $this->detteModel = new DetteModel($database);
        $this->factureModel = new FactureModel($database); // Initialisation du modèle de facture
    }

    public function afficherFormulairePaiement()
    {
        $detteId = $_GET['idDette'] ?? null;

        if ($detteId) {
            $dette = $this->detteModel->obtenirDetteParId($detteId);

            if ($dette) {
                $this->renderView('formulairePaiement', ['dette' => $dette]);
            } else {
                $this->renderView('formulairePaiement', ['error' => 'Aucune dette trouvée pour cet ID']);
            }
        } else {
            $this->renderView('formulairePaiement', ['error' => 'ID de dette manquant']);
        }
    }

    public function payerDette()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detteId = $_POST['idDette'] ?? null;
            $montantVerser = $_POST['montant_verser'] ?? null; // Correction de la variable $montantVerser

            if ($detteId && $montantVerser) {
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette) {
                    $nouveauMontantRestant = $dette->getMontantRestant() - $montantVerser;

                    if ($nouveauMontantRestant >= 0) {
                        // Mettre à jour le montant restant de la dette
                        $this->detteModel->mettreAJourMontantRestant($detteId, $nouveauMontantRestant);

                        // Création de la facture
                        $datePaiement = date('Y-m-d H:i:s');
                        $this->factureModel->creerFacture($detteId, $montantVerser, $datePaiement); // Utilisation de $montantVerser
                        // Rediriger vers une page de confirmation ou de détails
                        header('Location: /details-dette?idDette=' . $detteId);
                        exit;
                    } else {
                        $error = 'Le montant versé ne peut pas être supérieur au montant restant.';
                    }
                } else {
                    $error = 'Dette non trouvée pour cet ID.';
                }
            } else {
                $error = 'ID de dette ou montant versé manquant.';
            }

            // Si une erreur survient, afficher à nouveau le formulaire avec l'erreur
            $this->renderView('formulairePaiement', ['error' => $error]);
        } else {
            // Si la méthode n'est pas POST, rediriger vers une page d'erreur ou gérer selon votre logique
            http_response_code(405); // Méthode non autorisée
            echo 'Méthode non autorisée.';
        }
    }

    public function processPaiement()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detteId = $_POST['idDette'] ?? null;
            $montantVerser = $_POST['montant_verser'] ?? null;

            if ($detteId !== null && $montantVerser !== null) { // Utilisation de $montantVerser
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette !== null) {
                    $nouveauMontantRestant = $dette->getMontantRestant() - $montantVerser;

                    if ($nouveauMontantRestant >= 0) {
                        $this->detteModel->mettreAJourMontantRestant($detteId, $nouveauMontantRestant);
                        // Redirection vers les détails de la dette après le paiement
                        header('Location: /details-dette?idDette=' . $detteId);
                        exit;
                    } else {
                        $error = 'Le montant versé ne peut pas être supérieur au montant restant';
                    }
                } else {
                    $error = 'Dette non trouvée';
                }
            } else {
                $error = 'ID de dette ou montant versé manquant';
            }

            // Si une erreur survient, afficher à nouveau la vue avec l'erreur
            $this->renderView('payerDette', ['error' => $error]);
        } else {
            // Si la méthode n'est pas POST, rediriger vers une page d'erreur par exemple
            header('Location: /erreur');
            exit;
        }
    }
}
