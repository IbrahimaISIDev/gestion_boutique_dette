<?php
// src/App/Entity/ArticleEntity.php

namespace Src\App\Entity;

class ArticleEntity
{
    public $id;
    public $libelle;
    public $quantite;
    public $prix_unitaire;
    public $montant;
    public $dette_id; // Ajout de dette_id si nécessaire
    
    // Constructeur pour initialiser les propriétés
    public function __construct($id, $libelle, $quantite, $prix_unitaire, $montant, $dette_id)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->quantite = $quantite;
        $this->prix_unitaire = $prix_unitaire;
        $this->montant = $montant;
        $this->dette_id = $dette_id; // Initialisation de dette_id si nécessaire
    }
}
?>
