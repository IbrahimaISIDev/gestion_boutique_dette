<?php

namespace Src\App\Model;

use Src\Core\Model\Model;
use Src\App\Entity\ClientEntity;
use Src\Core\Database\MysqlDatabase;

class ClientModel extends Model
{
    private $db;
    protected $table = 'clients';

    public function classEntityModel()
    {
        return ClientEntity::class;
    }

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
        parent::__construct($db);
    }

    public function ajouterClient($nom, $prenom, $email, $telephone, $photoUrl)
    {
        $motDePasse = password_hash('Passer123', PASSWORD_DEFAULT);

        $query = "INSERT INTO clients (nom, prenom, email, telephone, photo_url, mot_de_passe)
              VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$nom, $prenom, $email, $telephone, $photoUrl, $motDePasse];

        // Log the query and the parameters
        error_log('Requête SQL : ' . $query);
        error_log('Paramètres : ' . json_encode($params));

        try {
            $stmt = $this->db->getPDO()->prepare($query);
            $result = $stmt->execute($params);
            if ($result) {
                error_log('Client ajouté avec succès dans la base de données.');
            } else {
                error_log('Échec de l\'ajout du client.');
            }
            return $result;
        } catch (\PDOException $e) {
            error_log('Erreur lors de l\'ajout du client: ' . $e->getMessage());
            return false;
        }
    }

    // Test the database connection
    public function testerConnexion()
    {
        $query = "SELECT 1";
        try {
            $stmt = $this->db->getPDO()->prepare($query);
            $result = $stmt->execute();
            if ($result) {
                error_log('Connexion à la base de données réussie.');
            } else {
                error_log('Échec de la connexion à la base de données.');
            }
            return $result;
        } catch (\PDOException $e) {
            error_log('Erreur de connexion: ' . $e->getMessage());
            return false;
        }
    }



    public function getClientByTelephone($telephone)
    {
        try {
            $sql = "SELECT * FROM clients WHERE telephone = :telephone";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['telephone' => $telephone]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du client par téléphone: ' . $e->getMessage());
            return false;
        }
    }

    public function telephoneExists($telephone)
    {
        try {
            $stmt = $this->db->getPDO()->prepare("SELECT COUNT(*) FROM clients WHERE telephone = :telephone");
            $stmt->bindParam(':telephone', $telephone);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la vérification du numéro de téléphone: ' . $e->getMessage());
            return false;
        }
    }

    public function getMontantVersee($clientId)
    {
        try {
            $sql = "SELECT SUM(montant) AS total FROM paiement WHERE client_id = :clientId";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['clientId' => $clientId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant versé par client: ' . $e->getMessage());
            return 0;
        }
    }

    public function getUserByPhone($phone)
    {
        try {
            $sql = "SELECT * FROM clients WHERE telephone = :telephone";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['telephone' => $phone]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du client par téléphone: ' . $e->getMessage());
            return false;
        }
    }

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

    public function getTotalDettes($clientId)
    {
        try {
            $sql = "SELECT montantGlobale as total FROM Dette WHERE utilisateur_id = :client_id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant total des dettes: ' . $e->getMessage());
            return 0;
        }
    }

    public function getMontantVerse($clientId)
    {
        try {
            $sql = "SELECT SUM(p.montantVerser) as total FROM Paiement p JOIN Dette d ON p.dette_id = d.id WHERE d.utilisateur_id = :client_id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant versé: ' . $e->getMessage());
            return 0;
        }
    }

    public function getMontantRestant($clientId)
    {
        try {
            $sql = "SELECT p.montantRestant FROM Paiement p JOIN Dette d ON p.dette_id = d.id WHERE d.utilisateur_id = :client_id ORDER BY p.date DESC LIMIT 1";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['montantRestant'] ?? $this->getTotalDettes($clientId);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant restant: ' . $e->getMessage());
            return $this->getTotalDettes($clientId);
        }
    }

    public function getClientById($id)
    {
        try {
            $sql = "SELECT c.*, d.montant_initial, d.montant_verser, d.montant_restant 
                    FROM clients c 
                    LEFT JOIN dette d ON c.id = d.client_id 
                    WHERE c.id = :id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du client par ID: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllClients()
    {
        try {
            $sql = "SELECT * FROM clients";
            $stmt = $this->db->getPDO()->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération de tous les clients: ' . $e->getMessage());
            return false;
        }
    }
}
