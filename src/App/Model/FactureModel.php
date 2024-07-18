<?php


namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;

class FactureModel
{
    private $db;

    public function __construct(MysqlDatabase $database)
    {
        $this->db = $database;
    }

    public function creerFacture($detteId, $montantVerse, $datePaiement)
    {
        $query = "INSERT INTO factures (dette_id, montant_verser, date_paiement) VALUES (:dette_id, :montant_verser, :date_paiement)";
        $params = [
            ':dette_id' => $detteId,
            ':montant_verser' => $montantVerse,
            ':date_paiement' => $datePaiement
        ];
        return $this->db->query($query, $params);
    }

    public function obtenirFactureParId($factureId)
    {
        $query = "SELECT * FROM factures WHERE id = :id";
        $params = [':id' => $factureId];
        return $this->db->query($query, $params)->fetch();
    }
}


// namespace App\Model;

// class FactureModel
// {
//     private $pdo;

//     public function __construct($pdo)
//     {
//         $this->pdo = $pdo;
//     }

//     public function creerFacture($detteId, $clientId, $montantTotal)
//     {
//         $stmt = $this->pdo->prepare("INSERT INTO factures (dette_id, client_id, montant_total) VALUES (:dette_id, :client_id, :montant_total)");
//         $stmt->execute([
//             'dette_id' => $detteId,
//             'client_id' => $clientId,
//             'montant_total' => $montantTotal,
//         ]);

//         return $this->pdo->lastInsertId();
//     }

//     public function obtenirFactureParId($id)
//     {
//         $stmt = $this->pdo->prepare("SELECT * FROM factures WHERE id = :id");
//         $stmt->execute(['id' => $id]);
//         return $stmt->fetch();
//     }
// }
