<?php

namespace Src\App\Controller;

use Src\App\Model\PaiementModel;
use Src\App\Model\DetteModel;
use Src\App\Model\FactureModel;
use Src\App\Model\ClientModel; // Import ClientModel
use Src\Core\Controller;
use Src\Core\Database\MysqlDatabase;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;

class PaiementController extends Controller
{
    private $paiementModel;
    private $detteModel;
    private $factureModel;
    private $clientModel; // Declare clientModel as a property

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->paiementModel = new PaiementModel($database);
        $this->detteModel = new DetteModel($database, $pdo);
        $this->factureModel = new FactureModel($database);
        $this->clientModel = new ClientModel($database); // Initialize clientModel
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
            $montantVerse = $_POST['montant_verser'] ?? null;

            if ($detteId !== null && $montantVerse !== null) {
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette !== null) {
                    $nouveauMontantRestant = $dette->montant_restant - $montantVerse;

                    if ($nouveauMontantRestant >= 0) {
                        $this->detteModel->mettreAJourMontantRestant($detteId, $nouveauMontantRestant);
                        $this->detteModel->mettreAJourMontantVerser($detteId, $montantVerse);

                        // Générer la facture et obtenir le nom du fichier
                        $fileName = $this->genererFacture($dette, $montantVerse);

                        // Redirection vers la page de visualisation de la facture
                        header('Location: /visualiser-facture?idDette=' . $detteId . '&fileName=' . $fileName);
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
            // Si la méthode n'est pas POST, afficher simplement la vue pour payer la dette
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


    private function genererFacture($dette, $montantVerse)
    {
        $client = $this->clientModel->obtenirClientParId($dette->client_id);
        $factureId = uniqid();
        $date = date('Y-m-d H:i:s');
        $fileName = "facture_{$factureId}.pdf";
        $filePath = __DIR__ . '/../../../public/factures/' . $fileName;

        // Création du PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Titre de la facture
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Facture de Paiement', 0, 1, 'C');
        $pdf->Ln(10);

        // Informations de la facture
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Facture ID: {$factureId}", 0, 1);
        $pdf->Cell(0, 10, "Date: {$date}", 0, 1);
        $pdf->Cell(0, 10, "Client: {$client->nom} {$client->prenom}", 0, 1);
        $pdf->Cell(0, 10, "Montant versé: {$montantVerse} F CFA", 0, 1);
        $pdf->Cell(0, 10, "Montant restant: " . ($dette->montant_restant - $montantVerse) . " F CFA", 0, 1);
        $pdf->Ln(10);

        // Ajouter plus de détails selon vos besoins...

        // Sauvegarde du PDF
        $pdf->Output('F', $filePath);

        // Retourner le nom du fichier pour l'affichage dans la vue HTML
        return $fileName;
    }

    public function SuiviDette($clientId)
    {
        // Récupération des dettes du client
        $dettes = $this->detteModel->getDetteByClientId($clientId);

        foreach ($dettes as $dette) {
            $dette['montant_verser'] = $this->detteModel->getMontantVerserParDette($dette['id']);
        }

        // Renvoyer les dettes avec les montants versés mis à jour
        return $dettes;
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

    public function visualiserFacture()
    {
        $detteId = $_GET['idDette'] ?? null;
        $fileName = $_GET['fileName'] ?? null;

        if ($detteId && $fileName) {
            $filePath = __DIR__ . '/../../../public/factures/' . $fileName;

            if (file_exists($filePath)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $fileName . '"');
                readfile($filePath);
            } else {
                echo "Facture non trouvée";
            }
        } else {
            echo "ID de dette ou nom de fichier manquant";
        }
    }

    public function telechargerFacture()
    {
        $detteId = $_GET['idDette'] ?? null;

        if ($detteId) {
            $dette = $this->detteModel->obtenirDetteParId($detteId);
            $client = $this->clientModel->obtenirClientParId($dette->client_id);

            $factureId = uniqid();
            $date = date('Y-m-d H:i:s');
            $montantVerse = $dette->montant_verser; // Exemple, vous pouvez ajuster en fonction de vos besoins

            ob_start();
            $this->renderView('facture', [
                'factureId' => $factureId,
                'date' => $date,
                'client' => $client,
                'montantVerse' => $montantVerse,
                'dette' => $dette
            ]);
            $html = ob_get_clean();

            // Options de Dompdf
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Sortie du PDF dans un fichier
            $fileName = "facture_{$factureId}.pdf";
            $filePath = __DIR__ . '/../../../public/factures/' . $fileName;
            file_put_contents($filePath, $dompdf->output());

            // Redirection pour télécharger le PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            readfile($filePath);
        } else {
            echo "ID de dette manquant";
        }
    }

    // PaiementController.php

    public function afficherListePaiements()
    {
        $detteId = $_GET['idDette'] ?? null;

        if ($detteId) {
            // Récupérer la dette depuis le modèle
            $dette = $this->detteModel->obtenirDetteParId($detteId);

            if ($dette) {
                // Récupérer les paiements associés à cette dette depuis le modèle
                $paiements = $this->paiementModel->obtenirPaiementsParDetteId($detteId);

                // Afficher la vue avec la liste des paiements
                $this->renderView('listePaiements', ['dette' => $dette, 'paiements' => $paiements]);
            } else {
                $this->renderView('listePaiements', ['error' => 'Aucune dette trouvée pour cet ID']);
            }
        } else {
            $this->renderView('listePaiements', ['error' => 'ID de dette manquant']);
        }
    }
}
