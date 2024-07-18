<?php

namespace Src\App\Entity;

use Src\Core\Entity\Entity;

class ClientEntity extends Entity
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $adresse;
    private $created_at;
}
