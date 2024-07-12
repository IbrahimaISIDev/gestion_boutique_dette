<?php
namespace Src\App\Entity;

use Src\Core\Entity\Entity;

class UtilisateurEntity extends Entity
{
    protected $id;
    protected $nom_utilisateur;
    protected $mot_de_passe;
    protected $role;
    protected $created_at;
}