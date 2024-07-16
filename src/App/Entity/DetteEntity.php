<?php
namespace Src\App\Entity;

use ReflectionClass;
use ReflectionProperty;

class DetteEntity
{
    private int $id;
    private int $client_id;
    private float $montant_initial;
    private float $montant_verser;
    private float $montant_restant;
    private string $date_creation;
    private string $statut;

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data)
    {
        $reflection = new ReflectionClass($this);
        foreach ($data as $key => $value) {
            if ($reflection->hasProperty($key)) {
                $property = $reflection->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($this, $value);
            }
        }
    }

    public function __get($name)
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->hasProperty($name)) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            return $property->getValue($this);
        }
        throw new \Exception("La propriété '$name' n'existe pas.");
    }

    public function __set($name, $value)
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->hasProperty($name)) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($this, $value);
        } else {
            throw new \Exception("La propriété '$name' n'existe pas.");
        }
    }

    // Les méthodes spécifiques peuvent rester pour une meilleure lisibilité
    public function getDateDette()
    {
        return $this->date_creation;
    }

    public function getMontantTotal()
    {
        return $this->montant_initial;
    }

    public function getMontantVerser()
    {
        return $this->montant_verser;
    }

    public function getMontantRestant()
    {
        return $this->montant_restant;
    }

    public function getId()
    {
        return $this->id;
    }
}