<?php

namespace Src\App\Model;

use Src\Core\Model\Model;
use Src\App\Entity\ClientEntity;
use Src\Core\Database\MysqlDatabase;
use ReflectionClass;

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
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la vérification du numéro de téléphone: ' . $e->getMessage());
            return false;
        }
    }

    public function getMontantVerserParDette($detteId)
    {
        try {
            $sql = "SELECT SUM(montant_verser) AS total FROM paiements WHERE dette_id = :dette_id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['dette_id' => $detteId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du montant versé pour la dette: ' . $e->getMessage());
            return 0;
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

    public function getTotalDetteByClientId($clientId)
    {
        try {
            $sql = "SELECT SUM(montant_initial) AS montant_initial 
                    FROM dettes 
                    WHERE client_id = :client_id";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            $montant_initial = $stmt->fetch(\PDO::FETCH_ASSOC)['montant_initial'] ?? 0;

            $sql = "SELECT SUM(montant_verser) AS montant_verser 
                    FROM paiements 
                    WHERE dette_id IN (SELECT id FROM dettes WHERE client_id = :client_id)";
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute(['client_id' => $clientId]);
            $montant_verser = $stmt->fetch(\PDO::FETCH_ASSOC)['montant_verser'] ?? 0;

            $montant_restant = $montant_initial - $montant_verser;

            return [
                'montant_initial' => $montant_initial,
                'montant_verser' => $montant_verser,
                'montant_restant' => $montant_restant
            ];
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération du total des dettes: ' . $e->getMessage());
            return [
                'montant_initial' => 0,
                'montant_verser' => 0,
                'montant_restant' => 0
            ];
        }
    }

    public function getClientById($id)
    {
        try {
            $sql = "SELECT c.*, d.montant_initial, d.montant_verser, d.montant_restant 
                    FROM clients c 
                    LEFT JOIN dettes d ON c.id = d.client_id 
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

    public function obtenirClientParId(int $clientId)
    {
        $sql = "SELECT * FROM clients WHERE id = ?";
        $result = $this->db->query($sql, [$clientId]);
        $row = $result->fetch();

        if (!$row) {
            echo "Aucun client trouvé avec l'ID : $clientId";
            return null;
        }

        $client = new ClientEntity();
        $reflectionClass = new ReflectionClass($client);

        foreach ($row as $property => $value) {
            if ($reflectionClass->hasProperty($property)) {
                $prop = $reflectionClass->getProperty($property);
                $prop->setAccessible(true);
                $prop->setValue($client, $value);
            }
        }

        return $client;
    }

    public function obtenirTousLesClients()
    {
        $query = "SELECT * FROM clients";
        return $this->database->query($query);
    }

    public function suiviDette($clientId)
    {
        $dettes = $this->getDetteByClientId($clientId);
        foreach ($dettes as &$dette) {
            $dette['montant_verser'] = $this->getMontantVerserParDette($dette['id']);
        }
        return $dettes;
    }
}
