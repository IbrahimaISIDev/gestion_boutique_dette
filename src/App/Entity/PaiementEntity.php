<?php

namespace Src\App\Entity;

class PaiementEntity
{
    public $id;
    public $dette_id;
    public $montant;
    public $date_paiement;

    public function __construct($id = null, $dette_id = null, $montant = null, $date_paiement = null)
    {
        $this->id = $id;
        $this->dette_id = $dette_id;
        $this->montant = $montant;
        $this->date_paiement = $date_paiement;
    }
}

// namespace Src\App\Entity;

// use Src\Core\Entity\Entity;

// class PaiementEntity extends Entity
// {
//     protected $id;
//     protected $dette_id;
//     protected $montant;
//     protected $date_paiement;
// }