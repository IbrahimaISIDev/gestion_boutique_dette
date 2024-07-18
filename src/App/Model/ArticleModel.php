<?php

namespace Src\App\Model;

use PDO;

class ArticleModel
{
    private $db;
    protected $table = 'articles';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllArticles()
    {
        $stmt = $this->db->prepare("SELECT id, libelle, prix_unitaire FROM articles");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getArticleById($id)
    {
        $stmt = $this->db->prepare("SELECT id, libelle, prix_unitaire FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>
