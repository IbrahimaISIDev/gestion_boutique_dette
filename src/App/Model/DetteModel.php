<?php
namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;
use Src\App\Entity\DetteEntity;

class DetteModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Ajoutez cette nouvelle méthode
    public function getTotalDettes()
    {
        $sql = "SELECT SUM(montant_restant) as total FROM dettes";
        $result = $this->db->query($sql);
        $row = $result->fetch();
        return $row['total'] ?: 0; // Retourne 0 si le total est NULL
    }

    public function creerNouvelleDette($clientId, $montantInitial, $dateCreation)
    {
        $sql = "INSERT INTO dettes (client_id, montant_initial, montant_restant, date_creation) VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, [$clientId, $montantInitial, $montantInitial, $dateCreation]);
    }

    public function obtenirDettesParClientId($clientId)
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

    public function mettreAJourMontantRestant($detteId, $nouveauMontant)
    {
        $sql = "UPDATE dettes SET montant_restant = ? WHERE id = ?";
        return $this->db->query($sql, [$nouveauMontant, $detteId]);
    }
}