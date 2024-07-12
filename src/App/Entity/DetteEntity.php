<?php
namespace Src\App\Entity;

use Src\Core\Entity\Entity;

class DetteEntity extends Entity
{
    protected $id;
    protected $client_id;
    protected $montant_initial;
    protected $montant_restant;
    protected $date_creation;
    protected $statut;
}