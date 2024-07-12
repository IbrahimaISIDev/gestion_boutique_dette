<?php
namespace Src\Core\Model;

use PDO;

abstract class Model
{
    protected $table;
    protected $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql);
    }

    public function find($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id], null, true);
    }

    public function save($obj)
    {
        if (!is_object($obj)) {
            throw new \InvalidArgumentException('Expected object, got ' . gettype($obj));
        }
        $fields = array_keys(get_object_vars($obj));
        $columns = implode(", ", $fields);
        $placeholders = ":" . implode(", :", $fields);

        if (property_exists($obj, 'id') && !empty($obj->id)) {
            $updateColumns = [];
            foreach ($fields as $field) {
                $updateColumns[] = "$field = :$field";
            }
            $updateSql = "UPDATE {$this->table} SET " . implode(", ", $updateColumns) . " WHERE id = :id";
            return $this->query($updateSql, (array) $obj);
        } else {
            $insertSql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            return $this->query($insertSql, (array) $obj);
        }
    }



    public function query($sql, $params = [], $classEntity = null, $single = false)
    {
        return $this->database->prepare($sql, $params, $classEntity ?: get_class($this), $single);
    }

    public function prepare($sql, $params = [], $classEntity = null, $single = false)
    {
        return $this->database->prepare($sql, $params, $classEntity ?: get_class($this), $single);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->query($sql, ['id' => $id]);
    }

    public function update($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE {$this->table} SET $fields WHERE id = :id";
        $data['id'] = $id;
        return $this->query($sql, $data);
    }

    public function setDatabase($database)
    {
        $this->database = $database;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->query($sql, ['id' => $id], get_class($this), true);
    }
}
