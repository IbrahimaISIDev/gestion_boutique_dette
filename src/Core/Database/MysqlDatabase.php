<?php

namespace Src\Core\Database;

use PDO;

class MysqlDatabase
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // PREPARE statement

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }
    public function execute($query, $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
