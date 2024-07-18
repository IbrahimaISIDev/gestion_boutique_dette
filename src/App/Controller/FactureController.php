<?php

namespace Src\App\Controller;

use Src\Core\Controller;
use Src\App\Model\ClientModel;
use TCPDF;

class FactureController extends Controller
{
    private $clientModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $this->clientModel = new ClientModel($pdo);
    }

    public function genererFacture($clientId, $montantVerse)
    {
        // Récupérer les détails du client
        $client = $this->clientModel->obtenirClientParId($clientId);

        if (!$client) {
            http_response_code(404);
            die("Client non trouvé.");
        }

        // Générer le nom de fichier unique pour la facture
        $nomFichier = 'facture_' . $client->nom . '_' . date('YmdHis') . '.pdf';
        
        // Chemin complet pour sauvegarder la facture dans le dossier public
        $cheminFichier = __DIR__ . '/../../../public/factures/' . $nomFichier;

        // Créer un nouvel objet TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Paramètres du document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Votre Nom');
        $pdf->SetTitle('Facture');
        $pdf->SetSubject('Facture Client');
        $pdf->SetKeywords('Facture, Client, Paiement');

        // Ajouter une page
        $pdf->AddPage();

        // Styles Tailwind CSS pour la facture (à adapter selon vos besoins)
        $styles = "
            <style>
                .font-bold { font-weight: bold; }
                .text-center { text-align: center; }
                .mt-4 { margin-top: 1rem; }
                .mb-4 { margin-bottom: 1rem; }
                .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                .px-4 { padding-left: 1rem; padding-right: 1rem; }
                .bg-blue-500 { background-color: #3b82f6; }
                .text-white { color: #ffffff; }
                .border { border: 1px solid #e2e8f0; }
            </style>
        ";

        // Ajouter les styles à la page PDF
        $pdf->writeHTML($styles, true, false, true, false, '');

        // Contenu de la facture (exemples simples)
        $html = '
            <div class="font-bold text-center mb-4">
                <h1>Facture</h1>
            </div>
            <div class="mb-4">
                <p><strong>Nom du Client:</strong> ' . htmlspecialchars($client->nom) . '</p>
                <p><strong>Email du Client:</strong> ' . htmlspecialchars($client->email) . '</p>
                <p><strong>Montant Versé:</strong> ' . htmlspecialchars($montantVerse) . ' F CFA</p>
            </div>
            <!-- Ajoutez d\'autres détails de la facture selon vos besoins -->
        ';

        // Écrire le contenu HTML dans le PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Sauvegarder le PDF dans le dossier public
        $pdf->Output($cheminFichier, 'F');

        // Redirection vers une page de confirmation ou téléchargement automatique
        header('Location: /confirmation-facture?nomFichier=' . urlencode($nomFichier));
        exit;
    }
}
