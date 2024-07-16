<?php
namespace Src\Core;

use Src\Core\Session;

abstract class Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    protected function renderView($view, $data = [])
    {
        extract($data);
        $viewPath = dirname(__DIR__) . "/App/Views/{$view}.php";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("View file not found: {$viewPath}");
        }
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }
}

