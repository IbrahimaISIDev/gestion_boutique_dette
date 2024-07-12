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
        require_once "/var/www/html/Diallo_Lebalmaa1/src/App/Views/" .$view. ".php";
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }
}

