<?php

namespace Src\App;

use Dotenv\Dotenv;
use Src\Core\Database\MysqlDatabase;

class App
{
    private static $instance = null;
    private $database;

    private function __construct()
    {
        // $this->database = Database::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    public function getDatabase(){
        if ($this->database === null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $this->database =new MysqlDatabase($_ENV['dsn'],$_ENV['DB_USER'],$_ENV['DB_PASSWORD']);
        }
        return $this->database;
    }

    public function getModel($model)
    {
        $modelClass = "Src\\App\\Model\\" . ucfirst($model);
        $newModel = new $modelClass($this->getDatabase());
        $newModel->setDatabase($this->getDatabase());
        
        return $newModel;
    }

    public function notFound()
    {
        http_response_code(404);
        echo '404 Not Found';
    }

    public function forbidden()
    {
        http_response_code(403);
        echo '403 Forbidden';
    }
}
