<?php

// DetteModel.php

namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;
use Src\App\Entity\DetteEntity;
use Src\App\Entity\ArticleEntity; // Assurez-vous d'importer la classe ArticleEntity
use PDO;
use Exception;

class DetteModel
{
    private $db;
    private $pdo;

    public function __construct(MysqlDatabase $db, PDO $pdo)
    {
        $this->db = $db;
        $this->pdo = $pdo;
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
    // public function creerNouvelleDette($clientId, $montantInitial, $dateCreation)
    // {
    //     $sql = "INSERT INTO dettes (client_id, montant_initial, montant_restant, date_creation) VALUES (?, ?, ?, ?)";
    //     return $this->db->query($sql, [$clientId, $montantInitial, $montantInitial, $dateCreation]);
    // }

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
    public function mettreAJourMontantRestant($detteId, $nouveauMontantRestant)
    {
        $query = "UPDATE dettes SET montant_restant = :montant_restant WHERE id = :id";
        $params = [
            ':montant_restant' => $nouveauMontantRestant,
            ':id' => $detteId
        ];
        return $this->db->query($query, $params);
    }
    public function getMontantVerseTotal($detteId)
    {
        $query = "SELECT SUM(montant_verser) AS total_verser FROM paiements WHERE dette_id = :dette_id";
        $params = [':dette_id' => $detteId];
        return $this->db->query($query, $params)->fetch()['total_verser'] ?? 0;
    }
    public function mettreAJourMontantVerser($detteId, $montantVerser)
    {
        $sql = "UPDATE dettes SET montant_verser = montant_verser + ? WHERE id = ?";
        return $this->db->query($sql, [$montantVerser, $detteId]);
    }

    public function getMontantVerserParDette($detteId)
    {
        try {
            $sql = "SELECT SUM(montant_verser) AS total FROM paiements WHERE dette_id = :detteId";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['detteId' => $detteId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant versé par dette: ' . $e->getMessage());
            return 0;
        }
    }
    // Ajoutez également votre méthode getDetteByClientId ici
    public function getDetteByClientId($clientId)
    {
        try {
            $sql = "SELECT * FROM dettes WHERE client_id = :client_id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération des dettes du client: ' . $e->getMessage());
            return [];
        }
    }

    public function obtenirPaiementsParDetteId($detteId)
    {
        $query = "SELECT * FROM paiement WHERE dette_id = ?";
        $params = [$detteId];

        return $this->db->query($query, $params)->fetchAll(); // Utilisez votre méthode de requête appropriée
    }
    public function ajouterArticleDette(int $detteId, int $articleId, int $quantite)
    {
        // Récupérer le prix unitaire de l'article
        $sqlArticle = "SELECT prix_unitaire FROM articles WHERE id = ?";
        $stmt = $this->db->query($sqlArticle, [$articleId]);
        $article = $stmt->fetch();

        if (!$article) {
            throw new \Exception("Article non trouvé.");
        }

        $prixUnitaire = $article['prix_unitaire'];
        $montant = $prixUnitaire * $quantite;

        // Insérer les détails de la dette
        $sql = "INSERT INTO details_dette (dette_id, article_id, quantite, prix_unitaire, montant) VALUES (?, ?, ?, ?, ?)";
        $this->db->query($sql, [$detteId, $articleId, $quantite, $prixUnitaire, $montant]);

        // Mettre à jour le montant restant de la dette
        $this->mettreAJourMontantRestant($detteId, $montant);
    }

    public function creerNouvelleDette($clientId, $montantInitial)
    {
        $sql = "INSERT INTO dettes (client_id, montant_initial, montant_restant, date_creation) VALUES (?, ?, ?, NOW())";
        $this->db->query($sql, [$clientId, $montantInitial, $montantInitial]);
        return $this->db->getPDO()->lastInsertId(); // Retourne l'ID de la nouvelle dette
    }

    public function validerDette($client_id, $articles)
    {
        try {
            // Démarrer la transaction
            $this->pdo->beginTransaction();

            // Calculer le montant restant du client
            $sqlMontantRestant = "SELECT SUM(montant_restant) AS total_restant FROM dettes WHERE client_id = :client_id";
            $stmtMontantRestant = $this->pdo->prepare($sqlMontantRestant);
            $stmtMontantRestant->execute(['client_id' => $client_id]);
            $montantRestant = $stmtMontantRestant->fetchColumn();

            // Insertion de la dette
            $stmtInsertDette = $this->pdo->prepare("INSERT INTO dettes (client_id, montant_restant, date_creation) VALUES (:client_id, :montant_restant, NOW())");
            $stmtInsertDette->execute(['client_id' => $client_id, 'montant_restant' => $montantRestant]);

            $detteId = $this->pdo->lastInsertId();

            // Insertion des articles dans la table de relation dette_articles
            $stmtInsertArticles = $this->pdo->prepare("INSERT INTO dette_articles (dette_id, article_id, quantite, prix_unitaire) VALUES (:dette_id, :article_id, :quantite, :prix_unitaire)");

            foreach ($articles as $item) {
                $stmtInsertArticles->execute([
                    'dette_id' => $detteId,
                    'article_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire']
                ]);
            }

            // Commit de la transaction
            $this->pdo->commit();

            return true;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $this->pdo->rollBack();
            throw new Exception('Erreur lors de la validation de la dette : ' . $e->getMessage());
        }
    }
}
