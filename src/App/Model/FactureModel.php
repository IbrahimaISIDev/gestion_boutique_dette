<?php

namespace App\Model;

class FactureModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function creerFacture($detteId, $clientId, $montantTotal)
    {
        $stmt = $this->pdo->prepare("INSERT INTO factures (dette_id, client_id, montant_total) VALUES (:dette_id, :client_id, :montant_total)");
        $stmt->execute([
            'dette_id' => $detteId,
            'client_id' => $clientId,
            'montant_total' => $montantTotal,
        ]);

        return $this->pdo->lastInsertId();
    }

    public function obtenirFactureParId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM factures WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
