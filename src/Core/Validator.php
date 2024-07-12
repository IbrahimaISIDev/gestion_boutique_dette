<?php
namespace Src\Core;

class Validator
{
    private $errors = [];

    public function validate($data, $rules)
    {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule => $parameter) {
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $data[$field] ?? null, $parameter);
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function validateRequired($field, $value, $parameter)
    {
        if (empty($value)) {
            $this->errors[$field][] = "Le champ $field est requis.";
        }
    }

    private function validateMin($field, $value, $parameter)
    {
        if (strlen($value) < $parameter) {
            $this->errors[$field][] = "Le champ $field doit contenir au moins $parameter caractères.";
        }
    }

    private function validateMax($field, $value, $parameter)
    {
        if (strlen($value) > $parameter) {
            $this->errors[$field][] = "Le champ $field ne doit pas dépasser $parameter caractères.";
        }
    }

    private function validateEmail($field, $value, $parameter)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Le champ $field doit être une adresse email valide.";
        }
    }

    // Ajoutez d'autres méthodes de validation selon vos besoins
}
?>
