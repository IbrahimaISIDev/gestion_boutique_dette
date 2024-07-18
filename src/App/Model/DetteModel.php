<?php

// DetteModel.php

namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;
use Src\App\Entity\DetteEntity;
use Src\App\Entity\ArticleEntity; // Assurez-vous d'importer la classe ArticleEntity

class DetteModel
{
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    // Méthode pour obtenir le total des dettes
    public function getTotalDettes()
    {
        $sql = "SELECT SUM(montant_restant) AS total FROM dettes";
        $result = $this->db->query($sql);
        $row = $result->fetch();
        return $row['total'] ?: 0; // Retourne 0 si le total est NULL
    }

    // Méthode pour créer une nouvelle dette pour un client
    public function creerNouvelleDette($clientId, $montantInitial, $dateCreation)
    {
        $sql = "INSERT INTO dettes (client_id, montant_initial, montant_restant, date_creation) VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, [$clientId, $montantInitial, $montantInitial, $dateCreation]);
    }

    // Méthode pour obtenir les détails d'une dette par son ID de dette
    /**
     * @param int $detteId
     * @return DetteEntity|null
     */
    public function obtenirDetteParId(int $detteId)
    {
        $sql = "SELECT * FROM dettes WHERE id = ?";
        $result = $this->db->query($sql, [$detteId]);
        $row = $result->fetch();

        if (!$row) {
            return null;
        }

        $dette = new DetteEntity();
        $dette->id = $row['id'];
        $dette->client_id = $row['client_id'];
        $dette->montant_initial = $row['montant_initial'];
        $dette->montant_verser = $row['montant_verser'];
        $dette->montant_restant = $row['montant_restant'];
        $dette->date_creation = $row['date_creation'];
        $dette->statut = $row['statut'];

        return $dette;
    }

    // Méthode pour obtenir les dettes par ID de client
    /**
     * @param int $clientId
     * @return DetteEntity[]
     */
    public function obtenirDettesParClientId(int $clientId)
    {
        $sql = "SELECT * FROM dettes WHERE client_id = ?";
        $result = $this->db->query($sql, [$clientId]);
        $dettes = [];

        while ($row = $result->fetch()) {
            $dette = new DetteEntity();
            $dette->id = $row['id'];
            $dette->client_id = $row['client_id'];
            $dette->montant_initial = $row['montant_initial'];
            $dette->montant_verser = $row['montant_verser'];
            $dette->montant_restant = $row['montant_restant'];
            $dette->date_creation = $row['date_creation'];
            $dette->statut = $row['statut'];
            $dettes[] = $dette;
        }

        return $dettes;
    }

    // Méthode pour obtenir les articles d'une dette par ID de dette
    public function obtenirArticlesParDetteId(int $detteId)
    {
        $sql = "SELECT a.id, a.libelle, ad.quantite, ad.prix_unitaire
                FROM articles a
                INNER JOIN details_dette ad ON a.id = ad.article_id
                WHERE ad.dette_id = ?";
        $result = $this->db->query($sql, [$detteId]);
        $articles = []; // Initialisation du tableau d'articles

        while ($row = $result->fetch()) {
            // Calcul du montant pour chaque article
            $montant = $row['quantite'] * $row['prix_unitaire'];

            // Instanciation de ArticleEntity avec les données récupérées
            $article = new ArticleEntity(
                $row['id'],
                $row['libelle'],
                $row['quantite'],
                $row['prix_unitaire'],
                $montant,
                $detteId
            );

            $articles[] = $article; // Ajout de l'article au tableau
        }

        return $articles; // Retour du tableau d'articles
    }



    // Méthode pour mettre à jour le montant restant d'une dette
    public function mettreAJourMontantRestant($detteId, $nouveauMontant)
    {
        $sql = "UPDATE dettes SET montant_restant = ? WHERE id = ?";
        return $this->db->query($sql, [$nouveauMontant, $detteId]);
    }
    public function mettreAJourMontantVerser($detteId, $montantVerser)
    {
        $sql = "UPDATE dettes SET montant_verser = montant_verser + ? WHERE id = ?";
        return $this->db->query($sql, [$montantVerser, $detteId]);
    }
}
