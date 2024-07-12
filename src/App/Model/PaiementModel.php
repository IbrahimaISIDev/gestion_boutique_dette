<?php
namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;
use Src\App\Entity\PaiementEntity;

class PaiementModel
{
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    // Ajoutez cette nouvelle méthode
    public function getPaiementsRecents($limit = 5)
    {
        $sql = "SELECT p.*, d.client_id FROM paiements p 
                JOIN dettes d ON p.dette_id = d.id 
                ORDER BY p.date_paiement DESC LIMIT ?";
        $result = $this->db->query($sql, [$limit]);
        $paiements = [];

        while ($row = $result->fetch()) {
            $paiement = new PaiementEntity();
            $paiement->id = $row['id'];
            $paiement->dette_id = $row['dette_id'];
            $paiement->montant = $row['montant'];
            $paiement->date_paiement = $row['date_paiement'];
            $paiement->client_id = $row['client_id']; // Ajout de l'ID du client
            $paiements[] = $paiement;
        }

        return $paiements;
    }

    public function enregistrerPaiement($detteId, $montant, $datePaiement)
    {
        $sql = "INSERT INTO paiements (dette_id, montant, date_paiement) VALUES (?, ?, ?)";
        return $this->db->query($sql, [$detteId, $montant, $datePaiement]);
    }

    public function obtenirPaiementsParDetteId($detteId)
    {
        $sql = "SELECT * FROM paiements WHERE dette_id = ?";
        $result = $this->db->query($sql, [$detteId]);
        $paiements = [];

        while ($row = $result->fetch()) {
            $paiement = new PaiementEntity();
            $paiement->id = $row['id'];
            $paiement->dette_id = $row['dette_id'];
            $paiement->montant = $row['montant'];
            $paiement->date_paiement = $row['date_paiement'];
            $paiements[] = $paiement;
        }

        return $paiements;
    }
}