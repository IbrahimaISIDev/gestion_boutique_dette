<?php
namespace Src\Core\Entity;

abstract class Entity
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get($property)
    {
        if (array_key_exists($property, $this->data)) {
            return $this->data[$property];
        } else {
            return $this->getPropertyValue($property);
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            $this->setPropertyValue($property, $value);
        }
    }

    protected function getPropertyValue($property)
    {
        $reflection = new \ReflectionClass($this);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($this);
    }

    protected function setPropertyValue($property, $value)
    {
        $reflection = new \ReflectionClass($this);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }
}
?>
