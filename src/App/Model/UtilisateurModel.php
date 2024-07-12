<?php
namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;
use Src\App\Entity\UtilisateurEntity;

class UtilisateurModel
{
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    public function authentifier($nomUtilisateur, $motDePasse)
    {
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = ?";
        $result = $this->db->query($sql, [$nomUtilisateur]);
        $user = $result->fetch();

        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            return new UtilisateurEntity($user);
        }

        return null;
    }

    public function obtenirUtilisateurParId($id)
    {
        $sql = "SELECT * FROM utilisateurs WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        $user = $result->fetch();

        if ($user) {
            return new UtilisateurEntity($user);
        }

        return null;
    }
}