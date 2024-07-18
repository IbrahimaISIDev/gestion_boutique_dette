<?php

namespace Src\App\Controller;

use Src\App\Model\DetteModel;
use Src\App\Model\ClientModel;
use Src\App\Model\ArticleModel;
use Src\Core\Controller;
use Src\Core\Database\MysqlDatabase;
use ReflectionClass;
use Exception;

class DetteController extends Controller
{
    private $detteModel;
    private $clientModel;
    private $articleModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../../config/config.php';
        $database = new MysqlDatabase($pdo);
        $this->detteModel = new DetteModel($database, $pdo);
        $this->clientModel = new ClientModel($database);
        $this->articleModel = new ArticleModel($pdo);
    }

    public function suiviDette()
    {
        $clientId = $_GET['client_id'] ?? null;
        $page = $_GET['page'] ?? 1; // Par défaut, afficher la page 1
        $itemsPerPage = 15; // Nombre d'éléments par page

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
                $articles = $this->detteModel->obtenirArticlesParDetteId($detteId); // Récupérer les articles liés à cette dette

                $this->renderView('detailsDette', [
                    'dette' => $detteDetails,
                    'client' => $clientDetails,
                    'articles' => $articles // Passez les articles à la vue
                ]);
            } else {
                $this->renderView('detailsDette', ['error' => 'Aucune dette trouvée pour cet ID']);
            }
        } else {
            $this->renderView('detailsDette', ['error' => 'ID de dette manquant']);
        }
    }

    public function detailsAvecArticles()
    {
        if (isset($_POST['idDette'])) {
            $detteId = $_POST['idDette'];
            $detteDetails = $this->detteModel->obtenirDetteParId($detteId);

            if ($detteDetails) {
                $clientDetails = $this->clientModel->obtenirClientParId($detteDetails->client_id);
                $articles = $this->detteModel->obtenirArticlesParDetteId($detteId); // Nouvelle méthode à ajouter

                $this->renderView('detailsDette', [
                    'dette' => $detteDetails,
                    'client' => $clientDetails,
                    'articles' => $articles // Passer les articles à la vue
                ]);
            } else {
                $this->renderView('detailsDette', ['error' => 'Aucune dette trouvée pour cet ID']);
            }
        } else {
            $this->renderView('detailsDette', ['error' => 'ID de dette manquant']);
        }
    }

    // Méthode pour afficher le formulaire de création d'une nouvelle dette
    public function nouvelleDette()
    {
        $client = null;
        $articles = $this->articleModel->getAllArticles();
        $prixUnitaire = 0;
        $selectedArticleId = null;

        // Vérifiez d'abord si client_id est présent dans GET ou POST
        $clientId = $_GET['client_id'] ?? $_POST['client_id'] ?? null;

        if ($clientId) {
            $client = $this->clientModel->obtenirClientParId($clientId);
            error_log("Client récupéré : " . print_r($client, true)); // Log pour le débogage
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['article_id'])) {
                $selectedArticleId = $_POST['article_id'];
                $selectedArticle = $this->articleModel->getArticleById($selectedArticleId);
                $prixUnitaire = $selectedArticle->prix_unitaire;
            }

            if (isset($_POST['client_id']) && isset($_POST['quantite'])) {
                $quantite = $_POST['quantite'];
                $montant = $prixUnitaire * $quantite;

                // Ajoutez l'article au panier
                $panier[] = [
                    'article_id' => $selectedArticleId,
                    'libelle' => $selectedArticle->libelle,
                    'quantite' => $quantite,
                    'montant' => $montant
                ];

                $_SESSION['panier'] = $panier;

                // Rediriger ou recharger la page pour afficher le panier mis à jour
                header('Location: nouvelleDette.php?client_id=' . $clientId);
                exit;
            }
        }

        // Utiliser la réflexion pour accéder aux propriétés privées de l'objet client
        $clientData = [];
        if ($client !== null) {
            $reflectionClass = new ReflectionClass($client);
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $clientData[$property->getName()] = $property->getValue($client);
            }
        }

        error_log("Données du client envoyées à la vue : " . print_r($clientData, true)); // Log pour le débogage

        $this->renderView('nouvelleDette', [
            'client' => $clientData,
            'articles' => $articles,
            'prix_unitaire' => $prixUnitaire,
            'selected_article_id' => $selectedArticleId,
            'panier' => $_SESSION['panier'] ?? []
        ]);
    }

    public function payerDette()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detteId = $_POST['idDette'] ?? null;
            $montantVerse = $_POST['montant_verser'] ?? null;

            // Log pour vérifier les données reçues
            error_log("ID de Dette: " . print_r($detteId, true));
            error_log("Montant Versé: " . print_r($montantVerse, true));

            if ($detteId !== null && $montantVerse !== null) {
                $dette = $this->detteModel->obtenirDetteParId($detteId);

                if ($dette !== null) {
                    $nouveauMontantRestant = $dette->montant_restant - $montantVerse;

                    if ($nouveauMontantRestant >= 0) {
                        $this->detteModel->mettreAJourMontantRestant($detteId, $nouveauMontantRestant);
                        $this->detteModel->mettreAJourMontantVerser($detteId, $montantVerse);

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

    public function listePaiements()
    {
        $detteId = $_GET['idDette'] ?? null;

        if ($detteId) {
            $dette = $this->detteModel->obtenirDetteParId($detteId);

            if ($dette !== null) {
                $paiements = $this->detteModel->obtenirPaiementsParDetteId($detteId); // Assurez-vous que cette méthode retourne les paiements

                $this->renderView('listePaiements', [
                    'dette' => $dette,
                    'paiements' => $paiements
                ]);
                return;
            } else {
                $error = 'Dette non trouvée';
            }
        } else {
            $error = 'ID de dette manquant';
        }

        // Afficher la vue avec l'erreur si quelque chose ne va pas
        $this->renderView('listePaiements', ['error' => $error]);
    }

    // public function validerPanier()
    // {
    //     // Récupération des données nécessaires pour la validation
    //     session_start();
    //     $panier = $_SESSION['panier'] ?? [];
    //     $client_id = $_SESSION['client_id'] ?? null;

    //     if (empty($panier) || !$client_id) {
    //         $_SESSION['message'] = 'Erreur : Panier vide ou client non défini.';
    //         header('Location: /'); // Rediriger vers la page d'accueil ou une autre page appropriée
    //         exit;
    //     }

    //     // Appel au modèle pour valider la dette
    //     try {
    //         $this->detteModel->validerDette($client_id, $panier);

    //         // Détruire la session et réinitialiser le panier
    //         unset($_SESSION['panier']);
    //         $_SESSION['message'] = 'La dette a été validée avec succès.';
    //         header('Location: /'); // Rediriger vers la page d'accueil ou une autre page appropriée
    //         exit;
    //     } catch (Exception $e) {
    //         $_SESSION['message'] = 'Erreur lors de la validation de la dette : ' . $e->getMessage();
    //         header('Location: /'); // Rediriger vers la page d'accueil ou une autre page appropriée
    //         exit;
    //     }
    // }

}