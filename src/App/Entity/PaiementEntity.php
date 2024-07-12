<?php
namespace Src\App\Entity;

use Src\Core\Entity\Entity;

class PaiementEntity extends Entity
{
    protected $id;
    protected $dette_id;
    protected $montant;
    protected $date_paiement;
}